@extends('layouts.app')
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Tableau de bord — Directeur
        </h2>
    @endsection
@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Page Title --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Vue d'ensemble</h1>
            <p class="text-sm text-slate-500 mt-0.5">Statistiques générales de l'établissement</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Formateurs --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-2xl font-bold text-slate-800 leading-none">{{ $stats['formateurs'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Formateurs</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0"></span>
            </div>

            {{-- Stagiaires --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-2xl font-bold text-slate-800 leading-none">{{ $stats['stagiaires'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Stagiaires</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></span>
            </div>

            {{-- Groupes --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-2xl font-bold text-slate-800 leading-none">{{ $stats['groupes'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Groupes</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-purple-500 flex-shrink-0"></span>
            </div>

            {{-- Modules --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-2xl font-bold text-slate-800 leading-none">{{ $stats['modules'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Modules</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 flex-shrink-0"></span>
            </div>

            {{-- Séances --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4338ca" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    {{-- ✅ تم التصحيح: seances → seances_total --}}
                    <p class="text-2xl font-bold text-slate-800 leading-none">{{ $stats['seances_total'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Séances</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 flex-shrink-0"></span>
            </div>

            {{-- Absences --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    {{-- ✅ تم التصحيح: absences → absences_total --}}
                    <p class="text-2xl font-bold text-slate-800 leading-none">{{ $stats['absences_total'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Absences</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></span>
            </div>

        </div>
    </div>
</div>

{{-- Analyse des absences --}}
<div class="max-w-7xl mx-auto px-6">
    <div class="mt-8 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Analyse des absences</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Classement mensuel par heures de séances, avec jours de stage séparés.</p>
                </div>

                <form method="GET" action="{{ route('directeur.dashboard') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="year" value="{{ $selectedAbsenceYear }}">

                    <select name="month" class="h-9 rounded-lg border-slate-200 text-sm text-slate-700">
                        @php
                            $months = [
                                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                            ];
                        @endphp
                        @foreach($months as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((int) $selectedAbsenceMonth === $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>

                    <select name="annee_scolaire_id" class="h-9 rounded-lg border-slate-200 text-sm text-slate-700">
                        <option value="">Année scolaire</option>
                        @foreach($anneesScolaires as $annee)
                            <option value="{{ $annee->id }}" @selected((string) ($absenceFilters['annee_scolaire_id'] ?? '') === (string) $annee->id)>{{ $annee->nom }}</option>
                        @endforeach
                    </select>

                    <select name="filiere_id" class="h-9 rounded-lg border-slate-200 text-sm text-slate-700">
                        <option value="">Filière</option>
                        @foreach($filieres as $filiere)
                            <option value="{{ $filiere->id }}" @selected((string) ($absenceFilters['filiere_id'] ?? '') === (string) $filiere->id)>{{ $filiere->nom }}</option>
                        @endforeach
                    </select>

                    <select name="groupe_id" class="h-9 rounded-lg border-slate-200 text-sm text-slate-700">
                        <option value="">Groupe</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" @selected((string) ($absenceFilters['groupe_id'] ?? '') === (string) $groupe->id)>{{ $groupe->nom }}</option>
                        @endforeach
                    </select>

                    <button class="h-9 px-4 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900 transition-colors">Filtrer</button>
                    <a href="{{ route('directeur.dashboard') }}" class="h-9 px-4 rounded-lg border border-slate-200 text-slate-600 text-sm font-semibold inline-flex items-center hover:bg-slate-50 transition-colors">Effacer</a>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-slate-500 font-semibold">#</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-semibold">Nom complet</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-semibold">CIN</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-semibold">Groupe</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-semibold">Filière</th>
                        <th class="text-center px-5 py-3 text-slate-500 font-semibold">seance_absence_hours</th>
                        <th class="text-center px-5 py-3 text-slate-500 font-semibold">stage_absence_days</th>
                        <th class="text-center px-5 py-3 text-slate-500 font-semibold">Statut d'alerte</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($absenceRows as $index => $row)
                        @php
                            $status = $row['alert_status'];
                            $statusLabels = [
                                'normal' => 'Normal',
                                'alert_required' => 'Alerte requise',
                                'sent' => 'Alerte envoyée',
                                'failed_no_phone' => 'Téléphone manquant',
                                'not_implemented' => 'Provider à configurer',
                            ];
                            $statusClasses = [
                                'normal' => 'bg-green-50 text-green-700 border-green-200',
                                'alert_required' => 'bg-red-50 text-red-700 border-red-200',
                                'sent' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'failed_no_phone' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'not_implemented' => 'bg-slate-50 text-slate-700 border-slate-200',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 font-semibold text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $row['full_name'] }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $row['cin'] ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $row['groupe']?->nom ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $row['filiere']?->nom ?? '-' }}</td>
                            <td class="px-5 py-3 text-center font-bold text-slate-800">{{ number_format((float) $row['seance_absence_hours'], 2) }}</td>
                            <td class="px-5 py-3 text-center font-bold text-slate-800">{{ $row['stage_absence_days'] }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                    {{ $statusLabels[$status] ?? $status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">Aucune absence trouvée pour ces filtres.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Séances par statut --}}
<div class="max-w-7xl mx-auto px-6">
    <div class="mt-8 mb-4">
        <h2 class="text-lg font-bold text-slate-800">Séances par statut</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['seances_validees'] }}</p>
                <p class="text-sm text-slate-500">Validées</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['seances_en_attente'] }}</p>
                <p class="text-sm text-slate-500">En attente</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['seances_refusees'] }}</p>
                <p class="text-sm text-slate-500">Refusées</p>
            </div>
        </div>
    </div>

    {{-- Séances en attente de validation --}}
    @if($seancesEnAttente->count())
    <div class="mt-8">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Séances en attente de validation</h2>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Date</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Module</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Formateur</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Groupe</th>
                        <th class="text-left px-5 py-3 text-slate-600 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($seancesEnAttente as $seance)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3 text-slate-700">{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-slate-700">{{ $seance->module->titre ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-700">{{ $seance->formateur->user->nom ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-700">{{ $seance->groupe->nom ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('directeur.seances.valider', $seance->id) }}" class="inline">
                                @csrf
                                <button class="px-3 py-1 bg-green-500 text-white rounded-lg text-xs font-medium hover:bg-green-600 transition-colors">
                                    Valider
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@endsection
