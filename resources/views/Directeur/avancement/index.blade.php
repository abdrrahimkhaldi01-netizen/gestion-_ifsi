@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Tableau de bord — Avancement</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Suivi de l'avancement des modules</div>
    </div>
</div>

{{-- Statistiques globales --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-5">
        <div class="text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-2">Modules</div>
        <div class="text-3xl font-bold text-[#1a3a5c]">{{ $stats['total_modules'] }}</div>
    </div>
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-5">
        <div class="text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-2">Séances Validées</div>
        <div class="text-3xl font-bold text-green-600">{{ $stats['seances_validees'] }}</div>
    </div>
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-5">
        <div class="text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-2">Heures Validées</div>
        <div class="text-3xl font-bold text-indigo-600">{{ number_format($stats['heures_validees'], 1) }}h</div>
    </div>
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-5">
        <div class="text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-2">Avancement Global</div>
        <div class="text-3xl font-bold text-yellow-500">{{ $stats['avancement_global'] }}%</div>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-6">
        <div class="text-sm font-bold text-[#1a3a5c] mb-4">Avancement par Module</div>
        <canvas id="avanChart" height="300"></canvas>
    </div>
    <div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-6">
        <div class="text-sm font-bold text-[#1a3a5c] mb-4">Séances par Statut Validation</div>
        <canvas id="statutChart" height="300"></canvas>
    </div>
</div>

{{-- Tableau détaillé --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Module</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Groupe</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Formateur</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Durée Prévue</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Heures Validées</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Avancement</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($modules as $module)
                @php
                    $h   = $module->heuresValidees();
                    $av  = $module->avancement();
                    $color = $av >= 100 ? 'bg-green-500'
                           : ($av >= 50  ? 'bg-blue-500'
                           : ($av >= 25  ? 'bg-yellow-400'
                           : 'bg-red-400'));
                    $badge = $av >= 100
                        ? ['bg-green-50 text-green-700 border-green-200',   'Terminé']
                        : ($av >= 50
                        ? ['bg-blue-50 text-blue-700 border-blue-200',      'En cours']
                        : ($av > 0
                        ? ['bg-yellow-50 text-yellow-700 border-yellow-200','Débuté']
                        : ['bg-slate-100 text-slate-500 border-slate-200',  'Non débuté']));
                @endphp
                <tr class="hover:bg-[#f0f7fc] border-b border-slate-100 last:border-b-0 transition-colors duration-100">
                    <td class="px-4 py-3 text-[13.5px] text-slate-800 font-medium">{{ $module->titre }}</td>
                    <td class="px-4 py-3 text-[13px] text-[#5a8aaa]">
                        {{ $module->groupes->pluck('nom')->join(', ') ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-[13px] text-[#5a8aaa]">{{ $module->formateur->user->nom ?? '—' }}</td>
                    <td class="px-4 py-3 text-[13px] text-slate-700">{{ $module->duree }}h</td>
                    <td class="px-4 py-3 text-[13px] text-slate-700">{{ number_format($h, 1) }}h</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-28 bg-slate-100 rounded-full h-2">
                                <div class="{{ $color }} h-2 rounded-full transition-all duration-300" style="width: {{ $av }}%"></div>
                            </div>
                            <span class="text-xs text-slate-600 font-medium">{{ $av }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $badge[0] }}">
                            {{ $badge[1] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('avanChart'), {
    type: 'bar',
    data: {
        labels: @json($modules->pluck('titre')),
        datasets: [{
            label: 'Avancement (%)',
            data: @json($modules->map(fn($m) => $m->avancement())),
            backgroundColor: @json($modules->map(fn($m) => $m->avancement() >= 100 ? '#22c55e' : ($m->avancement() >= 50 ? '#3b82f6' : ($m->avancement() >= 25 ? '#eab308' : '#f87171')))),
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        },
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById('statutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Validées', 'En attente', 'Refusées'],
        datasets: [{
            data: [
                {{ $stats['seances_validees'] }},
                {{ $stats['seances_en_attente'] }},
                {{ $stats['seances_refusees'] }}
            ],
            backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        cutout: '65%'
    }
});
</script>

@endsection
