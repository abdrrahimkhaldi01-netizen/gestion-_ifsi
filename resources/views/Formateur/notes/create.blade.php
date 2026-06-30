@extends('layouts.app')

@section('title', 'Saisie des notes')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8 flex flex-col gap-6">

    {{-- ── Page Header ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Saisie des notes</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                @if(request('type') === 'exam') Examens par stagiaire
                @elseif(request('type') === 'pfe') PFE par stagiaire
                @else Contrôles continus par stagiaire
                @endif
            </p>
        </div>
        <a href="{{ route('formateur.notes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600
                  border border-slate-200 rounded-lg bg-white hover:bg-slate-50
                  hover:border-slate-300 transition-colors">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Retour à la liste
        </a>
    </div>

    {{-- ── Flash success ── --}}
    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200
                text-emerald-700 text-sm font-medium rounded-xl">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Erreur client-side (note > 20) — affichée AVANT form reset ── --}}
    <div id="clientErrors" class="hidden flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200
                text-red-700 text-sm rounded-xl">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="shrink-0 mt-0.5">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div>
            <p class="font-semibold mb-1">Veuillez corriger les erreurs suivantes :</p>
            <ul id="clientErrorsList" class="list-disc list-inside space-y-0.5"></ul>
        </div>
    </div>

    <form action="{{ request('type') === 'pfe' ? route('formateur.notes.pfe.store') : route('formateur.notes.store') }}"
          method="POST" id="notesForm" novalidate>
        @csrf
        <input type="hidden" name="type" value="{{ request('type', 'cc') }}">

        {{-- ── Step 1 : Contexte ── --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                             bg-blue-600 text-white text-xs font-bold shrink-0">1</span>
                <h2 class="text-base font-semibold text-slate-800">Sélectionner le contexte</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Filière</label>
                        <select id="select_filiere" class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer transition-colors">
                            <option value="">-- Choisir --</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere['id'] }}">{{ $filiere['nom'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Groupe</label>
                        <select id="select_groupe" disabled class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed cursor-pointer transition-colors">
                            <option value="">-- Choisir filière --</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5" id="uniteWrapper" @if(request('type') === 'pfe') style="display:none" @endif>
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Unité</label>
                        <select id="select_unite" disabled class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed cursor-pointer transition-colors">
                            <option value="">-- Choisir groupe --</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5" id="moduleWrapper" @if(in_array(request('type'), ['exam', 'pfe'])) style="display:none" @endif>
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Module</label>
                        <select id="select_module" disabled class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed cursor-pointer transition-colors">
                            <option value="">-- Choisir unité --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Step 2 : Tables notes ── --}}
        <div id="notesSection" class="hidden flex flex-col gap-6 mt-6">

            {{-- CC --}}
            <div id="ccSection" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-cyan-600 text-white text-xs font-bold shrink-0">CC</span>
                    <h2 class="text-base font-semibold text-slate-800 flex-1">Contrôles continus</h2>
                    <span id="ccCount" class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr id="ccHeaderRow" class="bg-slate-50 border-b-2 border-slate-200">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Stagiaire</th>
                            </tr>
                        </thead>
                        <tbody id="ccTableBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>

            {{-- Exam --}}
            <div id="examSection" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-violet-600 text-white text-xs font-bold shrink-0">EX</span>
                    <h2 class="text-base font-semibold text-slate-800 flex-1">Examens</h2>
                    <span id="examCount" class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr id="examHeaderRow" class="bg-slate-50 border-b-2 border-slate-200">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Stagiaire</th>
                            </tr>
                        </thead>
                        <tbody id="examTableBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>

            {{-- PFE --}}
            <div id="pfeSection" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-500 text-white text-xs font-bold shrink-0">PF</span>
                    <h2 class="text-base font-semibold text-slate-800 flex-1">PFE</h2>
                    <span id="pfeCount" class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-200">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Stagiaire</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Note PFE (/20)</th>
                            </tr>
                        </thead>
                        <tbody id="pfeTableBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700
                               text-white text-sm font-semibold rounded-xl shadow-md
                               hover:shadow-lg transition-all active:scale-95">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    {{ request('type') === 'pfe' ? 'Enregistrer les notes PFE' : 'Enregistrer les notes' }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const APP_DATA = {
        groupes: @json($groupesJs),
        modules: @json($modulesJs),
    };
    const PAGE_TYPE      = '{{ request('type', 'cc') }}';
    const EXISTING_NOTES = @json($existingNotesJson);
