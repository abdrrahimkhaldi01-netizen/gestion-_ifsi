@extends('layouts.app')

@section('header')
@endsection

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-6">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('directeur.unites.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 no-underline transition-colors">
                    Unités
                </a>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <span class="text-sm text-gray-900 font-medium">Nouvelle unité</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Créer une unité</h1>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <form method="POST" action="{{ route('directeur.unites.store') }}" class="space-y-5">
                @csrf

                {{-- Nom --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                           placeholder="Ex: Unité de Mathématiques"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition @error('nom') border-red-300 bg-red-50 @enderror">
                    @error('nom')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Code --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Code <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="Ex: MATH-101"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition @error('code') border-red-300 bg-red-50 @enderror">
                    @error('code')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Heures + Coefficient --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Heures <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="heures" value="{{ old('heures') }}"
                               min="1" placeholder="Ex: 60"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none @error('heures') border-red-300 bg-red-50 @enderror">
                        @error('heures')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Coefficient <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="coefficient" value="{{ old('coefficient') }}"
                               min="0" step="1" placeholder="Ex: 2"
                               oninput="this.value = Math.floor(this.value)"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none @error('coefficient') border-red-300 bg-red-50 @enderror">
                        @error('coefficient')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Filière --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Filière <span class="text-red-500">*</span>
                    </label>
                    <select id="filiere_select"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition">
                        <option value="">— Sélectionner une filière —</option>
                        @foreach($niveaux->groupBy(fn($n) => $n->filiere->nom ?? 'Sans filière') as $filiereName => $niveauxGroup)
                            <option value="{{ $filiereName }}">{{ $filiereName }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Niveau --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Niveau <span class="text-red-500">*</span>
                    </label>
                    <select name="niveau_id" id="niveau_select" disabled
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition disabled:opacity-50 disabled:cursor-not-allowed @error('niveau_id') border-red-300 bg-red-50 @enderror">
                        <option value="">— Sélectionner un niveau —</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}"
                                    data-filiere="{{ $niveau->filiere->nom ?? 'Sans filière' }}"
                                    {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                {{ $niveau->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('niveau_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#185FA5] text-white text-sm font-semibold hover:bg-[#0C447C] transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Enregistrer
                    </button>
                    <a href="{{ route('directeur.unites.index') }}"
                       class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors no-underline">
                        Annuler
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
    const filiereSelect = document.getElementById('filiere_select');
    const niveauSelect = document.getElementById('niveau_select');
    const allOptions = [...niveauSelect.querySelectorAll('option[data-filiere]')];

    // إذا كان old('niveau_id') موجود، نحدد الفيليير تلقائيا
    const oldNiveauId = "{{ old('niveau_id') }}";
    if (oldNiveauId) {
        const selectedOpt = niveauSelect.querySelector(`option[value="${oldNiveauId}"]`);
        if (selectedOpt) {
            filiereSelect.value = selectedOpt.dataset.filiere;
            filterNiveaux(selectedOpt.dataset.filiere);
        }
    }

    filiereSelect.addEventListener('change', function () {
        niveauSelect.value = '';
        filterNiveaux(this.value);
    });

    function filterNiveaux(filiere) {
        if (filiere) {
            niveauSelect.disabled = false;
            allOptions.forEach(opt => {
                opt.hidden = opt.dataset.filiere !== filiere;
            });
        } else {
            niveauSelect.disabled = true;
            niveauSelect.value = '';
            allOptions.forEach(opt => opt.hidden = false);
        }
    }
</script>

@endsection