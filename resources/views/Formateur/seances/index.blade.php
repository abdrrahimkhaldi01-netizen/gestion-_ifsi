@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Mes séances</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Liste de vos séances planifiées</div>
    </div>
    <a href="{{ route('formateur.seances.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold shadow-md hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150 no-underline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter une séance
    </a>
</div>

{{-- Table Card --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Date</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Heure</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Module</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Groupe</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Type</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Statut</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Validation</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($seances as $seance)
            <tr class="hover:bg-[#f0f7fc] border-b border-slate-100 last:border-b-0 transition-colors duration-100">
                <td class="px-4 py-3 text-[13.5px] text-slate-800">
                    {{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3 text-[13px] text-[#5a8aaa]">
                    {{ $seance->heure_debut }} — {{ $seance->heure_fin }}
                </td>
                <td class="px-4 py-3 text-[13.5px] text-slate-800">
                    {{ $seance->module->titre ?? '—' }}
                </td>
                <td class="px-4 py-3 text-[13.5px] text-slate-800">
                    {{ $seance->groupe->nom ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        {{ ucfirst($seance->type ?? '—') }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @php
                        $badgeMap = [
                            'programmee' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'terminee'   => 'bg-green-50 text-green-700 border-green-200',
                            'annulee'    => 'bg-red-50 text-red-700 border-red-200',
                            'reportee'   => 'bg-orange-50 text-orange-700 border-orange-200',
                        ];
                        $labelMap = [
                            'programmee' => 'Programmée',
                            'terminee'   => 'Terminée',
                            'annulee'    => 'Annulée',
                            'reportee'   => 'Reportée',
                        ];
                        $cls = $badgeMap[$seance->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                        $lbl = $labelMap[$seance->status] ?? ucfirst($seance->status ?? '—');
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $cls }}">
                        {{ $lbl }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @if($seance->statut_validation === 'validee')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-50 text-green-700 border border-green-200">Validée</span>
                    @elseif($seance->statut_validation === 'refusee')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">Refusée</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">En attente</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($seance->statut_validation !== 'validee')
                        <div class="flex items-center gap-2">
                            <a href="{{ route('formateur.seances.edit', $seance) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Modifier
                            </a>
                            <form action="{{ route('formateur.seances.destroy', $seance) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette séance ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-100 hover:-translate-y-px transition-all duration-150 cursor-pointer font-['Inter']">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-xs text-slate-400 italic">Validée par directeur</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-12 px-4 text-slate-400">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2.5 block"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Aucune séance trouvée
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $seances->links() }}
</div>

@endsection
