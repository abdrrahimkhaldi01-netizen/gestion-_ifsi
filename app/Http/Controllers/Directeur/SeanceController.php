<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Seance;
use App\Models\Groupe;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Seance::with(['module', 'formateur.user', 'groupe'])
            ->orderBy('date_seance', 'desc');

        if ($request->filled('groupe_id')) {
            $query->where('group_id', $request->groupe_id);
        }

        if ($request->filled('statut_validation')) {
            $query->where('statut_validation', $request->statut_validation);
        }

        $seances = $query->paginate(20);
        $groupes = Groupe::orderBy('nom')->get();

        return view('directeur.seances.index', compact('seances', 'groupes'));
    }

    public function valider(string $id)
    {
        $seance = Seance::findOrFail($id);

        if ($seance->statut_validation === 'validee') {
            return back()->with('info', 'Cette séance est déjà validée.');
        }

        $seance->update(['statut_validation' => 'validee']);

        return back()->with('success', 'Séance validée avec succès.');
    }

    public function refuser(string $id)
    {
        $seance = Seance::findOrFail($id);

        $seance->update(['statut_validation' => 'refusee']);

        return back()->with('warning', 'Séance refusée.');
    }

    public function reinitialiser(string $id)
    {
        $seance = Seance::findOrFail($id);

        $seance->update(['statut_validation' => 'en_attente']);

        return back()->with('info', 'Séance remise en attente.');
    }
}
