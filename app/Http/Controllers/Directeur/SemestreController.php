<?php
// Directeur/SemestreController.php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\Niveau;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;

class SemestreController extends Controller
{
    public function index()
    {
        $semestres = Semestre::with('niveau.filiere', 'anneeScolaire')
                             ->orderBy('annee_scolaire_id')
                             ->orderBy('ordre')
                             ->get();
        return view('directeur.semestres.index', compact('semestres'));
    }

    public function create()
    {
        $niveaux = Niveau::with('filiere')->get();
        $annees  = AnneeScolaire::orderByDesc('id')->get();
        return view('directeur.semestres.create', compact('niveaux', 'annees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'               => 'required|string|max:255',
            'ordre'             => 'required|integer|min:1|max:2',
            'niveau_id'         => 'required|exists:niveaux,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'statut'            => 'required|in:inactif,ouvert,cloture',
        ]);

        $data = $request->only('nom', 'ordre', 'niveau_id', 'annee_scolaire_id', 'statut');

        if ($request->statut === 'ouvert') {
            $data['ouvert_at'] = now();
        } elseif ($request->statut === 'cloture') {
            $data['ouvert_at']   = now();
            $data['cloture_at']  = now();
        }

        Semestre::create($data);

        return redirect()->route('directeur.semestres.index')
                         ->with('success', 'Semestre ajouté avec succès');
    }

    public function edit(Semestre $semestre)
    {
        $niveaux = Niveau::with('filiere')->get();
        $annees  = AnneeScolaire::orderByDesc('id')->get();
        return view('directeur.semestres.edit', compact('semestre', 'niveaux', 'annees'));
    }

    public function update(Request $request, Semestre $semestre)
    {
        $request->validate([
            'nom'               => 'required|string|max:255',
            'ordre'             => 'required|integer|min:1|max:2',
            'niveau_id'         => 'required|exists:niveaux,id',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'statut'            => 'required|in:inactif,ouvert,cloture',
        ]);

        $data = $request->only('nom', 'ordre', 'niveau_id', 'annee_scolaire_id', 'statut');

        if ($request->statut === 'ouvert' && $semestre->statut !== 'ouvert') {
            $data['ouvert_at'] = now();
        }
        if ($request->statut === 'cloture' && $semestre->statut !== 'cloture') {
            $data['cloture_at'] = now();
        }

        $semestre->update($data);

        return redirect()->route('directeur.semestres.index')
                         ->with('success', 'Semestre modifié avec succès');
    }

    public function destroy(Semestre $semestre)
    {
        $semestre->delete();
        return back()->with('success', 'Semestre supprimé');
    }
}