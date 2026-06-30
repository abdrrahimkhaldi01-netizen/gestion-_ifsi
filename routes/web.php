<?php

use App\Http\Controllers\Formateur\DashboardController;
use App\Http\Controllers\Formateur\SeanceController;
use App\Http\Controllers\Formateur\AbsenceController;
use App\Http\Controllers\Formateur\NoteController;
use App\Http\Controllers\Formateur\ProfileController;

use App\Http\Controllers\Directeur\DashboardController as DirecteurDashboard;
use App\Http\Controllers\Directeur\FormateurController as DirecteurFormateur;
use App\Http\Controllers\Directeur\FiliereController;
use App\Http\Controllers\Directeur\GroupeController;
use App\Http\Controllers\Directeur\StagiaireController;
use App\Http\Controllers\Directeur\ModuleController;
use App\Http\Controllers\Directeur\NoteController as DirecteurNote;
use App\Http\Controllers\Directeur\AbsenceController as DirecteurAbsence;
use App\Http\Controllers\Directeur\AnneeScolaireController;
use App\Http\Controllers\Directeur\SeanceController as DirecteurSeance;
use App\Http\Controllers\Directeur\AvancementController;
use App\Http\Controllers\Directeur\UniteController;
use App\Http\Controllers\Directeur\EvaluationController;
use App\Http\Controllers\Directeur\SemestreController;
use App\Http\Controllers\Directeur\UnitExamController;
use App\Http\Controllers\Directeur\PfeController;
use App\Http\Controllers\Directeur\ProfileController as DirecteurProfile;
use App\Http\Controllers\Encadrant\StageController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'directeur' => redirect()->route('directeur.dashboard'),
            'encadrant' => redirect()->route('encadrant.stages.index'),
            default     => redirect()->route('formateur.dashboard'),
        };
    }
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return match(auth()->user()->role) {
        'directeur' => redirect()->route('directeur.dashboard'),
        'encadrant' => redirect()->route('encadrant.stages.index'),
        default     => redirect()->route('formateur.dashboard'),
    };
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| FORMATEUR ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:formateur'])
    ->prefix('formateur')
    ->name('formateur.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('seances',  SeanceController::class);
        Route::resource('absences', AbsenceController::class)->except(['show']);

        Route::get('notes/pfe',  [NoteController::class, 'pfeIndex'])->name('notes.pfe.index');
        Route::post('notes/pfe', [NoteController::class, 'pfeStore'])->name('notes.pfe.store');

        Route::resource('notes', NoteController::class)->except(['show']);

        Route::get('modules/{module}/evaluations', [NoteController::class, 'getEvaluations'])->name('modules.evaluations');
        Route::get('modules/{module}/unit-exams',  [NoteController::class, 'getUnitExams'])->name('modules.unitExams');
        Route::get('modules/{module}/stagiaires',  [NoteController::class, 'getStagiaires'])->name('modules.stagiaires');

        Route::get('/stagiaires-by-groupe/{groupeId}', [AbsenceController::class, 'stagiairesByGroupe'])
            ->name('stagiaires.by.groupe');

        Route::get('notes/releve/{stagiaireId}', [NoteController::class, 'releve'])
             ->name('notes.releve');

        Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

/*
|--------------------------------------------------------------------------
| ENCADRANT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:encadrant'])
    ->prefix('encadrant')
    ->name('encadrant.')
    ->group(function () {
        Route::resource('stages',   StageController::class);
        Route::get('/stagiaires-by-groupe/{groupeId}', [StageController::class, 'stagiairesByGroupe'])
            ->name('stagiaires.by.groupe');

        Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

/*
|--------------------------------------------------------------------------
| DIRECTEUR ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:directeur'])
    ->prefix('directeur')
    ->name('directeur.')
    ->group(function () {
        Route::get('/dashboard', [DirecteurDashboard::class, 'index'])->name('dashboard');

        Route::resource('formateurs',  DirecteurFormateur::class)->except(['show']);
        Route::resource('filieres',    FiliereController::class);
        Route::resource('groupes',     GroupeController::class);
        Route::resource('stagiaires',  StagiaireController::class);
        Route::resource('modules',     ModuleController::class);
        Route::resource('unites',      UniteController::class);
        Route::resource('evaluations', EvaluationController::class);
        Route::resource('semestres',   SemestreController::class)->except(['show']);
        Route::resource('unit_exams',  UnitExamController::class)
            ->only(['index', 'edit', 'update', 'destroy'])
            ->parameters(['unit_exams' => 'unitExam']);
        Route::resource('pfes',        PfeController::class)->only(['index']);
        Route::resource('absences',    DirecteurAbsence::class);

        /*-- WHATSAPP — bulk قبل {stagiaire} مهم! --*/
        Route::post('absences/bulk-whatsapp', [DirecteurAbsence::class, 'sendBulkWhatsApp'])->name('absences.bulk-whatsapp');
        Route::post('absences/{stagiaire}/whatsapp', [DirecteurAbsence::class, 'sendWhatsApp'])->name('absences.whatsapp');

        /*-- ANNEES SCOLAIRES --*/
        Route::resource('annees-scolaires', AnneeScolaireController::class)
            ->except(['show'])
            ->parameters(['annees-scolaires' => 'anneeScolaire'])
            ->names([
                'index'   => 'annees_scolaires.index',
                'create'  => 'annees_scolaires.create',
                'store'   => 'annees_scolaires.store',
                'show'    => 'annees_scolaires.show',
                'edit'    => 'annees_scolaires.edit',
                'update'  => 'annees_scolaires.update',
                'destroy' => 'annees_scolaires.destroy',
            ]);

        Route::post('annees-scolaires/{anneeScolaire}/activate', [AnneeScolaireController::class, 'activate'])
            ->name('annees_scolaires.activate');

        Route::post('annees-scolaires/{anneeScolaire}/archive', [AnneeScolaireController::class, 'archive'])
            ->name('annees_scolaires.archive');

        /*-- NOTES --*/
        Route::get('/notes/export-all',            [DirecteurNote::class, 'exportAll'])->name('notes.export-all');
        Route::get('/notes/export-pdf',            [DirecteurNote::class, 'exportPdf'])->name('notes.export-pdf');
        Route::get('/notes/export-stagiaire/{id}', [DirecteurNote::class, 'exportStagiaire'])->name('notes.export-stagiaire');
        Route::post('/notes/valider-tout',         [DirecteurNote::class, 'validerTout'])->name('notes.valider-tout');
        Route::post('/notes/devalider-tout',       [DirecteurNote::class, 'devaliderTout'])->name('notes.devalider-tout');
        Route::get('/notes',                       [DirecteurNote::class, 'index'])->name('notes.index');
        Route::get('/notes/releve/{stagiaireId}',  [DirecteurNote::class, 'releve'])->name('notes.releve');
        Route::get('/notes/{id}',                  [DirecteurNote::class, 'show'])->name('notes.show');
        Route::post('/notes/{id}/valider',         [DirecteurNote::class, 'valider'])->name('notes.valider');
        Route::post('/notes/{id}/devalider',       [DirecteurNote::class, 'devalider'])->name('notes.devalider');

        /*-- SEANCES --*/
        Route::get('/seances',                     [DirecteurSeance::class, 'index'])->name('seances.index');
        Route::post('/seances/{id}/valider',       [DirecteurSeance::class, 'valider'])->name('seances.valider');
        Route::post('/seances/{id}/refuser',       [DirecteurSeance::class, 'refuser'])->name('seances.refuser');
        Route::post('/seances/{id}/reinitialiser', [DirecteurSeance::class, 'reinitialiser'])->name('seances.reinitialiser');

        /*-- AVANCEMENT --*/
        Route::get('/avancement', [AvancementController::class, 'index'])->name('avancement.index');

        /*-- PROFILE --*/
        Route::get('/profile',          [DirecteurProfile::class, 'show'])->name('profile.show');
        Route::put('/profile',          [DirecteurProfile::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [DirecteurProfile::class, 'updatePassword'])->name('profile.password');
    });

require __DIR__ . '/auth.php';
