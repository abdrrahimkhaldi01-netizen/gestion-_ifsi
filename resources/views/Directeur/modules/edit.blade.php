@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Modifier le module</div>
            <div class="text-sm text-[#5a8aaa] mt-0.5">Mettre à jour les informations du module</div>
        </div>
        <a href="{{ route('directeur.modules.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 transition-all no-underline">
            ← Retour
        </a>
    </div>

    @if($errors->any())
        <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center px-5 py-4 border-b border-[#cde4f0]">
            <span class="text-sm font-bold text-[#1a3a5c]">Informations du module</span>
        </div>

        <div class="p-6">
            <form action="{{ route('directeur.modules.update', $module) }}" method="POST" id="moduleForm">
                @csrf
                @method('PUT')

                {{-- Titre --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="titre" value="{{ old('titre', $module->titre) }}" placeholder="Titre du module"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white transition-all @error('titre') border-red-400 bg-red-50 @enderror">
                    @error('titre') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Description <span class="text-[#5a8aaa] font-normal normal-case">(optionnel)</span>
                    </label>
                    <textarea name="description" rows="3" placeholder="Description du module..."
                              class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white transition-all resize-y @error('description') border-red-400 bg-red-50 @enderror">{{ old('description', $module->description) }}</textarea>
                    @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Durée --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Durée (heures) <span class="text-[#5a8aaa] font-normal normal-case">(optionnel)</span>
                    </label>
                    <input type="number" name="duree" value="{{ old('duree', $module->duree) }}" placeholder="Ex: 40"
                           min="1" step="1"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white transition-all
                                  [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none
                                  @error('duree') border-red-400 bg-red-50 @enderror">
                    @error('duree') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nombre CC --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Nombre de CC <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="nombre_cc" value="{{ old('nombre_cc', $module->nombre_cc) }}"
                           min="1" max="10" step="1" placeholder="Ex: 2"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white transition-all
                                  [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none
                                  @error('nombre_cc') border-red-400 bg-red-50 @enderror">
                    @error('nombre_cc') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Filière (filtre uniquement) --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Filière
                    </label>
                    <div class="relative">
                        <select id="filiere_filter"
                                class="w-full appearance-none px-3 py-2.5 pr-9 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white transition-all cursor-pointer">
                            <option value="">— Choisir une filière —</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Unité --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Unité <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="unite_id" id="unite_select"
                                class="w-full appearance-none px-3 py-2.5 pr-9 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white transition-all cursor-pointer @error('unite_id') border-red-400 bg-red-50 @enderror">
                            <option value="">— Choisir d'abord une filière —</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>
                    @error('unite_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Formateur --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Formateur <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="formateur_id"
                                class="w-full appearance-none px-3 py-2.5 pr-9 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white transition-all cursor-pointer @error('formateur_id') border-red-400 bg-red-50 @enderror">
                            <option value="">— Choisir un formateur —</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->id }}"
                                    {{ old('formateur_id', $module->formateur_id) == $formateur->id ? 'selected' : '' }}>
                                    {{ $formateur->user->nom }} {{ $formateur->user->prenom }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>
                    @error('formateur_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Groupes --}}
                <div class="mb-6">
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-2">
                        Groupes <span class="text-red-500">*</span>
                    </label>
                    @error('groupes')
                        <p class="text-red-600 text-xs mb-2">{{ $message }}</p>
                    @enderror
                    <div id="groupes-container" class="border border-slate-200 rounded-lg divide-y divide-slate-100 overflow-hidden">
                        <div class="px-4 py-6 text-center text-sm text-slate-400">— Choisir d'abord une filière —</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2.5 pt-5 border-t border-[#cde4f0]">
                    <a href="{{ route('directeur.modules.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 transition-all no-underline">
                        Annuler
                    </a>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold hover:bg-[#132d4a] transition-all border-none cursor-pointer">
                        Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- JSON Data --}}
<div id="unites-data" style="display:none">{{ json_encode($unites->map(fn($u) => [
    'id'         => $u->id,
    'nom'        => $u->nom . ($u->code ? ' ('.$u->code.')' : ''),
    'filiere_id' => optional($u->niveau->filiere)->id,
])) }}</div>

<div id="groupes-data" style="display:none">{{ json_encode($groupes->map(fn($g) => [
    'id'         => $g->id,
    'nom'        => $g->nom,
    'niveau'     => optional($g->niveau)->nom,
    'filiere_id' => optional($g->niveau->filiere)->id,
    'filiere_nom'=> optional($g->niveau->filiere)->nom,
])) }}</div>

<div id="current-unite-id" style="display:none">{{ json_encode(old('unite_id', $module->unite_id)) }}</div>
<div id="selected-groupes" style="display:none">{{ json_encode(old('groupes', $selectedGroupes)) }}</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Anti double-submit
    document.getElementById('moduleForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        if (btn.dataset.clicked) return false;
        btn.dataset.clicked = '1';
        btn.disabled = true;
        btn.style.opacity = '0.6';
    });

    const filiereSelect   = document.getElementById('filiere_filter');
    const uniteSelect     = document.getElementById('unite_select');
    const groupesCont     = document.getElementById('groupes-container');
    const allUnites       = JSON.parse(document.getElementById('unites-data').textContent);
    const allGroupes      = JSON.parse(document.getElementById('groupes-data').textContent);
    const currentUniteId  = JSON.parse(document.getElementById('current-unite-id').textContent);
    const selectedGroupes = JSON.parse(document.getElementById('selected-groupes').textContent);

    function buildUnites(filiereId) {
        uniteSelect.innerHTML = '';
        const ph = document.createElement('option');
        ph.value = '';
        ph.textContent = filiereId ? '— Choisir une unité —' : '— Choisir d\'abord une filière —';
        uniteSelect.appendChild(ph);

        if (!filiereId) return;

        const filtered = allUnites.filter(u => String(u.filiere_id) === String(filiereId));
        filtered.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = u.nom;
            if (currentUniteId && String(u.id) === String(currentUniteId)) opt.selected = true;
            uniteSelect.appendChild(opt);
        });

        if (filtered.length === 0) {
            const empty = document.createElement('option');
            empty.disabled = true;
            empty.textContent = 'Aucune unité pour cette filière';
            uniteSelect.appendChild(empty);
        }
    }

    function buildGroupes(filiereId) {
        groupesCont.innerHTML = '';

        if (!filiereId) {
            groupesCont.innerHTML = '<div class="px-4 py-6 text-center text-sm text-slate-400">— Choisir d\'abord une filière —</div>';
            return;
        }

        const filtered = allGroupes.filter(g => String(g.filiere_id) === String(filiereId));

        if (filtered.length === 0) {
            groupesCont.innerHTML = '<div class="px-4 py-6 text-center text-sm text-slate-400">Aucun groupe pour cette filière</div>';
            return;
        }

        const header = document.createElement('div');
        header.className = 'px-3 py-1.5 bg-slate-50';
        header.innerHTML = `<span class="text-[10px] font-bold text-[#5a8aaa] uppercase tracking-wider">${filtered[0].filiere_nom}</span>`;
        groupesCont.appendChild(header);

        filtered.forEach(g => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 cursor-pointer transition-colors';
            const checked = selectedGroupes.map(String).includes(String(g.id)) ? 'checked' : '';
            label.innerHTML = `
                <input type="checkbox" name="groupes[]" value="${g.id}" ${checked}
                       class="w-4 h-4 rounded border-slate-300 accent-[#4fa3d1] cursor-pointer">
                <div class="flex flex-col">
                    <span class="text-[13px] font-medium text-slate-800">${g.nom}</span>
                    ${g.niveau ? `<span class="text-[11px] text-slate-400">${g.niveau}</span>` : ''}
                </div>`;
            groupesCont.appendChild(label);
        });
    }

    filiereSelect.addEventListener('change', function () {
        buildUnites(this.value);
        buildGroupes(this.value);
    });

    // Auto-restore: detect filière from current unite
    if (currentUniteId) {
        const currentUnite = allUnites.find(u => String(u.id) === String(currentUniteId));
        if (currentUnite && currentUnite.filiere_id) {
            filiereSelect.value = String(currentUnite.filiere_id);
            buildUnites(currentUnite.filiere_id);
            buildGroupes(currentUnite.filiere_id);
        }
    } else {
        buildUnites('');
        buildGroupes('');
    }
});
</script>

@endsection