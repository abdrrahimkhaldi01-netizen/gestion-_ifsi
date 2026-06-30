@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family:'Syne',sans-serif">
            Ajouter un stage
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Saisir les informations du stage</p>
    </div>
    <a href="{{ route('encadrant.stages.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors no-underline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Retour
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden max-w-2xl">

    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#185FA5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
        </div>
        <span class="text-sm font-semibold text-gray-800" style="font-family:'Syne',sans-serif">
            Informations du stage
        </span>
    </div>

    <div class="p-6">
        <form action="{{ route('encadrant.stages.store') }}" method="POST">
            @csrf

            {{-- Entreprise --}}
            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">
                    Entreprise
                </label>
                <input type="text" name="entreprise" value="{{ old('entreprise') }}"
                       placeholder="Nom de l'entreprise"
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all @error('entreprise') border-red-400 @enderror">
                @error('entreprise')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">
                        Date de début
                    </label>
                    <input type="date" name="date_debut" value="{{ old('date_debut') }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all @error('date_debut') border-red-400 @enderror">
                    @error('date_debut')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">
                        Date de fin
                    </label>
                    <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all @error('date_fin') border-red-400 @enderror">
                    @error('date_fin')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Groupe --}}
            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">
                    Groupe
                </label>
                <div class="relative">
                    <select id="groupe_select" name="group_id"
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all appearance-none cursor-pointer pr-9 @error('group_id') border-red-400 @enderror">
                        <option value="">— Choisir un groupe —</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" {{ old('group_id') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                @error('group_id')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Stagiaires multi-select --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Stagiaires
                        <span id="count_badge"
                              class="hidden ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white text-[10px] font-bold">0</span>
                    </label>
                    <button type="button" id="select_all_btn"
                            class="hidden text-xs font-medium text-blue-500 hover:text-blue-700 transition-colors">
                        Tout sélectionner
                    </button>
                </div>

                <div id="stagiaires_container"
                     class="w-full rounded-lg border border-gray-200 bg-gray-50 overflow-hidden focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all @error('stagiaire_ids') border-red-400 @enderror">
                    <div id="placeholder_msg" class="px-3 py-2.5 text-sm text-gray-400 italic">
                        — Choisir d'abord un groupe —
                    </div>
                    <div id="loading_msg" class="hidden px-3 py-2.5 text-sm text-blue-400 flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Chargement...
                    </div>
                    <div id="stagiaires_list" class="hidden divide-y divide-gray-100 max-h-52 overflow-y-auto"></div>
                </div>

                <div id="selected_badges" class="flex flex-wrap gap-1.5 mt-2"></div>
                <div id="stagiaires_inputs"></div>

                @error('stagiaire_ids')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-5 border-t border-gray-100">
                <a href="{{ route('encadrant.stages.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors no-underline">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 transition-colors border-none cursor-pointer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Enregistrer
                </button>
            </div>

        </form>
    </div>
</div>

<script>
let selected = {};

const groupeSelect     = document.getElementById('groupe_select');
const placeholderMsg   = document.getElementById('placeholder_msg');
const loadingMsg       = document.getElementById('loading_msg');
const stagiairesList   = document.getElementById('stagiaires_list');
const selectedBadges   = document.getElementById('selected_badges');
const stagiairesInputs = document.getElementById('stagiaires_inputs');
const countBadge       = document.getElementById('count_badge');
const selectAllBtn     = document.getElementById('select_all_btn');

function renderSelected() {
    const ids = Object.keys(selected);
    selectedBadges.innerHTML   = '';
    stagiairesInputs.innerHTML = '';

    countBadge.classList.toggle('hidden', ids.length === 0);
    countBadge.textContent = ids.length;

    ids.forEach(id => {
        const s = selected[id];

        const badge = document.createElement('span');
        badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-800 border border-blue-200';
        badge.innerHTML = `${s.prenom} ${s.nom}
            <button type="button" data-id="${id}" class="text-blue-400 hover:text-red-500 transition-colors flex items-center">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>`;
        badge.querySelector('button').addEventListener('click', () => {
            delete selected[id];
            const chk = document.querySelector(`input[data-id="${id}"]`);
            if (chk) chk.checked = false;
            renderSelected();
            updateSelectAllBtn();
        });
        selectedBadges.appendChild(badge);

        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'stagiaire_ids[]';
        input.value = id;
        stagiairesInputs.appendChild(input);
    });
}

function updateSelectAllBtn() {
    const checkboxes = [...stagiairesList.querySelectorAll('input[type=checkbox]')];
    selectAllBtn.textContent = checkboxes.length && checkboxes.every(c => c.checked)
        ? 'Tout désélectionner'
        : 'Tout sélectionner';
}

function buildList(stagiaires) {
    stagiairesList.innerHTML = '';

    if (!stagiaires.length) {
        stagiairesList.innerHTML = '<div class="px-3 py-2.5 text-sm text-gray-400 italic">Aucun stagiaire dans ce groupe</div>';
        selectAllBtn.classList.add('hidden');
        return;
    }

    selectAllBtn.classList.remove('hidden');

    stagiaires.forEach(s => {
        const row = document.createElement('label');
        row.className = 'flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors duration-100';
        row.innerHTML = `
            <input type="checkbox" data-id="${s.id}"
                   class="w-4 h-4 rounded border-gray-300 accent-blue-500 cursor-pointer flex-shrink-0"
                   ${selected[s.id] ? 'checked' : ''}>
            <span class="text-sm text-gray-800">${s.prenom} ${s.nom}</span>`;

        row.querySelector('input').addEventListener('change', function () {
            if (this.checked) selected[s.id] = s;
            else delete selected[s.id];
            renderSelected();
            updateSelectAllBtn();
        });

        stagiairesList.appendChild(row);
    });

    updateSelectAllBtn();
}

selectAllBtn.addEventListener('click', () => {
    const checkboxes = [...stagiairesList.querySelectorAll('input[type=checkbox]')];
    const allChecked = checkboxes.every(c => c.checked);

    checkboxes.forEach(chk => {
        chk.checked = !allChecked;
        const id  = chk.dataset.id;
        const parts = chk.closest('label').querySelector('span').textContent.trim().split(' ');
        if (!allChecked) selected[id] = { id, prenom: parts[0], nom: parts.slice(1).join(' ') };
        else delete selected[id];
    });

    renderSelected();
    updateSelectAllBtn();
});

groupeSelect.addEventListener('change', function () {
    const groupeId = this.value;
    selected = {};
    renderSelected();
    stagiairesList.classList.add('hidden');
    stagiairesList.innerHTML = '';
    selectAllBtn.classList.add('hidden');

    if (!groupeId) {
        placeholderMsg.classList.remove('hidden');
        loadingMsg.classList.add('hidden');
        return;
    }

    placeholderMsg.classList.add('hidden');
    loadingMsg.classList.remove('hidden');

    fetch(`/encadrant/stagiaires-by-groupe/${groupeId}`)
        .then(res => res.json())
        .then(stagiaires => {
            loadingMsg.classList.add('hidden');
            stagiairesList.classList.remove('hidden');
            buildList(stagiaires);
        })
        .catch(() => {
            loadingMsg.classList.add('hidden');
            stagiairesList.classList.remove('hidden');
            stagiairesList.innerHTML = '<div class="px-3 py-2.5 text-sm text-red-500">Erreur de chargement</div>';
        });
});
</script>

@endsection