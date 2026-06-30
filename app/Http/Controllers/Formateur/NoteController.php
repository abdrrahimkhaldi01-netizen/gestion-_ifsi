<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Module;
use App\Models\Groupe;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NoteController extends Controller
{
    // =========================================================
    // PRIVÉ: Construit les données JS pour les dropdowns
    // =========================================================
    private function buildJsData(): array
    {
        $formateur = auth()->user()->formateur;

        $modules = Module::with([
            'unite',
            'groupes',
            'evaluations',
            'unite.unitExams',
        ])
        ->where('formateur_id', $formateur->id)
        ->get();

        $groupes = Groupe::with([
            'niveau.filiere',
            'stagiaires',
        ])->get();

        $groupesJs = $groupes->map(fn($g) => [
            'id'           => $g->id,
            'nom'          => $g->nom,
            'filiere_id'   => $g->niveau?->filiere?->id,
            'is_pfe_group' => $g->niveau?->ordre === $g->niveau?->filiere?->duree,
            'stagiaires'   => $g->stagiaires->map(fn($s) => [
                'id'  => $s->id,
                'nom' => trim($s->nom . ' ' . $s->prenom),
            ])->values(),
        ])->values();

        $modulesJs = $modules->map(fn($m) => [
            'id'          => $m->id,
            'nom'         => $m->titre ?? $m->nom,
            'unite_id'    => $m->unite_id,
            'unite_nom'   => $m->unite?->nom,
            'groupes'     => $m->groupes->pluck('id')->values(),
            'evaluations' => $m->evaluations->map(fn($e) => [
                'id'  => $e->id,
                'nom' => $e->nom,
            ])->values(),
            'unit_exams'  => $m->unite?->unitExams->map(fn($ex) => [
                'id'   => $ex->id,
                'nom'  => ucfirst($ex->type),
                'type' => $ex->type,
            ])->values() ?? [],
        ])->values();

        $filieres = $groupes
            ->map(fn($g) => $g->niveau?->filiere)
            ->filter()
            ->unique('id')
            ->map(fn($f) => ['id' => $f->id, 'nom' => $f->nom])
            ->values();

        return compact('modules', 'groupes', 'groupesJs', 'modulesJs', 'filieres');
    }

    private function getPfeGroupes($formateur)
    {
        $modules = \App\Models\Module::where('formateur_id', $formateur->id)
            ->with('unite.niveau.filiere')
            ->get();

        $filiereIds = $modules
            ->map(fn($module) => $module->unite?->niveau?->filiere?->id)
            ->filter()
            ->unique()
            ->values();

        return \App\Models\Groupe::with([
            'niveau.filiere',
            'stagiaires.pfe',
        ])
        ->whereHas('niveau.filiere', fn($q) => $q->whereIn('id', $filiereIds))
        ->get()
        ->filter(fn($groupe) => $groupe->niveau?->ordre === $groupe->niveau?->filiere?->duree)
        ->values();
    }

    private function getPfeStagiaires($formateur)
    {
        $groupeIds = $this->getPfeGroupes($formateur)->pluck('id');

        return \App\Models\Stagiaire::with([
            'groupe.niveau.filiere',
            'pfe',
        ])
        ->whereIn('group_id', $groupeIds)
        ->get();
    }

    private function getPoidsFromUnite(Unite $unite): array
    {
        $unitExams = $unite->unitExams;

        return [
            'poids_cc'   => (int) ($unitExams->firstWhere('type', 'cc')?->poids        ?? 0),
            'poids_theo' => (int) ($unitExams->firstWhere('type', 'theorique')?->poids  ?? 0),
            'poids_prat' => (int) ($unitExams->firstWhere('type', 'pratique')?->poids   ?? 0),
        ];
    }

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

    private function computeMoyUnite(
        ?float $moyCC, ?float $noteTheo, ?float $notePrat,
        int $poidsCC, int $poidsTheo, int $poidsPrat
    ): ?float {
        if ($poidsCC   > 0 && is_null($moyCC))    return null;
        if ($poidsTheo > 0 && is_null($noteTheo)) return null;
        if ($poidsPrat > 0 && is_null($notePrat)) return null;

        $totalPoids = 0;
        $totalNote  = 0;

        if ($poidsCC   > 0) { $totalNote += $moyCC    * $poidsCC;   $totalPoids += $poidsCC; }
        if ($poidsTheo > 0) { $totalNote += $noteTheo * $poidsTheo; $totalPoids += $poidsTheo; }
        if ($poidsPrat > 0) { $totalNote += $notePrat * $poidsPrat; $totalPoids += $poidsPrat; }

        return $totalPoids > 0 ? round($totalNote / $totalPoids, 2) : null;
    }

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
    // INDEX
    // =========================================================
    public function index()
    {
        $formateur = auth()->user()->formateur;

        $notes = Note::with([
            'stagiaire.groupe.niveau.filiere',
            'evaluation.module.evaluations',
            'unitExam.unite.modules.evaluations',
            'unitExam.unite.unitExams',
        ])
        ->where(function ($q) use ($formateur) {
            $q->whereHas('evaluation.module', fn($q2) =>
                $q2->where('formateur_id', $formateur->id)
            )
            ->orWhereHas('unitExam.unite.modules', fn($q3) =>
                $q3->where('formateur_id', $formateur->id)
            );
        })
        ->latest()
        ->get();

        // ── CC ──
        $ccRows = $notes
            ->whereNotNull('evaluation_id')
            ->groupBy(fn($n) => $n->stagiaire_id . '-' . $n->evaluation->module_id)
            ->map(function ($group) {
                $first  = $group->first();
                $module = $first->evaluation->module;
                $ccs    = $group->sortBy('evaluation.nom')->values();

                $totalEvaluations = $module->evaluations->count();
                $filledNotes      = $ccs->pluck('note')->filter(fn($n) => !is_null($n));

                $moyenne = ($filledNotes->count() === $totalEvaluations && $totalEvaluations > 0)
                    ? round($filledNotes->avg(), 2)
                    : null;

                return [
                    'stagiaire' => $first->stagiaire,
                    'module'    => $module,
                    'ccs'       => $ccs,
                    'nb_cc'     => $ccs->count(),
                    'moyenne'   => $moyenne,
                    'all_valid' => $ccs->every(fn($n) => $n->statut === 'validee'),
                ];
            })->values();

        $maxCC = $ccRows->max(fn($row) => $row['ccs']->count()) ?? 0;

        // ── EXAM ──
        $examRows = $notes
            ->whereNotNull('unit_exam_id')
            ->groupBy(fn($n) => $n->stagiaire_id . '-' . $n->unitExam->unite_id)
            ->map(function ($group) use ($notes) {
                $first     = $group->first();
                $stagiaire = $first->stagiaire;
                $unite     = $first->unitExam->unite;

                $theorique = $group->first(fn($n) => $n->unitExam->type === 'theorique');
                $pratique  = $group->first(fn($n) => $n->unitExam->type === 'pratique');

                $poids     = $this->getPoidsFromUnite($unite);
                $moduleIds = $unite->modules->pluck('id');
                $moyCC     = $this->computeMoyCC($notes, $stagiaire->id, $moduleIds);

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
                ];
            })->values();

        $uniteForPoids = Unite::with('unitExams')->first();
        $defaultPoids  = $uniteForPoids
            ? $this->getPoidsFromUnite($uniteForPoids)
            : ['poids_cc' => 30, 'poids_theo' => 20, 'poids_prat' => 30];

        $examHeaders = [];
        if ($defaultPoids['poids_cc']   > 0) $examHeaders[] = ['label' => 'Moy. CC',   'poids' => $defaultPoids['poids_cc']];
        if ($defaultPoids['poids_theo'] > 0) $examHeaders[] = ['label' => 'Théorique', 'poids' => $defaultPoids['poids_theo']];
        if ($defaultPoids['poids_prat'] > 0) $examHeaders[] = ['label' => 'Pratique',  'poids' => $defaultPoids['poids_prat']];

        $pfeGroupes    = $this->getPfeGroupes($formateur);
        $pfeStagiaires = \App\Models\Stagiaire::with(['groupe.niveau.filiere', 'pfe'])
            ->whereIn('group_id', $pfeGroupes->pluck('id'))
            ->get();

        $uniteNames = $examRows->map(fn($r) => $r['unite']->nom)->unique()->sort()->values()->toArray();

        $resultats = $examRows
            ->groupBy(fn($r) => $r['stagiaire']->id)
            ->map(function ($group) use ($uniteNames, $pfeStagiaires) {
                $stagiaire = $group->first()['stagiaire'];

                $unitesMoy = collect($uniteNames)->mapWithKeys(function ($uniteName) use ($group) {
                    $row = $group->first(fn($r) => $r['unite']->nom === $uniteName);
                    return [$uniteName => $row ? $row['moy_unite'] : null];
                })->toArray();

                $hasIncomplete = $group->contains(fn($row) => is_null($row['moy_unite']));
                $pfeStagiaire  = $pfeStagiaires->firstWhere('id', $stagiaire->id);
                $notesPfe      = $pfeStagiaire?->pfe?->note;

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

        $jsData = $this->buildJsData();

        return view('formateur.notes.index', array_merge(
            compact('ccRows', 'examRows', 'defaultPoids', 'maxCC', 'examHeaders',
                    'uniteNames', 'resultats', 'pfeStagiaires', 'pfeGroupes'),
            $jsData
        ));
    }

    // =========================================================
    // CREATE
    // =========================================================
    public function create()
    {
        $formateur = auth()->user()->formateur;
        $jsData    = $this->buildJsData();

        $existingNotes = Note::with(['evaluation', 'unitExam'])
            ->where(function ($q) use ($formateur) {
                $q->whereHas('evaluation.module', fn($q2) =>
                    $q2->where('formateur_id', $formateur->id)
                )
                ->orWhereHas('unitExam.unite.modules', fn($q3) =>
                    $q3->where('formateur_id', $formateur->id)
                );
            })
            ->get()
            ->map(fn($n) => [
                'stagiaire_id'  => $n->stagiaire_id,
                'evaluation_id' => $n->evaluation_id,
                'unit_exam_id'  => $n->unit_exam_id,
                'note'          => $n->note,
                'statut'        => $n->statut,
            ]);

        return view('formateur.notes.create', array_merge(
            $jsData,
            ['existingNotesJson' => $existingNotes]
        ));
    }

    // =========================================================
    // STORE — FIX PRINCIPAL
    // Problème: validation kayfchel même si note vide/absente
    // Solution: filter strict + messages d'erreur clairs
    // =========================================================
    public function store(Request $request)
    {
        // ── Étape 1: Nettoyer les données — remplacer virgule par point ──
        foreach (['ccs', 'exams'] as $key) {
            if ($request->has($key)) {
                $items = $request->input($key);
                foreach ($items as $i => $item) {
                    if (isset($item['note']) && $item['note'] !== '' && $item['note'] !== null) {
                        $items[$i]['note'] = str_replace(',', '.', $item['note']);
                    }
                }
                $request->merge([$key => $items]);
            }
        }

        // ── Étape 2: Filtrer AVANT validation — supprimer les lignes vides ──
        // Fix: les inputs vides ne doivent jamais passer en validation
        $ccsFiltered   = collect($request->input('ccs', []))
            ->filter(fn($cc) => isset($cc['note']) && $cc['note'] !== '' && $cc['note'] !== null)
            ->values()
            ->toArray();

        $examsFiltered = collect($request->input('exams', []))
            ->filter(fn($ex) => isset($ex['note']) && $ex['note'] !== '' && $ex['note'] !== null)
            ->values()
            ->toArray();

        // ── Étape 3: Remplacer les données dans la request avec les données filtrées ──
        $request->merge([
            'ccs'   => $ccsFiltered,
            'exams' => $examsFiltered,
        ]);

        // ── Étape 4: Validation seulement sur les notes effectivement saisies ──
        $request->validate([
            'ccs'                  => 'nullable|array',
            'ccs.*.stagiaire_id'   => 'required|exists:stagiaires,id',
            'ccs.*.evaluation_id'  => 'required|exists:evaluations,id',
            'ccs.*.note'           => [
                'required',
                'numeric',
                'min:0',
                'max:20',
            ],
            'exams'                => 'nullable|array',
            'exams.*.stagiaire_id' => 'required|exists:stagiaires,id',
            'exams.*.unit_exam_id' => 'required|exists:unit_exams,id',
            'exams.*.note'         => [
                'required',
                'numeric',
                'min:0',
                'max:20',
            ],
        ], [
            // Messages en français pour le formateur
            'ccs.*.note.max'     => 'Une note CC ne peut pas dépasser 20.',
            'ccs.*.note.min'     => 'Une note CC ne peut pas être négative.',
            'ccs.*.note.numeric' => 'La note doit être un nombre valide.',
            'exams.*.note.max'   => 'Une note d\'examen ne peut pas dépasser 20.',
            'exams.*.note.min'   => 'Une note d\'examen ne peut pas être négative.',
        ]);

        // ── Étape 5: Enregistrement — updateOrCreate pour éviter les doublons ──
        $savedCC   = 0;
        $savedExam = 0;

        foreach ($request->input('ccs', []) as $cc) {
            Note::updateOrCreate(
                [
                    'stagiaire_id'  => $cc['stagiaire_id'],
                    'evaluation_id' => $cc['evaluation_id'],
                ],
                [
                    'note'   => $cc['note'],
                    'type'   => 'cc',
                    'statut' => 'en_attente',
                ]
            );
            $savedCC++;
        }

        foreach ($request->input('exams', []) as $ex) {
            Note::updateOrCreate(
                [
                    'stagiaire_id' => $ex['stagiaire_id'],
                    'unit_exam_id' => $ex['unit_exam_id'],
                ],
                [
                    'note'   => $ex['note'],
                    'type'   => 'exam',
                    'statut' => 'en_attente',
                ]
            );
            $savedExam++;
        }

        $msg = 'Notes enregistrées avec succès';
        if ($savedCC > 0 && $savedExam === 0)   $msg = "{$savedCC} note(s) CC enregistrée(s)";
        if ($savedExam > 0 && $savedCC === 0)   $msg = "{$savedExam} note(s) d'examen enregistrée(s)";
        if ($savedCC > 0 && $savedExam > 0)     $msg = "{$savedCC} CC + {$savedExam} examen(s) enregistré(s)";
        if ($savedCC === 0 && $savedExam === 0) $msg = 'Aucune note saisie — rien n\'a été enregistré';

        return redirect()
            ->route('formateur.notes.index')
            ->with('success', $msg);
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit(Request $request, Note $note)
    {
        $formateur = auth()->user()->formateur;
        $jsData    = $this->buildJsData();

        $existingNotes = Note::with(['evaluation', 'unitExam'])
            ->where(function ($q) use ($formateur) {
                $q->whereHas('evaluation.module', fn($q2) =>
                    $q2->where('formateur_id', $formateur->id)
                )
                ->orWhereHas('unitExam.unite.modules', fn($q3) =>
                    $q3->where('formateur_id', $formateur->id)
                );
            })
            ->get()
            ->map(fn($n) => [
                'stagiaire_id'  => $n->stagiaire_id,
                'evaluation_id' => $n->evaluation_id,
                'unit_exam_id'  => $n->unit_exam_id,
                'note'          => $n->note,
                'statut'        => $n->statut,
            ]);

        return view('formateur.notes.edit', array_merge($jsData, [
            'existingNotesJson' => $existingNotes,
            'preFiliere'        => $request->filiere_id ?? '',
            'preGroupe'         => $request->groupe_id  ?? '',
            'preUnite'          => $request->unite_id   ?? '',
            'preModule'         => $request->module_id  ?? '',
        ]));
    }

    // =========================================================
    // UPDATE — FIX: message d'erreur clair + trim
    // =========================================================
    public function update(Request $request, Note $note)
    {
        $this->authorizeNote($note);

        // Remplacer virgule par point (input mobile/fr)
        $noteValue = str_replace(',', '.', $request->input('note', ''));
        $request->merge(['note' => $noteValue]);

        $request->validate([
            'note' => 'required|numeric|min:0|max:20',
        ], [
            'note.max'     => 'La note ne peut pas dépasser 20.',
            'note.min'     => 'La note ne peut pas être négative.',
            'note.numeric' => 'Veuillez saisir un nombre valide.',
            'note.required'=> 'La note est obligatoire.',
        ]);

        $note->update(['note' => $request->note]);

        return redirect()
            ->route('formateur.notes.index')
            ->with('success', 'Note modifiée avec succès');
    }

    // =========================================================
    // DESTROY
    // =========================================================
    public function destroy(Note $note)
    {
        $this->authorizeNote($note);
        $note->delete();

        return redirect()
            ->route('formateur.notes.index')
            ->with('success', 'Note supprimée');
    }

    // =========================================================
    // PRIVÉ: Vérifie que la note appartient au formateur
    // =========================================================
    private function authorizeNote(Note $note): void
    {
        $formateur = auth()->user()->formateur;

        $ownedByCC = $note->evaluation_id &&
            $note->evaluation?->module?->formateur_id === $formateur->id;

        $ownedByExam = $note->unit_exam_id &&
            $note->unitExam?->unite?->modules
                ->contains('formateur_id', $formateur->id);

        if (!$ownedByCC && !$ownedByExam) {
            abort(403, 'Accès non autorisé à cette note.');
        }
    }

    // =========================================================
    // PFE INDEX
    // =========================================================
    public function pfeIndex()
    {
        $formateur = auth()->user()->formateur;

        $modules = \App\Models\Module::where('formateur_id', $formateur->id)
            ->with('unite.niveau.groupes')
            ->get();

        $groupeIds = $modules
            ->flatMap(function ($module) {
                return optional($module->unite?->niveau?->groupes)
                    ->pluck('id') ?? collect();
            })
            ->unique()
            ->values();

        $stagiaires = \App\Models\Stagiaire::with([
            'groupe.niveau.filiere',
            'pfe',
        ])
        ->whereIn('group_id', $groupeIds)
        ->get();

        return view('formateur.notes.pfe', compact('stagiaires'));
    }

    // =========================================================
    // PFE STORE
    // =========================================================
    public function pfeStore(Request $request)
    {
        $request->validate([
            'pfes'                => 'required|array',
            'pfes.*.stagiaire_id' => 'required|exists:stagiaires,id',
            'pfes.*.note'         => 'nullable|numeric|min:0|max:20',
        ], [
            'pfes.*.note.max'     => 'La note PFE ne peut pas dépasser 20.',
            'pfes.*.note.numeric' => 'Veuillez saisir un nombre valide pour la note PFE.',
        ]);

        foreach (collect($request->pfes)->filter(fn($p) => !is_null($p['note']) && $p['note'] !== '') as $p) {
            \App\Models\Pfe::updateOrCreate(
                ['stagiaire_id' => $p['stagiaire_id']],
                ['note' => $p['note'], 'titre' => 'PFE']
            );
        }

        return redirect()
            ->route('formateur.notes.index', ['tab' => 'pfe'])
            ->with('success', 'Notes PFE enregistrées avec succès');
    }
public function releve($stagiaireId)
{
    $formateur = auth()->user()->formateur;

    $stagiaire = \App\Models\Stagiaire::with([
        'groupe.niveau.filiere',
        'pfe',
    ])->findOrFail($stagiaireId);

    $notes = Note::with([
        'stagiaire.groupe.niveau.filiere',
        'evaluation.module.evaluations',
        'unitExam.unite',                       // ← أضف هذا أولاً
        'unitExam.unite.modules.evaluations',
        'unitExam.unite.unitExams',
    ])
    ->where('stagiaire_id', $stagiaireId)
    ->where(function ($q) use ($formateur) {
        $q->whereHas('evaluation.module', function ($q2) use ($formateur) {
            $q2->where('formateur_id', $formateur->id);
        })
        ->orWhereHas('unitExam.unite.modules', function ($q3) use ($formateur) {
            $q3->where('formateur_id', $formateur->id);
        });
    })
    ->get();

    $examRows = $notes
        ->whereNotNull('unit_exam_id')
        ->groupBy(function ($n) {
            return optional($n->unitExam)->unite_id;
        })
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
                'coef'       => (float) ($unite->coefficient ?? 1), // ← أضف هذا
                'all_valid'  => $group->every(fn($n) => $n->statut === 'validee'),
            ];
        })
        ->filter()
        ->values();

    $uniteNames = $examRows
        ->map(fn($r) => $r['unite']->nom)
        ->unique()->sort()->values()->toArray();

    // ── PFE ──
    $pfeGroupes   = $this->getPfeGroupes($formateur);
    $pfeStagiaires = \App\Models\Stagiaire::with(['groupe.niveau.filiere', 'pfe'])
        ->whereIn('group_id', $pfeGroupes->pluck('id'))
        ->get();

    $pfeStagiaire = $pfeStagiaires->firstWhere('id', $stagiaireId);
    $notePfe      = optional($pfeStagiaire?->pfe)->note;

    // ── moyennes par unité ──
    $unitesMoy = collect($uniteNames)->mapWithKeys(function ($uniteName) use ($examRows) {
        $row = $examRows->first(fn($r) => $r['unite']->nom === $uniteName);
        return [$uniteName => $row ? $row['moy_unite'] : null];
    })->toArray();

    $hasIncomplete = $examRows->contains(fn($row) => is_null($row['moy_unite']));

    // ── moyenne générale ──
    if ($hasIncomplete) {
        $moyGen = null;
    } else {
        $totalNote  = 0.0;
        $totalCoeff = 0.0;

        foreach ($examRows as $row) {
            $coeff       = $row['coef']; // ← من الـ array مباشرة
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

    return view('formateur.notes._releve_detail', compact(
        'res', 'uniteNames', 'pfeStagiaires', 'examRows'
    ));
}
}