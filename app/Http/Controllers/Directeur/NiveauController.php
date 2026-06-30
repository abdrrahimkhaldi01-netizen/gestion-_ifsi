<?php
// Directeur/NiveauController.php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::with('filiere')
            ->orderBy('ordre')
            ->get();

        return view('directeur.niveaux.index', compact('niveaux'));
    }

    public function show(Niveau $niveau)
    {
        $niveau->load([
            'filiere',
            'groupes',
            'unites.modules',
            'semestres'
        ]);

        return view('directeur.niveaux.show', compact('niveau'));
    }
}