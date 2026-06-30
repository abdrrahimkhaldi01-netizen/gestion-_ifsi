{{-- Partial: filter bar --}}
{{-- Variables: type, color, filieres, groupes, extraId (nullable), extraLabel, extraOptions --}}

@php $fc = "focus:border-[#{{ $color }}] focus:shadow-[0_0_0_3px_rgba(0,0,0,0.08)]"; @endphp

<div class="relative flex-1 min-w-[180px]">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
    </svg>
    <input id="{{ $type }}-search" type="text" placeholder="Nom / CIN stagiaire..."
           class="w-full pl-8 pr-3 py-2 text-sm border border-slate-200 rounded-lg bg-white outline-none {{ $fc }} transition-all [appearance:textfield]">
</div>

<select id="{{ $type }}-filiere" class="px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white outline-none {{ $fc }} transition-all cursor-pointer">
    <option value="">Toutes les filières</option>
    @foreach($filieres as $f)
        <option value="{{ $f }}">{{ $f }}</option>
    @endforeach
</select>

<select id="{{ $type }}-groupe" class="px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white outline-none {{ $fc }} transition-all cursor-pointer">
    <option value="">Tous les groupes</option>
    @foreach($groupes as $g)
        <option value="{{ $g }}">{{ $g }}</option>
    @endforeach
</select>

@if(!empty($extraId))
<select id="{{ $extraId }}" class="px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white outline-none {{ $fc }} transition-all cursor-pointer">
    <option value="">{{ $extraLabel ?? 'Tous' }}</option>
    @foreach($extraOptions ?? [] as $opt)
        <option value="{{ $opt }}">{{ $opt }}</option>
    @endforeach
</select>
@endif

<button onclick="resetFilters('{{ $type }}')" class="px-3 py-2 text-xs text-slate-500 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition-colors">
    Réinitialiser
</button>