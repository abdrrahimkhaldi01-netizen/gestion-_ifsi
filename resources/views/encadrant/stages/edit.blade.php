@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Modifier le stage</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Mettre à jour les informations du stage</div>
    </div>
    <a href="{{ route('encadrant.stages.index') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Retour
    </a>
</div>

{{-- Form Card --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden max-w-2xl">

    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-[#cde4f0]">
        <div class="w-8 h-8 rounded-lg bg-[#ddeef8] flex items-center justify-center flex-shrink-0">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1a3a5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-[#1a3a5c]">Informations du stage</span>
    </div>

    <div class="p-6">
        <form action="{{ route('encadrant.stages.update', $stage) }}" method="POST">
            @csrf @method('PUT')

            {{-- Entreprise --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Entreprise</label>
                <input type="text" name="entreprise"
                       value="{{ old('entreprise', $stage->entreprise) }}"
                       placeholder="Nom de l'entreprise"
                       class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('entreprise') border-red-400 @enderror">
                @error('entreprise') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Date début + Date fin --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Date début</label>
                    <input type="date" name="date_debut"
                           value="{{ old('date_debut', $stage->date_debut) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('date_debut') border-red-400 @enderror">
                    @error('date_debut') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Date fin</label>
                    <input type="date" name="date_fin"
                           value="{{ old('date_fin', $stage->date_fin) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('date_fin') border-red-400 @enderror">
                    @error('date_fin') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Stagiaire --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Stagiaire</label>
                <select name="stagiaire_id"
                        class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9 @error('stagiaire_id') border-red-400 @enderror">
                    <option value="">— Choisir un stagiaire —</option>
                    @foreach($stagiaires as $stagiaire)
                        <option value="{{ $stagiaire->id }}"
                            {{ old('stagiaire_id', $stage->stagiaire_id) == $stagiaire->id ? 'selected' : '' }}>
                            {{ $stagiaire->user->prenom }} {{ $stagiaire->user->nom }}
                        </option>
                    @endforeach
                </select>
                @error('stagiaire_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Groupe --}}
            <div class="mb-6">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Groupe</label>
                <select name="group_id"
                        class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9 @error('group_id') border-red-400 @enderror">
                    <option value="">— Choisir un groupe —</option>
                    @foreach($groupes as $groupe)
                        <option value="{{ $groupe->id }}"
                            {{ old('group_id', $stage->group_id) == $groupe->id ? 'selected' : '' }}>
                            {{ $groupe->nom }}
                        </option>
                    @endforeach
                </select>
                @error('group_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2.5">
                <a href="{{ route('encadrant.stages.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 transition-all duration-150 no-underline">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#4fa3d1] text-white text-sm font-semibold shadow-md hover:bg-[#3d8ab8] hover:-translate-y-px transition-all duration-150 cursor-pointer font-['Inter'] border-none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Modifier
                </button>
            </div>

        </form>
    </div>
</div>

@endsection