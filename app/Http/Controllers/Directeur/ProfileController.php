<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * ==============================
     * SHOW PROFILE
     * ==============================
     */
    public function show()
    {
        $user = auth()->user();

        // Relation formateur
        $formateur = $user->formateur;

        return view('directeur.profile.show', compact(
            'user',
            'formateur'
        ));
    }

    /**
     * ==============================
     * UPDATE PROFILE
     * ==============================
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:100',
            ],

            'prenom' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email,' . $user->id,
            ],

            'telephone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'adresse' => [
                'nullable',
                'string',
                'max:255',
            ],

            'specialite' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        // Update user
        $user->update([
            'nom'     => $validated['nom'],
            'prenom'  => $validated['prenom'],
            'email'   => $validated['email'],
        ]);

        // Update formateur/directeur infos
        if ($user->formateur) {

            $user->formateur->update([
                'telephone'  => $validated['telephone'] ?? null,
                'adresse'    => $validated['adresse'] ?? null,
                'specialite' => $validated['specialite'] ?? null,
            ]);
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Profil mis à jour avec succès.'
            );
    }

    /**
     * ==============================
     * UPDATE PASSWORD
     * ==============================
     */
    public function updatePassword(Request $request)
    {
        $request->validate([

            'current_password' => [
                'required',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $user = auth()->user();

        // Vérification ancien mot de passe
        if (! Hash::check(
            $request->current_password,
            $user->password
        )) {

            return back()->withErrors([
                'current_password' =>
                    'Ancien mot de passe incorrect.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make(
                $request->password
            ),
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Mot de passe modifié avec succès.'
            );
    }
}