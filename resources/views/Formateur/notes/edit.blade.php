@extends('layouts.app')

@section('title', 'Modifier les notes')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8 flex flex-col gap-6">

    {{-- ── Page Header ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Modifier les notes</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                @if(request('type') === 'exam')
                    Examens par stagiaire
                @elseif(request('type') === 'pfe')
                    PFE par stagiaire
                @else
                    Contrôles continus par stagiaire
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

    {{-- ── Flash ── --}}
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

    <form action="{{ request('type') === 'pfe' ? route('formateur.notes.pfe.store') : route('formateur.notes.store') }}"
          method="POST" id="notesForm">
        @csrf
        <input type="hidden" name="type" value="{{ request('type', 'cc') }}">

        {{-- ── Step 1 : Filters ── --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                             bg-blue-600 text-white text-xs font-bold shrink-0">1</span>
                <h2 class="text-base font-semibold text-slate-800">Sélectionner le contexte</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                    {{-- Filière --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Filière</label>
                        <select id="select_filiere"
                                class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer transition-colors">
                            <option value="">-- Choisir --</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere['id'] }}"
                                    {{ $preFiliere == $filiere['id'] ? 'selected' : '' }}>
                                    {{ $filiere['nom'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Groupe --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Groupe</label>
                        <select id="select_groupe" disabled
                                class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed cursor-pointer transition-colors">
                            <option value="">-- Choisir filière --</option>
                        </select>
                    </div>

                    {{-- Unité --}}
                    <div class="flex flex-col gap-1.5" id="uniteWrapper"
                         @if(request('type') === 'pfe') style="display:none" @endif>
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Unité</label>
                        <select id="select_unite" disabled
                                class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed cursor-pointer transition-colors">
                            <option value="">-- Choisir groupe --</option>
                        </select>
                    </div>

                    {{-- Module --}}
                    <div class="flex flex-col gap-1.5" id="moduleWrapper"
                         @if(in_array(request('type'), ['exam', 'pfe'])) style="display:none" @endif>
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Module</label>
                        <select id="select_module" disabled
                                class="w-full px-3 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-lg appearance-none
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed cursor-pointer transition-colors">
                            <option value="">-- Choisir unité --</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Step 2 : Notes tables ── --}}
        <div id="notesSection" class="hidden flex flex-col gap-6 mt-6">

            {{-- CC table --}}
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

            {{-- Exam table --}}
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

            {{-- PFE table --}}
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
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700
                               text-white text-sm font-semibold rounded-xl shadow-md
                               hover:shadow-lg transition-all active:scale-95">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    {{ request('type') === 'pfe' ? 'Mettre à jour les notes PFE' : 'Mettre à jour les notes' }}
                </button>
            </div>

        </div>
    </form>
</div>

{{-- JS data --}}
<script>
    const APP_DATA = {
        groupes: @json($groupesJs),
        modules: @json($modulesJs),
    };
    const PAGE_TYPE      = '{{ request('type', 'cc') }}';
    const EXISTING_NOTES = @json($existingNotesJson);

    // Pre-selection data (restore dropdowns)
    const PRE_FILIERE = '{{ $preFiliere ?? '' }}';
    const PRE_GROUPE  = '{{ $preGroupe ?? '' }}';
    const PRE_UNITE   = '{{ $preUnite ?? '' }}';
    const PRE_MODULE  = '{{ $preModule ?? '' }}';
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

    function resetSelect(sel, placeholder) {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        sel.disabled = true;
    }

    function populateSelect(sel, items, labelKey, preValue) {
        sel.innerHTML = `<option value="">-- Choisir --</option>`;
        items.forEach(item => {
            const o = document.createElement('option');
            o.value = item.id;
            o.textContent = item[labelKey];
            if (preValue && String(item.id) === String(preValue)) o.selected = true;
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
    }

    /* ── Filière → Groupe ── */
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

    /* ── Groupe → Unité ── */
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

    /* ── Unité → Module ou Exam ── */
    selUnite.addEventListener('change', function () {
        resetSelect(selModule, '-- Choisir unité --');
        hideSections();
        if (!this.value) return;

        const groupeId     = parseInt(selGroupe.value);
        const uniteModules = modules.filter(m =>
            String(m.unite_id) === this.value &&
            m.groupes && m.groupes.includes(groupeId)
        );

        if (PAGE_TYPE === 'exam') {
            const group = groupes.find(g => String(g.id) === selGroupe.value);
            if (!group) return;

            const allExams = [];
            uniteModules.forEach(m => {
                (m.unit_exams ?? []).forEach(ex => {
                    if (ex.type !== 'cc' && !allExams.find(e => e.id === ex.id)) {
                        allExams.push(ex);
                    }
                });
            });

            buildExamTable({ unit_exams: allExams }, group.stagiaires);
            document.getElementById('ccSection').classList.add('hidden');
            notesSection.classList.remove('hidden');
        } else {
            populateSelect(selModule, uniteModules, 'nom');
        }
    });

    /* ── Module → CC table ── */
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

    /* ── Input classes ── */
    const CC_INPUT_CLASS   = `w-20 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-slate-800 outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150`;
    const EXAM_INPUT_CLASS = `w-20 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-slate-800 outline-none focus:border-[#7c3aed] focus:bg-white focus:shadow-[0_0_0_3px_rgba(124,58,237,0.12)] transition-all duration-150`;
    const PFE_INPUT_CLASS  = `w-24 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-slate-800 outline-none focus:border-[#f97316] focus:bg-white focus:shadow-[0_0_0_3px_rgba(249,115,22,0.12)] transition-all duration-150`;
    const LOCKED_CLASS     = `w-20 px-2 py-1.5 text-sm text-center rounded-lg border-[1.5px] border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed`;

    /* ── CC table ── */
    function buildCCTable(mod, stagiaires) {
        const section = document.getElementById('ccSection');
        const evals   = mod.evaluations ?? [];
        if (!evals.length) { section.classList.add('hidden'); return; }

        document.getElementById('ccCount').textContent =
            `${evals.length} évaluation${evals.length > 1 ? 's' : ''}`;

        const headerRow = document.getElementById('ccHeaderRow');
        headerRow.querySelectorAll('.eval-th').forEach(el => el.remove());
        evals.forEach(e => {
            const th = document.createElement('th');
            th.className = 'eval-th text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap';
            th.textContent = e.nom;
            headerRow.appendChild(th);
        });

        document.getElementById('ccTableBody').innerHTML =
            stagiaires.map((s, si) =>
                `<tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3 font-medium text-slate-800 whitespace-nowrap">${escHtml(s.nom)}</td>
                    ${evals.map((e, ei) => {
                        const idx      = si * evals.length + ei;
                        const existing = EXISTING_NOTES.find(n =>
                            n.stagiaire_id == s.id && n.evaluation_id == e.id
                        );
                        const val    = existing ? existing.note : '';
                        const locked = existing?.statut === 'validee';
                        return `<td class="px-5 py-3">
                            <input type="text" inputmode="decimal"
                                   class="${locked ? LOCKED_CLASS : CC_INPUT_CLASS}"
                                   name="ccs[${idx}][note]"
                                   placeholder="—"
                                   value="${val}"
                                   ${locked ? 'disabled title="Note validée — non modifiable"' : ''}>
                            <input type="hidden" name="ccs[${idx}][stagiaire_id]"  value="${s.id}">
                            <input type="hidden" name="ccs[${idx}][evaluation_id]" value="${e.id}">
                        </td>`;
                    }).join('')}
                </tr>`
            ).join('');

        section.classList.remove('hidden');
    }

    /* ── Exam table ── */
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
            th.className = 'eval-th text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap';
            th.textContent = examLabels[ex.type] ?? ex.nom;
            headerRow.appendChild(th);
        });

        document.getElementById('examTableBody').innerHTML =
            stagiaires.map((s, si) =>
                `<tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3 font-medium text-slate-800 whitespace-nowrap">${escHtml(s.nom)}</td>
                    ${exams.map((ex, ei) => {
                        const idx      = si * exams.length + ei;
                        const existing = EXISTING_NOTES.find(n =>
                            n.stagiaire_id == s.id && n.unit_exam_id == ex.id
                        );
                        const val    = existing ? existing.note : '';
                        const locked = existing?.statut === 'validee';
                        return `<td class="px-5 py-3">
                            <input type="text" inputmode="decimal"
                                   class="${locked ? LOCKED_CLASS : EXAM_INPUT_CLASS}"
                                   name="exams[${idx}][note]"
                                   placeholder="—"
                                   value="${val}"
                                   ${locked ? 'disabled title="Note validée — non modifiable"' : ''}>
                            <input type="hidden" name="exams[${idx}][stagiaire_id]"  value="${s.id}">
                            <input type="hidden" name="exams[${idx}][unit_exam_id]"  value="${ex.id}">
                        </td>`;
                    }).join('')}
                </tr>`
            ).join('');

        section.classList.remove('hidden');
    }

    /* ── PFE table ── */
    function buildPfeTable(stagiaires) {
        const section = document.getElementById('pfeSection');

        document.getElementById('pfeCount').textContent =
            `${stagiaires.length} stagiaire${stagiaires.length > 1 ? 's' : ''}`;

        document.getElementById('pfeTableBody').innerHTML =
            stagiaires.map((s, i) => {
                const existing = EXISTING_NOTES.find(n =>
                    n.stagiaire_id == s.id && n.unit_exam_id == null && n.evaluation_id == null
                );
                const val    = existing ? existing.note : '';
                const locked = existing?.statut === 'validee';
                return `<tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3 font-medium text-slate-800 whitespace-nowrap">${escHtml(s.nom)}</td>
                    <td class="px-5 py-3">
                        <input type="text" inputmode="decimal"
                               class="${locked ? LOCKED_CLASS : PFE_INPUT_CLASS}"
                               name="pfes[${i}][note]"
                               placeholder="—"
                               value="${val}"
                               ${locked ? 'disabled title="Note validée — non modifiable"' : ''}>
                        <input type="hidden" name="pfes[${i}][stagiaire_id]" value="${s.id}">
                    </td>
                </tr>`;
            }).join('');

        section.classList.remove('hidden');
    }

    /* ── Auto-restore dropdowns ── */
    (function restore() {
        if (!PRE_FILIERE) return;

        // Filière already selected in HTML (selected attribute)
        // Populate groupes
        populateSelect(
            selGroupe,
            groupes.filter(g => String(g.filiere_id) === PRE_FILIERE && (PAGE_TYPE !== 'pfe' || g.is_pfe_group)),
            'nom',
            PRE_GROUPE
        );

        if (!PRE_GROUPE) return;

        if (PAGE_TYPE === 'pfe') {
            const group = groupes.find(g => String(g.id) === PRE_GROUPE);
            if (!group) return;
            buildPfeTable(group.stagiaires);
            notesSection.classList.remove('hidden');
            return;
        }

        // Populate unités
        const groupeId = parseInt(PRE_GROUPE);
        const uniteMap = {};
        modules
            .filter(m => m.groupes && m.groupes.includes(groupeId))
            .forEach(m => {
                if (m.unite_id && !uniteMap[m.unite_id])
                    uniteMap[m.unite_id] = { id: m.unite_id, nom: m.unite_nom };
            });
        populateSelect(selUnite, Object.values(uniteMap), 'nom', PRE_UNITE);

        if (!PRE_UNITE) return;

        if (PAGE_TYPE === 'exam') {
            const group = groupes.find(g => String(g.id) === PRE_GROUPE);
            if (!group) return;

            const uniteModules = modules.filter(m =>
                String(m.unite_id) === PRE_UNITE && m.groupes && m.groupes.includes(groupeId)
            );
            const allExams = [];
            uniteModules.forEach(m => {
                (m.unit_exams ?? []).forEach(ex => {
                    if (ex.type !== 'cc' && !allExams.find(e => e.id === ex.id))
                        allExams.push(ex);
                });
            });
            buildExamTable({ unit_exams: allExams }, group.stagiaires);
            document.getElementById('ccSection').classList.add('hidden');
            notesSection.classList.remove('hidden');
            return;
        }

        // Populate modules
        const uniteModules = modules.filter(m =>
            String(m.unite_id) === PRE_UNITE && m.groupes && m.groupes.includes(groupeId)
        );
        populateSelect(selModule, uniteModules, 'nom', PRE_MODULE);

        if (!PRE_MODULE) return;

        const mod   = modules.find(m => String(m.id) === PRE_MODULE);
        const group = groupes.find(g => String(g.id) === PRE_GROUPE);
        if (!mod || !group) return;

        buildCCTable(mod, group.stagiaires);
        document.getElementById('examSection').classList.add('hidden');
        notesSection.classList.remove('hidden');
    })();

})();
</script>

@endsection