</script>

<script>
(function () {
    'use strict';

    const { groupes, modules } = APP_DATA;

    const selFiliere   = document.getElementById('select_filiere');
    const selGroupe    = document.getElementById('select_groupe');
    const selUnite     = document.getElementById('select_unite');
    const selModule    = document.getElementById('select_module');
    const notesSection = document.getElementById('notesSection');
    const clientErrors = document.getElementById('clientErrors');
    const errorsList   = document.getElementById('clientErrorsList');

    /* ══════════════════════════════════════════════════
       VALIDATION CLIENT-SIDE — bloque le submit si note invalide
       Sans reset des autres inputs corrects
    ══════════════════════════════════════════════════ */
    document.getElementById('notesForm').addEventListener('submit', function(e) {
        const errors = [];
        let firstBadInput = null;

        // Réinitialiser les styles d'erreur précédents
        this.querySelectorAll('.input-error').forEach(el => {
            el.classList.remove('input-error', '!border-red-400', '!bg-red-50', '!shadow-[0_0_0_3px_rgba(239,68,68,0.12)]');
        });

        // Vérifier tous les inputs note visibles et non-disabled
        this.querySelectorAll('input[type="text"][name$="[note]"]:not([disabled])').forEach(function(input) {
            const raw = input.value.trim().replace(',', '.');
            if (raw === '' || raw === '—') return; // vide = skip

            const val = parseFloat(raw);
            const nameAttr = input.getAttribute('name');

            // Trouver le nom du stagiaire dans la même ligne
            const row = input.closest('tr');
            const stagName = row ? (row.querySelector('td:first-child')?.textContent?.trim() ?? '') : '';

            if (isNaN(val)) {
                input.classList.add('input-error', '!border-red-400', '!bg-red-50', '!shadow-[0_0_0_3px_rgba(239,68,68,0.12)]');
                errors.push(`${stagName || nameAttr} — valeur invalide "${input.value}"`);
                if (!firstBadInput) firstBadInput = input;
            } else if (val < 0) {
                input.classList.add('input-error', '!border-red-400', '!bg-red-50', '!shadow-[0_0_0_3px_rgba(239,68,68,0.12)]');
                errors.push(`${stagName} — la note ne peut pas être négative (${val})`);
                if (!firstBadInput) firstBadInput = input;
            } else if (val > 20) {
                input.classList.add('input-error', '!border-red-400', '!bg-red-50', '!shadow-[0_0_0_3px_rgba(239,68,68,0.12)]');
                errors.push(`${stagName} — la note ${val} dépasse 20`);
                if (!firstBadInput) firstBadInput = input;
            }
        });

        if (errors.length > 0) {
            e.preventDefault(); // bloquer l'envoi
            errorsList.innerHTML = errors.map(err => `<li>${err}</li>`).join('');
            clientErrors.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (firstBadInput) {
                setTimeout(() => firstBadInput.focus(), 300);
            }
            return false;
        }

        // Pas d'erreurs — cacher le bloc erreur et soumettre
        clientErrors.classList.add('hidden');
    });

    /* ══════════════════════════════════════════════════
       VALIDATION TEMPS-RÉEL sur chaque input
       Affiche rouge immédiatement si val > 20
    ══════════════════════════════════════════════════ */
    function attachRealTimeValidation(input) {
        input.addEventListener('input', function() {
            const raw = this.value.trim().replace(',', '.');
            if (raw === '' || raw === '—') {
                this.classList.remove('input-error', '!border-red-400', '!bg-red-50', '!shadow-[0_0_0_3px_rgba(239,68,68,0.12)]');
                return;
            }
            const val = parseFloat(raw);
            if (isNaN(val) || val < 0 || val > 20) {
                this.classList.add('input-error', '!border-red-400', '!bg-red-50', '!shadow-[0_0_0_3px_rgba(239,68,68,0.12)]');
            } else {
                this.classList.remove('input-error', '!border-red-400', '!bg-red-50', '!shadow-[0_0_0_3px_rgba(239,68,68,0.12)]');
            }
        });

        // Bloquer la frappe au-delà de 20 avec tooltip
        input.addEventListener('blur', function() {
            const raw = this.value.trim().replace(',', '.');
            if (raw === '') return;
            const val = parseFloat(raw);
            if (!isNaN(val) && val > 20) {
                this.title = '⚠️ La note ne peut pas dépasser 20';
            } else {
                this.title = '';
            }
        });
    }

    /* ══════════════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════════════ */
    function resetSelect(sel, placeholder) {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        sel.disabled = true;
    }

    function populateSelect(sel, items, labelKey) {
        sel.innerHTML = `<option value="">-- Choisir --</option>`;
        items.forEach(item => {
            const o = document.createElement('option');
            o.value = item.id;
            o.textContent = item[labelKey];
            sel.appendChild(o);
        });
        sel.disabled = items.length === 0;
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str ?? ''));
        return d.innerHTML;
    }

    function hideSections() {
        document.getElementById('ccSection').classList.add('hidden');
        document.getElementById('examSection').classList.add('hidden');
        document.getElementById('pfeSection').classList.add('hidden');
        notesSection.classList.add('hidden');
        clientErrors.classList.add('hidden');
    }

    /* ══════════════════════════════════════════════════
       INPUT CLASSES
    ══════════════════════════════════════════════════ */
    const CC_INPUT_BASE   = `w-20 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150`;
    const EXAM_INPUT_BASE = `w-20 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-slate-800 outline-none focus:border-[#7c3aed] focus:bg-white focus:shadow-[0_0_0_3px_rgba(124,58,237,0.12)] transition-all duration-150`;
    const PFE_INPUT_BASE  = `w-24 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-slate-800 outline-none focus:border-[#f97316] focus:bg-white focus:shadow-[0_0_0_3px_rgba(249,115,22,0.12)] transition-all duration-150`;
    const LOCKED_CLASS    = `w-20 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed`;

    /* ── Crée un input note avec validation intégrée ── */
    function makeNoteInput(cls, name, value, locked, lockedTitle) {
        const input = document.createElement('input');
        input.type        = 'text';
        input.inputMode   = 'decimal';
        input.className   = locked ? LOCKED_CLASS : cls;
        input.name        = name;
        input.placeholder = '—';
        input.value       = value;
        if (locked) {
            input.disabled = true;
            input.title    = lockedTitle ?? 'Note validée — non modifiable';
        }
        if (!locked) attachRealTimeValidation(input);
        return input;
    }

    /* ══════════════════════════════════════════════════
       LISTENERS SELECTS
    ══════════════════════════════════════════════════ */
    selFiliere.addEventListener('change', function () {
        resetSelect(selGroupe, '-- Choisir filière --');
        resetSelect(selUnite,  '-- Choisir groupe --');
        resetSelect(selModule, '-- Choisir unité --');
        hideSections();
        if (!this.value) return;
        populateSelect(
            selGroupe,
            groupes.filter(g => String(g.filiere_id) === this.value && (PAGE_TYPE !== 'pfe' || g.is_pfe_group)),
            'nom'
        );
    });

    selGroupe.addEventListener('change', function () {
        resetSelect(selUnite,  '-- Choisir groupe --');
        resetSelect(selModule, '-- Choisir unité --');
        hideSections();
        if (!this.value) return;

        if (PAGE_TYPE === 'pfe') {
            const group = groupes.find(g => String(g.id) === this.value);
            if (!group) return;
            buildPfeTable(group.stagiaires);
            notesSection.classList.remove('hidden');
            return;
        }

        const groupeId = parseInt(this.value);
        const uniteMap = {};
        modules
            .filter(m => m.groupes && m.groupes.includes(groupeId))
            .forEach(m => {
                if (m.unite_id && !uniteMap[m.unite_id])
                    uniteMap[m.unite_id] = { id: m.unite_id, nom: m.unite_nom };
            });
        populateSelect(selUnite, Object.values(uniteMap), 'nom');
    });

    selUnite.addEventListener('change', function () {
        resetSelect(selModule, '-- Choisir unité --');
        hideSections();
        if (!this.value) return;

        const groupeId     = parseInt(selGroupe.value);
        const uniteModules = modules.filter(m =>
            String(m.unite_id) === this.value && m.groupes && m.groupes.includes(groupeId)
        );

        if (PAGE_TYPE === 'exam') {
            const group = groupes.find(g => String(g.id) === selGroupe.value);
            if (!group) return;
            const allExams = [];
            uniteModules.forEach(m => {
                (m.unit_exams ?? []).forEach(ex => {
                    if (ex.type !== 'cc' && !allExams.find(e => e.id === ex.id)) allExams.push(ex);
                });
            });
            buildExamTable({ unit_exams: allExams }, group.stagiaires);
            document.getElementById('ccSection').classList.add('hidden');
            notesSection.classList.remove('hidden');
        } else {
            populateSelect(selModule, uniteModules, 'nom');
        }
    });

    selModule.addEventListener('change', function () {
        hideSections();
        if (!this.value) return;
        const mod   = modules.find(m => String(m.id) === this.value);
        const group = groupes.find(g => String(g.id) === selGroupe.value);
        if (!mod || !group) return;
        buildCCTable(mod, group.stagiaires);
        document.getElementById('examSection').classList.add('hidden');
        notesSection.classList.remove('hidden');
    });

    /* ══════════════════════════════════════════════════
       BUILD CC TABLE
    ══════════════════════════════════════════════════ */
    function buildCCTable(mod, stagiaires) {
        const section = document.getElementById('ccSection');
        const evals   = mod.evaluations ?? [];
        if (!evals.length) { section.classList.add('hidden'); return; }

        document.getElementById('ccCount').textContent =
            `${evals.length} évaluation${evals.length > 1 ? 's' : ''}`;

        // Header
        const headerRow = document.getElementById('ccHeaderRow');
        headerRow.querySelectorAll('.eval-th').forEach(el => el.remove());
        evals.forEach(e => {
            const th = document.createElement('th');
            th.className   = 'eval-th text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap';
            th.textContent = e.nom;
            headerRow.appendChild(th);
        });

        // Body
        const tbody = document.getElementById('ccTableBody');
        tbody.innerHTML = '';

        stagiaires.forEach((s, si) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';

            const nameTd = document.createElement('td');
            nameTd.className   = 'px-5 py-3 font-medium text-slate-800 whitespace-nowrap';
            nameTd.textContent = s.nom;
            tr.appendChild(nameTd);

            evals.forEach((e, ei) => {
                const idx      = si * evals.length + ei;
                const existing = EXISTING_NOTES.find(n =>
                    n.stagiaire_id == s.id && n.evaluation_id == e.id
                );
                const val    = existing ? existing.note : '';
                const locked = existing?.statut === 'validee';

                const td = document.createElement('td');
                td.className = 'px-5 py-3';

                // Input note
                const noteInput = makeNoteInput(CC_INPUT_BASE, `ccs[${idx}][note]`, val, locked);
                td.appendChild(noteInput);

                // Hiddens
                const hidStag = document.createElement('input');
                hidStag.type  = 'hidden';
                hidStag.name  = `ccs[${idx}][stagiaire_id]`;
                hidStag.value = s.id;
                td.appendChild(hidStag);

                const hidEval = document.createElement('input');
                hidEval.type  = 'hidden';
                hidEval.name  = `ccs[${idx}][evaluation_id]`;
                hidEval.value = e.id;
                td.appendChild(hidEval);

                tr.appendChild(td);
            });

            tbody.appendChild(tr);
        });

        section.classList.remove('hidden');
    }

    /* ══════════════════════════════════════════════════
       BUILD EXAM TABLE
    ══════════════════════════════════════════════════ */
    function buildExamTable(mod, stagiaires) {
        const section = document.getElementById('examSection');
        const exams   = (mod.unit_exams ?? []).filter(ex => ex.type !== 'cc');
        if (!exams.length) { section.classList.add('hidden'); return; }

        document.getElementById('examCount').textContent =
            `${exams.length} examen${exams.length > 1 ? 's' : ''}`;

        const examLabels = { 'theorique': 'Exam Théorique', 'pratique': 'Exam Pratique' };

        const headerRow = document.getElementById('examHeaderRow');
        headerRow.querySelectorAll('.eval-th').forEach(el => el.remove());
        exams.forEach(ex => {
            const th = document.createElement('th');
            th.className   = 'eval-th text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap';
            th.textContent = examLabels[ex.type] ?? ex.nom;
            headerRow.appendChild(th);
        });

        const tbody = document.getElementById('examTableBody');
        tbody.innerHTML = '';

        stagiaires.forEach((s, si) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';

            const nameTd = document.createElement('td');
            nameTd.className   = 'px-5 py-3 font-medium text-slate-800 whitespace-nowrap';
            nameTd.textContent = s.nom;
            tr.appendChild(nameTd);

            exams.forEach((ex, ei) => {
                const idx      = si * exams.length + ei;
                const existing = EXISTING_NOTES.find(n =>
                    n.stagiaire_id == s.id && n.unit_exam_id == ex.id
                );
                const val    = existing ? existing.note : '';
                const locked = existing?.statut === 'validee';

                const td = document.createElement('td');
                td.className = 'px-5 py-3';

                const noteInput = makeNoteInput(EXAM_INPUT_BASE, `exams[${idx}][note]`, val, locked);
                td.appendChild(noteInput);

                const hidStag = document.createElement('input');
                hidStag.type  = 'hidden';
                hidStag.name  = `exams[${idx}][stagiaire_id]`;
                hidStag.value = s.id;
                td.appendChild(hidStag);

                const hidExam = document.createElement('input');
                hidExam.type  = 'hidden';
                hidExam.name  = `exams[${idx}][unit_exam_id]`;
                hidExam.value = ex.id;
                td.appendChild(hidExam);

                tr.appendChild(td);
            });

            tbody.appendChild(tr);
        });

        section.classList.remove('hidden');
    }

    /* ══════════════════════════════════════════════════
       BUILD PFE TABLE
    ══════════════════════════════════════════════════ */
    function buildPfeTable(stagiaires) {
        const section = document.getElementById('pfeSection');

        document.getElementById('pfeCount').textContent =
            `${stagiaires.length} stagiaire${stagiaires.length > 1 ? 's' : ''}`;

        const tbody = document.getElementById('pfeTableBody');
        tbody.innerHTML = '';

        stagiaires.forEach((s, i) => {
            const existing = EXISTING_NOTES.find(n =>
                n.stagiaire_id == s.id && n.unit_exam_id == null && n.evaluation_id == null
            );
            const val    = existing ? existing.note : '';
            const locked = existing?.statut === 'validee';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';

            const nameTd = document.createElement('td');
            nameTd.className   = 'px-5 py-3 font-medium text-slate-800 whitespace-nowrap';
            nameTd.textContent = s.nom;
            tr.appendChild(nameTd);

            const td = document.createElement('td');
            td.className = 'px-5 py-3';

            const noteInput = makeNoteInput(PFE_INPUT_BASE, `pfes[${i}][note]`, val, locked);
            td.appendChild(noteInput);

            const hidStag = document.createElement('input');
            hidStag.type  = 'hidden';
            hidStag.name  = `pfes[${i}][stagiaire_id]`;
            hidStag.value = s.id;
            td.appendChild(hidStag);

            tr.appendChild(nameTd);
            tr.appendChild(td);
            tbody.appendChild(tr);
        });

        section.classList.remove('hidden');
    }

})();
</script>

@endsection