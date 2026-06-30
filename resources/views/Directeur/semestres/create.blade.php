@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('directeur.semestres.index') }}"
           class="inline-flex items-center gap-1.5 text-[13px] text-slate-500 hover:text-slate-800 no-underline transition-colors duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Retour à la liste
        </a>
        <h1 class="text-xl font-bold text-slate-800 mt-2">Ajouter un semestre</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('directeur.semestres.store') }}">
            @csrf

            {{-- Nom --}}
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">
                    Nom du semestre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nom" value="{{ old('nom') }}"
                       placeholder="ex: Semestre 1"
                       class="w-full px-3.5 py-2.5 rounded-xl border text-[13px] text-slate-800 bg-white outline-none transition-all duration-150
                              {{ $errors->has('nom') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' }}">
                @error('nom')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ordre --}}
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">
                    Ordre <span class="text-red-500">*</span>
                </label>
                <select name="ordre"
                        class="w-full px-3.5 py-2.5 rounded-xl border text-[13px] text-slate-800 bg-white outline-none transition-all duration-150
                               {{ $errors->has('ordre') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' }}">
                    <option value="">-- Choisir --</option>
                    <option value="1" {{ old('ordre') == 1 ? 'selected' : '' }}>S1 — Premier semestre</option>
                    <option value="2" {{ old('ordre') == 2 ? 'selected' : '' }}>S2 — Deuxième semestre</option>
                </select>
                @error('ordre')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Niveau --}}
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">
                    Niveau <span class="text-red-500">*</span>
                </label>
                <select name="niveau_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border text-[13px] text-slate-800 bg-white outline-none transition-all duration-150
                               {{ $errors->has('niveau_id') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' }}">
                    <option value="">-- Choisir un niveau --</option>
                    @foreach($niveaux as $niveau)
                        <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
                            {{ $niveau->nom }} — {{ $niveau->filiere?->nom }}
                        </option>
                    @endforeach
                </select>
                @error('niveau_id')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Année scolaire --}}
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">
                    Année scolaire <span class="text-red-500">*</span>
                </label>
                <select name="annee_scolaire_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border text-[13px] text-slate-800 bg-white outline-none transition-all duration-150
                               {{ $errors->has('annee_scolaire_id') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' }}">
                    <option value="">-- Choisir une année --</option>
                    @foreach($annees as $annee)
                        <option value="{{ $annee->id }}" {{ old('annee_scolaire_id') == $annee->id ? 'selected' : '' }}>
                            {{ $annee->nom }}
                            @if($annee->statut === 'active')
                                ✓ Active
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('annee_scolaire_id')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Statut --}}
            <div class="mb-6">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">
                    Statut <span class="text-red-500">*</span>
                </label>
                <select name="statut"
                        class="w-full px-3.5 py-2.5 rounded-xl border text-[13px] text-slate-800 bg-white outline-none transition-all duration-150
                               {{ $errors->has('statut') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' }}">
                    <option value="inactif" {{ old('statut', 'inactif') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    <option value="ouvert"  {{ old('statut') === 'ouvert'  ? 'selected' : '' }}>Ouvert</option>
                    <option value="cloture" {{ old('statut') === 'cloture' ? 'selected' : '' }}>Clôturé</option>
                </select>
                @error('statut')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Boutons --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="px-5 py-2.5 bg-[#1a3a5c] text-white text-[13px] font-semibold rounded-xl hover:bg-[#0f2942] transition-colors duration-150 cursor-pointer border-none font-['Inter']">
                    Enregistrer
                </button>
                <a href="{{ route('directeur.semestres.index') }}"
                   class="px-5 py-2.5 bg-slate-100 text-slate-600 text-[13px] font-semibold rounded-xl hover:bg-slate-200 transition-colors duration-150 no-underline">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection