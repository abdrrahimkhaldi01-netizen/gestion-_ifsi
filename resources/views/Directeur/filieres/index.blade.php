@extends('layouts.app')

@section('header')
@endsection

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Filières</h1>
                <p class="text-sm text-gray-500 mt-0.5">Liste de toutes les filières</p>
            </div>
            <a href="{{ route('directeur.filieres.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#185FA5] text-white text-sm font-semibold hover:bg-[#0C447C] transition-colors no-underline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Ajouter une filière
            </a>
        </div>

        {{-- Flash success --}}
        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Table card --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="min-w-full" style="table-layout:fixed">
                <colgroup>
                    <col style="width:30%">
                    <col style="width:18%">
                    <col style="width:16%">
                    <col style="width:14%">
                    <col style="width:22%">
                </colgroup>
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Niveau</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Durée</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($filieres as $filiere)
                    <tr class="hover:bg-gray-50 transition-colors">
{{-- Titre --}}
<td class="px-5 py-3.5">
    <span class="text-sm font-medium text-gray-900">{{ $filiere->nom }}</span> {{-- ✅ مشي titre --}}
</td>

{{-- Type badge --}}
<td class="px-5 py-3.5">
    @php
        $typeMap = [
            'qualification'          => ['bg-purple-50 text-purple-800 border-purple-200', 'Qualification'],
            'technicien'             => ['bg-teal-50 text-teal-800 border-teal-200',       'Technicien'],
            'technicien_specialise'  => ['bg-amber-50 text-amber-800 border-amber-200',    'Tech. Spéc.'],
        ];
        $type   = $filiere->type ?? '';
        $tStyle = $typeMap[$type][0] ?? 'bg-gray-100 text-gray-700 border-gray-200';
        $tLabel = $typeMap[$type][1] ?? ucfirst(str_replace('_', ' ', $type));
    @endphp
    <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium border {{ $tStyle }}">
        {{ $tLabel }}
    </span>
</td>
{{-- Niveaux --}}
<td class="px-5 py-3.5">
    <div class="flex flex-wrap gap-1">

        @forelse($filiere->niveaux as $niveau)

            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                {{ $niveau->nom }}
            </span>

        @empty

            <span class="text-xs text-gray-400">
                Aucun niveau
            </span>

        @endforelse

    </div>
</td>

{{-- Durée --}}
<td class="px-5 py-3.5">
    <span class="text-sm text-gray-700">{{ $filiere->duree }} an{{ $filiere->duree > 1 ? 's' : '' }}</span>
</td>

                       

                        {{-- Actions --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('directeur.filieres.edit', $filiere) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-colors no-underline">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Modifier
                                </a>

                                <form action="{{ route('directeur.filieres.destroy', $filiere) }}" method="POST"
                                      onsubmit="return confirm('Supprimer la filière « {{ $filiere->nom }} » ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-xs font-medium text-red-700 hover:bg-red-100 hover:border-red-300 transition-colors cursor-pointer">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="9" y1="13" x2="15" y2="13"/>
                                </svg>
                                <p class="text-sm text-gray-400">Aucune filière enregistrée</p>
                                <a href="{{ route('directeur.filieres.create') }}"
                                   class="mt-1 text-xs font-medium text-blue-600 hover:text-blue-800 no-underline">
                                    Créer la première filière →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection