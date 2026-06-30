<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Groupe;
use App\Models\Niveau;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function index()
    {
        $groupes = Groupe::with('niveau.filiere', 'anneeScolaire')
                         ->orderBy('nom')
                         ->paginate(15);

        return view('directeur.groupes.index', compact('groupes'));
    }

    public function create()
    {
        $niveaux  = Niveau::with('filiere')
                          ->orderBy('filiere_id')
                          ->orderBy('ordre')
                          ->get();

        $filieres = $niveaux->groupBy('filiere_id');

        $annees   = AnneeScolaire::orderByDesc('date_debut')->get(); // ✅ مضاف

        return view('directeur.groupes.create', compact('niveaux', 'filieres', 'annees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:255|unique:groupes,nom',
            'niveau_id'         => 'required|integer|exists:niveaux,id',
            'filiere_id'        => 'required|integer|exists:filieres,id',        // ✅ مضاف
            'annee_scolaire_id' => 'required|integer|exists:annees_scolaires,id', // ✅ مضاف
        ]);

        Groupe::create($validated);

        return redirect()->route('directeur.groupes.index')
                         ->with('success', 'Groupe ajouté avec succès');
    }

    public function show(Groupe $groupe)
    {
        $groupe->load('niveau.filiere', 'anneeScolaire', 'stagiaires', 'modules');
        return view('directeur.groupes.show', compact('groupe'));
    }

    public function edit(Groupe $groupe)
    {
        $groupe->load('niveau.filiere');

        $niveaux  = Niveau::with('filiere')
                          ->orderBy('filiere_id')
                          ->orderBy('ordre')
                          ->get();

        $filieres = $niveaux->groupBy('filiere_id');

        $annees   = AnneeScolaire::orderByDesc('date_debut')->get(); // ✅ مضاف

        return view('directeur.groupes.edit', compact('groupe', 'niveaux', 'filieres', 'annees'));
    }

    public function update(Request $request, Groupe $groupe)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:255|unique:groupes,nom,' . $groupe->id,
            'niveau_id'         => 'required|integer|exists:niveaux,id',
            'filiere_id'        => 'required|integer|exists:filieres,id',        // ✅ مضاف
            'annee_scolaire_id' => 'required|integer|exists:annees_scolaires,id', // ✅ مضاف
        ]);

        $groupe->update($validated);

        return redirect()->route('directeur.groupes.index')
                         ->with('success', 'Groupe modifié avec succès');
    }

    public function destroy(Groupe $groupe)
    {
        if ($groupe->stagiaires()->exists()) {
            return redirect()->route('directeur.groupes.index')
                             ->with('error', 'Impossible de supprimer un groupe contenant des stagiaires.');
        }

        $groupe->delete();

        return redirect()->route('directeur.groupes.index')
                         ->with('success', 'Groupe supprimé avec succès');
    }
}