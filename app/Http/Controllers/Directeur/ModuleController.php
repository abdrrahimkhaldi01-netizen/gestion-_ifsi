<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Unite;
use App\Models\Filiere;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $filieres   = Filiere::orderBy('nom')->get();
        $formateurs = Formateur::with('user')->get();

        $modules = Module::with([
            'formateur.user',
            'unite.niveau.filiere',
            'groupes',
            'seances',
        ])
        ->when($request->filiere_id, function ($q, $filiereId) {
            $q->whereHas('unite.niveau', function ($q2) use ($filiereId) {
                $q2->where('filiere_id', $filiereId);
            });
        })
        ->when($request->formateur_id, function ($q, $formateurId) {
            $q->where('formateur_id', $formateurId);
        })
        ->orderBy('titre')
        ->paginate(20)
        ->withQueryString();

        return view('directeur.modules.index', compact('modules', 'filieres', 'formateurs'));
    }

    public function create()
{
    $formateurs = Formateur::with('user')->get();
    $groupes    = Groupe::with('niveau.filiere')->get();
    $unites     = Unite::with('niveau.filiere')->get();
    $filieres   = \App\Models\Filiere::orderBy('nom')->get(); // ✅ زيد هذا

    return view('directeur.modules.create', compact('formateurs', 'groupes', 'unites', 'filieres'));
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'duree'        => 'nullable|integer|min:1',
            'nombre_cc'    => 'required|integer|min:1|max:10',
            'formateur_id' => 'required|exists:formateurs,id',
            'unite_id'     => 'required|exists:unites,id',
            'groupes'      => 'required|array',
            'groupes.*'    => 'exists:groupes,id',
        ]);

        $module = Module::create([
            'titre'        => $validated['titre'],
            'description'  => $validated['description'] ?? null,
            'duree'        => $validated['duree'] ?? null,
            'nombre_cc'    => $validated['nombre_cc'],
            'formateur_id' => $validated['formateur_id'],
            'unite_id'     => $validated['unite_id'],
        ]);

        $module->groupes()->sync($validated['groupes']);

        return redirect()
            ->route('directeur.modules.index')
            ->with('success', 'Module ajouté avec succès.');
    }

    public function show(Module $module)
    {
        $module->load(
            'formateur.user',
            'unite.niveau.filiere',
            'groupes',
            'evaluations',
            'seances'
        );

        return view('directeur.modules.show', compact('module'));
    }

   public function edit(Module $module)
{
    $formateurs      = Formateur::with('user')->get();
    $groupes         = Groupe::with('niveau.filiere')->get();
    $unites          = Unite::with('niveau.filiere')->get();
    $filieres        = Filiere::orderBy('nom')->get(); // ✅ زيد هذا
    $selectedGroupes = $module->groupes->pluck('id')->toArray();

    return view(
        'directeur.modules.edit',
        compact('module', 'formateurs', 'groupes', 'unites', 'filieres', 'selectedGroupes')
    );
}

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'titre'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'duree'        => 'nullable|integer|min:1',
            'nombre_cc'    => 'required|integer|min:1|max:10',
            'formateur_id' => 'required|exists:formateurs,id',
            'unite_id'     => 'required|exists:unites,id',
            'groupes'      => 'required|array',
            'groupes.*'    => 'exists:groupes,id',
        ]);

        $module->update([
            'titre'        => $validated['titre'],
            'description'  => $validated['description'] ?? null,
            'duree'        => $validated['duree'] ?? null,
            'nombre_cc'    => $validated['nombre_cc'],
            'formateur_id' => $validated['formateur_id'],
            'unite_id'     => $validated['unite_id'],
        ]);

        $module->groupes()->sync($validated['groupes']);

        return redirect()
            ->route('directeur.modules.index')
            ->with('success', 'Module modifié avec succès.');
    }

    public function destroy(Module $module)
    {
        $module->groupes()->detach();
        $module->delete();

        return redirect()
            ->route('directeur.modules.index')
            ->with('success', 'Module supprimé avec succès.');
    }
}
