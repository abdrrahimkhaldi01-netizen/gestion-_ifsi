<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Seance;
use App\Models\Stagiaire;
use App\Models\Groupe;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index()
    {
        $formateur = auth()->user()->formateur;

        $absences = Absence::where('formateur_id', $formateur->id)
                          ->with(['stagiaire.groupe', 'seance.module'])
                          ->orderBy('date_absence', 'desc')
                          ->paginate(20);

        return view('formateur.absences.index', compact('absences'));
    }

    public function create()
    {
        $formateur = auth()->user()->formateur;

        $seances = Seance::where('formateur_id', $formateur->id)
                        ->with('groupe')
                        ->get();

        $groupes    = Groupe::all();
        $stagiaires = collect();

        return view('formateur.absences.create', compact('seances', 'stagiaires', 'groupes'));
    }

    public function store(Request $request)
    {
        $isEncadrant = auth()->user()->role === 'encadrant';

        // Validation de base
      $rules = [
    'date_absence'    => 'required|date',
    'stagiaire_ids'   => 'required|array',
    'stagiaire_ids.*' => 'exists:stagiaires,id',
    'seance_id'       => 'required|exists:seances,id',
    'motif'           => 'nullable|string',
    'justifiee'       => 'boolean', // ✅ مشي false
];

        // Type obligatoire seulement pour encadrant
        if ($isEncadrant) {
            $rules['type'] = 'required|in:seance,stage';
        }

        $request->validate($rules);

        $formateur = auth()->user()->formateur;

        // Type : encadrant choisit, formateur toujours 'seance'
        $type = $isEncadrant ? $request->type : 'seance';

        foreach ($request->stagiaire_ids as $stagiaireId) {
          Absence::create([
    'date_absence' => $request->date_absence,
    'stagiaire_id' => $stagiaireId,
    'seance_id'    => $request->seance_id,
    'motif'        => $request->motif,
    'formateur_id' => $formateur->id,
    'type'         => $type,
    'justifiee'    => false, // ✅ default false
]);
        }

        return redirect()->route('formateur.absences.index')
                        ->with('success', 'Absences enregistrées avec succès');
    }

    public function edit(Absence $absence)
    {
        $formateur = auth()->user()->formateur;

        $seances = Seance::where('formateur_id', $formateur->id)
                        ->with('groupe')
                        ->get();

        $groupes    = Groupe::all();
        $stagiaires = collect();

        return view('formateur.absences.edit', compact('absence', 'seances', 'stagiaires', 'groupes'));
    }

    public function update(Request $request, Absence $absence)
    {
        $isEncadrant = auth()->user()->role === 'encadrant';

        $rules = [
            'date_absence' => 'required|date',
            'stagiaire_id' => 'required|exists:stagiaires,id',
            'seance_id'    => 'required|exists:seances,id',
            'motif'        => 'nullable|string',
            'justifiee' => 'boolean',
        ];

        // Type obligatoire seulement pour encadrant
        if ($isEncadrant) {
            $rules['type'] = 'required|in:seance,stage';
        }

        $request->validate($rules);

        $absence->update([
            'date_absence' => $request->date_absence,
            'stagiaire_id' => $request->stagiaire_id,
            'seance_id'    => $request->seance_id,
            'motif'        => $request->motif,
            'type'         => $isEncadrant ? $request->type : 'seance',
            'justifiee' => $request->boolean('justifiee'),
        ]);

        return redirect()->route('formateur.absences.index')
                        ->with('success', 'Absence modifiée avec succès');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();

        return redirect()->route('formateur.absences.index')
                        ->with('success', 'Absence supprimée');
    }

    public function stagiairesByGroupe($groupeId)
    {
        $stagiaires = Stagiaire::where('group_id', $groupeId)
                              ->get(['id', 'nom', 'prenom']);

        return response()->json($stagiaires);
    }
}
