@extends('layouts.app')
@section('content')

{{-- =====================================================================
     ANNÉES SCOLAIRES — INDEX  (Premium Tailwind Design)
====================================================================== --}}

<style>
    .row-active { background: linear-gradient(90deg, rgba(59,130,246,.04) 0%, transparent 100%); }
    .row-active td:first-child { border-left: 3px solid #3b82f6; }
    tr { transition: background .15s; }
    tbody tr:hover { background: #f8fafc; }
    .btn-icon { display:inline-flex; align-items:center; justify-content:center;
                width:30px; height:30px; border-radius:8px; border:none; cursor:pointer;
                transition: all .15s; }
    .btn-icon:hover { transform: scale(1.08); }
    .btn-icon.success { background:#f0fdf4; color:#16a34a; }
    .btn-icon.success:hover { background:#dcfce7; }
    .btn-icon.warning { background:#fffbeb; color:#d97706; }
    .btn-icon.warning:hover { background:#fef3c7; }
    .btn-icon.edit    { background:#eff6ff; color:#2563eb; }
    .btn-icon.edit:hover { background:#dbeafe; }
    .btn-icon.danger  { background:#fff1f2; color:#e11d48; }
    .btn-icon.danger:hover { background:#ffe4e6; }
    .inline-form { display:inline; }
</style>

<div class="space-y-5">

    {{-- ── PAGE HEADER ────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-[22px] font-bold text-slate-800 leading-tight">Années Scolaires</h1>
            <p class="text-[13px] text-slate-400 mt-0.5">
                {{ $annees->total() }} année(s) enregistrée(s)
            </p>
        </div>

        <a href="{{ route('directeur.annees_scolaires.create') }}"
           class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-[13px] font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all"
           style="background: linear-gradient(135deg,#3b82f6,#6366f1)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvelle Année
        </a>

    </div>

    {{-- ── STATS CARDS ─────────────────────────────────────────────── --}}
    @php
        $total    = $annees->total();
        $active   = $annees->getCollection()->where('statut','active')->count();
        $archived = $annees->getCollection()->where('statut','archivee')->count();
    @endphp

    <div class="grid grid-cols-3 gap-4">

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div class="text-[22px] font-bold text-slate-800 leading-none">{{ $total }}</div>
                <div class="text-[11.5px] text-slate-400 mt-0.5">Total années</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="text-[22px] font-bold text-slate-800 leading-none">{{ $active }}</div>
                <div class="text-[11.5px] text-slate-400 mt-0.5">Active(s)</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            </div>
            <div>
                <div class="text-[22px] font-bold text-slate-800 leading-none">{{ $archived }}</div>
                <div class="text-[11.5px] text-slate-400 mt-0.5">Archivée(s)</div>
            </div>
        </div>

    </div>

    {{-- ── TABLE CARD ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13px]">

                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-5 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase w-10">#</th>
                        <th class="text-left px-4 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Nom</th>
                        <th class="text-left px-4 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Date début</th>
                        <th class="text-left px-4 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Date fin</th>
                        <th class="text-center px-4 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Groupes</th>
                        <th class="text-center px-4 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Résultats</th>
                        <th class="text-left px-4 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Statut</th>
                        <th class="text-right px-5 py-3.5 text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">

                    @forelse($annees as $index => $annee)

                        <tr class="{{ $annee->isActive() ? 'row-active' : '' }}">

                            {{-- # --}}
                            <td class="px-5 py-3.5 text-slate-400 text-[12px]">
                                {{ $annees->firstItem() + $index }}
                            </td>

                            {{-- Nom --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    @if($annee->isActive())
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0 animate-pulse"></div>
                                    @endif
                                    <span class="font-semibold text-slate-800">{{ $annee->nom }}</span>
                                </div>
                            </td>

                            {{-- Date début --}}
                            <td class="px-4 py-3.5 text-slate-500">
                                {{ $annee->date_debut->format('d/m/Y') }}
                            </td>

                            {{-- Date fin --}}
                            <td class="px-4 py-3.5 text-slate-500">
                                {{ $annee->date_fin->format('d/m/Y') }}
                            </td>

                            {{-- Groupes --}}
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-lg text-[11.5px] font-semibold bg-blue-50 text-blue-600">
                                    {{ $annee->groupes_count }}
                                </span>
                            </td>

                            {{-- Résultats --}}
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-lg text-[11.5px] font-semibold bg-slate-100 text-slate-600">
                                    {{ $annee->resultats_count }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-3.5">
                                @if($annee->isActive())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11.5px] font-semibold bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @elseif($annee->isArchivee())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11.5px] font-semibold bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Archivée
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11.5px] font-semibold bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Planifiée
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- Activer --}}
                                    @if(!$annee->isActive())
                                        <form method="POST"
                                              action="{{ route('directeur.annees_scolaires.activate', $annee) }}"
                                              class="inline-form">
                                            @csrf
                                            <button type="submit" class="btn-icon success" title="Activer cette année">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Archiver --}}
                                    @if(!$annee->isArchivee())
                                        <form method="POST"
                                              action="{{ route('directeur.annees_scolaires.archive', $annee) }}"
                                              class="inline-form"
                                              onsubmit="return confirm('Archiver cette année scolaire ?')">
                                            @csrf
                                            <button type="submit" class="btn-icon warning" title="Archiver">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Modifier --}}
                                    <a href="{{ route('directeur.annees_scolaires.edit', $annee) }}"
                                       class="btn-icon edit" title="Modifier">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>

                                    {{-- Supprimer --}}
                                    @if(!$annee->groupes_count && !$annee->resultats_count)
                                        <form method="POST"
                                              action="{{ route('directeur.annees_scolaires.destroy', $annee) }}"
                                              class="inline-form"
                                              onsubmit="return confirm('Supprimer cette année scolaire ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon danger" title="Supprimer">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[13.5px] font-semibold text-slate-500">Aucune année scolaire créée</p>
                                        <p class="text-[12px] text-slate-400 mt-0.5">Commencez par créer la première année</p>
                                    </div>
                                    <a href="{{ route('directeur.annees_scolaires.create') }}"
                                       class="mt-1 flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12.5px] font-semibold text-white shadow-sm hover:opacity-90 transition-all"
                                       style="background: linear-gradient(135deg,#3b82f6,#6366f1)">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Créer la première année
                                    </a>
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($annees->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100">
                {{ $annees->links() }}
            </div>
        @endif

    </div>

</div>

@endsection
