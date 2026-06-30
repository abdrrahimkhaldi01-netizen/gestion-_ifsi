<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnneeScolaireController extends Controller
{
    // =========================================
    // INDEX
    // =========================================

  public function index()
{
    $annees = AnneeScolaire::withCount(['groupes', 'resultats'])
        ->orderByDesc('date_debut')
        ->paginate(15);

    $stats = [
        'total'    => AnneeScolaire::count(),
        'active'   => AnneeScolaire::where('statut', 'active')->count(),
        'archivee' => AnneeScolaire::where('statut', 'archivee')->count(),
    ];

    return view('directeur.annees_scolaires.index', compact('annees', 'stats'));
}

    // =========================================
    // CREATE
    // =========================================

    public function create()
    {
        return view('directeur.annees_scolaires.create');
    }

    // =========================================
    // STORE
    // =========================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'        => ['required', 'string', 'max:20', 'unique:annees_scolaires,nom'],
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after:date_debut'],
            'statut'     => ['required', Rule::in(['active', 'archivee'])],
        ]);

        $annee = AnneeScolaire::create($validated);

        // إذا أنشأها active، نشغل activate() لأرشفة الباقيات
        if ($annee->statut === AnneeScolaire::ACTIVE) {
            $annee->activate();
        }

        return redirect()
            ->route('directeur.annees_scolaires.index')
            ->with('success', 'Année scolaire créée avec succès');
    }

    // =========================================
    // EDIT
    // =========================================

    public function edit(AnneeScolaire $anneeScolaire)
    {
        return view('directeur.annees_scolaires.edit', compact('anneeScolaire'));
    }

    // =========================================
    // UPDATE
    // =========================================

    public function update(Request $request, AnneeScolaire $anneeScolaire)
    {
        $validated = $request->validate([
            'nom'        => ['required', 'string', 'max:20', Rule::unique('annees_scolaires', 'nom')->ignore($anneeScolaire->id)],
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after:date_debut'],
            'statut'     => ['required', Rule::in(['active', 'archivee'])],
        ]);

        $anneeScolaire->update($validated);

        if ($anneeScolaire->statut === AnneeScolaire::ACTIVE) {
            $anneeScolaire->activate();
        }

        return redirect()
            ->route('directeur.annees_scolaires.index')
            ->with('success', 'Année scolaire modifiée avec succès');
    }

    // =========================================
    // DESTROY
    // =========================================

    public function destroy(AnneeScolaire $anneeScolaire)
    {
        // Do not delete a school year that is already used.
        if (
            $anneeScolaire->groupes()->exists()   ||
            $anneeScolaire->resultats()->exists()
        ) {
            return back()->with('error', 'Impossible de supprimer une année déjà utilisée.');
        }

        $anneeScolaire->delete();

        return redirect()
            ->route('directeur.annees_scolaires.index')
            ->with('success', 'Année scolaire supprimée avec succès');
    }

    // =========================================
    // ACTIVATE
    // =========================================

    public function activate(AnneeScolaire $anneeScolaire)
    {
        $anneeScolaire->activate();

        return back()->with('success', 'Année scolaire activée avec succès');
    }

    // =========================================
    // ARCHIVE
    // =========================================

    public function archive(AnneeScolaire $anneeScolaire)
    {
        try {
            $anneeScolaire->archive();
            return back()->with('success', 'Année scolaire archivée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
