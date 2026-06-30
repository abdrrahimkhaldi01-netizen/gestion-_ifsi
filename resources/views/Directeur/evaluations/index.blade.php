@extends('layouts.app')

@section('title', 'Gestion des Évaluations')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Évaluations (CC)</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $evaluations->total() }} évaluation(s)
            </p>
        </div>

        <a href="{{ route('directeur.evaluations.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle Évaluation
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Alert --}}
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        @if($evaluations->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm font-medium">Aucune évaluation trouvée</p>
            </div>
        @else

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3">CC</th>
                        <th class="px-4 py-3">Module</th>
                        <th class="px-4 py-3">Unité</th>
                        <th class="px-4 py-3">Filière</th>
                        <th class="px-4 py-3">Note /20</th>
                        <th class="px-4 py-3">Moyenne</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach($evaluations as $i => $evaluation)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">

                            {{-- # --}}
                            <td class="px-4 py-3 text-gray-400 font-medium">
                                {{ $i + 1 }}
                            </td>

                            {{-- CC NAME --}}
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $evaluation->nom }}
                            </td>

                            {{-- MODULE --}}
                            <td class="px-4 py-3 text-gray-600">
                                {{ $evaluation->module->titre ?? '—' }}
                            </td>

                            {{-- UNITE --}}
                            <td class="px-4 py-3 text-gray-600">
                                {{ $evaluation->module->unite->nom ?? '—' }}
                            </td>

                            {{-- FILIERE --}}
                            <td class="px-4 py-3 text-gray-600">
                                {{ $evaluation->module->unite->niveau->filiere->nom ?? '—' }}
                            </td>

                            {{-- NOTE --}}
                            <td class="px-4 py-3">
                                <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    / {{ $evaluation->note_sur }}
                                </span>
                            </td>

                            {{-- MOYENNE --}}
                            <td class="px-4 py-3">
                                @php
                                    $moyenne = $evaluation->notes->avg('note');
                                @endphp
                                @if($moyenne !== null)
                                    <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        {{ number_format($moyenne, 2) }} / {{ $evaluation->note_sur }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3">
                                @if($evaluation->validated ?? false)
                                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Validé
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                                        En attente
                                    </span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('directeur.evaluations.edit', $evaluation) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors duration-150"
                                       title="Modifier">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('directeur.evaluations.destroy', $evaluation) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Supprimer cette évaluation ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors duration-150"
                                                title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        @if($evaluations->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $evaluations->links() }}
            </div>
        @endif

        @endif
    </div>

</div>
@endsection