@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Nouvelle séance</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Ajouter une séance à votre planning</div>
    </div>
    <a href="{{ route(auth()->user()->role . '.seances.index') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Retour
    </a>
</div>

{{-- Form Card --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">

    {{-- Card Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#cde4f0]">
        <span class="text-sm font-bold text-[#1a3a5c]">Informations de la séance</span>
    </div>

    {{-- Card Body --}}
    <div class="p-6">
        <form action="{{ route(auth()->user()->role . '.seances.store') }}" method="POST">
            @csrf

            {{-- Ligne 1 : Date + Heure début + Heure fin --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Date</label>
                    <input type="date" name="date_seance"
                           value="{{ old('date_seance', date('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('date_seance') border-red-400 @enderror">
                    @error('date_seance')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Heure début</label>
                    <input type="time" name="heure_debut"
                           value="{{ old('heure_debut') }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('heure_debut') border-red-400 @enderror">
                    @error('heure_debut')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Heure fin</label>
                    <input type="time" name="heure_fin"
                           value="{{ old('heure_fin') }}"
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('heure_fin') border-red-400 @enderror">
                    @error('heure_fin')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ligne 2 : Module + Groupe --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Module</label>
                    <select name="module_id"
                            class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9 @error('module_id') border-red-400 @enderror">
                        <option value="">— Choisir un module —</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                {{ $module->titre }}
                            </option>
                        @endforeach
                    </select>
                    @error('module_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Groupe</label>
                    <select name="group_id"
                            class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9 @error('group_id') border-red-400 @enderror">
                        <option value="">— Choisir un groupe —</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" {{ old('group_id') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ligne 3 : Type + Statut --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Type</label>
                    <select name="type"
                            class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9">
                        @foreach(['cours' => 'Cours', 'td' => 'TD', 'tp' => 'TP', 'controle' => 'Contrôle', 'examen' => 'Examen', 'rattrapage' => 'Rattrapage'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Statut</label>
                    <select name="status"
                            class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9">
                        @foreach(['programmee' => 'Programmée', 'terminee' => 'Terminée', 'annulee' => 'Annulée', 'reportee' => 'Reportée'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-6">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 resize-y">{{ old('description') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2.5">
                <a href="{{ route(auth()->user()->role . '.seances.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 transition-all duration-150 no-underline">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold shadow-md hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150 cursor-pointer font-['Inter'] border-none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Enregistrer
                </button>
            </div>

        </form>
    </div>
</div>

@endsection