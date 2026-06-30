<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Seance;
use Carbon\Carbon;

class AvancementController extends Controller
{
    public function index()
    {
        $modules = Module::with([
            'seances',
            'formateur.user',
            'unite.niveau.filiere', // ✅ مشي filiere مباشرة
            'groupes',              // ✅ many-to-many
        ])->orderBy('titre')->get();

        // ✅ حساب heures_validees من seances مباشرة
        $seancesValidees = Seance::where('statut_validation', 'validee')->get();

        $heuresValidees = $seancesValidees->sum(function ($s) {
            return Carbon::parse($s->heure_debut)
                ->diffInMinutes(Carbon::parse($s->heure_fin)) / 60;
        });

        $stats = [
            'total_modules'      => $modules->count(),
            'seances_validees'   => Seance::where('statut_validation', 'validee')->count(),
            'seances_en_attente' => Seance::where('statut_validation', 'en_attente')->count(),
            'seances_refusees'   => Seance::where('statut_validation', 'refusee')->count(),
            'heures_validees'    => round($heuresValidees, 2),
            'avancement_global'  => 0,
        ];

        $totalDuree = $modules->sum('duree');
        if ($totalDuree > 0) {
            $stats['avancement_global'] = round(($heuresValidees / $totalDuree) * 100, 1);
        }

        // ✅ avancement لكل module
        $modules->each(function ($module) {
            $module->heures_validees = $module->heuresValidees();
            $module->avancement      = $module->avancement();
        });

        return view('directeur.avancement.index', compact('modules', 'stats'));
    }
}
