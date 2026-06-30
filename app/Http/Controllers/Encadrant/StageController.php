<?php

namespace App\Http\Controllers\Encadrant;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Groupe;
use Illuminate\Http\Request;

class StageController extends Controller
{
    public function index()
    {
        // Relation plurielle : un stage a plusieurs stagiaires
        $stages = Stage::with(['stagiaires', 'group'])->get();
        return view('encadrant.stages.index', compact('stages'));
    }

    public function create()
    {
        $groupes = Groupe::all();
        // Les stagiaires sont chargés dynamiquement via AJAX par groupe
        return view('encadrant.stages.create', compact('groupes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entreprise'    => 'required|string|max:255',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
            'group_id'      => 'required|exists:groupes,id',
            // Tableau de stagiaires, au moins un requis
            'stagiaire_ids'   => 'required|array|min:1',
            'stagiaire_ids.*' => 'exists:stagiaires,id',
        ]);

        $stage = Stage::create([
            'entreprise'  => $request->entreprise,
            'date_debut'  => $request->date_debut,
            'date_fin'    => $request->date_fin,
            'group_id'    => $request->group_id,
        ]);

        // Attache les stagiaires via la table pivot
        $stage->stagiaires()->sync($request->stagiaire_ids);

        return redirect()->route('encadrant.stages.index')
                         ->with('success', 'Stage ajouté avec succès');
    }
    public function show(Stage $stage)
{
    $stage->load(['stagiaires', 'groupe.niveau.filiere', 'absences.stagiaire']);
    return view('encadrant.stages.show', compact('stage'));
}

    public function edit(Stage $stage)
    {
        $groupes = Groupe::all();
        return view('encadrant.stages.edit', compact('stage', 'groupes'));
    }

    public function update(Request $request, Stage $stage)
    {
        $request->validate([
            'entreprise'    => 'required|string|max:255',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
            'group_id'      => 'required|exists:groupes,id',
            'stagiaire_ids'   => 'required|array|min:1',
            'stagiaire_ids.*' => 'exists:stagiaires,id',
        ]);

        $stage->update([
            'entreprise' => $request->entreprise,
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
            'group_id'   => $request->group_id,
        ]);

        $stage->stagiaires()->sync($request->stagiaire_ids);

        return redirect()->route('encadrant.stages.index')
                         ->with('success', 'Stage modifié avec succès');
    }

    public function destroy(Stage $stage)
    {
        $stage->stagiaires()->detach(); // Nettoyage pivot avant suppression
        $stage->delete();

        return redirect()->route('encadrant.stages.index')
                         ->with('success', 'Stage supprimé');
    }
    public function stagiairesByGroupe($groupeId)
{
    $stagiaires = \App\Models\Stagiaire::where('group_id', $groupeId)
                    ->get(['id', 'nom', 'prenom']);
    return response()->json($stagiaires);
}
}