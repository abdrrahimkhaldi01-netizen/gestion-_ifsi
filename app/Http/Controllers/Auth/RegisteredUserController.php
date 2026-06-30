<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Formateur;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['nullable', 'string', 'max:255'],
            'nom'      => ['nullable', 'required_without:name', 'string', 'max:255'],
            'prenom'   => ['nullable', 'required_without:name', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['nullable', 'in:directeur,formateur,encadrant'],
        ]);

        [$prenom, $nom] = $this->resolveNameParts($request);
        $role = $request->input('role', 'formateur');

        $user = User::create([
            'nom'      => $nom,
            'prenom'   => $prenom,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $role,
        ]);

        // إذا كان formateur — نبنيو profile ديالو تلقائياً
        if ($user->role === 'formateur') {
            Formateur::create([
                'user_id'    => $user->id,
                'telephone'  => '',
                'adresse'    => '',
                'specialite' => '',
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        // redirect حسب الـ role
        return redirect()->route('dashboard');
    }

    private function resolveNameParts(Request $request): array
    {
        if ($request->filled('nom') || $request->filled('prenom')) {
            return [
                trim((string) $request->input('prenom')),
                trim((string) $request->input('nom')),
            ];
        }

        $parts = preg_split('/\s+/', trim((string) $request->input('name')), 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? $parts[0] ?? '',
        ];
    }

}
