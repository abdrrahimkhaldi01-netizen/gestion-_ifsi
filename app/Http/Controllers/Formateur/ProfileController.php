<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $formateur = $user->formateur;

        $role = $user->role;

        $view = $role === 'encadrant'
            ? 'encadrant.profile.show'
            : 'formateur.profile.show';

        return view($view, compact('user', 'formateur'));
    }

    // ==============================
    // UPDATE PROFILE
    // ==============================
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'telephone'  => 'nullable|string|max:20',
            'adresse'    => 'nullable|string|max:255',
            'specialite' => 'nullable|string|max:100',
        ]);

        $user->update([
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'email'  => $request->email,
        ]);

        if ($user->formateur) {

            $user->formateur->update([
                'telephone'  => $request->telephone,
                'adresse'    => $request->adresse,
                'specialite' => $request->specialite,
            ]);
        }

        return back()->with(
            'success',
            'Profil mis à jour avec succès.'
        );
    }

    // ==============================
    // UPDATE PASSWORD
    // ==============================
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with(
            'success',
            'Mot de passe modifié avec succès.'
        );
    }
}