@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Gestion des groupes</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Liste de tous les groupes de formation</div>
    </div>
    <a href="{{ route('directeur.groupes.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150 no-underline">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter Groupe
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Table Card --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">

    <div class="flex items-center justify-between px-5 py-3.5 bg-[#f0f7fc] border-b border-[#cde4f0]">
        <span class="text-sm font-bold text-[#1a3a5c]">Tous les groupes</span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
            {{ $groupes->total() }} groupe{{ $groupes->total() > 1 ? 's' : '' }}
        </span>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="px-5 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Nom</th>
                <th class="px-5 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Niveau</th>
                <th class="px-5 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Filière</th>
                <th class="px-5 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupes as $groupe)
            <tr class="hover:bg-[#f0f7fc] border-b border-slate-100 last:border-b-0 transition-colors duration-100">

                <td class="px-5 py-3.5 text-[13.5px] font-semibold text-[#1a3a5c]">
                    {{ $groupe->nom }}
                </td>

                <td class="px-5 py-3.5">
                    @if($groupe->niveau)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $groupe->niveau->nom }}
                        </span>
                    @else
                        <span class="text-slate-300 text-sm">—</span>
                    @endif
                </td>

                <td class="px-5 py-3.5">
                    @if($groupe->niveau?->filiere)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $groupe->niveau->filiere->nom }}
                        </span>
                    @else
                        <span class="text-slate-300 text-sm">—</span>
                    @endif
                </td>

                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('directeur.groupes.edit', $groupe) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold hover:bg-blue-100 hover:-translate-y-px transition-all duration-150 no-underline">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Modifier
                        </a>

                        <form action="{{ route('directeur.groupes.destroy', $groupe) }}"
                              method="POST"
                              onsubmit="return confirm('Supprimer ce groupe ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-100 hover:-translate-y-px transition-all duration-150 cursor-pointer">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-12 px-4 text-slate-400">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2.5 block"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Aucun groupe enregistré
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

{{-- Pagination --}}
@if($groupes->hasPages())
<div class="mt-4">
    {{ $groupes->links() }}
</div>
@endif

@endsection