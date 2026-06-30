@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Semestres</h1>
            <p class="text-sm text-slate-500 mt-0.5">Gestion des semestres par année scolaire et niveau</p>
        </div>
        <a href="{{ route('directeur.semestres.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#1a3a5c] text-white text-[13px] font-semibold rounded-xl hover:bg-[#0f2942] transition-colors duration-150 no-underline shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter un semestre
        </a>
    </div>

    @if($semestres->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center py-16 text-center">
            <svg class="w-10 h-10 text-slate-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8" y2="18"/>
            </svg>
            <p class="text-slate-500 text-sm font-medium">Aucun semestre enregistré</p>
            <p class="text-slate-400 text-xs mt-1">Commencez par ajouter un semestre.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">#</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">Nom</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">Ordre</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">Niveau</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">Filière</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">Année scolaire</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">Statut</th>
                        <th class="text-right px-5 py-3 font-semibold text-slate-500 uppercase tracking-wide text-[11px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($semestres as $semestre)
                    <tr class="hover:bg-slate-50 transition-colors duration-100">
                        <td class="px-5 py-3 text-slate-400 font-mono text-[12px]">{{ $loop->iteration }}</td>

                        <td class="px-5 py-3 font-semibold text-slate-800">{{ $semestre->nom }}</td>

                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                {{ $semestre->ordre === 1 ? 'bg-blue-50 text-blue-700' : 'bg-violet-50 text-violet-700' }}">
                                S{{ $semestre->ordre }}
                            </span>
                        </td>

                        <td class="px-5 py-3 text-slate-600">{{ $semestre->niveau?->nom ?? '—' }}</td>

                        <td class="px-5 py-3 text-slate-500">{{ $semestre->niveau?->filiere?->nom ?? '—' }}</td>

                        <td class="px-5 py-3 text-slate-600">{{ $semestre->anneeScolaire?->nom ?? '—' }}</td>

                        <td class="px-5 py-3">
                            @php
                                $statut = $semestre->statut ?? 'inactif';
                                $badge = match($statut) {
                                    'ouvert'  => 'bg-green-50 text-green-700',
                                    'cloture' => 'bg-red-50 text-red-600',
                                    default   => 'bg-slate-100 text-slate-500',
                                };
                                $label = match($statut) {
                                    'ouvert'  => 'Ouvert',
                                    'cloture' => 'Clôturé',
                                    default   => 'Inactif',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">
                                {{ $label }}
                            </span>
                        </td>

                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('directeur.semestres.edit', $semestre) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[12px] font-medium text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 no-underline">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Modifier
                                </a>
                                <form method="POST" action="{{ route('directeur.semestres.destroy', $semestre) }}"
                                      onsubmit="return confirm('Supprimer ce semestre ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[12px] font-medium text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer border-none bg-transparent font-['Inter']">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-slate-400 mt-3 text-right">{{ $semestres->count() }} semestre(s) au total</p>
    @endif
</div>
@endsection