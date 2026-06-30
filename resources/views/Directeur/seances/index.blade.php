@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Gestion des séances</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Valider ou refuser les séances des formateurs</div>
    </div>
</div>

{{-- Alerts --}}
@foreach(['success' => 'green', 'info' => 'blue', 'warning' => 'yellow'] as $key => $color)
    @if(session($key))
        <div class="mb-4 px-4 py-3 rounded-lg bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 text-sm">
            {{ session($key) }}
        </div>
    @endif
@endforeach

{{-- Filtres --}}
<form method="GET" action="{{ route('directeur.seances.index') }}"
      class="mb-4 flex gap-3 items-center flex-wrap">

    <select name="groupe_id"
            class="px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-white text-[13px] text-slate-700 outline-none focus:border-[#4fa3d1] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9">
        <option value="">— Tous les groupes —</option>
        @foreach($groupes as $groupe)
            <option value="{{ $groupe->id }}" {{ request('groupe_id') == $groupe->id ? 'selected' : '' }}>
                {{ $groupe->nom }}
            </option>
        @endforeach
    </select>

    <select name="statut_validation"
            class="px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-white text-[13px] text-slate-700 outline-none focus:border-[#4fa3d1] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9">
        <option value="">— Tous les statuts —</option>
        <option value="en_attente"  {{ request('statut_validation') == 'en_attente'  ? 'selected' : '' }}>En attente</option>
        <option value="validee"     {{ request('statut_validation') == 'validee'     ? 'selected' : '' }}>Validée</option>
        <option value="refusee"     {{ request('statut_validation') == 'refusee'     ? 'selected' : '' }}>Refusée</option>
    </select>

    <button type="submit"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150 border-none cursor-pointer">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Filtrer
    </button>

    @if(request('groupe_id') || request('statut_validation'))
        <a href="{{ route('directeur.seances.index') }}"
           class="inline-flex items-center gap-1 text-sm text-red-400 hover:text-red-600 transition-colors no-underline">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Réinitialiser
        </a>
    @endif
</form>

{{-- Table Card --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Date</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Formateur</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Module</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Groupe</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Horaire</th>
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
                <td class="px-4 py-3 text-[13.5px] text-slate-800 font-medium">
                    {{ $seance->formateur->user->name ?? '—' }}
                </td>
                <td class="px-4 py-3 text-[13.5px] text-slate-800">
                    {{ $seance->module->titre ?? '—' }}
                </td>
                <td class="px-4 py-3 text-[13.5px] text-slate-800">
                    {{ $seance->groupe->nom ?? '—' }}
                </td>
                <td class="px-4 py-3 text-[13px] text-[#5a8aaa]">
                    {{ $seance->heure_debut }} — {{ $seance->heure_fin }}
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
                    <div class="flex items-center gap-1.5">
                        @if($seance->statut_validation !== 'validee')
                            <button type="button"
                                onclick="submitPost('{{ route('directeur.seances.valider', $seance->id) }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 text-xs font-semibold hover:bg-green-100 hover:-translate-y-px transition-all duration-150 cursor-pointer">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Valider
                            </button>
                        @endif
                        @if($seance->statut_validation !== 'refusee')
                            <button type="button"
                                onclick="submitPost('{{ route('directeur.seances.refuser', $seance->id) }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-100 hover:-translate-y-px transition-all duration-150 cursor-pointer">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Refuser
                            </button>
                        @endif
                        @if($seance->statut_validation !== 'en_attente')
                            <button type="button"
                                onclick="submitPost('{{ route('directeur.seances.reinitialiser', $seance->id) }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 cursor-pointer">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.87"/></svg>
                                Réinit.
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-12 px-4 text-slate-400">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2.5 block"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Aucune séance trouvée
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $seances->links() }}
</div>

<script>
function submitPost(url) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}
</script>

@endsection
