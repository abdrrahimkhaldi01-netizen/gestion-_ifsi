@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
    تعديل المجموعة
</h2>
@endsection

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('directeur.groupes.update', $groupe) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nom --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nom du groupe
                    </label>
                    <input type="text"
                           name="nom"
                           value="{{ old('nom', $groupe->nom) }}"
                           class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white @error('nom') border-red-500 @enderror">
                    @error('nom')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Année Scolaire ✅ --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Année Scolaire <span class="text-red-400">*</span>
                    </label>
                    <select name="annee_scolaire_id"
                            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white @error('annee_scolaire_id') border-red-500 @enderror">
                        <option value="">-- Choisir une année --</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee->id }}"
                                {{ old('annee_scolaire_id', $groupe->annee_scolaire_id) == $annee->id ? 'selected' : '' }}>
                                {{ $annee->nom }}
                                @if($annee->isActive()) (Active) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('annee_scolaire_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Filière ✅ مع hidden input --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Filière
                    </label>

                    {{-- ✅ hidden input يحمل filiere_id الفعلي --}}
                    <input type="hidden" name="filiere_id" id="filiere_id_input"
                           value="{{ old('filiere_id', $groupe->niveau->filiere_id ?? '') }}">

                    <select id="filiere_select"
                            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white">
                        <option value="">-- Choisir une filière --</option>
                        @foreach($filieres as $filiereId => $niveauxGroup)
                            <option value="{{ $filiereId }}"
                                {{ old('filiere_id', $groupe->niveau->filiere_id ?? '') == $filiereId ? 'selected' : '' }}>
                                {{ $niveauxGroup->first()->filiere->nom ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('filiere_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Niveau --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Niveau
                    </label>
                    <select name="niveau_id"
                            id="niveau_select"
                            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white @error('niveau_id') border-red-500 @enderror">
                        <option value="">-- Choisir un niveau --</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}"
                                    data-filiere="{{ $niveau->filiere_id }}"
                                    {{ old('niveau_id', $groupe->niveau_id) == $niveau->id ? 'selected' : '' }}>
                                {{ $niveau->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('niveau_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('directeur.groupes.index') }}"
                       class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Modifier
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    const filiereSelect  = document.getElementById('filiere_select');
    const filiereIdInput = document.getElementById('filiere_id_input'); // ✅
    const niveauSelect   = document.getElementById('niveau_select');
    const allOptions     = Array.from(niveauSelect.querySelectorAll('option[data-filiere]'));

    const currentFiliere = "{{ e(old('filiere_id', $groupe->niveau->filiere_id ?? '')) }}";
    const currentNiveau  = "{{ e(old('niveau_id',  $groupe->niveau_id ?? '')) }}";

    function filterNiveaux(resetValue = true) {
        const selectedFiliere = filiereSelect.value;

        // ✅ تحديث hidden input
        filiereIdInput.value = selectedFiliere;

        allOptions.forEach(opt => {
            if (selectedFiliere && opt.dataset.filiere === selectedFiliere) {
                opt.hidden   = false;
                opt.disabled = false;
            } else {
                opt.hidden   = true;
                opt.disabled = true;
                if (resetValue) opt.selected = false;
            }
        });

        niveauSelect.disabled = !selectedFiliere;

        if (selectedFiliere) {
            niveauSelect.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            niveauSelect.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    filiereSelect.addEventListener('change', () => {
        niveauSelect.value = '';
        filterNiveaux(true);
    });

    // ✅ عند التحميل
    filiereSelect.value = currentFiliere;
    filterNiveaux(false);
    niveauSelect.value = currentNiveau;
</script>

@endsection