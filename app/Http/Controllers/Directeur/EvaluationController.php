<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Module;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        $evaluations = Evaluation::with('module.unite.niveau.filiere')
            ->latest()
            ->paginate(20);

        return view('directeur.evaluations.index', compact('evaluations'));
    }

    // =========================================================
    // CREATE (OPTIONAL - mostly not needed anymore)
    // =========================================================
    public function create()
    {
        $modules = Module::with('unite.niveau.filiere')->get();

        return view('directeur.evaluations.create', compact('modules'));
    }

    // =========================================================
    // STORE (manual evaluation ONLY if needed)
    // =========================================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'nom'       => 'required|string|max:255',
            'note_sur'  => 'required|numeric|min:1|max:100',
        ]);

        Evaluation::create([
            'module_id' => $data['module_id'],
            'nom'       => $data['nom'],
            'type'      => 'CC',
            'note_sur'  => $data['note_sur'],
        ]);

        return redirect()
            ->route('directeur.evaluations.index')
            ->with('success', 'Évaluation créée avec succès');
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show(Evaluation $evaluation)
    {
        $evaluation->load('module.unite.niveau.filiere', 'notes.stagiaire');

        return view('directeur.evaluations.show', compact('evaluation'));
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit(Evaluation $evaluation)
    {
        $modules = Module::with('unite.niveau.filiere')->get();

        return view('directeur.evaluations.edit', compact('evaluation', 'modules'));
    }

    // =========================================================
    // UPDATE
    // =========================================================
    public function update(Request $request, Evaluation $evaluation)
    {
        $data = $request->validate([
            'nom'       => 'required|string|max:255',
            'note_sur'  => 'required|numeric|min:1|max:100',
            'module_id' => 'required|exists:modules,id',
        ]);

        $evaluation->update($data);

        return redirect()
            ->route('directeur.evaluations.index')
            ->with('success', 'Évaluation mise à jour');
    }

    // =========================================================
    // DESTROY
    // =========================================================
    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return back()->with('success', 'Évaluation supprimée');
    }
}