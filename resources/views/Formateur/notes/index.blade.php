@extends('layouts.app')

@section('content')
<div class="nts-wrap">

{{-- ══════════════════════ TOPBAR ══════════════════════ --}}
<div class="nts-topbar">
    <div class="nts-topbar-left">
        <div class="nts-icon-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </div>
        <div>
            <h1>Gestion des Notes</h1>
            <p>Suivi des évaluations — contrôles, examens &amp; résultats</p>
        </div>
    </div>
    <a id="btn-ajouter" href="{{ route('formateur.notes.create', ['type' => 'cc']) }}" class="nts-btn-add">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span id="btn-ajouter-label">Ajouter une note</span>
    </a>
</div>

{{-- ══════════════════════ ALERTS ══════════════════════ --}}
@foreach(['success'=>['alert-success','M22 11.08V12a10 10 0 1 1-5.93-9.14 M22 4L12 14.01l-3-2.99'],'error'=>['alert-error','M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z M12 9v4 M12 17h.01'],'warning'=>['alert-warning','M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z M12 9v4 M12 17h.01']] as $key=>[$cls,$icon])
    @if(session($key))
        <div class="nts-alert {{ $cls }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">@foreach(explode(' M',$icon) as $i=>$d)<path d="{{ ($i>0?'M':'').$d }}"/>@endforeach</svg>
            {{ session($key) }}
        </div>
    @endif
@endforeach

{{-- ══════════════════════ TABS ══════════════════════ --}}
<div class="nts-tabs">
    <button onclick="switchTab('cc')" id="tab-cc" class="nts-tab tab-active"><span class="tab-pill pill-cc">CC</span>Contrôles Continus</button>
    <button onclick="switchTab('exam')" id="tab-exam" class="nts-tab"><span class="tab-pill pill-exam">EX</span>Examens</button>
    <button onclick="switchTab('pfe')" id="tab-pfe" class="nts-tab"><span class="tab-pill pill-pfe">PF</span>PFE</button>
    <button onclick="switchTab('resultats')" id="tab-resultats" class="nts-tab"><span class="tab-pill pill-res">T</span>Totales</button>
</div>

{{-- ══════════════════════ MACRO: FILTER BAR ══════════════════════ --}}
{{-- CC --}}
<div class="nts-filter" id="filter-cc">
    <div class="nts-filter-inner">
        <div class="nts-filter-field"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="filter-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" id="cc-search" placeholder="Rechercher stagiaire / CIN…" class="nts-input" style="padding-left:30px"></div>
        <select id="cc-filiere" class="nts-select"><option value="">Toutes les filières</option>@foreach(collect($filieres)->pluck('nom')->filter()->unique()->sort() as $f)<option value="{{ strtolower($f) }}">{{ $f }}</option>@endforeach</select>
        <select id="cc-groupe"  class="nts-select"><option value="">Tous les groupes</option>@foreach($groupes->pluck('nom')->filter()->unique()->sort() as $g)<option value="{{ strtolower($g) }}">{{ $g }}</option>@endforeach</select>
        <select id="cc-module"  class="nts-select"><option value="">Tous les modules</option>@foreach($ccRows->map(fn($r)=>$r['module']->titre??$r['module']->nom)->filter()->unique()->sort() as $m)<option value="{{ strtolower($m) }}">{{ $m }}</option>@endforeach</select>
        <select id="cc-unite"   class="nts-select"><option value="">Toutes les unités</option>@foreach($ccRows->map(fn($r)=>$r['module']->unite?->nom??$r['module']->uniteCompetence?->nom??null)->filter()->unique()->sort() as $u)<option value="{{ strtolower($u) }}">{{ $u }}</option>@endforeach</select>
        <button onclick="resetFilters('cc')" class="nts-btn-reset"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.21"/></svg>Réinitialiser</button>
    </div>
</div>

{{-- ══════════════════════ PANEL CC ══════════════════════ --}}
<div id="panel-cc">
@php $ccGrouped = collect($ccRows)->groupBy(fn($r)=>$r['module']->unite?->nom??$r['module']->uniteCompetence?->nom??'—')->sortKeys(); @endphp
@if($ccGrouped->isEmpty())
    <div class="nts-empty"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg><p>Aucun contrôle enregistré</p></div>
