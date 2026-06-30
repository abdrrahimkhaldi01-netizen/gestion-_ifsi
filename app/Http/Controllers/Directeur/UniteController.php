<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Unite;
use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Http\Request;

class UniteController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        $filieres = Filiere::orderBy('nom')->get();

        $unites = Unite::with('niveau.filiere')
            ->when($request->filiere_id, function ($query, $filiereId) {
                $query->whereHas('niveau', function ($q) use ($filiereId) {
                    $q->where('filiere_id', $filiereId);
                });
            })
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        return view('directeur.unites.index', compact('unites', 'filieres'));
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create()
    {
        $niveaux = Niveau::with('filiere')->get();

        return view('directeur.unites.create', compact('niveaux'));
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'heures'      => 'required|integer|min:1',
            'coefficient' => 'required|integer|min:0',
            'niveau_id'   => 'required|exists:niveaux,id',
        ]);

        $unite = Unite::create($validated);

        // optional safety (model already does it)
     $unite->generateExamsIfEmpty();

        return redirect()
            ->route('directeur.unites.index')
            ->with('success', 'Unité créée avec succès.');
    }

    // ============================================================
    // SHOW
    // ============================================================
    public function show(Unite $unite)
    {
        $unite->load('niveau.filiere', 'modules', 'unitExams');

        return view('directeur.unites.show', compact('unite'));
    }

    // ============================================================
    // EDIT
    // ============================================================
   public function edit(Unite $unite)
{
    $niveaux  = Niveau::with('filiere')->get();
    $filieres = Filiere::orderBy('nom')->get(); // 👈 add this

    return view('directeur.unites.edit', compact('unite', 'niveaux', 'filieres')); // 👈 and here
}

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, Unite $unite)
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'heures'      => 'required|integer|min:1',
            'coefficient' => 'required|numeric|min:0',
            'niveau_id'   => 'required|exists:niveaux,id',
        ]);

        $unite->update($validated);

        return redirect()
            ->route('directeur.unites.index')
            ->with('success', 'Unité mise à jour avec succès.');
    }

    // ============================================================
    // DESTROY
    // ============================================================
    public function destroy(Unite $unite)
    {
        $unite->unitExams()->delete(); // optional safety
        $unite->delete();

        return redirect()
            ->route('directeur.unites.index')
            ->with('success', 'Unité supprimée avec succès.');
    }
}
