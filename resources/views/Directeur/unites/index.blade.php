@extends('layouts.app')

@section('header')
@endsection

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Unités</h1>
                <p class="text-sm text-gray-500 mt-0.5">Gestion des unités pédagogiques</p>
            </div>
            <a href="{{ route('directeur.unites.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#185FA5] text-white text-sm font-semibold hover:bg-[#0C447C] transition-colors no-underline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouvelle Unité
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

        {{-- Filter --}}
        <form method="GET" action="{{ route('directeur.unites.index') }}" class="mb-5 flex items-center gap-3">
            <select name="filiere_id"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">Toutes les filières</option>
                @foreach($filieres as $filiere)
                    <option value="{{ $filiere->id }}"
                        {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                        {{ $filiere->nom }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors">
                Filtrer
            </button>

            @if(request('filiere_id'))
                <a href="{{ route('directeur.unites.index') }}"
                   class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors no-underline">
                    Réinitialiser
                </a>
            @endif
        </form>

        {{-- Table card --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="min-w-full" style="table-layout:fixed">
                <colgroup>
                    <col style="width:25%">
                    <col style="width:11%">
                    <col style="width:10%">
                    <col style="width:10%">
                    <col style="width:22%">
                    <col style="width:22%">
                </colgroup>
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Heures</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Coeff.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Filière / Niveau</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($unites as $unite)
                    <tr class="hover:bg-gray-50 transition-colors">

                        {{-- Nom --}}
                        <td class="px-5 py-3.5">
                            <span class="text-sm font-medium text-gray-900">{{ $unite->nom }}</span>
                        </td>

                        {{-- Code --}}
                        <td class="px-5 py-3.5">
                            @if($unite->code)
                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ $unite->code }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Heures --}}
                        <td class="px-5 py-3.5">
                            <span class="text-sm text-gray-700">{{ $unite->heures }}h</span>
                        </td>

                        {{-- Coefficient --}}
                        <td class="px-5 py-3.5">
                            <span class="text-sm text-gray-700">{{ $unite->coefficient }}</span>
                        </td>

                        {{-- Filière / Niveau ✅ niveau->filiere->nom --}}
                        <td class="px-5 py-3.5">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-xs font-medium text-gray-800">
                                    {{ $unite->niveau->filiere->nom ?? '—' }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $unite->niveau->nom ?? '—' }}
                                </span>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('directeur.unites.edit', $unite) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-colors no-underline">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Modifier
                                </a>

                                <form action="{{ route('directeur.unites.destroy', $unite) }}" method="POST"
                                      onsubmit="return confirm('Supprimer l\'unité « {{ $unite->nom }} » ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-xs font-medium text-red-700 hover:bg-red-100 hover:border-red-300 transition-colors cursor-pointer">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
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

                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="9" y1="13" x2="15" y2="13"/>
                                </svg>
                                <p class="text-sm text-gray-400">Aucune unité enregistrée</p>
                                <a href="{{ route('directeur.unites.create') }}"
                                   class="mt-1 text-xs font-medium text-blue-600 hover:text-blue-800 no-underline">
                                    Créer la première unité →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $unites->links() }}
        </div>

    </div>
</div>
@endsection