@else
<div id="cc-accordion" class="nts-accordion">
    @foreach($ccGrouped as $uniteName => $uniteRows)
    @php $moduleGrouped=$uniteRows->groupBy(fn($r)=>$r['module']->titre??$r['module']->nom??'—'); $totalRows=$uniteRows->count(); $validatedRows=$uniteRows->filter(fn($r)=>$r['all_valid'])->count(); @endphp
    <div class="acc-unit open" data-unite="{{ strtolower($uniteName) }}">
        <div class="acc-unit-header" onclick="toggleAcc(this)">
            <div class="acc-unit-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
            <span class="acc-unit-name">{{ $uniteName }}</span>
            <div class="acc-unit-meta">
                <span class="acc-badge badge-blue">{{ $totalRows }} enreg.</span>
                @if($validatedRows>0)<span class="acc-badge badge-green">{{ $validatedRows }} validé(s)</span>@endif
            </div>
            <svg class="acc-chevron" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="acc-unit-body">
            @foreach($moduleGrouped as $moduleName => $moduleRows)
            @php $nbCC=$moduleRows->max('nb_cc'); @endphp
            <div class="acc-mod open" data-module="{{ strtolower($moduleName) }}">
                <div class="acc-mod-header" onclick="toggleModAcc(this)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    <span class="acc-mod-name">{{ $moduleName }}</span>
                    <span class="acc-mod-count">{{ $moduleRows->count() }} stagiaire(s)</span>
                    <svg class="mod-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="acc-mod-body">
                    {{-- 3-panel XLS: frozen-left | scroll-middle | frozen-right --}}
                    <div class="xls-wrap">
                        {{-- LEFT FROZEN: # + Stagiaire --}}
                        <div class="xls-frozen xls-frozen-left">
                            <div class="xls-frozen-head">
                                <div class="xls-cell xls-th xls-rownum">#</div>
                                <div class="xls-cell xls-th xls-name-col">Stagiaire</div>
                            </div>
                            <div class="xls-frozen-body">
                                @foreach($moduleRows as $idx => $row)
                                <div class="xls-frozen-row cc-frozen-left"
                                     data-stagiaire="{{ strtolower($row['stagiaire']->nom.' '.$row['stagiaire']->prenom) }}"
                                     data-groupe="{{ strtolower($row['stagiaire']->groupe?->nom??'') }}"
                                     data-filiere="{{ strtolower($row['stagiaire']->groupe?->niveau?->filiere?->nom??'') }}"
                                     data-module="{{ strtolower($moduleName) }}"
                                     data-unite="{{ strtolower($uniteName) }}"
                                     data-idx="{{ $idx }}">
                                    <div class="xls-cell xls-rownum">{{ $idx+1 }}</div>
                                    <div class="xls-cell xls-name-col"><span class="cell-name">{{ $row['stagiaire']->nom }} {{ $row['stagiaire']->prenom }}</span></div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- MIDDLE SCROLL: Groupe | Filière | CC1..N | Moyenne --}}
                        <div class="xls-scroll">
                            <div class="xls-scroll-inner">
                                <div class="xls-scroll-head">
                                    <div class="xls-cell xls-th xls-col-sm">Groupe</div>
                                    <div class="xls-cell xls-th xls-col-md">Filière</div>
                                    @for($i=1;$i<=$nbCC;$i++)<div class="xls-cell xls-th xls-col-cc">CC{{ $i }}</div>@endfor
                                    <div class="xls-cell xls-th xls-col-cc">Moyenne</div>
                                </div>
                                @foreach($moduleRows as $idx => $row)
                                <div class="xls-scroll-row cc-row"
                                     data-stagiaire="{{ strtolower($row['stagiaire']->nom.' '.$row['stagiaire']->prenom) }}"
                                     data-groupe="{{ strtolower($row['stagiaire']->groupe?->nom??'') }}"
                                     data-filiere="{{ strtolower($row['stagiaire']->groupe?->niveau?->filiere?->nom??'') }}"
                                     data-module="{{ strtolower($moduleName) }}"
                                     data-unite="{{ strtolower($uniteName) }}"
                                     data-idx="{{ $idx }}">
                                    <div class="xls-cell xls-col-sm"><span class="cell-badge">{{ $row['stagiaire']->groupe?->nom??'—' }}</span></div>
                                    <div class="xls-cell xls-col-md cell-muted">{{ $row['stagiaire']->groupe?->niveau?->filiere?->nom??'—' }}</div>
                                    @for($i=0;$i<$nbCC;$i++)
                                    <div class="xls-cell xls-col-cc xls-center">
                                        @if($i<$row['nb_cc']&&($row['ccs']->get($i)?->note!==null))
                                            @php $n=$row['ccs']->get($i)->note; @endphp
                                            <span class="note-pill {{ $n>=10?'note-ok':'note-fail' }}">{{ number_format($n,2) }}</span>
                                        @else<span class="cell-dash">—</span>@endif
                                    </div>
                                    @endfor
                                    <div class="xls-cell xls-col-cc xls-center">
                                        @if($row['moyenne']!==null)<span class="note-pill note-avg {{ $row['moyenne']>=10?'note-ok':'note-fail' }}">{{ $row['moyenne'] }}</span>@else<span class="cell-dash">—</span>@endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- RIGHT FROZEN: Statut | Actions --}}
                        <div class="xls-frozen xls-frozen-right">
                            <div class="xls-frozen-head">
                                <div class="xls-cell xls-th xls-col-stat">Statut</div>
                                <div class="xls-cell xls-th xls-col-act2">Actions</div>
                            </div>
                            <div class="xls-frozen-body">
                                @foreach($moduleRows as $idx => $row)
                                <div class="xls-frozen-row cc-frozen-right" data-idx="{{ $idx }}">
                                    <div class="xls-cell xls-col-stat xls-center">
                                        @include('formateur.notes._statut_badge',['valid'=>$row['all_valid']])
                                    </div>
                                    <div class="xls-cell xls-col-act2 xls-center">
                                        @if(!$row['all_valid'])
                                            <div class="icon-actions">
                                                @if($row['ccs']->isNotEmpty())
                                                    {{-- Edit icon button --}}
                                                    <a href="{{ route('formateur.notes.edit', $row['ccs']->first()->id) }}" class="icon-btn icon-btn-edit" title="Modifier">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    </a>
                                                    {{-- Delete icon button --}}
                                                    <button onclick="confirmDel('{{ $row['stagiaire']->id }}-{{ $loop->index }}','{{ $row['stagiaire']->nom }} {{ $row['stagiaire']->prenom }}',{{ $row['ccs']->first()->id }})" class="icon-btn icon-btn-del" title="Supprimer">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <span class="cell-validated">✓</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
<div id="cc-no-result" class="nts-empty hidden"><p>Aucun résultat trouvé</p></div>
@endif
</div>

{{-- ══════════════════════ PANEL EXAM ══════════════════════ --}}
<div class="nts-filter hidden" id="filter-exam">
    <div class="nts-filter-inner">
        <div class="nts-filter-field"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="filter-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" id="exam-search" placeholder="Rechercher stagiaire…" class="nts-input" style="padding-left:30px"></div>
        <select id="exam-filiere" class="nts-select"><option value="">Toutes les filières</option>@foreach(collect($filieres)->pluck('nom')->filter()->unique()->sort() as $f)<option value="{{ strtolower($f) }}">{{ $f }}</option>@endforeach</select>
        <select id="exam-groupe"  class="nts-select"><option value="">Tous les groupes</option>@foreach($groupes->pluck('nom')->filter()->unique()->sort() as $g)<option value="{{ strtolower($g) }}">{{ $g }}</option>@endforeach</select>
        <select id="exam-unite"   class="nts-select"><option value="">Toutes les unités</option>@foreach($examRows->map(fn($r)=>$r['unite']?->nom)->filter()->unique()->sort() as $u)<option value="{{ strtolower($u) }}">{{ $u }}</option>@endforeach</select>
        <button onclick="resetFilters('exam')" class="nts-btn-reset"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.21"/></svg>Réinitialiser</button>
    </div>
</div>

