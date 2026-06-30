<?php

namespace App\Http\Controllers\Directeur;

use App\Exports\NotesExport;
use App\Exports\StagiaireNotesExport;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Unite;
use App\Models\Groupe;
use App\Models\Filiere;
use App\Models\Stagiaire;
use App\Services\Notes\StudentResultsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NoteController extends Controller
{
    // =========================================================
    // PRIVÉ: Poids CC / Théo / Prat depuis la DB
    // =========================================================
    private function getPoidsFromUnite(Unite $unite): array
    {
        $unitExams = $unite->unitExams;
        return [
            'poids_cc'   => (int) ($unitExams->firstWhere('type', 'cc')?->poids        ?? 0),
            'poids_theo' => (int) ($unitExams->firstWhere('type', 'theorique')?->poids  ?? 0),
            'poids_prat' => (int) ($unitExams->firstWhere('type', 'pratique')?->poids   ?? 0),
        ];
    }

    // =========================================================
    // PRIVÉ: Calcule moyenne CC
    // =========================================================
    private function computeMoyCC(Collection $allNotes, int $stagiaireId, Collection $moduleIds): ?float
    {
        $ccNotes = $allNotes
            ->whereNotNull('evaluation_id')
            ->where('stagiaire_id', $stagiaireId)
            ->filter(fn($n) => $moduleIds->contains($n->evaluation?->module_id));

        $modulesMoyennes = $ccNotes
            ->groupBy(fn($n) => $n->evaluation->module_id)
            ->map(function ($modGroup) {
                $module    = $modGroup->first()->evaluation->module;
                $totalEval = $module->evaluations->count();
                $filled    = $modGroup->pluck('note')->filter(fn($n) => !is_null($n));

                if ($filled->count() === $totalEval && $totalEval > 0) {
                    return round($filled->avg(), 2);
                }
                return null;
            })
            ->filter(fn($m) => !is_null($m));

        return $modulesMoyennes->count() > 0
            ? round($modulesMoyennes->avg(), 2)
            : null;
    }

    // =========================================================
    // PRIVÉ: Calcule moyenne unité avec poids
    // =========================================================
    private function computeMoyUnite(
        ?float $moyCC, ?float $noteTheo, ?float $notePrat,
        int $poidsCC, int $poidsTheo, int $poidsPrat
    ): ?float {
        if ($poidsCC   > 0 && is_null($moyCC))    return null;
        if ($poidsTheo > 0 && is_null($noteTheo)) return null;
        if ($poidsPrat > 0 && is_null($notePrat)) return null;

        $totalPoids = $totalNote = 0;

        if ($poidsCC   > 0) { $totalNote += $moyCC    * $poidsCC;   $totalPoids += $poidsCC;   }
        if ($poidsTheo > 0) { $totalNote += $noteTheo * $poidsTheo; $totalPoids += $poidsTheo; }
        if ($poidsPrat > 0) { $totalNote += $notePrat * $poidsPrat; $totalPoids += $poidsPrat; }

        return $totalPoids > 0 ? round($totalNote / $totalPoids, 2) : null;
    }

    // =========================================================
    // PRIVÉ: Mention selon la moyenne
    // =========================================================
    private function getMention(?float $moy): ?array
    {
        if (is_null($moy)) return null;

        return match(true) {
            $moy >= 16 => ['label' => 'Très Bien',  'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            $moy >= 14 => ['label' => 'Bien',        'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
            $moy >= 12 => ['label' => 'Assez Bien',  'class' => 'bg-cyan-50 text-cyan-700 border-cyan-200'],
            $moy >= 10 => ['label' => 'Passable',    'class' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
            default    => ['label' => 'Insuffisant', 'class' => 'bg-red-50 text-red-700 border-red-200'],
        };
    }

    // =========================================================
    // PRIVÉ: Groupes PFE (dernière année de chaque filière)
    // =========================================================
    private function getPfeGroupes(): Collection
    {
        return Groupe::with(['niveau.filiere', 'stagiaires.pfe'])
            ->get()
            ->filter(fn($g) => $g->niveau?->ordre === $g->niveau?->filiere?->duree)
            ->values();
    }

    // =========================================================
    // INDEX — عرض كل النوتات (CC + Exam + PFE + Totales)
    // =========================================================
    public function index(Request $request, StudentResultsService $studentResults)
    {
        $filters = $this->validateResultFilters($request);

        $allNotes = Note::with([
            'stagiaire.groupe.niveau.filiere',
            'evaluation.module.evaluations',
            'unitExam.unite.modules.evaluations',
            'unitExam.unite.unitExams',
        ])->latest()->get();

        // ── CC Rows ──
        $ccRows = $allNotes
            ->whereNotNull('evaluation_id')
            ->groupBy(fn($n) => $n->stagiaire_id . '-' . $n->evaluation?->module_id)
            ->filter(fn($group) => $group->first()->evaluation?->module_id)
            ->map(function ($group) {
                $ccs              = $group->sortBy('evaluation.nom')->values();
                $module           = $ccs->first()->evaluation?->module;
                $totalEvaluations = $module->evaluations->count();
                $filled           = $ccs->pluck('note')->filter(fn($n) => !is_null($n));

                $moyenne = ($filled->count() === $totalEvaluations && $totalEvaluations > 0)
                    ? round($filled->avg(), 2)
                    : null;

                return [
                    'stagiaire' => $ccs->first()->stagiaire,
                    'module'    => $module,
                    'ccs'       => $ccs,
                    'nb_cc'     => $ccs->count(),
                    'moyenne'   => $moyenne,
                    'all_valid' => $ccs->every(fn($n) => $n->statut === 'validee'),
                    'ids'       => $ccs->pluck('id')->toArray(),
                ];
            })->values();

        $maxCC = $ccRows->max(fn($row) => $row['ccs']->count()) ?? 0;

        // ── Exam Rows ──
        $examRows = $allNotes
            ->whereNotNull('unit_exam_id')
            ->groupBy(fn($n) => $n->stagiaire_id . '-' . $n->unitExam?->unite_id)
            ->filter(fn($g) => $g->first()->unitExam?->unite_id)
            ->map(function ($group) use ($allNotes) {
                $first     = $group->first();
                $unite     = $first->unitExam->unite;
                $stagiaire = $first->stagiaire;

                $theorique = $group->first(fn($n) => $n->unitExam->type === 'theorique');
                $pratique  = $group->first(fn($n) => $n->unitExam->type === 'pratique');

                $poids     = $this->getPoidsFromUnite($unite);
                $moduleIds = $unite->modules->pluck('id');
                $moyCC     = $this->computeMoyCC($allNotes, $stagiaire->id, $moduleIds);

                $moyUnite = $this->computeMoyUnite(
                    $moyCC,
                    $theorique?->note,
                    $pratique?->note,
                    $poids['poids_cc'],
                    $poids['poids_theo'],
                    $poids['poids_prat']
                );

                return [
                    'stagiaire'  => $stagiaire,
                    'unite'      => $unite,
                    'theorique'  => $theorique,
                    'pratique'   => $pratique,
                    'moy_cc'     => $moyCC,
                    'poids_cc'   => $poids['poids_cc'],
                    'poids_theo' => $poids['poids_theo'],
                    'poids_prat' => $poids['poids_prat'],
                    'moy_unite'  => $moyUnite,
                    'all_valid'  => $group->every(fn($n) => $n->statut === 'validee'),
                    'ids'        => $group->pluck('id')->toArray(),
                ];
            })->values();

        // ── Exam Headers ──
        $uniteForPoids = Unite::with('unitExams')->first();
        $defaultPoids  = $uniteForPoids
            ? $this->getPoidsFromUnite($uniteForPoids)
            : ['poids_cc' => 30, 'poids_theo' => 20, 'poids_prat' => 30];

        $examHeaders = [];
        if ($defaultPoids['poids_cc']   > 0) $examHeaders[] = ['label' => 'Moy. CC',   'poids' => $defaultPoids['poids_cc']];
        if ($defaultPoids['poids_theo'] > 0) $examHeaders[] = ['label' => 'Théorique', 'poids' => $defaultPoids['poids_theo']];
        if ($defaultPoids['poids_prat'] > 0) $examHeaders[] = ['label' => 'Pratique',  'poids' => $defaultPoids['poids_prat']];

        // ── PFE ──
        $pfeGroupes    = $this->getPfeGroupes();
        $pfeStagiaires = Stagiaire::with(['groupe.niveau.filiere', 'pfe'])
            ->whereIn('group_id', $pfeGroupes->pluck('id'))
            ->get();

        // ── Totales (Résultats) ──
        $uniteNames = $examRows
            ->map(fn($r) => $r['unite']->nom)
            ->unique()->sort()->values()->toArray();

        $resultats = $examRows
            ->groupBy(fn($r) => $r['stagiaire']->id)
            ->map(function ($group) use ($uniteNames, $pfeStagiaires) {
                $stagiaire = $group->first()['stagiaire'];

                $unitesMoy = collect($uniteNames)->mapWithKeys(function ($uniteName) use ($group) {
                    $row = $group->first(fn($r) => $r['unite']->nom === $uniteName);
                    return [$uniteName => $row ? $row['moy_unite'] : null];
                })->toArray();

                $hasIncomplete = $group->contains(fn($row) => is_null($row['moy_unite']));

                $pfeStagiaire = $pfeStagiaires->firstWhere('id', $stagiaire->id);
                $notesPfe     = $pfeStagiaire?->pfe?->note;

                if ($hasIncomplete) {
                    $moyGen = null;
                } else {
                    $totalNote  = 0.0;
                    $totalCoeff = 0.0;
                    foreach ($group as $row) {
                        $coeff       = (float) ($row['unite']->coefficient ?? 1);
                        $totalNote  += $row['moy_unite'] * $coeff;
                        $totalCoeff += $coeff;
                    }
                    $moyUnits = $totalCoeff > 0 ? round($totalNote / $totalCoeff, 2) : null;

                    $filiereDuree = (int) ($stagiaire->groupe?->niveau?->filiere?->duree ?? 0);
                    $niveauOrdre  = (int) ($stagiaire->groupe?->niveau?->ordre ?? 0);
                    $isLastYear   = $niveauOrdre === $filiereDuree;

                    if ($isLastYear) {
                        $moyGen = (is_null($moyUnits) || is_null($notesPfe))
                            ? null
                            : round(($moyUnits * 0.8) + ($notesPfe * 0.2), 2);
                    } else {
                        $moyGen = $moyUnits;
                    }
                }

                return [
                    'stagiaire'  => $stagiaire,
                    'unites_moy' => $unitesMoy,
                    'note_pfe'   => $notesPfe,
                    'moy_gen'    => $moyGen,
                    'mention'    => $this->getMention($moyGen),
                ];
            })->values();

        // ── Filtres (Filières + Groupes) ──
        $filieres = Filiere::orderBy('nom')->get();
        $groupes  = Groupe::with('niveau.filiere')->orderBy('nom')->get();
        $selectedFiliereId = $filters['filiere_id'] ?? null;
        $selectedGroupeId = $filters['groupe_id'] ?? null;
        $selectedFiliere = $selectedFiliereId ? $filieres->firstWhere('id', (int) $selectedFiliereId) : null;
        $selectedGroupe = $selectedGroupeId ? $groupes->firstWhere('id', (int) $selectedGroupeId) : null;
        $reportResults = $studentResults->getResults($selectedFiliereId, $selectedGroupeId);

        return view('directeur.notes.index', compact(
            'ccRows',
            'examRows',
            'maxCC',
            'examHeaders',
            'defaultPoids',
            'pfeGroupes',
            'pfeStagiaires',
            'resultats',
            'uniteNames',
            'filieres',
            'groupes',
            'selectedFiliereId',
            'selectedGroupeId',
            'selectedFiliere',
            'selectedGroupe',
            'reportResults'
        ));
    }

    // =========================================================
    // VALIDER — note واحدة
    // =========================================================
    public function valider($id)
    {
        $note = Note::findOrFail($id);

        if ($note->statut === 'validee') {
            return back()->with('info', 'Déjà validée.');
        }

        $note->update(['statut' => 'validee', 'validee_at' => now()]);

        return back()->with('success', 'Note validée.');
    }

    // =========================================================
    // DEVALIDER — note واحدة
    // =========================================================
    public function devalider($id)
    {
        $note = Note::findOrFail($id);

        if ($note->statut === 'en_attente') {
            return back()->with('info', 'Déjà en attente.');
        }

        $note->update(['statut' => 'en_attente', 'validee_at' => null]);

        return back()->with('warning', 'Note remise en attente.');
    }

    // =========================================================
    // VALIDER TOUT — بالجملة
    // =========================================================
    public function validerTout(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:notes,id',
        ]);

        $count = Note::whereIn('id', $request->ids)
            ->where('statut', 'en_attente')
            ->update(['statut' => 'validee', 'validee_at' => now()]);

        return back()->with('success', "$count note(s) validée(s).");
    }

    // =========================================================
    // DEVALIDER TOUT — بالجملة
    // =========================================================
    public function devaliderTout(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:notes,id',
        ]);

        $count = Note::whereIn('id', $request->ids)
            ->where('statut', 'validee')
            ->update(['statut' => 'en_attente', 'validee_at' => null]);

        return back()->with('warning', "$count note(s) remise(s) en attente.");
    }

    // =========================================================
    // EXPORT ALL — تصدير كل النوتات
    // =========================================================
    public function exportAll(Request $request)
    {
        $filters = $this->validateResultFilters($request);

        return Excel::download(
            new NotesExport($filters['filiere_id'] ?? null, $filters['groupe_id'] ?? null),
            'bulletins_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    // =========================================================
    // EXPORT STAGIAIRE — تصدير نوتات stagiaire واحد
    // =========================================================
    public function exportPdf(Request $request, StudentResultsService $studentResults)
    {
        $filters = $this->validateResultFilters($request);
        $selectedFiliere = isset($filters['filiere_id']) ? Filiere::find($filters['filiere_id']) : null;
        $selectedGroupe = isset($filters['groupe_id']) ? Groupe::find($filters['groupe_id']) : null;

        $pdf = Pdf::loadView('directeur.notes.pdf', [
            'results' => $studentResults->getResults($filters['filiere_id'] ?? null, $filters['groupe_id'] ?? null),
            'selectedFiliere' => $selectedFiliere,
            'selectedGroupe' => $selectedGroupe,
        ])->setPaper('a4');

        return $pdf->download('rapport_notes_' . now()->format('Y-m-d_H-i') . '.pdf');
    }

    public function exportStagiaire($id)
    {
        $stagiaire = Stagiaire::findOrFail($id);
        $fileName = 'bulletin_' . str($stagiaire->nom . '_' . $stagiaire->prenom)
            ->slug('_')
            ->toString() . '_' . now()->format('Y-m-d_H-i') . '.xlsx';

        return Excel::download(new StagiaireNotesExport((int) $id), $fileName);
    }

    // =========================================================
    // SHOW — عرض نوتات stagiaire واحد
    // =========================================================
    private function validateResultFilters(Request $request): array
    {
        return $request->validate([
            'filiere_id' => ['nullable', 'integer', 'exists:filieres,id'],
            'groupe_id' => ['nullable', 'integer', 'exists:groupes,id'],
        ]);
    }

    public function show($id)
    {
        $stagiaire = Stagiaire::with([
            'groupe.niveau.filiere',
            'notes.evaluation.module',
            'notes.unitExam.unite',
            'pfe',
        ])->findOrFail($id);

        return view('directeur.notes.show', compact('stagiaire'));
    }
    // =========================================================
// RELEVE — عرض ريليفي ديال stagiaire واحد
// =========================================================
public function releve($stagiaireId)
{
    $stagiaire = Stagiaire::with([
        'groupe.niveau.filiere',
        'pfe',
    ])->findOrFail($stagiaireId);

    // ── جيب جميع النوتات ديال الستاجيير بلا فيلتر formateur ──
    $notes = Note::with([
        'stagiaire.groupe.niveau.filiere',
        'evaluation.module.evaluations',
        'unitExam.unite',
        'unitExam.unite.modules.evaluations',
        'unitExam.unite.unitExams',
    ])
    ->where('stagiaire_id', $stagiaireId)
    ->get();

    // ── Exam Rows ──
    $examRows = $notes
        ->whereNotNull('unit_exam_id')
        ->groupBy(fn($n) => optional($n->unitExam)->unite_id)
        ->map(function ($group) use ($notes, $stagiaireId) {

            $first = $group->first();
            $unite = optional($first->unitExam)->unite;

            if (!$unite) return null;

            $theorique = $group->first(fn($n) => optional($n->unitExam)->type === 'theorique');
            $pratique  = $group->first(fn($n) => optional($n->unitExam)->type === 'pratique');

            $poids     = $this->getPoidsFromUnite($unite);
            $moduleIds = $unite->modules->pluck('id');

            $notesForStagiaire = $notes->where('stagiaire_id', $stagiaireId);
            $moyCC = $this->computeMoyCC($notesForStagiaire, $stagiaireId, $moduleIds);

            $moyUnite = $this->computeMoyUnite(
                $moyCC,
                $theorique?->note,
                $pratique?->note,
                $poids['poids_cc'],
                $poids['poids_theo'],
                $poids['poids_prat']
            );

            return [
                'stagiaire'  => $first->stagiaire,
                'unite'      => $unite,
                'theorique'  => $theorique,
                'pratique'   => $pratique,
                'moy_cc'     => $moyCC,
                'poids_cc'   => $poids['poids_cc'],
                'poids_theo' => $poids['poids_theo'],
                'poids_prat' => $poids['poids_prat'],
                'moy_unite'  => $moyUnite,
                'coef'       => (float) ($unite->coefficient ?? 1),
                'all_valid'  => $group->every(fn($n) => $n->statut === 'validee'),
            ];
        })
        ->filter()
        ->values();

    $uniteNames = $examRows
        ->map(fn($r) => $r['unite']->nom)
        ->unique()->sort()->values()->toArray();

    // ── PFE ──
    $pfeGroupes    = $this->getPfeGroupes();
    $pfeStagiaires = Stagiaire::with(['groupe.niveau.filiere', 'pfe'])
        ->whereIn('group_id', $pfeGroupes->pluck('id'))
        ->get();

    $pfeStagiaire = $pfeStagiaires->firstWhere('id', $stagiaireId);
    $notePfe      = optional($pfeStagiaire?->pfe)->note;

    // ── Moyennes par unité ──
    $unitesMoy = collect($uniteNames)->mapWithKeys(function ($uniteName) use ($examRows) {
        $row = $examRows->first(fn($r) => $r['unite']->nom === $uniteName);
        return [$uniteName => $row ? $row['moy_unite'] : null];
    })->toArray();

    $hasIncomplete = $examRows->contains(fn($row) => is_null($row['moy_unite']));

    // ── Moyenne générale ──
    if ($hasIncomplete) {
        $moyGen = null;
    } else {
        $totalNote  = 0.0;
        $totalCoeff = 0.0;

        foreach ($examRows as $row) {
            $coeff       = $row['coef'];
            $totalNote  += $row['moy_unite'] * $coeff;
            $totalCoeff += $coeff;
        }

        $moyUnits = $totalCoeff > 0 ? round($totalNote / $totalCoeff, 2) : null;

        $isLastYear = (int)($stagiaire->groupe?->niveau?->ordre ?? 0)
                   === (int)($stagiaire->groupe?->niveau?->filiere?->duree ?? 0);

        if ($isLastYear) {
            $moyGen = (is_null($moyUnits) || is_null($notePfe))
                ? null
                : round(($moyUnits * 0.8) + ($notePfe * 0.2), 2);
        } else {
            $moyGen = $moyUnits;
        }
    }

    $res = [
        'stagiaire'  => $stagiaire,
        'unites_moy' => $unitesMoy,
        'note_pfe'   => $notePfe,
        'moy_gen'    => $moyGen,
        'mention'    => $this->getMention($moyGen),
    ];

    // ── نعاودو نستعملو نفس الview ديال الفورماتور ──
    return view('formateur.notes._releve_detail', compact(
        'res', 'uniteNames', 'pfeStagiaires', 'examRows'
    ));
}
}
