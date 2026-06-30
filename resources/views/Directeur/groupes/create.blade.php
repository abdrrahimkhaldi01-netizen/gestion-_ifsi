@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('directeur.groupes.store') }}" method="POST">
                @csrf

                {{-- Nom --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du groupe</label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                           class="w-full border rounded-lg px-3 py-2 @error('nom') border-red-500 @enderror">
                    @error('nom')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Année Scolaire ✅ مضاف --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Année Scolaire <span class="text-red-400">*</span>
                    </label>
                    <select name="annee_scolaire_id"
                            class="w-full border rounded-lg px-3 py-2 @error('annee_scolaire_id') border-red-500 @enderror">
                        <option value="">-- Choisir une année --</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee->id }}"
                                {{ old('annee_scolaire_id', optional(App\Models\AnneeScolaire::current())->id) == $annee->id ? 'selected' : '' }}>
                                {{ $annee->nom }}
                                @if($annee->isActive()) (Active) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('annee_scolaire_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Filière --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filière</label>

                    {{-- ✅ hidden input يحمل filiere_id الفعلي --}}
                    <input type="hidden" name="filiere_id" id="filiere_id_input" value="{{ old('filiere_id') }}">

                    <select id="filiere_select"
                            class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- Choisir une filière --</option>
                        @foreach($filieres as $filiereId => $niveauxGroup)
                            <option value="{{ $filiereId }}"
                                {{ old('filiere_id') == $filiereId ? 'selected' : '' }}>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Niveau</label>
                    <select name="niveau_id" id="niveau_select" disabled
                            class="w-full border rounded-lg px-3 py-2 opacity-50 cursor-not-allowed @error('niveau_id') border-red-500 @enderror">
                        <option value="">-- Choisir d'abord une filière --</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}"
                                    data-filiere="{{ $niveau->filiere_id }}"
                                    hidden
                                    {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
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
                        Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    const filiereSelect     = document.getElementById('filiere_select');
    const filiereIdInput    = document.getElementById('filiere_id_input'); // ✅
    const niveauSelect      = document.getElementById('niveau_select');
    const allOptions        = Array.from(niveauSelect.querySelectorAll('option[data-filiere]'));

    function filterNiveaux() {
        const selectedFiliere = filiereSelect.value;

        // ✅ نحدث hidden input
        filiereIdInput.value = selectedFiliere;

        allOptions.forEach(opt => {
            if (selectedFiliere && opt.dataset.filiere === selectedFiliere) {
                opt.hidden   = false;
                opt.disabled = false;
            } else {
                opt.hidden   = true;
                opt.disabled = true;
                opt.selected = false;
            }
        });

        niveauSelect.value    = '';
        niveauSelect.disabled = !selectedFiliere;

        if (selectedFiliere) {
            niveauSelect.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            niveauSelect.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    filiereSelect.addEventListener('change', filterNiveaux);

    const oldFiliere = "{{ e(old('filiere_id')) }}";
    const oldNiveau  = "{{ e(old('niveau_id')) }}";

    if (oldFiliere) {
        filiereSelect.value = oldFiliere;
        filterNiveaux();
        niveauSelect.value = oldNiveau;
    }
</script>

@endsection