<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;

class PfeController extends Controller
{
    public function index()
    {
        return redirect()->route('directeur.notes.index', ['tab' => 'pfe']);
    }
}