<div id="panel-exam" class="hidden">
<div class="xls-wrap xls-standalone">
    {{-- LEFT --}}
    <div class="xls-frozen xls-frozen-left">
        <div class="xls-frozen-head"><div class="xls-cell xls-th xls-rownum">#</div><div class="xls-cell xls-th xls-name-col">Stagiaire</div></div>
        <div class="xls-frozen-body">
            @forelse($examRows as $idx => $row)
            <div class="xls-frozen-row exam-frozen-left" data-idx="{{ $idx }}"
                 data-stagiaire="{{ strtolower($row['stagiaire']->nom.' '.$row['stagiaire']->prenom) }}"
                 data-groupe="{{ strtolower($row['stagiaire']->groupe?->nom??'') }}"
                 data-filiere="{{ strtolower($row['stagiaire']->groupe?->niveau?->filiere?->nom??'') }}"
                 data-unite="{{ strtolower($row['unite']?->nom??'') }}">
                <div class="xls-cell xls-rownum">{{ $idx+1 }}</div>
                <div class="xls-cell xls-name-col"><span class="cell-name">{{ $row['stagiaire']->nom }} {{ $row['stagiaire']->prenom }}</span></div>
            </div>
            @empty
            @endforelse
        </div>
    </div>
    {{-- SCROLL --}}
    <div class="xls-scroll">
        <div class="xls-scroll-inner">
            <div class="xls-scroll-head">
                <div class="xls-cell xls-th xls-col-sm">Groupe</div>
                <div class="xls-cell xls-th xls-col-md">Filière</div>
                <div class="xls-cell xls-th xls-col-md">Unité</div>
                @foreach($examHeaders as $h)<div class="xls-cell xls-th xls-col-cc">{{ $h['label'] }} <span class="th-poids">{{ $h['poids'] }}%</span></div>@endforeach
                <div class="xls-cell xls-th xls-col-cc">Moy. Unité</div>
            </div>
            @forelse($examRows as $idx => $row)
            <div class="xls-scroll-row exam-row" data-idx="{{ $idx }}"
                 data-stagiaire="{{ strtolower($row['stagiaire']->nom.' '.$row['stagiaire']->prenom) }}"
                 data-groupe="{{ strtolower($row['stagiaire']->groupe?->nom??'') }}"
                 data-filiere="{{ strtolower($row['stagiaire']->groupe?->niveau?->filiere?->nom??'') }}"
                 data-unite="{{ strtolower($row['unite']?->nom??'') }}">
                <div class="xls-cell xls-col-sm"><span class="cell-badge">{{ $row['stagiaire']->groupe?->nom??'—' }}</span></div>
                <div class="xls-cell xls-col-md cell-muted">{{ $row['stagiaire']->groupe?->niveau?->filiere?->nom??'—' }}</div>
                <div class="xls-cell xls-col-md cell-muted">{{ $row['unite']?->nom??'—' }}</div>
                <div class="xls-cell xls-col-cc xls-center">@if($row['moy_cc']!==null)<span class="note-pill note-info">{{ $row['moy_cc'] }}</span>@else<span class="cell-dash">—</span>@endif</div>
                <div class="xls-cell xls-col-cc xls-center">@if($row['theorique']?->note!==null)@php $n=$row['theorique']->note; @endphp<span class="note-pill {{ $n>=10?'note-ok':'note-fail' }}">{{ $n }}</span>@else<span class="cell-dash">—</span>@endif</div>
                <div class="xls-cell xls-col-cc xls-center">@if($row['pratique']?->note!==null)@php $n=$row['pratique']->note; @endphp<span class="note-pill {{ $n>=10?'note-ok':'note-fail' }}">{{ $n }}</span>@else<span class="cell-dash">—</span>@endif</div>
                <div class="xls-cell xls-col-cc xls-center">@if($row['moy_unite']!==null)<span class="note-pill note-avg {{ $row['moy_unite']>=10?'note-ok':'note-fail' }}">{{ $row['moy_unite'] }}</span>@else<span class="cell-dash">—</span>@endif</div>
            </div>
            @empty
            <div class="xls-scroll-row"><div class="xls-cell" style="width:100%;padding:40px;text-align:center;color:#94a3b8">Aucun examen enregistré</div></div>
            @endforelse
        </div>
    </div>
    {{-- RIGHT FROZEN --}}
    <div class="xls-frozen xls-frozen-right">
        <div class="xls-frozen-head"><div class="xls-cell xls-th xls-col-stat">Statut</div><div class="xls-cell xls-th xls-col-act2">Actions</div></div>
        <div class="xls-frozen-body">
            @forelse($examRows as $idx => $row)
            <div class="xls-frozen-row exam-frozen-right" data-idx="{{ $idx }}">
                <div class="xls-cell xls-col-stat xls-center">@include('formateur.notes._statut_badge',['valid'=>$row['all_valid']])</div>
                <div class="xls-cell xls-col-act2 xls-center">
                    @if(!$row['all_valid'])
                        @php $examCcs=collect(); if($row['theorique']) $examCcs->push($row['theorique']); if($row['pratique']) $examCcs->push($row['pratique']); @endphp
                        <div class="icon-actions">
                            @if($row['theorique'])<a href="{{ route('formateur.notes.edit',$row['theorique']->id) }}" class="icon-btn icon-btn-edit" title="Modifier"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>@endif
                            @if($examCcs->isNotEmpty())<button onclick="confirmDel('exam-{{ $row['stagiaire']->id }}','{{ $row['stagiaire']->nom }} {{ $row['stagiaire']->prenom }}',{{ $examCcs->first()->id }})" class="icon-btn icon-btn-del" title="Supprimer"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>@endif
                        </div>
                    @else<span class="cell-validated">✓</span>@endif
                </div>
            </div>
            @empty
            @endforelse
        </div>
    </div>
</div>
<div id="exam-no-result" class="nts-empty hidden"><p>Aucun résultat trouvé</p></div>
</div>

{{-- ══════════════════════ PANEL PFE ══════════════════════ --}}
<div class="nts-filter hidden" id="filter-pfe">
    <div class="nts-filter-inner">
        <div class="nts-filter-field"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="filter-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" id="pfe-search" placeholder="Rechercher stagiaire…" class="nts-input" style="padding-left:30px"></div>
        <select id="pfe-filiere" class="nts-select"><option value="">Toutes les filières</option>@foreach($pfeGroupes->map(fn($g)=>$g->niveau?->filiere?->nom)->filter()->unique()->sort() as $f)<option value="{{ strtolower($f) }}">{{ $f }}</option>@endforeach</select>
        <select id="pfe-groupe"  class="nts-select"><option value="">Tous les groupes</option>@foreach($pfeGroupes->pluck('nom')->filter()->unique()->sort() as $g)<option value="{{ strtolower($g) }}">{{ $g }}</option>@endforeach</select>
        <button onclick="resetFilters('pfe')" class="nts-btn-reset"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.21"/></svg>Réinitialiser</button>
    </div>
</div>

