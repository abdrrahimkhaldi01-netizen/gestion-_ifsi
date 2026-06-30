@extends('layouts.app')

@section('title', 'Modifier l\'Unité')

@section('content')

{{-- ===== DELETE FORM (hidden, outside update form) ===== --}}
<form id="delete-form"
      action="{{ route('directeur.unites.destroy', $unite) }}"
      method="POST"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<div class="min-h-screen bg-gray-50/60 py-10 px-4">
    <div class="max-w-2xl mx-auto">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('directeur.unites.index') }}"
               class="hover:text-blue-600 transition-colors font-medium no-underline">
                Unités
            </a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2.5"
                 viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-gray-700 font-medium">Modifier</span>
        </nav>

        {{-- Page header --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                    Modifier l'Unité
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Mise à jour de
                    <span class="font-semibold text-gray-700">{{ $unite->nom }}</span>
                </p>
            </div>
            @if($unite->code)
                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-50 text-blue-700
                             text-sm font-bold font-mono border border-blue-100 mt-1">
                    {{ $unite->code }}
                </span>
            @endif
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="mb-6 flex gap-3 p-4 rounded-xl bg-red-50 border border-red-200">
                <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                     stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <ul class="text-sm text-red-700 space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== UPDATE FORM ===== --}}
        <form action="{{ route('directeur.unites.update', $unite) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Form body --}}
                <div class="p-6 space-y-5">

                    {{-- Nom --}}
                    <div>
                        <label for="nom" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nom de l'unité
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            value="{{ old('nom', $unite->nom) }}"
                            placeholder="Ex: Mathématiques Appliquées"
                            autofocus
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm text-gray-900
                                   placeholder-gray-400 bg-white transition
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                   @error('nom') border-red-400 bg-red-50 @else border-gray-200 @enderror"
                        >
                        @error('nom')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Code + Heures + Coefficient --}}
                    <div class="grid grid-cols-3 gap-4">

                        {{-- Code --}}
                        <div>
                            <label for="code" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Code
                                <span class="text-gray-400 font-normal text-xs ml-1">(optionnel)</span>
                            </label>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                value="{{ old('code', $unite->code) }}"
                                placeholder="MATH-101"
                                class="w-full px-3.5 py-2.5 rounded-xl border text-sm text-gray-900
                                       placeholder-gray-400 bg-white transition font-mono
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                       @error('code') border-red-400 bg-red-50 @else border-gray-200 @enderror"
                            >
                            @error('code')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Heures --}}
                        <div>
                            <label for="heures" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Heures <span class="text-red-500 ml-0.5">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    id="heures"
                                    name="heures"
                                    value="{{ old('heures', $unite->heures) }}"
                                    min="1"
                                    placeholder="60"
                                    class="w-full px-3.5 py-2.5 pr-8 rounded-xl border text-sm text-gray-900
                                           placeholder-gray-400 bg-white transition
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                           [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none
                                           @error('heures') border-red-400 bg-red-50 @else border-gray-200 @enderror"
                                >
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none select-none">h</span>
                            </div>
                            @error('heures')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Coefficient --}}
                        <div>
                            <label for="coefficient" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Coefficient <span class="text-red-500 ml-0.5">*</span>
                            </label>
                            <input
                                type="number"
                                id="coefficient"
                                name="coefficient"
                                value="{{ old('coefficient', $unite->coefficient) }}"
                                min="0"
                                step="1"
                                placeholder="2"
                                oninput="this.value = Math.floor(this.value)"
                                class="w-full px-3.5 py-2.5 rounded-xl border text-sm text-gray-900
                                       placeholder-gray-400 bg-white transition
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                       [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none
                                       @error('coefficient') border-red-400 bg-red-50 @else border-gray-200 @enderror"
                            >
                            @error('coefficient')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Section: Rattachement --}}
                    <div class="pt-1">
                        <div class="flex items-center gap-3 mb-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                                Rattachement
                            </p>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>

                        <div class="space-y-4">

                            {{-- Filière (helper, NOT submitted) --}}
                            <div>
                                <label for="filiere_select" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Filière <span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        id="filiere_select"
                                        class="w-full appearance-none px-3.5 py-2.5 pr-10 rounded-xl border border-gray-200
                                               text-sm text-gray-900 bg-white transition cursor-pointer
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    >
                                        <option value="">— Sélectionner une filière —</option>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                             stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="6 9 12 15 18 9"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Niveau (submitted) --}}
                            <div>
                                <label for="niveau_select" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Niveau <span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        name="niveau_id"
                                        id="niveau_select"
                                        class="w-full appearance-none px-3.5 py-2.5 pr-10 rounded-xl border text-sm
                                               text-gray-900 bg-white transition cursor-pointer
                                               focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                               disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50
                                               @error('niveau_id') border-red-400 bg-red-50 @else border-gray-200 @enderror"
                                    >
                                        <option value="">— Sélectionner un niveau —</option>
                                        @foreach($niveaux as $niveau)
                                            <option
                                                value="{{ $niveau->id }}"
                                                data-filiere="{{ $niveau->filiere_id }}"
                                                {{ old('niveau_id', $unite->niveau_id) == $niveau->id ? 'selected' : '' }}
                                            >
                                                {{ $niveau->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                             stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="6 9 12 15 18 9"/>
                                        </svg>
                                    </div>
                                </div>
                                @error('niveau_id')
                                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>{{-- /p-6 --}}

                {{-- Footer actions --}}
                <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center gap-3 flex-wrap">

                    {{-- Cancel --}}
                    <a href="{{ route('directeur.unites.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200
                              bg-white text-sm font-semibold text-gray-600 hover:bg-gray-100
                              transition-colors no-underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Annuler
                    </a>

                    <div class="flex-1"></div>

                    {{-- Delete (triggers hidden form via JS) --}}
                    <button
                        type="button"
                        onclick="if(confirm('Supprimer définitivement cette unité ? Cette action est irréversible.')) document.getElementById('delete-form').submit()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-red-200
                               bg-white text-sm font-semibold text-red-600 hover:bg-red-50 hover:border-red-300
                               transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                        </svg>
                        Supprimer
                    </button>

                    {{-- Save --}}
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                               text-white text-sm font-semibold
                               transition-colors shadow-sm cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Sauvegarder les modifications
                    </button>

                </div>

            </div>{{-- /card --}}
        </form>

    </div>
</div>

{{-- ===== JS : cascade filière → niveau ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filiereSelect = document.getElementById('filiere_select');
    const niveauSelect  = document.getElementById('niveau_select');
    const allOpts       = [...niveauSelect.querySelectorAll('option[data-filiere]')];

    // Auto-detect filière from pre-selected niveau on load
    const currentNiveauId = "{{ old('niveau_id', $unite->niveau_id) }}";
    const currentOpt = niveauSelect.querySelector(`option[value="${currentNiveauId}"]`);
    if (currentOpt && currentOpt.dataset.filiere) {
        filiereSelect.value = currentOpt.dataset.filiere;
        filterNiveaux(currentOpt.dataset.filiere, false);
    }

    filiereSelect.addEventListener('change', function () {
        niveauSelect.value = '';
        filterNiveaux(this.value, true);
    });

    function filterNiveaux(filiereId, resetValue) {
        if (filiereId) {
            allOpts.forEach(opt => { opt.hidden = opt.dataset.filiere !== filiereId; });
            niveauSelect.disabled = false;
        } else {
            allOpts.forEach(opt => { opt.hidden = false; });
            niveauSelect.disabled = true;
            if (resetValue) niveauSelect.value = '';
        }
    }
});
</script>

@endsection