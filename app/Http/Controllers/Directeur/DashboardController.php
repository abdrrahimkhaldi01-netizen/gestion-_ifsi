<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Models\Stagiaire;
use App\Models\Seance;
use App\Models\Absence;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Note;
use App\Models\Filiere;
use App\Models\AnneeScolaire;
use App\Services\Absences\AbsenceAnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, AbsenceAnalyticsService $absenceAnalytics)
    {
        $absenceFilters = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'annee_scolaire_id' => ['nullable', 'integer', 'exists:annees_scolaires,id'],
            'filiere_id' => ['nullable', 'integer', 'exists:filieres,id'],
            'groupe_id' => ['nullable', 'integer', 'exists:groupes,id'],
        ]);
        [$selectedAbsenceMonth, $selectedAbsenceYear] = $absenceAnalytics->resolveMonthYear($absenceFilters);
        $absenceFilters['month'] = $selectedAbsenceMonth;
        $absenceFilters['year'] = $selectedAbsenceYear;

        $stats = [
            'formateurs' => Formateur::count(),
            'stagiaires' => Stagiaire::count(),
            'groupes'    => Groupe::count(),
            'modules'    => Module::count(),
            'filieres'   => Filiere::count(),

            // ✅ seances par statut
            'seances_total'      => Seance::count(),
            'seances_validees'   => Seance::where('statut_validation', 'validee')->count(),
            'seances_en_attente' => Seance::where('statut_validation', 'en_attente')->count(),
            'seances_refusees'   => Seance::where('statut_validation', 'refusee')->count(),

            // ✅ absences
            'absences_total'      => Absence::count(),
            'absences_justifiees' => Absence::where('justifiee', true)->count(),

            // ✅ notes
            'notes_en_attente' => Note::where('statut', 'en_attente')->count(),
            'notes_validees'   => Note::where('statut', 'validee')->count(),
        ];

        // ✅ dernières séances en attente
        $seancesEnAttente = Seance::where('statut_validation', 'en_attente')
            ->with(['module', 'formateur.user', 'groupe'])
            ->orderBy('date_seance', 'desc')
            ->take(5)
            ->get();

        $absenceRows = $absenceAnalytics->getDashboardRows($absenceFilters);
        $anneesScolaires = AnneeScolaire::orderByDesc('date_debut')->get();
        $filieres = Filiere::orderBy('nom')->get();
        $groupes = Groupe::with('niveau.filiere')->orderBy('nom')->get();

        return view('directeur.dashboard', compact(
            'stats',
            'seancesEnAttente',
            'absenceRows',
            'anneesScolaires',
            'filieres',
            'groupes',
            'selectedAbsenceMonth',
            'selectedAbsenceYear',
            'absenceFilters'
        ));
    }
}
