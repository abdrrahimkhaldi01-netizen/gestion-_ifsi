<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth','admin'])->group(function () {

    Route::resource('formateurs', FormateurController::class);

    Route::resource('formations', FormationController::class);

    Route::resource('stagiaires', StagiaireController::class);

});
Route::middleware(['auth','formateur'])->group(function () {

    Route::resource('notes', NoteController::class);

    Route::resource('absences', AbsenceController::class);

    Route::resource('seances', SeanceController::class);

});

require __DIR__.'/auth.php';
