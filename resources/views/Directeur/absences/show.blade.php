@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Détail d'une absence</h2>
@endsection
@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-6">

        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('directeur.absences.index') }}"
                   class="text-slate-400 hover:text-slate-600 transition-colors duration-150 no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <span class="text-sm text-slate-400">Absences</span>
                <span class="text-sm text-slate-300">/</span>
                <span class="text-sm text-slate-600 font-medium">Détail</span>
            </div>
            <div class="text-xl font-bold text-slate-800 tracking-tight">Détail de l'absence</div>
            <div class="text-sm text-slate-500 mt-0.5">Informations complètes sur cette absence</div>
        </div>

        {{-- Detail Card --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- Stagiaire header --}}
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-blue-600">
                        {{ strtoupper(substr($absence->stagiaire->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($absence->stagiaire->nom ?? '', 0, 1)) }}
                    </span>
                </div>
                <div>
                    <div class="text-sm font-semibold text-slate-800">{{ $absence->stagiaire->nom ?? '—' }} {{ $absence->stagiaire->prenom ?? '' }}</div>
                    <div class="text-xs text-slate-400">Stagiaire</div>
                </div>
                <div class="ml-auto">
                    @if($absence->statut === 'justifiee')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 border border-green-100 text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Justifiée
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-100 text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            Injustifiée
                        </span>
                    @endif
                </div>
            </div>

            {{-- Fields --}}
            <div class="divide-y divide-slate-100">

                <div class="px-6 py-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Date d'absence</span>
                    <span class="text-sm font-medium text-slate-800">
                        {{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}
                    </span>
                </div>

                <div class="px-6 py-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Séance</span>
                    <span class="text-sm font-medium text-slate-800">
                        {{ $absence->seance ? \Carbon\Carbon::parse($absence->seance->date_seance)->format('d/m/Y') : '—' }}
                    </span>
                </div>

                <div class="px-6 py-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Type</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium
                        {{ $absence->type === 'seance' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-purple-50 text-purple-700 border border-purple-100' }}">
                        {{ ucfirst($absence->type) }}
                    </span>
                </div>

                <div class="px-6 py-4">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Motif</span>
                    <p class="text-sm text-slate-700 leading-relaxed">
                        {{ $absence->motif ?? 'Aucun motif renseigné.' }}
                    </p>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('directeur.absences.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-100 hover:-translate-y-px transition-all duration-150 no-underline">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Retour
                </a>
                <a href="{{ route('directeur.absences.edit', $absence->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold shadow-sm hover:bg-slate-700 hover:-translate-y-px transition-all duration-150 no-underline">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Modifier
                </a>
            </div>

        </div>

    </div>
</div>
@endsection