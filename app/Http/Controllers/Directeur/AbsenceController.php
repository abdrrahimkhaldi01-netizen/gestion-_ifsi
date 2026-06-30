<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Stagiaire;
use App\Models\Seance;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
class AbsenceController extends Controller
{
  public function index(Request $request)
{
    $query = Absence::with(['stagiaire.groupe', 'seance.module', 'stage'])
        ->orderBy('date_absence', 'desc');

    if ($request->filled('type'))         $query->where('type', $request->type);
    if ($request->filled('justifiee'))    $query->where('justifiee', $request->justifiee);
    if ($request->filled('stagiaire_id')) $query->where('stagiaire_id', $request->stagiaire_id);

    $absences   = $query->paginate(20)->withQueryString();
    $stagiaires = Stagiaire::orderBy('nom')->get();

    $seuil = 3;

    $topAbsents = Absence::selectRaw('
            stagiaire_id,
            COUNT(*) as total_absences,
            SUM(CASE WHEN justifiee = 0 THEN 1 ELSE 0 END) as injustifiees
        ')
        ->with('stagiaire.groupe')
        ->groupBy('stagiaire_id')
        ->orderByDesc('total_absences')
        ->limit(10)
        ->get()
        ->map(function ($row) use ($seuil) {
            $row->a_risque = $row->total_absences > $seuil;
            $row->taux     = $row->total_absences > 0
                ? round(($row->injustifiees / $row->total_absences) * 100)
                : 0;
            return $row;
        });

    $statsGlobales = [
        'total'        => Absence::count(),
        'injustifiees' => Absence::where('justifiee', false)->count(),
        'justifiees'   => Absence::where('justifiee', true)->count(),
        'a_risque'     => $topAbsents->where('a_risque', true)->count(),
    ];

    $atRiskJs = $topAbsents->where('a_risque', true)->map(function ($r) {
        return [
            'prenom' => $r->stagiaire->prenom ?? '',
            'phone'  => '212' . ltrim($r->stagiaire->telephone ?? '', '0'),
            'total'  => $r->total_absences,
            'injust' => $r->injustifiees,
        ];
    })->values();
    

    return view('directeur.absences.index', compact(
        'absences', 'stagiaires', 'statsGlobales', 'topAbsents', 'seuil', 'atRiskJs'
    ));
}

    public function create()
    {
        $stagiaires = Stagiaire::with('groupe')->get();
        $seances    = Seance::with('module')->get();
        return view('directeur.absences.create', compact('stagiaires', 'seances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stagiaire_id' => 'required|exists:stagiaires,id',
            'date_absence' => 'required|date',
            'type'         => 'required|in:seance,stage',
            'motif'        => 'nullable|string|max:500',
            'justifiee'    => 'boolean', // ✅
            'seance_id'    => 'nullable|exists:seances,id',
            'stage_id'     => 'nullable|exists:stages,id',
            'formateur_id' => 'required|exists:formateurs,id',
        ]);

        Absence::create($request->only([
            'stagiaire_id', 'date_absence', 'type',
            'motif', 'justifiee', 'seance_id', 'stage_id', 'formateur_id',
        ]));

        return redirect()->route('directeur.absences.index')
                         ->with('success', 'Absence enregistrée.');
    }

    public function show(string $id)
    {
        $absence = Absence::with(['stagiaire.groupe', 'seance.module', 'stage', 'formateur.user'])
                          ->findOrFail($id);
        return view('directeur.absences.show', compact('absence'));
    }

    public function edit(string $id)
    {
        $absence    = Absence::findOrFail($id);
        $stagiaires = Stagiaire::with('groupe')->get();
        $seances    = Seance::with('module')->get();
        return view('directeur.absences.edit', compact('absence', 'stagiaires', 'seances'));
    }

    public function update(Request $request, string $id)
    {
        $absence = Absence::findOrFail($id);

        $request->validate([
            'date_absence' => 'required|date',
            'justifiee'    => 'boolean', // ✅ مشي statut
            'motif'        => 'nullable|string|max:500',
        ]);

        $absence->update($request->only('date_absence', 'justifiee', 'motif'));

        return redirect()->route('directeur.absences.index')
                         ->with('success', 'Absence mise à jour.');
    }

    public function destroy(string $id)
    {
        Absence::findOrFail($id)->delete();
        return back()->with('success', 'Absence supprimée.');
    }
    public function sendWhatsApp(Request $request, $stagiaireId)
{
    $stagiaire     = Stagiaire::findOrFail($stagiaireId);
    $totalAbsences = $stagiaire->absences()->count();
    $injustifiees  = $stagiaire->absences()->where('justifiee', false)->count();

   $message = "Bonjour {$stagiaire->prenom} {$stagiaire->nom},\n\n"
         . "Nous avons constaté *{$totalAbsences} absence(s)* répétée(s) dont "
         . "*{$injustifiees}* sans justification.\n\n"
         . "Nous vous prions de contacter l'administration ou le responsable administratif "
         . "dans les plus brefs délais afin de régulariser votre situation.\n\n"
         . "À défaut, des mesures appropriées seront prises.\n\n"
         . "_Cordialement,\n— Direction_";

    $wa   = new WhatsAppService();
    $sent = $wa->send($stagiaire->telephone, $message);

    return back()->with(
        $sent ? 'success' : 'error',
        $sent ? "✅ Message envoyé à {$stagiaire->prenom}" : "❌ Échec de l'envoi"
        
    );
}

public function sendBulkWhatsApp(Request $request)
{
    $seuil  = 3;
    $atRisk = Stagiaire::withCount([
        'absences as total_absences',
        'absences as injustifiees' => fn($q) => $q->where('justifiee', false),
    ])
    ->having('total_absences', '>', $seuil)
    ->whereNotNull('telephone')
    ->get();

    if ($atRisk->isEmpty()) {
        return back()->with('info', 'ما كاين حتى stagiaire à risque');
    }

    $contacts = $atRisk->map(fn($s) => [
        'phone'   => $s->telephone,
    'message' => "Bonjour {$s->prenom} {$s->nom},\n\n"
           . "Nous avons constaté *{$s->total_absences} absence(s)* répétée(s) dont "
           . "*{$s->injustifiees}* sans justification.\n\n"
           . "Nous vous prions de contacter l'administration ou le responsable administratif "
           . "dans les plus brefs délais afin de régulariser votre situation.\n\n"
           . "À défaut, des mesures appropriées seront prises.\n\n"
           . "_Cordialement,\n— Direction_",
    ])->toArray();

    $wa      = new WhatsAppService();
    $results = $wa->sendBulk($contacts);
    $success = collect($results)->where('success', true)->count();
    $failed  = collect($results)->where('success', false)->count();

    return back()->with('success', "✅ {$success} رسالة تبعتات" . ($failed ? " — ❌ {$failed} فشلات" : ''));
}
}