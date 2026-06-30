<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FormateurController extends Controller
{
    public function index()
    {
        $formateurs = Formateur::with('user')
            ->whereHas('user')
            ->latest()
            ->paginate(20);

        return view('directeur.formateurs.index', compact('formateurs'));
    }

    public function create()
    {
        return view('directeur.formateurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'        => 'required|string',
            'prenom'     => 'required|string',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'telephone'  => 'required|string',
            'adresse'    => 'required|string',
            'specialite' => 'required|string',
            'role'       => 'required|in:formateur,encadrant', // ← ajouté
        ]);

        $user = User::create([
            'nom'      => $request->nom,
            'prenom'   => $request->prenom,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role, // ← ajouté
        ]);

        Formateur::create([
            'telephone'  => $request->telephone,
            'adresse'    => $request->adresse,
            'specialite' => $request->specialite,
            'user_id'    => $user->id,
        ]);

        return redirect()->route('directeur.formateurs.index')
                        ->with('success', 'Formateur ajouté avec succès');
    }

    public function edit(Formateur $formateur)
    {
        return view('directeur.formateurs.edit', compact('formateur'));
    }
public function update(Request $request, Formateur $formateur)
{
    $rules = [
        'nom'        => 'required|string',
        'prenom'     => 'required|string',
        'telephone'  => 'required|string',
        'adresse'    => 'required|string',
        'specialite' => 'required|string',
        'role'       => 'required|in:formateur,encadrant',
        'password'   => 'nullable|min:6|confirmed', // ← ajouté
    ];

    $request->validate($rules);

    $userData = [
        'nom'    => $request->nom,
        'prenom' => $request->prenom,
        'role'   => $request->role,
    ];

    // كيبدل password غير إلا تعبا
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->password);
    }

    $formateur->user->update($userData);

    $formateur->update([
        'telephone'  => $request->telephone,
        'adresse'    => $request->adresse,
        'specialite' => $request->specialite,
    ]);

    return redirect()->route('directeur.formateurs.index')
                    ->with('success', 'Formateur modifié avec succès');
}
    public function destroy(Formateur $formateur)
    {
        $formateur->user->delete();
        return redirect()->route('directeur.formateurs.index')
                        ->with('success', 'Formateur supprimé');
    }
}
