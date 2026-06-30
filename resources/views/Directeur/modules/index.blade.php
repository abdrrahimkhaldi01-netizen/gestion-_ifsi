@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Modules</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Liste des modules de formation</div>
    </div>
    <a href="{{ route('directeur.modules.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold shadow-md hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150 no-underline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Ajouter un module
    </a>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- ✅ Filtres --}}
<form method="GET" action="{{ route('directeur.modules.index') }}" class="mb-5 flex flex-wrap items-center gap-3">

    {{-- Filière --}}
    <select name="filiere_id"
            class="border border-slate-200 rounded-lg px-3 py-2 text-[13px] text-slate-700 bg-white outline-none focus:border-[#4fa3d1] focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all">
        <option value="">Toutes les filières</option>
        @foreach($filieres as $filiere)
            <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                {{ $filiere->nom }}
            </option>
        @endforeach
    </select>

    {{-- Formateur --}}
    <select name="formateur_id"
            class="border border-slate-200 rounded-lg px-3 py-2 text-[13px] text-slate-700 bg-white outline-none focus:border-[#4fa3d1] focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all">
        <option value="">Tous les formateurs</option>
        @foreach($formateurs as $formateur)
            <option value="{{ $formateur->id }}" {{ request('formateur_id') == $formateur->id ? 'selected' : '' }}>
                {{ $formateur->user->nom }} {{ $formateur->user->prenom }}
            </option>
        @endforeach
    </select>

    <button type="submit"
            class="px-4 py-2 bg-[#1a3a5c] text-white rounded-lg text-[13px] font-semibold hover:bg-[#132d4a] transition-all">
        Filtrer
    </button>

    @if(request('filiere_id') || request('formateur_id'))
        <a href="{{ route('directeur.modules.index') }}"
           class="px-4 py-2 border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-all no-underline">
            Réinitialiser
        </a>
    @endif

</form>

{{-- Table Card --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Titre</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Filière / Unité</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Formateur</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Groupes</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Durée</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">
    CC
</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Avancement</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($modules as $module)
                @php
                    $heuresValidees = $module->heuresValidees();
                    $avancement     = $module->avancement();
                    $color = $avancement >= 100 ? 'bg-green-500'
                           : ($avancement >= 50  ? 'bg-blue-500'
                           : ($avancement >= 25  ? 'bg-yellow-400'
                           : 'bg-red-400'));
                @endphp
                <tr class="hover:bg-[#f0f7fc] border-b border-slate-100 last:border-b-0 transition-colors duration-100">

                    {{-- Titre --}}
                    <td class="px-4 py-3 text-[13.5px] text-slate-800 font-medium">
                        {{ $module->titre }}
                    </td>

                    {{-- ✅ Filière + Unité عبر العلاقة الصحيحة --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[13px] font-medium text-slate-700">
                                {{ $module->unite->niveau->filiere->nom ?? '—' }}
                            </span>
                            <span class="text-[11px] text-[#5a8aaa]">
                                {{ $module->unite->nom ?? '—' }}
                            </span>
                        </div>
                    </td>

                    {{-- Formateur --}}
                    <td class="px-4 py-3 text-[13px] text-[#5a8aaa]">
                        {{ optional($module->formateur->user)->nom ?? '—' }}
                        {{ optional($module->formateur->user)->prenom ?? '' }}
                    </td>

                    {{-- ✅ Groupes many-to-many --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse($module->groupes as $groupe)
                                <span class="inline-flex px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $groupe->nom }}
                                </span>
                            @empty
                                <span class="text-[13px] text-slate-400">—</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Durée --}}
                    <td class="px-4 py-3 text-[13px] text-slate-700">
                        {{ $module->duree ? $module->duree.'h' : '—' }}
                    </td>
                    <td class="px-4 py-3 text-[13px] text-slate-700">
    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
        {{ $module->nombre_cc }} 
    </span>
</td>

                    {{-- Avancement --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-24 bg-slate-100 rounded-full h-2">
                                <div class="{{ $color }} h-2 rounded-full transition-all duration-300"
                                     style="width: {{ min($avancement, 100) }}%"></div>
                            </div>
                            <span class="text-xs text-slate-500 whitespace-nowrap">
                                {{ number_format($heuresValidees, 1) }}h / {{ $module->duree }}h
                                <span class="font-semibold text-slate-700">({{ $avancement }}%)</span>
                            </span>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('directeur.modules.edit', $module) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Modifier
                            </a>
                            <form action="{{ route('directeur.modules.destroy', $module) }}" method="POST"
                                  onsubmit="return confirm('Supprimer le module « {{ $module->titre }} » ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-100 hover:-translate-y-px transition-all duration-150 cursor-pointer">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 px-4 text-slate-400">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2.5 block">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                        Aucun module trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $modules->links() }}
</div>

@endsection
