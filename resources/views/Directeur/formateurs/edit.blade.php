@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Modifier le formateur</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Mettre à jour les informations</div>
    </div>
    <a href="{{ route('directeur.formateurs.index') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Retour
    </a>
</div>

{{-- Form Card --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden max-w-2xl">

    {{-- Card Header --}}
    <div class="flex items-center px-5 py-4 border-b border-[#cde4f0]">
        <span class="text-sm font-bold text-[#1a3a5c]">Informations du formateur</span>
    </div>

    {{-- Card Body --}}
    <div class="p-6">
        <form action="{{ route('directeur.formateurs.update', $formateur) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nom + Prénom --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Nom</label>
                    <input type="text" name="nom" value="{{ old('nom', $formateur->user->nom) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('nom') border-red-400 @enderror">
                    @error('nom')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $formateur->user->prenom) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('prenom') border-red-400 @enderror">
                    @error('prenom')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Téléphone + Adresse --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $formateur->telephone) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('telephone') border-red-400 @enderror">
                    @error('telephone')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $formateur->adresse) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('adresse') border-red-400 @enderror">
                    @error('adresse')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Rôle + Spécialité --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Rôle</label>
                    <select name="role"
                            class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9 @error('role') border-red-400 @enderror">
                        <option value="formateur" {{ old('role', $formateur->user->role) == 'formateur' ? 'selected' : '' }}>Formateur</option>
                        <option value="encadrant" {{ old('role', $formateur->user->role) == 'encadrant' ? 'selected' : '' }}>Encadrant</option>
                    </select>
                    @error('role')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Spécialité</label>
                    <input type="text" name="specialite" value="{{ old('specialite', $formateur->specialite) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('specialite') border-red-400 @enderror">
                    @error('specialite')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Mot de passe --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Nouveau mot de passe
                        <span class="text-[10px] text-slate-400 normal-case font-normal">(laisser vide pour ne pas changer)</span>
                    </label>
                    <input type="password" name="password" placeholder="••••••••"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2.5 pt-5 border-t border-[#cde4f0]">
                <a href="{{ route('directeur.formateurs.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 transition-all duration-150 no-underline">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold shadow-md hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150 cursor-pointer border-none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Modifier
                </button>
            </div>

        </form>
    </div>
</div>

@endsection