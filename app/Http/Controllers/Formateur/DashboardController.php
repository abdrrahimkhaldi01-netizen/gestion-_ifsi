<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Seance;
use App\Models\Module;
use App\Models\Absence;


class DashboardController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return redirect()->route('encadrant.stages.index');
        }

        $seances = Seance::where('formateur_id', $formateur->id)
            ->with(['module', 'groupe'])
            ->orderBy('date_seance', 'desc')
            ->get();

        $modules = Module::where('formateur_id', $formateur->id)
            ->with(['seances', 'groupes', 'unite'])
            ->get();

        // ✅ زيد stats
        $stats = [
            'total_modules'      => $modules->count(),
            'seances_validees'   => $seances->where('statut_validation', 'validee')->count(),
            'seances_en_attente' => $seances->where('statut_validation', 'en_attente')->count(),
            'seances_refusees'   => $seances->where('statut_validation', 'refusee')->count(),
            'total_absences'     => Absence::where('formateur_id', $formateur->id)->count(),
        ];

        // ✅ avancement لكل module
        $modules->each(function ($module) {
            $module->heures_validees = $module->heuresValidees();
            $module->avancement      = $module->avancement();
        });

        return view('formateur.dashboard', compact('seances', 'modules', 'stats'));
    }
}