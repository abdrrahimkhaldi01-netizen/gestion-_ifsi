<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StagiaireController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        $search  = $request->get('search');
        $filiere = $request->get('filiere_id');

        $stagiaires = Stagiaire::with(['filiere:id,nom', 'groupe:id,nom'])
            ->select('id','nom','prenom','cin','date_naissance','telephone','responsable_telephone','filiere_id','group_id')
            ->when($search, fn($q) =>
                $q->where(function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT(nom, ' ', prenom)"), 'like', "%{$search}%")
                        ->orWhere('cin', 'like', "%{$search}%");
                })
            )
            ->when($filiere, fn($q) => $q->where('filiere_id', $filiere))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        $filieres = Filiere::select('id','nom')->orderBy('nom')->get();

        return view('directeur.stagiaires.index', compact('stagiaires', 'filieres', 'search', 'filiere'));
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create()
    {
        $filieres = Filiere::select('id','nom')->orderBy('nom')->get();

        // ✅ فقط groupes ديال السنة الحالية
        $groupes = Groupe::with(['niveau', 'filiere'])
            ->whereHas('anneeScolaire', fn($q) => $q->where('statut', 'active'))
            ->select('id','nom','niveau_id','filiere_id')
            ->orderBy('nom')
            ->get();

        return view('directeur.stagiaires.create', compact('filieres', 'groupes'));
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'                   => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'prenom'                => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'date_naissance'        => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'cin'                   => ['required', 'string', 'max:20', 'unique:stagiaires,cin'],
            'adresse'               => ['nullable', 'string', 'max:500'],
            'telephone'             => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-()\/.]{7,20}$/'],
            'responsable_telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-()\/.]{7,20}$/'],
            'filiere_id'            => ['required', 'integer', 'exists:filieres,id'],
            'group_id'              => ['required', 'integer', 'exists:groupes,id'],
        ]);

        $validated['nom']    = strip_tags(trim($validated['nom']));
        $validated['prenom'] = strip_tags(trim($validated['prenom']));
        $validated['cin']    = strtoupper(trim($validated['cin']));

        try {
            DB::beginTransaction();
            Stagiaire::create($validated);
            DB::commit();

            Log::info('Stagiaire créé', ['cin' => $validated['cin'], 'user' => auth()->id()]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur création stagiaire', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return redirect()->route('directeur.stagiaires.index')
                         ->with('success', 'Stagiaire ajouté avec succès.');
    }

    // ============================================================
    // SHOW
    // ============================================================
    public function show(Stagiaire $stagiaire)
    {
        $stagiaire->load([
            'filiere:id,nom',
            'groupe:id,nom',
            'notes.evaluation',
            'absences',
            'stages',
            'pfe',
        ]);

        return view('directeur.stagiaires.show', compact('stagiaire'));
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit(Stagiaire $stagiaire)
    {
        $filieres = Filiere::select('id','nom')->orderBy('nom')->get();

        // ✅ فقط groupes ديال السنة الحالية
        $groupes = Groupe::with(['niveau', 'filiere'])
            ->whereHas('anneeScolaire', fn($q) => $q->where('statut', 'active'))
            ->select('id','nom','niveau_id','filiere_id')
            ->orderBy('nom')
            ->get();

        return view('directeur.stagiaires.edit', compact('stagiaire', 'filieres', 'groupes'));
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, Stagiaire $stagiaire)
    {
        $validated = $request->validate([
            'nom'                   => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'prenom'                => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'date_naissance'        => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'cin'                   => ['required', 'string', 'max:20', 'unique:stagiaires,cin,' . $stagiaire->id],
            'adresse'               => ['nullable', 'string', 'max:500'],
            'telephone'             => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-()\/.]{7,20}$/'],
            'responsable_telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-()\/.]{7,20}$/'],
            'filiere_id'            => ['required', 'integer', 'exists:filieres,id'],
            'group_id'              => ['required', 'integer', 'exists:groupes,id'],
        ]);

        $validated['nom']    = strip_tags(trim($validated['nom']));
        $validated['prenom'] = strip_tags(trim($validated['prenom']));
        $validated['cin']    = strtoupper(trim($validated['cin']));

        try {
            DB::beginTransaction();
            $stagiaire->update($validated);
            DB::commit();

            Log::info('Stagiaire modifié', ['id' => $stagiaire->id, 'user' => auth()->id()]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur modification stagiaire', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return redirect()->route('directeur.stagiaires.index')
                         ->with('success', 'Stagiaire modifié avec succès.');
    }

    // ============================================================
    // DESTROY
    // ============================================================
    public function destroy(Stagiaire $stagiaire)
    {
        if ($stagiaire->notes()->exists()) {
            return back()->with('error', 'Impossible de supprimer : ce stagiaire a des notes.');
        }

        if ($stagiaire->absences()->exists()) {
            return back()->with('error', 'Impossible de supprimer : ce stagiaire a des absences.');
        }

        if ($stagiaire->stages()->exists()) {
            return back()->with('error', 'Impossible de supprimer : ce stagiaire a des stages.');
        }

        try {
            DB::beginTransaction();
            Log::info('Stagiaire supprimé', ['id' => $stagiaire->id, 'cin' => $stagiaire->cin, 'user' => auth()->id()]);
            $stagiaire->delete();
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur suppression stagiaire', ['error' => $e->getMessage()]);
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }

        return redirect()->route('directeur.stagiaires.index')
                         ->with('success', 'Stagiaire supprimé avec succès.');
    }
}