<div id="panel-pfe" class="hidden">
<div class="xls-wrap xls-standalone">
    <div class="xls-frozen xls-frozen-left">
        <div class="xls-frozen-head"><div class="xls-cell xls-th xls-rownum">#</div><div class="xls-cell xls-th xls-name-col">Stagiaire</div></div>
        <div class="xls-frozen-body">
            @forelse($pfeStagiaires as $idx => $stagiaire)
            <div class="xls-frozen-row pfe-frozen-left" data-idx="{{ $idx }}"
                 data-stagiaire="{{ strtolower($stagiaire->nom.' '.$stagiaire->prenom) }}"
                 data-groupe="{{ strtolower($stagiaire->groupe?->nom??'') }}"
                 data-filiere="{{ strtolower($stagiaire->groupe?->niveau?->filiere?->nom??'') }}">
                <div class="xls-cell xls-rownum">{{ $idx+1 }}</div>
                <div class="xls-cell xls-name-col"><span class="cell-name">{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</span></div>
            </div>
            @empty
            @endforelse
        </div>
    </div>
    <div class="xls-scroll">
        <div class="xls-scroll-inner">
            <div class="xls-scroll-head">
                <div class="xls-cell xls-th xls-col-sm">Groupe</div>
                <div class="xls-cell xls-th xls-col-md">Filière</div>
                <div class="xls-cell xls-th xls-col-cc">Note PFE <span class="th-poids">/20</span></div>
            </div>
            @forelse($pfeStagiaires as $idx => $stagiaire)
            <div class="xls-scroll-row pfe-row" data-idx="{{ $idx }}"
                 data-stagiaire="{{ strtolower($stagiaire->nom.' '.$stagiaire->prenom) }}"
                 data-groupe="{{ strtolower($stagiaire->groupe?->nom??'') }}"
                 data-filiere="{{ strtolower($stagiaire->groupe?->niveau?->filiere?->nom??'') }}">
                <div class="xls-cell xls-col-sm"><span class="cell-badge">{{ $stagiaire->groupe?->nom??'—' }}</span></div>
                <div class="xls-cell xls-col-md cell-muted">{{ $stagiaire->groupe?->niveau?->filiere?->nom??'—' }}</div>
                <div class="xls-cell xls-col-cc xls-center">@if($stagiaire->pfe?->note!==null)<span class="note-pill note-pfe">{{ $stagiaire->pfe->note }}</span>@else<span class="cell-dash">—</span>@endif</div>
            </div>
            @empty
            <div class="xls-scroll-row"><div class="xls-cell" style="width:100%;padding:40px;text-align:center;color:#94a3b8">Aucun stagiaire trouvé</div></div>
            @endforelse
        </div>
    </div>
</div>
<div id="pfe-no-result" class="nts-empty hidden"><p>Aucun résultat trouvé</p></div>
</div>

{{-- ══════════════════════ PANEL RÉSULTATS ══════════════════════ --}}
<div class="nts-filter hidden" id="filter-resultats">
    <div class="nts-filter-inner">
        <div class="nts-filter-field"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="filter-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" id="res-search" placeholder="Rechercher stagiaire…" class="nts-input" style="padding-left:30px"></div>
        <select id="res-filiere" class="nts-select"><option value="">Toutes les filières</option>@foreach(collect($filieres)->pluck('nom')->filter()->unique()->sort() as $f)<option value="{{ strtolower($f) }}">{{ $f }}</option>@endforeach</select>
        <select id="res-groupe"  class="nts-select"><option value="">Tous les groupes</option>@foreach($groupes->pluck('nom')->filter()->unique()->sort() as $g)<option value="{{ strtolower($g) }}">{{ $g }}</option>@endforeach</select>
        <button onclick="resetFilters('resultats')" class="nts-btn-reset"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.21"/></svg>Réinitialiser</button>
    </div>
</div>

<div id="panel-resultats" class="hidden">
<div class="xls-wrap xls-standalone">

    {{-- LEFT FROZEN --}}
    <div class="xls-frozen xls-frozen-left">
        <div class="xls-frozen-head"><div class="xls-cell xls-th xls-rownum">#</div><div class="xls-cell xls-th xls-name-col">Stagiaire</div></div>
        <div class="xls-frozen-body">
            @forelse($resultats as $idx => $res)
            <div class="xls-frozen-row res-frozen-left" data-idx="{{ $idx }}"
                 data-stagiaire="{{ strtolower($res['stagiaire']->nom.' '.$res['stagiaire']->prenom) }}"
                 data-groupe="{{ strtolower($res['stagiaire']->groupe?->nom??'') }}"
                 data-filiere="{{ strtolower($res['stagiaire']->groupe?->niveau?->filiere?->nom??'') }}">
                <div class="xls-cell xls-rownum">{{ $idx+1 }}</div>
                <div class="xls-cell xls-name-col"><span class="cell-name">{{ $res['stagiaire']->nom }} {{ $res['stagiaire']->prenom }}</span></div>
            </div>
            @empty
            @endforelse
        </div>
    </div>

    {{-- SCROLL MIDDLE --}}
    <div class="xls-scroll">
        <div class="xls-scroll-inner">

            {{-- HEAD: Groupe | Filière | Moy.Unités | PFE | Moy.Générale --}}
            <div class="xls-scroll-head">
                <div class="xls-cell xls-th xls-col-sm">Groupe</div>
                <div class="xls-cell xls-th xls-col-md">Filière</div>
                <div class="xls-cell xls-th xls-col-cc">Moy. Unités <span class="th-poids">80%</span></div>
                @if($pfeStagiaires->isNotEmpty())
                <div class="xls-cell xls-th xls-col-cc">PFE <span class="th-poids">20%</span></div>
                @endif
                <div class="xls-cell xls-th xls-col-cc">Moy. Générale</div>
            </div>

            @forelse($resultats as $idx => $res)
            @php
                $sumP = 0; $sumC = 0;
                foreach($uniteNames as $u) {
                    $moy  = $res['unites_moy'][$u] ?? null;
                    $coef = $examRows->first(fn($r) => ($r['unite']->nom ?? '') === $u)?->unite?->coefficient ?? 1;
                    if ($moy !== null) { $sumP += $moy * $coef; $sumC += $coef; }
                }
                $moyUnites = $sumC > 0 ? round($sumP / $sumC, 2) : null;
            @endphp
            <div class="xls-scroll-row res-row" data-idx="{{ $idx }}"
                 data-stagiaire="{{ strtolower($res['stagiaire']->nom.' '.$res['stagiaire']->prenom) }}"
                 data-groupe="{{ strtolower($res['stagiaire']->groupe?->nom??'') }}"
                 data-filiere="{{ strtolower($res['stagiaire']->groupe?->niveau?->filiere?->nom??'') }}">
                <div class="xls-cell xls-col-sm"><span class="cell-badge">{{ $res['stagiaire']->groupe?->nom??'—' }}</span></div>
                <div class="xls-cell xls-col-md cell-muted">{{ $res['stagiaire']->groupe?->niveau?->filiere?->nom??'—' }}</div>

                {{-- Moy. Unités (colonne unique pondérée) --}}
                <div class="xls-cell xls-col-cc xls-center">
                    @if($moyUnites !== null)
                        <span class="note-pill {{ $moyUnites >= 10 ? 'note-ok' : 'note-fail' }}">{{ $moyUnites }}</span>
                    @else<span class="cell-dash">—</span>@endif
                </div>

                {{-- PFE --}}
                @if($pfeStagiaires->isNotEmpty())
                <div class="xls-cell xls-col-cc xls-center">
                    @if(isset($res['note_pfe']) && $res['note_pfe'] !== null)
                        <span class="note-pill {{ $res['note_pfe'] >= 10 ? 'note-pfe' : 'note-fail' }}">{{ $res['note_pfe'] }}</span>
                    @else<span class="cell-dash">—</span>@endif
                </div>
                @endif

                {{-- Moy. Générale --}}
                <div class="xls-cell xls-col-cc xls-center">
                    @if($res['moy_gen'] !== null)
                        <span class="note-pill note-avg {{ $res['moy_gen'] >= 10 ? 'note-ok' : 'note-fail' }}">{{ $res['moy_gen'] }}</span>
                    @else<span class="cell-dash">—</span>@endif
                </div>
            </div>
            @empty
            <div class="xls-scroll-row"><div class="xls-cell" style="width:100%;padding:40px;text-align:center;color:#94a3b8">Aucun résultat disponible</div></div>
            @endforelse

        </div>
    </div>

    {{-- RIGHT FROZEN: Mention + Bouton Détail --}}
    <div class="xls-frozen xls-frozen-right">
        <div class="xls-frozen-head">
            <div class="xls-cell xls-th xls-col-mention">Mention</div>
            <div class="xls-cell xls-th xls-col-act2">Détail</div>
        </div>
        <div class="xls-frozen-body">
            @forelse($resultats as $idx => $res)
            <div class="xls-frozen-row res-frozen-right" data-idx="{{ $idx }}">
                <div class="xls-cell xls-col-mention xls-center">
                    @if($res['mention'])
                        <span class="mention-badge mention-{{ Str::slug($res['mention']['label']) }}">{{ $res['mention']['label'] }}</span>
                    @else<span class="cell-dash">—</span>@endif
                </div>
                <div class="xls-cell xls-col-act2 xls-center">
                    <button class="icon-btn icon-btn-details"
                            title="Voir le relevé"
                            onclick="openReleve({{ $res['stagiaire']->id }})">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            @endforelse
        </div>
    </div>

</div>
<div id="res-no-result" class="nts-empty hidden"><p>Aucun résultat trouvé</p></div>
</div>

{{-- ══════════════════════ RELEVÉ SLIDE PANEL ══════════════════════ --}}
<div id="releve-overlay" class="rld-overlay hidden" onclick="rldCloseOverlay(event)">
    <div id="releve-panel" class="rld-panel">
        <div id="releve-content">
            <div class="rld-loading"><div class="rld-spinner"></div></div>
        </div>
    </div>
</div>
{{-- ══════════════════════ STYLES RELEVÉ ══════════════════════ --}}
<style>
/* ── ICON DETAILS ── */
.icon-btn-details{border-color:#c4b5fd;color:#7c3aed;}
.icon-btn-details:hover{background:#f5f3ff;border-color:#a78bfa;transform:translateY(-1px);}

/* ── SLIDE PANEL ── */
.rld-overlay{position:fixed;inset:0;background:rgba(15,31,51,.38);backdrop-filter:blur(3px);z-index:9998;display:flex;justify-content:flex-end;}
.rld-panel{width:700px;max-width:96vw;height:100%;background:#fff;box-shadow:-6px 0 40px rgba(0,0,0,.14);overflow-y:auto;animation:rldSlideIn .22s cubic-bezier(.22,1,.36,1);}
@keyframes rldSlideIn{from{transform:translateX(100%)}to{transform:translateX(0)}}
.rld-loading{display:flex;align-items:center;justify-content:center;height:200px;}
.rld-spinner{width:30px;height:30px;border:3px solid #e2e8f0;border-top-color:#1a3a5c;border-radius:50%;animation:rldSpin .7s linear infinite;}
@keyframes rldSpin{to{transform:rotate(360deg)}}

/* ── RELEVÉ CONTENT ── */
.rld-wrap{font-family:'DM Sans',sans-serif;}
.rld-header{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;background:#f8fafc;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:10;}
.rld-header-left{display:flex;align-items:center;gap:12px;}
.rld-avatar{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;flex-shrink:0;}
.rld-name{font-size:15px;font-weight:600;color:#0f1f33;}
.rld-meta{font-size:11.5px;color:#94a3b8;margin-top:2px;}
.rld-header-actions{display:flex;align-items:center;gap:8px;}
.rld-btn-print{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#475569;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .14s;}
.rld-btn-print:hover{border-color:#94a3b8;color:#1e293b;background:#f8fafc;}
.rld-btn-close{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:8px;border:1px solid #fda4af;background:#fff1f2;color:#e11d48;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .14s;}
.rld-btn-close:hover{background:#ffe4e6;}
.rld-body{padding:20px 22px;}
.rld-section{margin-bottom:22px;}
.rld-section-title{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#475569;margin-bottom:10px;}
.rld-coef{font-size:11px;font-weight:500;padding:1px 7px;border-radius:20px;background:#dbeafe;color:#1d4ed8;text-transform:none;letter-spacing:0;}
.rld-table{width:100%;border-collapse:collapse;font-size:12.5px;}
.rld-table thead tr{background:#f1f5f9;}
.rld-table th{padding:7px 12px;font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#64748b;border-bottom:1.5px solid #e2e8f0;}
.rld-table td{padding:8px 12px;border-bottom:1px solid #f1f5f9;color:#334155;}
.rld-table tbody tr:last-child td{border-bottom:none;}
.rld-table .tc{text-align:center;}
.mod-name{font-weight:500;color:#1e293b;}
.rld-total-row td{background:#f8fafc;border-top:1.5px solid #e2e8f0!important;padding:9px 12px;}
.np{display:inline-flex;align-items:center;justify-content:center;min-width:44px;height:22px;padding:0 6px;border-radius:20px;font-size:11.5px;font-weight:700;font-family:'DM Mono',monospace;border:1.5px solid;}
.np-ok  {background:#f0fdf4;color:#15803d;border-color:#86efac;}
.np-fail{background:#fff1f2;color:#be123c;border-color:#fda4af;}
.np-avg {min-width:48px;height:24px;font-size:12px;}
.np-big {min-width:54px;height:26px;font-size:13px;}
.ndash  {color:#cbd5e1;font-size:13px;}
.rld-summary{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;}
.rld-calc-grid{display:grid;grid-template-columns:1fr 1fr 1.2fr;gap:12px;margin-bottom:14px;}
.rld-calc-card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;}
.rld-calc-card-main{background:linear-gradient(135deg,#1a3a5c,#2563a8);border-color:transparent;}
.rld-calc-label{font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:5px;}
.rld-calc-card-main .rld-calc-label{color:rgba(255,255,255,.6);}
.rld-calc-val{font-size:22px;font-weight:700;font-family:'DM Mono',monospace;line-height:1;}
.rld-gen-val{font-size:26px;font-weight:700;font-family:'DM Mono',monospace;color:#fff;line-height:1;}
.rld-gen-denom{font-size:14px;font-weight:400;opacity:.6;margin-left:2px;}
.cv-ok  {color:#15803d;}
.cv-fail{color:#be123c;}
.rld-calc-formula{font-size:11px;color:#94a3b8;margin-top:4px;}
.rld-calc-formula strong{color:#475569;}
.rld-calc-line{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-size:12px;color:#64748b;}
.rld-calc-step strong{color:#0f1f33;font-family:'DM Mono',monospace;}
</style>

{{-- ══════════════════════ JAVASCRIPT RELEVÉ ══════════════════════ --}}
<script>
window.openReleve = function(stagiaireId) {
    const overlay = document.getElementById('releve-overlay');
    const content = document.getElementById('releve-content');
    overlay.classList.remove('hidden');
    content.innerHTML = '<div class="rld-loading"><div class="rld-spinner"></div></div>';
    fetch(`/formateur/notes/releve/${stagiaireId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => {
        if (!r.ok) throw new Error('Erreur serveur');
        return r.text();
    })
    .then(html => { content.innerHTML = html; })
    .catch(() => {
        content.innerHTML = '<p style="padding:30px;color:#e11d48;font-family:DM Sans,sans-serif;">Erreur de chargement du relevé.</p>';
    });
};

window.rldClose = function() {
    document.getElementById('releve-overlay').classList.add('hidden');
    document.getElementById('releve-content').innerHTML = '<div class="rld-loading"><div class="rld-spinner"></div></div>';
};

window.rldCloseOverlay = function(e) {
    if (e.target === document.getElementById('releve-overlay')) rldClose();
};

window.rldPrint = function(id) {
    const el = document.getElementById('rld-print-' + id);
    if (!el) return;
    const styles = [...document.querySelectorAll('style')].map(s => s.outerHTML).join('');
    const win = window.open('', '_blank');
    win.document.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><title>Relevé</title>${styles}</head><body style="padding:24px;font-family:DM Sans,sans-serif">${el.outerHTML}</body></html>`);
    win.document.close();
    win.focus();
    setTimeout(() => win.print(), 400);
};

document.addEventListener('keydown', e => { if (e.key === 'Escape') rldClose(); });
</script>

{{-- ══════════════════════ MODAL CONFIRM DELETE ══════════════════════ --}}
<div id="del-modal-overlay" class="del-overlay hidden" onclick="closeDel()">
    <div class="del-modal" onclick="event.stopPropagation()">
        <div class="del-modal-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <h3 class="del-modal-title">Supprimer la note ?</h3>
        <p class="del-modal-sub">Cette action est irréversible. La note de <strong id="del-modal-name"></strong> sera définitivement supprimée.</p>
        <div class="del-modal-actions">
            <button onclick="closeDel()" class="del-btn-cancel">Annuler</button>
            <form id="del-modal-form" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="del-btn-confirm">Supprimer</button>
            </form>
        </div>
    </div>
</div>

</div>{{-- end nts-wrap --}}

{{-- ══════════════════════ STYLES ══════════════════════ --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@400;500&display=swap');
*,*::before,*::after{box-sizing:border-box;}
.nts-wrap{font-family:'DM Sans',sans-serif;--row-h:36px;--head-h:34px;--frozen-lw:280px;--rownum-w:38px;--name-w:calc(var(--frozen-lw) - var(--rownum-w));}

/* ── TOPBAR ── */
.nts-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e8edf3;}
.nts-topbar-left{display:flex;align-items:center;gap:12px;}
.nts-icon-box{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563a8);display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;}
.nts-topbar h1{font-size:17px;font-weight:600;color:#0f1f33;margin:0;line-height:1.2;}
.nts-topbar p{font-size:12px;color:#94a3b8;margin:3px 0 0;}
.nts-btn-add{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;background:#1a3a5c;color:#fff;font-size:12.5px;font-weight:600;text-decoration:none;transition:background .15s,transform .1s;}
.nts-btn-add:hover{background:#132d4a;transform:translateY(-1px);}
.hidden{display:none!important;}

/* ── ALERTS ── */
.nts-alert{display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid;font-size:13px;margin-bottom:12px;}
.alert-success{background:#f0fdf4;border-color:#bbf7d0;color:#15803d;}
.alert-error{background:#fff1f2;border-color:#fecdd3;color:#be123c;}
.alert-warning{background:#fffbeb;border-color:#fde68a;color:#b45309;}

/* ── TABS ── */
.nts-tabs{display:inline-flex;gap:2px;padding:4px;background:#f1f5f9;border-radius:12px;margin-bottom:14px;}
.nts-tab{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:9px;font-size:12.5px;font-weight:500;color:#64748b;background:transparent;border:none;cursor:pointer;transition:all .15s;}
.nts-tab:hover{background:#fff;color:#1e293b;}
.tab-active{background:#fff!important;color:#0f1f33!important;box-shadow:0 1px 6px rgba(0,0,0,.1);font-weight:600;}
.tab-pill{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:6px;font-size:10px;font-weight:700;}
.pill-cc{background:#dbeafe;color:#1d4ed8;} .tab-active .pill-cc{background:#1d4ed8;color:#fff;}
.pill-exam{background:#ede9fe;color:#6d28d9;} .tab-active .pill-exam{background:#6d28d9;color:#fff;}
.pill-pfe{background:#ffedd5;color:#c2410c;} .tab-active .pill-pfe{background:#c2410c;color:#fff;}
.pill-res{background:#d1fae5;color:#065f46;} .tab-active .pill-res{background:#065f46;color:#fff;}

/* ── FILTER ── */
.nts-filter{margin-bottom:10px;}
.nts-filter-inner{display:flex;flex-wrap:wrap;align-items:center;gap:8px;padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;}
.nts-filter-field{position:relative;display:flex;align-items:center;}
.filter-icon{position:absolute;left:9px;color:#94a3b8;pointer-events:none;}
.nts-input{height:32px;padding:0 10px;border:1px solid #dde3ed;border-radius:7px;font-size:12.5px;font-family:'DM Sans',sans-serif;background:#fff;color:#334155;outline:none;min-width:190px;transition:border .15s,box-shadow .15s;}
.nts-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.1);}
.nts-select{height:32px;padding:0 28px 0 10px;border:1px solid #dde3ed;border-radius:7px;font-size:12.5px;font-family:'DM Sans',sans-serif;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E") no-repeat right 10px center;appearance:none;color:#334155;outline:none;cursor:pointer;transition:border .15s;}
.nts-select:focus{border-color:#3b82f6;}
.nts-btn-reset{margin-left:auto;height:32px;padding:0 12px;border:1px solid #dde3ed;border-radius:7px;background:#fff;color:#64748b;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:all .15s;}
.nts-btn-reset:hover{border-color:#94a3b8;color:#334155;}

/* ── ACCORDION UNIT ── */
.nts-accordion{display:flex;flex-direction:column;gap:8px;}
.acc-unit{border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.04);}
.acc-unit-header{display:flex;align-items:center;gap:10px;padding:10px 16px;background:#f8fafc;cursor:pointer;user-select:none;transition:background .12s;}
.acc-unit-header:hover{background:#f0f6ff;}
.acc-unit-icon{width:30px;height:30px;border-radius:8px;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.acc-unit-name{flex:1;font-size:13px;font-weight:600;color:#1e293b;}
.acc-unit-meta{display:flex;gap:5px;}
.acc-badge{padding:2px 8px;border-radius:20px;font-size:11px;font-weight:500;}
.badge-blue{background:#dbeafe;color:#1d4ed8;}
.badge-green{background:#d1fae5;color:#065f46;}
.acc-chevron{color:#94a3b8;transition:transform .2s;flex-shrink:0;}
.acc-unit-body{display:none;}
.acc-unit.open .acc-unit-body{display:block;}
.acc-unit.open .acc-chevron{transform:rotate(180deg);}

/* ── ACCORDION MODULE ── */
.acc-mod{border-top:1px solid #f1f5f9;}
.acc-mod-header{display:flex;align-items:center;gap:8px;padding:7px 16px;background:#fafbfc;cursor:pointer;user-select:none;transition:background .12s;}
.acc-mod-header:hover{background:#f0f6ff;}
.acc-mod-name{flex:1;font-size:12.5px;font-weight:500;color:#475569;}
.acc-mod-count{font-size:11.5px;color:#94a3b8;margin-right:4px;}
.mod-chevron{color:#94a3b8;transition:transform .2s;flex-shrink:0;}
.acc-mod-body{display:none;}
.acc-mod.open .acc-mod-body{display:block;}
.acc-mod.open .mod-chevron{transform:rotate(180deg);}

/* ══ XLS 3-PANEL LAYOUT ══ */
.xls-wrap{display:flex;overflow:hidden;border-top:1px solid #e8edf3;}
.xls-standalone{border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);}

/* Left frozen */
.xls-frozen-left{flex-shrink:0;width:var(--frozen-lw);border-right:2px solid #cbd5e1;background:#fff;z-index:3;}

/* Right frozen */
.xls-frozen-right{flex-shrink:0;border-left:2px solid #cbd5e1;background:#fff;z-index:3;}

.xls-frozen-head{display:flex;height:var(--head-h);border-bottom:1.5px solid #cbd5e1;background:#f1f5f9;}
.xls-frozen-body{overflow:hidden;}
.xls-frozen-row{display:flex;height:var(--row-h);border-bottom:1px solid #f1f5f9;transition:background .1s;}
.xls-frozen-row:last-child{border-bottom:none;}
.xls-frozen-row.row-hover{background:#eef6ff;}

/* Scroll middle */
.xls-scroll{flex:1;overflow-x:auto;overflow-y:hidden;min-width:0;}
.xls-scroll::-webkit-scrollbar{height:5px;}
.xls-scroll::-webkit-scrollbar-track{background:#f1f5f9;}
.xls-scroll::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px;}
.xls-scroll-inner{display:inline-flex;flex-direction:column;min-width:100%;}
.xls-scroll-head{display:flex;height:var(--head-h);border-bottom:1.5px solid #cbd5e1;background:#f1f5f9;}
.xls-scroll-row{display:flex;height:var(--row-h);border-bottom:1px solid #f1f5f9;transition:background .1s;}
.xls-scroll-row:last-child{border-bottom:none;}
.xls-scroll-row.row-hover{background:#eef6ff;}

/* Cells */
.xls-cell{display:flex;align-items:center;padding:0 10px;font-size:12.5px;white-space:nowrap;flex-shrink:0;}
.xls-th{font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#64748b;}
.xls-center{justify-content:center;}
.xls-rownum{width:var(--rownum-w);justify-content:center;font-size:11px;color:#94a3b8;font-family:'DM Mono',monospace;flex-shrink:0;border-right:1px solid #e8edf3;}
.xls-name-col{width:var(--name-w);flex-shrink:0;}

/* Column widths */
.xls-col-sm     {width:80px;}
.xls-col-md     {width:120px;}
.xls-col-cc     {width:82px;justify-content:center;}
.xls-col-unite  {min-width:140px;justify-content:center;}
.xls-col-stat   {width:112px;justify-content:center;}
.xls-col-act2   {width:80px;justify-content:center;}
.xls-col-mention{width:120px;justify-content:center;}

/* ── CELL CONTENT ── */
.cell-name{font-weight:500;color:#1e293b;font-size:12.5px;overflow:hidden;text-overflow:ellipsis;}
.cell-badge{display:inline-flex;align-items:center;padding:2px 7px;border-radius:5px;background:#f1f5f9;color:#475569;font-size:11px;font-weight:500;}
.cell-muted{color:#64748b;font-size:12px;}
.cell-dash{color:#cbd5e1;font-size:13px;}
.cell-validated{font-size:13px;color:#22c55e;font-weight:600;}
.th-poids{font-weight:400;color:#94a3b8;font-size:10px;text-transform:none;letter-spacing:0;margin-left:2px;}

/* ── NOTES ── */
.note-pill{display:inline-flex;align-items:center;justify-content:center;min-width:46px;height:22px;padding:0 6px;border-radius:20px;font-size:11.5px;font-weight:700;font-family:'DM Mono',monospace;border:1.5px solid;}
.note-ok  {background:#f0fdf4;color:#15803d;border-color:#86efac;}
.note-fail{background:#fff1f2;color:#be123c;border-color:#fda4af;}
.note-avg {min-width:50px;height:24px;font-size:12px;}
.note-info{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
.note-pfe {background:#fff7ed;color:#c2410c;border-color:#fed7aa;}

/* ── MENTION ── */
.mention-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;border:1px solid;white-space:nowrap;}
.mention-tres-bien  {background:#f0fdf4;color:#15803d;border-color:#86efac;}
.mention-bien       {background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
.mention-assez-bien {background:#fefce8;color:#a16207;border-color:#fde047;}
.mention-passable   {background:#fff7ed;color:#c2410c;border-color:#fed7aa;}
.mention-insuffisant{background:#fff1f2;color:#be123c;border-color:#fda4af;}

/* ── ICON ACTION BUTTONS ── */
.icon-actions{display:flex;align-items:center;gap:5px;}
.icon-btn{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;border:1px solid;cursor:pointer;transition:all .15s;background:transparent;text-decoration:none;flex-shrink:0;}
.icon-btn-edit{border-color:#bfdbfe;color:#2563eb;}
.icon-btn-edit:hover{background:#eff6ff;border-color:#93c5fd;transform:translateY(-1px);}
.icon-btn-del{border-color:#fda4af;color:#e11d48;}
.icon-btn-del:hover{background:#fff1f2;border-color:#fb7185;transform:translateY(-1px);}

/* ── DELETE MODAL ── */
.del-overlay{position:fixed;inset:0;background:rgba(15,31,51,.45);backdrop-filter:blur(2px);z-index:9999;display:flex;align-items:center;justify-content:center;}
.del-modal{background:#fff;border-radius:16px;padding:28px 28px 22px;width:380px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,.18);animation:modalIn .18s ease;}
@keyframes modalIn{from{transform:scale(.94);opacity:0}to{transform:scale(1);opacity:1}}
.del-modal-icon{width:48px;height:48px;border-radius:12px;background:#fff1f2;color:#e11d48;display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
.del-modal-title{font-size:16px;font-weight:600;color:#0f1f33;margin:0 0 6px;}
.del-modal-sub{font-size:13px;color:#64748b;margin:0 0 20px;line-height:1.5;}
.del-modal-sub strong{color:#334155;}
.del-modal-actions{display:flex;gap:8px;justify-content:flex-end;}
.del-btn-cancel{padding:8px 16px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#475569;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .12s;}
.del-btn-cancel:hover{background:#f8fafc;border-color:#cbd5e1;}
.del-btn-confirm{padding:8px 16px;border-radius:8px;border:none;background:#e11d48;color:#fff;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .12s;}
.del-btn-confirm:hover{background:#be123c;}

/* ── EMPTY ── */
.nts-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;color:#94a3b8;gap:10px;}
.nts-empty p{font-size:13px;margin:0;}
</style>

{{-- ══════════════════════ JAVASCRIPT ══════════════════════ --}}
<script>
(function(){
'use strict';

/* ── TABS ── */
const TABS={
    cc:       {url:'{{ route('formateur.notes.create',['type'=>'cc']) }}',  hasAdd:true},
    exam:     {url:'{{ route('formateur.notes.create',['type'=>'exam']) }}',hasAdd:true},
    pfe:      {url:'{{ route('formateur.notes.create',['type'=>'pfe']) }}', hasAdd:true},
    resultats:{url:null,hasAdd:false},
};
window.switchTab=function(active){
    const btnAdd=document.getElementById('btn-ajouter');
    const btnLabel=document.getElementById('btn-ajouter-label');
    Object.keys(TABS).forEach(tab=>{
        const on=tab===active;
        document.getElementById('panel-' +tab)?.classList.toggle('hidden',!on);
        document.getElementById('filter-'+tab)?.classList.toggle('hidden',!on);
        document.getElementById('tab-'  +tab)?.classList.toggle('tab-active',on);
    });
    if(btnAdd){
        btnAdd.classList.toggle('hidden',!TABS[active].hasAdd);
        if(TABS[active].url) btnAdd.href=TABS[active].url;
        if(btnLabel) btnLabel.textContent=active==='pfe'?'Ajouter une note PFE':'Ajouter une note';
    }
    /* Re-sync hover after tab switch */
    requestAnimationFrame(()=>bindAllHover());
};
const initialTab=@json(request('tab','cc'));
if(TABS[initialTab]) window.switchTab(initialTab);

/* ── ROW HOVER SYNC (left + middle + right) ── */
function bindHoverGroup(rows){
    /* rows = array of arrays: [[left_el, mid_el, right_el], ...] */
    rows.forEach(group=>{
        const on =()=>group.forEach(el=>el?.classList.add('row-hover'));
        const off=()=>group.forEach(el=>el?.classList.remove('row-hover'));
        group.forEach(el=>{ if(!el) return; el.addEventListener('mouseenter',on); el.addEventListener('mouseleave',off); });
    });
}

function bindAllHover(){
    /* CC - per module */
    document.querySelectorAll('.acc-mod').forEach(mod=>{
        const fl=[...mod.querySelectorAll('.cc-frozen-left')];
        const sc=[...mod.querySelectorAll('.cc-row')];
        const fr=[...mod.querySelectorAll('.cc-frozen-right')];
        const groups=fl.map((_,i)=>[fl[i],sc[i],fr[i]]);
        bindHoverGroup(groups);
    });
    /* standalone panels */
    [['exam-frozen-left','exam-row','exam-frozen-right'],
     ['pfe-frozen-left','pfe-row',null],
     ['res-frozen-left','res-row','res-frozen-right']].forEach(([lc,mc,rc])=>{
        const fl=[...document.querySelectorAll('.'+lc)];
        const sc=[...document.querySelectorAll('.'+mc)];
        const fr=rc?[...document.querySelectorAll('.'+rc)]:[];
        const groups=fl.map((_,i)=>[fl[i],sc[i],fr[i]||null]);
        bindHoverGroup(groups);
    });
}
bindAllHover();

/* ── ACCORDION ── */
window.toggleAcc=function(header){ header.closest('.acc-unit').classList.toggle('open'); };
window.toggleModAcc=function(header){ header.closest('.acc-mod').classList.toggle('open'); };

/* ── FILTER CC ── */
function filterCC(){
    const search =(document.getElementById('cc-search')?.value??'').toLowerCase();
    const filiere=(document.getElementById('cc-filiere')?.value??'').toLowerCase();
    const groupe =(document.getElementById('cc-groupe')?.value??'').toLowerCase();
    const module_=(document.getElementById('cc-module')?.value??'').toLowerCase();
    const unite_ =(document.getElementById('cc-unite')?.value??'').toLowerCase();
    const hasFilter=search||filiere||groupe||module_||unite_;
    let total=0;
    document.querySelectorAll('#cc-accordion .acc-unit').forEach(unit=>{
        if(unite_&&unit.dataset.unite!==unite_){unit.classList.add('hidden');return;}
        let unitVis=0;
        unit.querySelectorAll('.acc-mod').forEach(mod=>{
            if(module_&&mod.dataset.module!==module_){mod.classList.add('hidden');return;}
            mod.classList.remove('hidden');
            const fl=[...mod.querySelectorAll('.cc-frozen-left')];
            const sc=[...mod.querySelectorAll('.cc-row')];
            const fr=[...mod.querySelectorAll('.cc-frozen-right')];
            let modVis=0;
            fl.forEach((el,i)=>{
                const show=(!search||el.dataset.stagiaire.includes(search))
                        &&(!filiere||el.dataset.filiere===filiere)
                        &&(!groupe||el.dataset.groupe===groupe);
                el.classList.toggle('hidden',!show);
                sc[i]?.classList.toggle('hidden',!show);
                fr[i]?.classList.toggle('hidden',!show);
                if(show) modVis++;
            });
            mod.classList.toggle('hidden',modVis===0);
            if(hasFilter&&modVis>0) mod.classList.add('open');
            unitVis+=modVis;
        });
        unit.classList.toggle('hidden',unitVis===0);
        if(hasFilter&&unitVis>0) unit.classList.add('open');
        total+=unitVis;
    });
    document.getElementById('cc-no-result')?.classList.toggle('hidden',total>0);
}

/* ── FILTER GENERIC ── */
function filterRows(type){
    const get=id=>(document.getElementById(id)?.value??'').toLowerCase();
    const search =get(type+'-search');
    const filiere=get(type+'-filiere');
    const groupe =get(type+'-groupe');
    const extra  =type==='exam'?get('exam-unite'):'';
    const suffix ={exam:['exam-frozen-left','exam-row','exam-frozen-right'],pfe:['pfe-frozen-left','pfe-row',null],res:['res-frozen-left','res-row','res-frozen-right']};
    const [lc,mc,rc]=suffix[type]||[null,null,null];
    const fl=lc?[...document.querySelectorAll('.'+lc)]:[];
    const sc=mc?[...document.querySelectorAll('.'+mc)]:[];
    const fr=rc?[...document.querySelectorAll('.'+rc)]:[];
    let vis=0;
    fl.forEach((el,i)=>{
        const show=(!search||el.dataset.stagiaire.includes(search))
                &&(!filiere||el.dataset.filiere===filiere)
                &&(!groupe||el.dataset.groupe===groupe)
                &&(!extra||el.dataset.unite===extra);
        el.classList.toggle('hidden',!show);
        sc[i]?.classList.toggle('hidden',!show);
        fr[i]?.classList.toggle('hidden',!show);
        if(show) vis++;
    });
    const nr=document.getElementById(type+'-no-result');
    if(nr) nr.classList.toggle('hidden',vis>0||fl.length===0);
}

/* ── EVENTS ── */
['cc-search','cc-filiere','cc-groupe','cc-module','cc-unite'].forEach(id=>{
    const el=document.getElementById(id);
    if(el) ['input','change'].forEach(ev=>el.addEventListener(ev,filterCC));
});
[['exam'],['pfe'],['res']].forEach(([t])=>{
    ['search','filiere','groupe'].forEach(f=>{
        const el=document.getElementById(t+'-'+f);
        if(el) ['input','change'].forEach(ev=>el.addEventListener(ev,()=>filterRows(t)));
    });
});
const eu=document.getElementById('exam-unite');
if(eu) ['input','change'].forEach(ev=>eu.addEventListener(ev,()=>filterRows('exam')));

window.resetFilters=function(type){
    const map={cc:['cc-search','cc-filiere','cc-groupe','cc-module','cc-unite'],exam:['exam-search','exam-filiere','exam-groupe','exam-unite'],pfe:['pfe-search','pfe-filiere','pfe-groupe'],resultats:['res-search','res-filiere','res-groupe']};
    (map[type]??[]).forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
    if(type==='cc') filterCC(); else filterRows(type==='resultats'?'res':type);
};

})();

/* ── DELETE MODAL ── */
window.confirmDel=function(rowId,name,ccId){
    document.getElementById('del-modal-name').textContent=name;
    document.getElementById('del-modal-form').action='/formateur/notes/'+ccId;
    document.getElementById('del-modal-overlay').classList.remove('hidden');
};
window.closeDel=function(){
    document.getElementById('del-modal-overlay').classList.add('hidden');
};
document.addEventListener('keydown',function(e){if(e.key==='Escape') closeDel();});
</script>

@endsection