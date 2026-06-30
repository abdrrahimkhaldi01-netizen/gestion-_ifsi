@extends('layouts.app')
@section('content')

<div class="max-w-2xl mx-auto">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-[12px] text-slate-400 mb-6">
        <a href="{{ route('directeur.annees_scolaires.index') }}"
           class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Années Scolaires
        </a>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-slate-600 font-medium">Nouvelle Année</span>
    </nav>

    {{-- CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        {{-- Gradient top stripe --}}
        <div class="h-1 w-full" style="background:linear-gradient(90deg,#3b82f6,#6366f1,#8b5cf6)"></div>

        {{-- Card header --}}
        <div class="px-8 pt-7 pb-5 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-[17px] font-bold text-slate-800 leading-tight">Nouvelle Année Scolaire</h1>
                    <p class="text-[12.5px] text-slate-400 mt-0.5">Renseignez les informations de la nouvelle année scolaire</p>
                </div>
            </div>
        </div>

        {{-- ERRORS --}}
        @if($errors->any())
            <div class="mx-8 mt-6 flex gap-2.5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[13px]">
                <svg class="flex-shrink-0 mt-0.5" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <strong class="font-semibold">Veuillez corriger les erreurs suivantes :</strong>
                    <ul class="list-disc pl-4 mt-1 space-y-0.5 text-[12px]">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('directeur.annees_scolaires.store') }}" class="px-8 py-7 space-y-6">
            @csrf

            {{-- NOM --}}
            <div>
                <label for="nom" class="block text-[11.5px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                    Nom de l'année scolaire <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="nom" name="nom"
                           value="{{ old('nom') }}"
                           placeholder="Ex : 2026-2027"
                           maxlength="20"
                           readonly
                           class="w-full px-4 py-2.5 pr-32 rounded-xl border text-[13.5px] text-slate-800 bg-slate-50 transition-all
                                  {{ $errors->has('nom') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}
                                  focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 bg-white px-2 py-1 rounded-lg border border-slate-200">
                        Auto-généré
                    </span>
                </div>
                <p class="flex items-center gap-1 text-[11.5px] text-slate-400 mt-1.5">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Généré automatiquement à partir de la date de début.
                </p>
                @error('nom')
                    <p class="text-[11.5px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- DATES --}}
            <div class="grid grid-cols-2 gap-4">

                {{-- Date début --}}
                <div>
                    <label for="date_debut" class="block text-[11.5px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                        Date de début <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <input type="date" id="date_debut" name="date_debut"
                               value="{{ old('date_debut') }}"
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-[13.5px] text-slate-800 bg-white transition-all
                                      {{ $errors->has('date_debut') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}
                                      focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    @error('date_debut')
                        <p class="text-[11.5px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date fin --}}
                <div>
                    <label for="date_fin" class="block text-[11.5px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                        Date de fin <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <input type="date" id="date_fin" name="date_fin"
                               value="{{ old('date_fin') }}"
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-[13.5px] text-slate-800 bg-white transition-all
                                      {{ $errors->has('date_fin') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}
                                      focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    @error('date_fin')
                        <p class="text-[11.5px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- STATUT --}}
            <div>
                <label class="block text-[11.5px] font-semibold text-slate-500 uppercase tracking-wider mb-2.5">
                    Statut <span class="text-red-400">*</span>
                </label>

                <div class="space-y-2.5" id="statut-group">

                    {{-- Active --}}
                    <label id="label-active"
                           class="flex items-center justify-between gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('statut', 'active') === 'active' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <input type="radio" name="statut" value="active" class="sr-only"
                               {{ old('statut', 'active') === 'active' ? 'checked' : '' }}
                               onchange="updateStatutUI()">
                        <div class="flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4.5 h-4.5 text-emerald-600" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[13px] font-semibold text-slate-800">Active</div>
                                <div class="text-[11.5px] text-slate-400 mt-0.5">Cette année sera l'année courante</div>
                            </div>
                        </div>
                        <div id="check-active"
                             class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all
                                    {{ old('statut', 'active') === 'active' ? 'border-blue-500 bg-blue-500' : 'border-slate-300' }}">
                            <svg class="{{ old('statut', 'active') === 'active' ? '' : 'hidden' }} text-white" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </label>

                    {{-- Archivée --}}
                    <label id="label-archivee"
                           class="flex items-center justify-between gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('statut') === 'archivee' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <input type="radio" name="statut" value="archivee" class="sr-only"
                               {{ old('statut') === 'archivee' ? 'checked' : '' }}
                               onchange="updateStatutUI()">
                        <div class="flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4.5 h-4.5 text-amber-600" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[13px] font-semibold text-slate-800">Archivée</div>
                                <div class="text-[11.5px] text-slate-400 mt-0.5">Année inactive / terminée</div>
                            </div>
                        </div>
                        <div id="check-archivee"
                             class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all
                                    {{ old('statut') === 'archivee' ? 'border-blue-500 bg-blue-500' : 'border-slate-300' }}">
                            <svg class="{{ old('statut') === 'archivee' ? '' : 'hidden' }} text-white" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </label>

                </div>

                {{-- Warning box --}}
                <div id="warning-active"
                     class="{{ old('statut', 'active') === 'active' ? 'flex' : 'hidden' }} items-center gap-2.5 mt-3 px-4 py-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-[12px]">
                    <svg class="flex-shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>L'année active actuelle sera automatiquement archivée.</span>
                </div>

                @error('statut')
                    <p class="text-[11.5px] text-red-500 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <a href="{{ route('directeur.annees_scolaires.index') }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[13px] font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Annuler
                </a>

                <button type="submit"
                        class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-[13px] font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all"
                        style="background:linear-gradient(135deg,#3b82f6,#6366f1)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Créer l'année scolaire
                </button>
            </div>

        </form>
    </div>
</div>

<script>
const statuts = ['active', 'archivee'];

function updateStatutUI() {
    const radios  = document.querySelectorAll('input[name="statut"]');
    const warning = document.getElementById('warning-active');
    let   checked = 'active';

    radios.forEach(r => { if (r.checked) checked = r.value; });

    statuts.forEach(val => {
        const label = document.getElementById('label-' + val);
        const check = document.getElementById('check-' + val);
        const icon  = check.querySelector('svg');
        const isActive = (val === checked);

        // label border/bg
        if (isActive) {
            label.classList.add('border-blue-500', 'bg-blue-50');
            label.classList.remove('border-slate-200', 'hover:border-slate-300');
        } else {
            label.classList.remove('border-blue-500', 'bg-blue-50');
            label.classList.add('border-slate-200', 'hover:border-slate-300');
        }

        // check circle
        if (isActive) {
            check.classList.add('border-blue-500', 'bg-blue-500');
            check.classList.remove('border-slate-300');
            icon.classList.remove('hidden');
        } else {
            check.classList.remove('border-blue-500', 'bg-blue-500');
            check.classList.add('border-slate-300');
            icon.classList.add('hidden');
        }
    });

    // Warning
    if (checked === 'active') {
        warning.classList.remove('hidden');
        warning.classList.add('flex');
    } else {
        warning.classList.add('hidden');
        warning.classList.remove('flex');
    }
}

// Auto-fill nom & date_fin
const dateDebut = document.getElementById('date_debut');
const dateFin   = document.getElementById('date_fin');
const nomInput  = document.getElementById('nom');

dateDebut.addEventListener('change', function () {
    if (!this.value) return;
    const d  = new Date(this.value);
    const y1 = d.getFullYear();
    nomInput.value = `${y1}-${y1 + 1}`;
    // Auto date_fin = un an - 1 jour
    const fin = new Date(d);
    fin.setFullYear(y1 + 1);
    fin.setDate(fin.getDate() - 1);
    dateFin.value = fin.toISOString().split('T')[0];
    dateFin.min = this.value;
});

document.addEventListener('DOMContentLoaded', updateStatutUI);
</script>

@endsection