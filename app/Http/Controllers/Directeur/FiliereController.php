<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Semestre;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    // ============================================================
    // ✅ Method مشتركة — إنشاء نيفو + سيمستراته
    // ============================================================
    private function createNiveauWithSemestres(Filiere $filiere, int $ordre): Niveau
    {
        $suffix = ($ordre === 1) ? 'ère' : 'ème';

        $niveau = Niveau::create([
            'nom'        => $ordre . $suffix . ' année',
            'ordre'      => $ordre,
            'filiere_id' => $filiere->id,
        ]);

        foreach (['S1' => 1, 'S2' => 2] as $nom => $sOrdre) {
            Semestre::create([
                'nom'       => $nom,
                'ordre'     => $sOrdre,
                'niveau_id' => $niveau->id,
            ]);
        }

        return $niveau;
    }

    // ============================================================
    // ✅ Method مشتركة — حذف نيفو مع كل علاقاته (cascade يدوي)
    // ============================================================
    private function deleteNiveauWithRelations(Niveau $niveau): void
    {
        // حذف السيمسترات مع علاقاتها (مثلاً modules, notes...)
        $niveau->semestres->each(function (Semestre $semestre) {
            // إلا عندك علاقات أخرى في Semestre — حذفها هنا
            // $semestre->modules()->delete();
            // $semestre->notes()->delete();
            $semestre->delete();
        });

        $niveau->delete();
    }

    // ============================================================
    // INDEX — عرض كل الفيليارات
    // ============================================================
    public function index()
    {
        $filieres = Filiere::with('niveaux.semestres')->get();

        return view('directeur.filieres.index', compact('filieres'));
    }

    // ============================================================
    // CREATE — فورم الإضافة
    // ============================================================
    public function create()
    {
        return view('directeur.filieres.create');
    }

    // ============================================================
    // STORE — حفظ فيليار جديد
    // ============================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:255',
            'duree' => 'required|integer|min:1|max:3',
            'type'  => 'required|in:qualification,technicien,technicien_specialise',
        ]);

        $filiere = Filiere::create($validated);

        for ($i = 1; $i <= $filiere->duree; $i++) {
            $this->createNiveauWithSemestres($filiere, $i);
        }

        return redirect()
            ->route('directeur.filieres.index')
            ->with('success', 'Filière ajoutée avec succès.');
    }

    // ============================================================
    // SHOW — عرض تفاصيل فيليار
    // ============================================================
    public function show(Filiere $filiere)
    {
        $filiere->load([
            'niveaux.semestres',
            'groupes',
            'unites.modules',
        ]);

        return view('directeur.filieres.show', compact('filiere'));
    }

    // ============================================================
    // EDIT — فورم التعديل
    // ============================================================
    public function edit(Filiere $filiere)
    {
        return view('directeur.filieres.edit', compact('filiere'));
    }

    // ============================================================
    // UPDATE — تحديث فيليار
    // ============================================================
    public function update(Request $request, Filiere $filiere)
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:255',
            'duree' => 'required|integer|min:1|max:3',
            'type'  => 'required|in:qualification,technicien,technicien_specialise',
        ]);

        $oldDuree = $filiere->duree;

        $filiere->update($validated);

        if ($request->duree > $oldDuree) {
            // ✅ زيد النيفوات الناقصة
            for ($i = $oldDuree + 1; $i <= $request->duree; $i++) {
                $this->createNiveauWithSemestres($filiere, $i);
            }
        } elseif ($request->duree < $oldDuree) {
            // ✅ حذف النيفوات الزائدة مع علاقاتها
            $filiere->niveaux()
                ->where('ordre', '>', $request->duree)
                ->get()
                ->each(fn(Niveau $niveau) => $this->deleteNiveauWithRelations($niveau));
        }

        return redirect()
            ->route('directeur.filieres.index')
            ->with('success', 'Filière modifiée avec succès.');
    }

    // ============================================================
    // DESTROY — حذف فيليار مع كل علاقاته
    // ============================================================
    public function destroy(Filiere $filiere)
    {
        // ✅ حذف يدوي cascade: نيفوات → سيمسترات → ...
        $filiere->load('niveaux.semestres');

        $filiere->niveaux->each(
            fn(Niveau $niveau) => $this->deleteNiveauWithRelations($niveau)
        );

        $filiere->delete();

        return redirect()
            ->route('directeur.filieres.index')
            ->with('success', 'Filière supprimée avec succès.');
    }
}