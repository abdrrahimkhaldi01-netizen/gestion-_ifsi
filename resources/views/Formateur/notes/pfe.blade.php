@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Notes PFE</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Saisie des notes de projet de fin d'études</div>
    </div>
    <a href="{{ route('formateur.notes.index') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-all duration-150">
        ← Retour aux notes
    </a>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('formateur.notes.pfe.store') }}" method="POST">
    @csrf

    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-[#f0f7fc] border-b border-[#cde4f0]">
                    @foreach(['Stagiaire', 'Groupe', 'Filière', 'Note PFE (/20)', 'Statut'] as $th)
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider">
                            {{ $th }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($stagiaires as $i => $stagiaire)
                <tr class="hover:bg-[#f0f7fc] border-b border-slate-100 last:border-b-0 transition-colors duration-100">
                    <input type="hidden" name="pfes[{{ $i }}][stagiaire_id]" value="{{ $stagiaire->id }}">

                    {{-- Nom --}}
                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                        {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                    </td>

                    {{-- Groupe --}}
                    <td class="px-4 py-3 text-slate-600">
                        {{ $stagiaire->groupe?->nom ?? '—' }}
                    </td>

                    {{-- Filière --}}
                    <td class="px-4 py-3 text-slate-600">
                        {{ $stagiaire->groupe?->niveau?->filiere?->nom ?? '—' }}
                    </td>

                    {{-- Note PFE --}}
                    <td class="px-4 py-3">
                        <input
                            type="number"
                            name="pfes[{{ $i }}][note]"
                            value="{{ $stagiaire->pfe?->note }}"
                            min="0" max="20" step="0.01"
                            placeholder="—"
                            class="w-24 px-3 py-1.5 rounded-lg border border-slate-200 text-sm text-slate-700
                                   focus:outline-none focus:ring-2 focus:ring-[#4fa3d1] focus:border-transparent
                                   @error('pfes.'.$i.'.note') border-red-400 @enderror">
                        @error('pfes.'.$i.'.note')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </td>

                    {{-- Statut --}}
                    <td class="px-4 py-3">
                        @if($stagiaire->pfe?->note !== null)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                ● Saisie
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-50 text-slate-400 border border-slate-200">
                                ○ En attente
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-slate-400">
                        Aucun stagiaire trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stagiaires->count() > 0)
    <div class="mt-4 flex justify-end">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold shadow-md hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            Enregistrer les notes PFE
        </button>
    </div>
    @endif

</form>

@endsection