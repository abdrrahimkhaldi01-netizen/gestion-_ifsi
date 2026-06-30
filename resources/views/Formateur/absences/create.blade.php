@extends('layouts.app')

@section('content')

<style>
.page-header          { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-title           { font-size:20px; font-weight:500; color:var(--color-text-primary); }
.page-subtitle        { font-size:13px; color:var(--color-text-secondary); margin-top:3px; }

.btn-back {
    display:inline-flex; align-items:center; gap:6px; font-size:13px;
    color:var(--color-text-secondary); background:var(--color-background-secondary);
    border:0.5px solid var(--color-border-secondary); border-radius:var(--border-radius-md);
    padding:7px 14px; cursor:pointer; text-decoration:none; transition:background .15s;
}
.btn-back:hover { background:var(--color-background-tertiary); }

.c-card               { background:var(--color-background-primary); border:0.5px solid var(--color-border-tertiary); border-radius:var(--border-radius-lg); overflow:hidden; }
.c-card-header        { padding:16px 20px; border-bottom:0.5px solid var(--color-border-tertiary); display:flex; align-items:center; gap:10px; }
.card-icon            { width:32px; height:32px; background:var(--color-background-info); border-radius:var(--border-radius-md); display:flex; align-items:center; justify-content:center; color:var(--color-text-info); font-size:16px; }
.c-card-title         { font-size:14px; font-weight:500; color:var(--color-text-primary); }
.c-card-body          { padding:20px; }

.ifsi-grid-3          { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin-bottom:14px; }
.ifsi-grid-2          { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; margin-bottom:14px; }
.ifsi-grid-1          { margin-bottom:14px; }

.ifsi-label {
    display:block; font-size:12px; font-weight:500; color:var(--color-text-secondary);
    margin-bottom:5px; text-transform:uppercase; letter-spacing:.04em;
}
.ifsi-input {
    width:100%; background:var(--color-background-secondary);
    border:0.5px solid var(--color-border-secondary); border-radius:var(--border-radius-md);
    padding:8px 12px; font-size:14px; color:var(--color-text-primary);
    font-family:var(--font-sans); outline:none; transition:border-color .15s, box-shadow .15s;
    appearance:none; -webkit-appearance:none;
}
.ifsi-input:focus { border-color:var(--color-border-info); box-shadow:0 0 0 3px rgba(55,138,221,.12); }
.ifsi-select {
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 10px center; padding-right:30px;
}
.border-red { border-color:var(--color-border-danger) !important; }

.section-divider { display:flex; align-items:center; gap:10px; margin:20px 0 14px; }
.section-divider span { font-size:11px; font-weight:500; color:var(--color-text-tertiary); text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }
.section-divider::before, .section-divider::after { content:''; flex:1; height:0.5px; background:var(--color-border-tertiary); }

.type-pills           { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:4px; }
.type-pill {
    display:inline-flex; align-items:center; gap:5px; padding:6px 14px;
    border-radius:999px; border:0.5px solid var(--color-border-secondary);
    font-size:13px; color:var(--color-text-secondary); cursor:pointer;
    transition:all .15s; background:var(--color-background-secondary);
}
.type-pill:hover      { border-color:var(--color-border-info); color:var(--color-text-info); }
.type-pill.active     { background:var(--color-background-info); border-color:var(--color-border-info); color:var(--color-text-info); font-weight:500; }

.badge-area           { min-height:36px; display:flex; flex-wrap:wrap; gap:6px; padding:6px 0; }
.stagiaire-badge {
    display:inline-flex; align-items:center; gap:5px; background:var(--color-background-info);
    border:0.5px solid var(--color-border-info); color:var(--color-text-info);
    font-size:13px; padding:4px 10px; border-radius:999px;
}
.stagiaire-badge button { background:none; border:none; color:var(--color-text-danger); cursor:pointer; font-size:12px; padding:0; line-height:1; display:flex; align-items:center; }
.stagiaire-badge button:hover { opacity:.7; }
.empty-badge          { font-size:13px; color:var(--color-text-tertiary); padding:4px 0; }

.form-actions         { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:0.5px solid var(--color-border-tertiary); }
.btn-secondary        { background:var(--color-background-secondary); border:0.5px solid var(--color-border-secondary); color:var(--color-text-secondary); border-radius:var(--border-radius-md); padding:9px 18px; font-size:14px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-secondary:hover  { background:var(--color-background-tertiary); }
.btn-primary          { background:var(--color-text-info); border:none; color:#fff; border-radius:var(--border-radius-md); padding:9px 18px; font-size:14px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:opacity .15s; }
.btn-primary:hover    { opacity:.88; }

.error-msg            { color:var(--color-text-danger); font-size:11px; margin-top:4px; }
</style>

{{-- Page header --}}
<div class="page-header">
    <div>
        <div class="page-title">Enregistrer une absence</div>
        <div class="page-subtitle">Saisir les informations de l'absence</div>
    </div>
    <a href="{{ route('formateur.absences.index') }}" class="btn-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Retour
    </a>
</div>

{{-- Main card --}}
<div class="c-card">
    <div class="c-card-header">
        <div class="card-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="10" y1="14" x2="10" y2="18"/><line x1="14" y1="14" x2="14" y2="18"/></svg>
        </div>
        <span class="c-card-title">Informations de l'absence</span>
    </div>

    <div class="c-card-body">
        <form action="{{ route('formateur.absences.store') }}" method="POST">
            @csrf

            {{-- Ligne 1 : Date + Séance + Groupe --}}
            <div class="ifsi-grid-3">
                <div>
                    <label class="ifsi-label">Date</label>
                    <input type="date" name="date_absence"
                           value="{{ old('date_absence') }}"
                           class="ifsi-input @error('date_absence') border-red @enderror">
                    @error('date_absence')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ifsi-label">Séance</label>
                    <select name="seance_id" id="seance_id"
                            class="ifsi-input ifsi-select @error('seance_id') border-red @enderror">
                        <option value="">— Choisir une séance —</option>
                        @foreach($seances as $seance)
                            <option value="{{ $seance->id }}"
                                {{ old('seance_id') == $seance->id ? 'selected' : '' }}>
                                {{ $seance->date_seance }} — {{ $seance->groupe->nom ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('seance_id')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ifsi-label">Groupe</label>
                    <select name="groupe_id" id="groupe_select"
                            class="ifsi-input ifsi-select">
                        <option value="">— Choisir un groupe —</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}"
                                {{ old('groupe_id') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Type d'absence (encadrant seulement) --}}
            @if(auth()->user()->role === 'encadrant')
            <div class="section-divider"><span>Type d'absence</span></div>
            <input type="hidden" name="type" id="type_hidden" value="{{ old('type', 'seance') }}">
            <div class="type-pills">
                @foreach(['seance' => ['label' => 'Séance', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z'], 'stage' => ['label' => 'Stage', 'icon' => 'M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z']] as $val => $opt)
                <div class="type-pill {{ old('type', 'seance') === $val ? 'active' : '' }}"
                     data-val="{{ $val }}"
                     onclick="selectType(this)">
                    {{ $opt['label'] }}
                </div>
                @endforeach
            </div>
            @endif

            {{-- Stagiaires --}}
            <div class="section-divider"><span>Stagiaires</span></div>
            <div class="ifsi-grid-2">
                <div>
                    <label class="ifsi-label">Ajouter un stagiaire</label>
                    <select id="stagiaire_select"
                            class="ifsi-input ifsi-select @error('stagiaire_ids') border-red @enderror">
                        <option value="">— Choisir d'abord un groupe —</option>
                    </select>
                    @error('stagiaire_ids')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ifsi-label">
                        Sélectionnés (<span id="stagiaire_count">0</span>)
                    </label>
                    <div class="badge-area" id="badge_area">
                        <span class="empty-badge">Aucun stagiaire sélectionné</span>
                    </div>
                </div>
            </div>

            {{-- Hidden inputs pour stagiaire_ids[] --}}
            <div id="stagiaires_inputs"></div>

            {{-- Motif --}}
            <div class="ifsi-grid-1">
                <label class="ifsi-label">Motif</label>
                <textarea name="motif" rows="3"
                          class="ifsi-input"
                          placeholder="Motif de l'absence (optionnel)...">{{ old('motif') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('formateur.absences.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Enregistrer
                </button>
            </div>

        </form>
    </div>
</div>

<script>
/* ── Type pills ── */
function selectType(el) {
    document.querySelectorAll('.type-pill').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('type_hidden').value = el.dataset.val;
}

/* ── Stagiaires ── */
let selectedStagiaires = [];

const badgeArea        = document.getElementById('badge_area');
const stagiairesInputs = document.getElementById('stagiaires_inputs');
const countEl          = document.getElementById('stagiaire_count');

function renderBadges() {
    countEl.textContent    = selectedStagiaires.length;
    badgeArea.innerHTML    = '';
    stagiairesInputs.innerHTML = '';

    if (selectedStagiaires.length === 0) {
        badgeArea.innerHTML = '<span class="empty-badge">Aucun stagiaire sélectionné</span>';
        return;
    }

    selectedStagiaires.forEach((s, i) => {
        const badge = document.createElement('span');
        badge.className = 'stagiaire-badge';
        badge.innerHTML = `
            ${s.nom}
            <button type="button" onclick="removeStagiaire(${i})" aria-label="Retirer ${s.nom}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        `;
        badgeArea.appendChild(badge);

        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'stagiaire_ids[]';
        input.value = s.id;
        stagiairesInputs.appendChild(input);
    });
}

function removeStagiaire(index) {
    selectedStagiaires.splice(index, 1);
    renderBadges();
}

function attachStagiaireListener() {
    const sel = document.getElementById('stagiaire_select');
    sel.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) return;
        if (selectedStagiaires.find(s => s.id == opt.value)) { this.selectedIndex = 0; return; }
        selectedStagiaires.push({ id: opt.value, nom: opt.dataset.nom });
        renderBadges();
        this.selectedIndex = 0;
    });
}

attachStagiaireListener();

document.getElementById('groupe_select').addEventListener('change', function () {
    const sel = document.getElementById('stagiaire_select');
    sel.innerHTML = '<option value="">Chargement...</option>';

    if (!this.value) {
        sel.innerHTML = "<option value=''>— Choisir d'abord un groupe —</option>";
        return;
    }

    fetch(`/formateur/stagiaires-by-groupe/${this.value}`)
        .then(r => r.json())
        .then(stagiaires => {
            if (!stagiaires.length) {
                sel.innerHTML = '<option value="">Aucun stagiaire</option>';
                return;
            }
            sel.innerHTML = '<option value="">— Choisir un stagiaire —</option>';
            stagiaires.forEach(s => {
                const o = document.createElement('option');
                o.value        = s.id;
                o.dataset.nom  = `${s.nom} ${s.prenom}`;
                o.textContent  = `${s.nom} ${s.prenom}`;
                sel.appendChild(o);
            });
            attachStagiaireListener();
        });
});
</script>

@endsection