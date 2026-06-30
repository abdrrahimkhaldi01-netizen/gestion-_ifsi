@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Absences</h2>
@endsection

@section('content')
<div class="py-8">
<div class="max-w-7xl mx-auto px-6 space-y-6">

{{-- ══════════════════════════════════════
     STATS CARDS
══════════════════════════════════════ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach([
        ['Total absences',    $statsGlobales['total'],        'slate',  'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['Stagiaires à risque', $statsGlobales['a_risque'],   'red',    'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['Injustifiées',      $statsGlobales['injustifiees'], 'amber',  'M6 18L18 6M6 6l12 12'],
        ['Justifiées',        $statsGlobales['justifiees'],   'green',  'M5 13l4 4L19 7'],
    ] as [$label, $val, $color, $icon])
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-{{ $color }}-50 flex items-center justify-center flex-shrink-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="text-{{ $color }}-500">
                <path d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-bold text-{{ $color }}-600 leading-none">{{ $val }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ══════════════════════════════════════
     ANALYTICS — TOP ABSENTS
══════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ef4444"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-sm font-semibold text-slate-700">Analyse — Stagiaires à risque</span>
            <span class="ml-1 px-2 py-0.5 rounded-full bg-red-50 text-red-600 text-[10px] font-bold border border-red-100">
                seuil > {{ $seuil }} absences
            </span>
        </div>
        {{-- Bouton Bulk WhatsApp --}}
        @if($topAbsents->where('a_risque', true)->count() > 0)
       <form action="{{ route('directeur.absences.bulk-whatsapp') }}" method="POST" style="display:inline">
    @csrf
    <button type="submit"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#25D366] hover:bg-[#1ebe5d]
                   text-white text-xs font-semibold rounded-lg transition-colors duration-150 cursor-pointer">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        Alerter tous ({{ $topAbsents->where('a_risque', true)->count() }})
    </button>
</form>
        @endif
    </div>

    {{-- Mini-table analytics --}}
    <table class="w-full border-collapse">
        <thead>
            <tr>
                @foreach(['Stagiaire', 'Groupe', 'Total', 'Injustifiées', 'Taux injust.', 'Statut', 'WhatsApp'] as $h)
                <th class="px-4 py-2 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-100">
                    {{ $h }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($topAbsents as $row)
            @php $s = $row->stagiaire; @endphp
            <tr class="{{ $row->a_risque ? 'bg-red-50/60' : '' }} border-b border-slate-100 last:border-b-0">
                {{-- Stagiaire --}}
                <td class="px-4 py-2.5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full {{ $row->a_risque ? 'bg-red-100' : 'bg-blue-100' }}
                                    flex items-center justify-center flex-shrink-0">
                            <span class="text-[10px] font-bold {{ $row->a_risque ? 'text-red-600' : 'text-blue-600' }}">
                                {{ strtoupper(substr($s->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($s->nom ?? '', 0, 1)) }}
                            </span>
                        </div>
                        <span class="text-sm font-medium text-slate-800">{{ $s->nom ?? '—' }} {{ $s->prenom ?? '' }}</span>
                    </div>
                </td>
                {{-- Groupe --}}
                <td class="px-4 py-2.5">
                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100 text-[11px] font-medium">
                        {{ $s->groupe->nom ?? '—' }}
                    </span>
                </td>
                {{-- Total --}}
                <td class="px-4 py-2.5">
                    <span class="text-sm font-bold {{ $row->a_risque ? 'text-red-600' : 'text-slate-700' }}">
                        {{ $row->total_absences }}
                    </span>
                </td>
                {{-- Injustifiées --}}
                <td class="px-4 py-2.5 text-sm font-semibold text-amber-600">
                    {{ $row->injustifiees }}
                </td>
                {{-- Barre de taux --}}
                <td class="px-4 py-2.5">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-slate-100 rounded-full h-1.5 min-w-[60px]">
                            <div class="h-1.5 rounded-full {{ $row->taux > 60 ? 'bg-red-500' : ($row->taux > 30 ? 'bg-amber-400' : 'bg-green-400') }}"
                                 style="width: {{ $row->taux }}%"></div>
                        </div>
                        <span class="text-[11px] text-slate-400 min-w-[28px]">{{ $row->taux }}%</span>
                    </div>
                </td>
                {{-- Statut badge --}}
                <td class="px-4 py-2.5">
                    @if($row->a_risque)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-red-700 border border-red-100 text-[11px] font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> À risque
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-green-50 text-green-700 border border-green-100 text-[11px] font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> OK
                        </span>
                    @endif
                </td>
                {{-- WhatsApp --}}
                <td class="px-4 py-2.5">
                    @if(!empty($s->telephone))
                    @php
                        $tel  = '212' . ltrim($s->telephone, '0');
                        $msg  = urlencode("Bonjour {$s->prenom}, l'établissement a enregistré {$row->total_absences} absence(s) dont {$row->injustifiees} non justifiée(s). Merci de régulariser votre situation au plus vite.");
                    @endphp
                   <form action="{{ route('directeur.absences.whatsapp', $s->id) }}" method="POST">
    @csrf
    <button type="submit"
            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#25D366] hover:bg-[#1ebe5d]
                   text-white text-[11px] font-semibold rounded-lg transition-colors cursor-pointer">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        Envoyer
    </button>
</form>
                    @else
                        <span class="text-slate-300 text-[11px]">Pas de tél.</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════════════
     FILTRES
══════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-5 py-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Type</label>
            <select name="type" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">Tous</option>
                <option value="seance"  {{ request('type') === 'seance'  ? 'selected' : '' }}>Séance</option>
                <option value="stage"   {{ request('type') === 'stage'   ? 'selected' : '' }}>Stage</option>
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Statut</label>
            <select name="justifiee" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">Tous</option>
                <option value="1" {{ request('justifiee') === '1' ? 'selected' : '' }}>Justifiée</option>
                <option value="0" {{ request('justifiee') === '0' ? 'selected' : '' }}>Injustifiée</option>
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stagiaire</label>
            <select name="stagiaire_id" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">Tous</option>
                @foreach($stagiaires as $st)
                    <option value="{{ $st->id }}" {{ request('stagiaire_id') == $st->id ? 'selected' : '' }}>
                        {{ $st->nom }} {{ $st->prenom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition-colors cursor-pointer">
                Filtrer
            </button>
            @if(request()->hasAny(['type','justifiee','stagiaire_id']))
            <a href="{{ route('directeur.absences.index') }}"
               class="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-200 transition-colors no-underline">
                Réinitialiser
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ══════════════════════════════════════
     TABLE ABSENCES
══════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
        <span class="text-sm font-semibold text-slate-700">
            Liste des absences
            <span class="ml-1 text-slate-400 font-normal">({{ $absences->total() }})</span>
        </span>
        <a href="{{ route('directeur.absences.create') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700
                  text-white text-xs font-semibold rounded-lg transition-colors no-underline">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvelle absence
        </a>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr>
                @foreach(['Stagiaire','Date','Séance','Motif','Type','Statut','Actions'] as $h)
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">
                    {{ $h }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($absences as $absence)
            <tr class="hover:bg-slate-50 border-b border-slate-100 last:border-b-0 transition-colors duration-100">
                {{-- Stagiaire --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-blue-600">
                                {{ strtoupper(substr($absence->stagiaire->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($absence->stagiaire->nom ?? '', 0, 1)) }}
                            </span>
                        </div>
                        <span class="text-sm font-medium text-slate-800">
                            {{ $absence->stagiaire->nom ?? '—' }} {{ $absence->stagiaire->prenom ?? '' }}
                        </span>
                    </div>
                </td>
                {{-- Date --}}
                <td class="px-4 py-3 text-sm text-slate-500">
                    {{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}
                </td>
                {{-- Séance --}}
                <td class="px-4 py-3 text-sm text-slate-500">
                    {{ $absence->seance ? \Carbon\Carbon::parse($absence->seance->date_seance)->format('d/m/Y') : '—' }}
                </td>
                {{-- Motif --}}
                <td class="px-4 py-3 text-sm text-slate-500 max-w-[160px] truncate">
                    {{ $absence->motif ?? '—' }}
                </td>
                {{-- Type --}}
                <td class="px-4 py-3">
                    @if($absence->type === 'seance')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100 text-xs font-medium">Séance</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-100 text-xs font-medium">{{ ucfirst($absence->type) }}</span>
                    @endif
                </td>
                {{-- Statut — justifiee boolean --}}
                <td class="px-4 py-3">
                    @if($absence->justifiee)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-green-50 text-green-700 border border-green-100 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Justifiée
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-red-700 border border-red-100 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Injustifiée
                        </span>
                    @endif
                </td>
                {{-- Actions --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('directeur.absences.show', $absence->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Voir
                        </a>
                        <a href="{{ route('directeur.absences.edit', $absence->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Modifier
                        </a>
                        <form action="{{ route('directeur.absences.destroy', $absence->id) }}" method="POST"
                              onsubmit="return confirm('Supprimer cette absence ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-100 hover:-translate-y-px transition-all duration-150 cursor-pointer">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 px-4 text-slate-400">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1"
                         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                         class="mx-auto mb-2.5 block">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    Aucune absence trouvée
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($absences->hasPages())
    <div>{{ $absences->links() }}</div>
@endif

</div>
</div>

{{-- ══════════════════════════════════════
     JS — Bulk WhatsApp
══════════════════════════════════════ --}}
<script>
const atRisk = @json($atRiskJs);
function bulkWhatsApp() {
    if (!atRisk.length) return;
    if (!confirm(`Envoyer un message WhatsApp à ${atRisk.length} stagiaire(s) à risque ?`)) return;

    atRisk.forEach((s, i) => {
        if (!s.phone || s.phone === '212') return;
        const msg = encodeURIComponent(
            `Bonjour ${s.prenom}, l'établissement a enregistré ${s.total} absence(s) dont ${s.injust} non justifiée(s). Merci de régulariser votre situation au plus vite.`
        );
        setTimeout(() => window.open(`https://wa.me/${s.phone}?text=${msg}`, '_blank'), i * 700);
    });
}
</script>
@endsection