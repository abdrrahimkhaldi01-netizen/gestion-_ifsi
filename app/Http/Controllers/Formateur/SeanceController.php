<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Seance;
use App\Models\Module;
use App\Models\Groupe;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    public function index()
    {
        $formateur = auth()->user()->formateur;

        $seances = Seance::where('formateur_id', $formateur->id)
                         ->with(['module', 'groupe'])
                         ->orderBy('date_seance', 'desc')
                         ->paginate(20);

        return view('formateur.seances.index', compact('seances'));
    }

    public function create()
    {
        $formateur = auth()->user()->formateur;

        // ✅ غير modules ديال هاد الformateur
        $modules = Module::where('formateur_id', $formateur->id)->get();
        $groupes = Groupe::all();

        return view('formateur.seances.create', compact('modules', 'groupes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin'   => 'required|date_format:H:i|after:heure_debut',
            'module_id'   => 'required|exists:modules,id',
            'group_id'    => 'required|exists:groupes,id',
            'description' => 'required|string',
            'type'        => 'required|in:cours,td,tp,controle,examen,rattrapage',
            // ❌ حذف status — ما كاينش
        ], [
            'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
        ]);

        $formateur = auth()->user()->formateur;

        if (!$formateur) {
            return back()->with('error', 'Aucun formateur lié à cet utilisateur');
        }

        Seance::create([
            'date_seance'       => $request->date_seance,
            'heure_debut'       => $request->heure_debut,
            'heure_fin'         => $request->heure_fin,
            'module_id'         => $request->module_id,
            'group_id'          => $request->group_id,
            'description'       => $request->description,
            'type'              => $request->type,
            'statut_validation' => 'en_attente', // ✅ default
            'formateur_id'      => $formateur->id,
        ]);

        return redirect()->route('formateur.seances.index')
                         ->with('success', 'Séance ajoutée avec succès');
    }

    public function show(Seance $seance)
    {
        $seance->load('module', 'groupe', 'absences.stagiaire');
        return view('formateur.seances.show', compact('seance'));
    }

    public function edit(Seance $seance)
    {
        $formateur = auth()->user()->formateur;

        // ✅ ما يقدرش يبدل seance ديال formateur آخر
        if ($seance->formateur_id !== $formateur->id) {
            abort(403);
        }

        // ✅ ما يقدرش يبدل seance validee
        if ($seance->statut_validation === 'validee') {
            return back()->with('error', 'Impossible de modifier une séance validée.');
        }

        $modules = Module::where('formateur_id', $formateur->id)->get();
        $groupes = Groupe::all();

        return view('formateur.seances.edit', compact('seance', 'modules', 'groupes'));
    }

    public function update(Request $request, Seance $seance)
    {
        $formateur = auth()->user()->formateur;

        if ($seance->formateur_id !== $formateur->id) {
            abort(403);
        }

        if ($seance->statut_validation === 'validee') {
            return back()->with('error', 'Impossible de modifier une séance validée.');
        }

        $request->validate([
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin'   => 'required|date_format:H:i|after:heure_debut',
            'module_id'   => 'required|exists:modules,id',
            'group_id'    => 'required|exists:groupes,id',
            'description' => 'required|string',
            'type'        => 'required|in:cours,td,tp,controle,examen,rattrapage',
        ], [
            'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
        ]);

        $seance->update($request->only(
            'date_seance', 'heure_debut', 'heure_fin',
            'module_id', 'group_id', 'description', 'type'
        ));

        return redirect()->route('formateur.seances.index')
                         ->with('success', 'Séance modifiée avec succès');
    }

    public function destroy(Seance $seance)
    {
        $formateur = auth()->user()->formateur;

        if ($seance->formateur_id !== $formateur->id) {
            abort(403);
        }

        if ($seance->statut_validation === 'validee') {
            return back()->with('error', 'Impossible de supprimer une séance validée.');
        }

        $seance->delete();

        return redirect()->route('formateur.seances.index')
                         ->with('success', 'Séance supprimée');
    }
}
