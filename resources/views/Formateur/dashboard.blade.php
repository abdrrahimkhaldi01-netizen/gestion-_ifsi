@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Tableau de bord</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Bienvenue, {{ auth()->user()->prenom }} {{ auth()->user()->nom }}</div>
    </div>
</div>

{{-- ===== STATS ===== --}}
<div class="grid grid-cols-3 gap-4 mb-6">

    {{-- Séances totales --}}
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:-translate-y-px transition-all duration-150">
        <div class="w-11 h-11 rounded-xl bg-[#ddeef8] flex items-center justify-center flex-shrink-0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a3a5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="text-[28px] font-bold text-[#1a3a5c] leading-none tracking-tight">{{ $seances->count() }}</div>
            <div class="text-[11px] font-semibold text-[#5a8aaa] uppercase tracking-wider mt-1">Séances totales</div>
        </div>
    </div>

    {{-- Séances à venir --}}
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:-translate-y-px transition-all duration-150">
        <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div>
            <div class="text-[28px] font-bold text-green-700 leading-none tracking-tight">
                {{ $seances->where('date_seance', '>=', today())->count() }}
            </div>
            <div class="text-[11px] font-semibold text-[#5a8aaa] uppercase tracking-wider mt-1">Séances à venir</div>
        </div>
    </div>

    {{-- Séances passées --}}
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:-translate-y-px transition-all duration-150">
        <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
        </div>
        <div>
            <div class="text-[28px] font-bold text-slate-600 leading-none tracking-tight">
                {{ $seances->where('date_seance', '<', today())->count() }}
            </div>
            <div class="text-[11px] font-semibold text-[#5a8aaa] uppercase tracking-wider mt-1">Séances passées</div>
        </div>
    </div>

</div>

{{-- ===== BOTTOM GRID ===== --}}
<div class="grid grid-cols-2 gap-5">

    {{-- Avancement des modules --}}
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-[#cde4f0]">
            <div class="w-8 h-8 rounded-lg bg-[#ddeef8] flex items-center justify-center flex-shrink-0">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1a3a5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-[#1a3a5c]">Avancement de mes modules</span>
        </div>
        <div class="p-5">
            @forelse($modules as $module)
                @php
                    $h  = $module->heuresValidees();
                    $av = $module->avancement();
                    $barColor = $av >= 100 ? 'bg-green-500'
                              : ($av >= 50  ? 'bg-[#4fa3d1]'
                              : ($av >= 25  ? 'bg-amber-400'
                              : 'bg-red-400'));
                    [$badgeCls, $badgeTxt] = $av >= 100
                        ? ['bg-green-50 text-green-700 border-green-200',  'Terminé']
                        : ($av >= 50
                        ? ['bg-[#ddeef8] text-[#1a3a5c] border-[#b8d4e8]', 'En cours']
                        : ($av > 0
                        ? ['bg-amber-50 text-amber-700 border-amber-200',  'Débuté']
                        : ['bg-slate-100 text-slate-500 border-slate-200', 'Non débuté']));
                @endphp
                <div class="mb-4 pb-4 border-b border-slate-100 last:border-0 last:mb-0 last:pb-0">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[13.5px] font-semibold text-[#1a3a5c]">{{ $module->titre }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $badgeCls }}">
                            {{ $badgeTxt }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2.5 mb-1">
                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500"
                                 style="width: {{ $av }}%"></div>
                        </div>
                        <span class="text-[11px] text-[#5a8aaa] whitespace-nowrap font-medium">
                            {{ number_format($h, 1) }}h / {{ $module->duree }}h &middot; {{ $av }}%
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400">Groupe : {{ $module->groupe->nom ?? '—' }}</p>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Aucun module assigné
                </div>
            @endforelse
        </div>
    </div>

    {{-- Dernières séances --}}
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-[#cde4f0]">
            <div class="w-8 h-8 rounded-lg bg-[#ddeef8] flex items-center justify-center flex-shrink-0">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1a3a5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-[#1a3a5c]">Dernières séances</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($seances->take(5) as $seance)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-[#f0f7fc] transition-colors duration-100">
                <div class="flex flex-col gap-1">
                    <span class="text-[13.5px] font-semibold text-[#1a3a5c]">{{ $seance->module->titre ?? '—' }}</span>
                    <span class="text-[11px] text-[#5a8aaa]">{{ $seance->groupe->nom ?? '—' }}</span>
                    {{-- Validation badge --}}
                    @if($seance->statut_validation === 'validee')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-50 text-green-700 border border-green-200 w-fit">Validée</span>
                    @elseif($seance->statut_validation === 'refusee')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200 w-fit">Refusée</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-50 text-orange-700 border border-orange-200 w-fit">En attente</span>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="text-[13px] font-semibold text-slate-700">
                        {{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}
                    </span>
                    <span class="text-[11px] text-[#5a8aaa]">{{ $seance->heure_debut }} — {{ $seance->heure_fin }}</span>
                    {{-- Status badge --}}
                    @if($seance->status === 'terminee')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-50 text-green-700 border border-green-200">Terminée</span>
                    @elseif($seance->status === 'annulee')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">Annulée</span>
                    @elseif($seance->status === 'reportee')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">Reportée</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#ddeef8] text-[#1a3a5c] border border-[#b8d4e8]">Programmée</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-slate-400">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Aucune séance
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
