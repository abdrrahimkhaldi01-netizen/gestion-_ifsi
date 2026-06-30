<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\UnitExam;

use Illuminate\Http\Request;

class UnitExamController extends Controller
{
    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        $unitExams = UnitExam::with('unite.niveau.filiere')
            ->latest()
            ->paginate(20);

        return view('directeur.unit_exams.index', compact('unitExams'));
    }

    // =========================================================
    // EDIT ONLY (no create manual)
    // =========================================================
    public function edit(UnitExam $unitExam)
    {
        $unitExam->load('unite');

        return view('directeur.unit_exams.edit', compact('unitExam'));
    }

    // =========================================================
    // UPDATE (only poids)
    // =========================================================
    public function update(Request $request, UnitExam $unitExam)
    {
        $request->validate([
            'poids' => 'required|numeric|min:0|max:100',
        ]);

        $unitExam->update([
            'poids' => $request->poids,
        ]);

        return redirect()
            ->route('directeur.unit_exams.index')
            ->with('success', 'Examen mis à jour');
    }

    // =========================================================
    // DESTROY (optional - usually NOT recommended)
    // =========================================================
    public function destroy(UnitExam $unitExam)
    {
        $unitExam->delete();

        return back()->with('success', 'Examen supprimé');
    }
}
