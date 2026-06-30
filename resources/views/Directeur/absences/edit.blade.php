@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Modifier une absence</h2>
@endsection
@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-6">

        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('directeur.absences.index') }}"
                   class="text-slate-400 hover:text-slate-600 transition-colors duration-150 no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <span class="text-sm text-slate-400">Absences</span>
                <span class="text-sm text-slate-300">/</span>
                <span class="text-sm text-slate-600 font-medium">Modifier</span>
            </div>
            <div class="text-xl font-bold text-slate-800 tracking-tight">Modifier une absence</div>
            <div class="text-sm text-slate-500 mt-0.5">Mise à jour des informations de l'absence</div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <form action="{{ route('directeur.absences.update', $absence->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="p-6 space-y-5">

                    {{-- Stagiaire (lecture seule) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Stagiaire</label>
                        <select name="stagiaire_id" disabled
                                class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-400 bg-slate-50 cursor-not-allowed focus:outline-none">
                            @foreach($stagiaires as $s)
                                <option value="{{ $s->id }}" {{ $absence->stagiaire_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->nom }} {{ $s->prenom }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Le stagiaire ne peut pas être modifié.</p>
                    </div>

                    {{-- Date absence --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date de l'absence</label>
                        <input type="date" name="date_absence" value="{{ old('date_absence', $absence->date_absence) }}"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all @error('date_absence') border-red-400 bg-red-50 @enderror">
                        @error('date_absence') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Statut</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition-all duration-150
                                {{ old('statut', $absence->statut) === 'justifiee' ? 'border-green-400 bg-green-50' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                <input type="radio" name="statut" value="justifiee"
                                       {{ old('statut', $absence->statut) === 'justifiee' ? 'checked' : '' }}
                                       class="accent-green-600">
                                <div>
                                    <span class="flex items-center gap-1.5 text-sm font-semibold text-green-700">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Justifiée
                                    </span>
                                    <span class="text-xs text-slate-400">Absence avec justificatif</span>
                                </div>
                            </label>
                            <label class="relative flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition-all duration-150
                                {{ old('statut', $absence->statut) === 'injustifiee' ? 'border-red-400 bg-red-50' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                <input type="radio" name="statut" value="injustifiee"
                                       {{ old('statut', $absence->statut) === 'injustifiee' ? 'checked' : '' }}
                                       class="accent-red-600">
                                <div>
                                    <span class="flex items-center gap-1.5 text-sm font-semibold text-red-700">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Injustifiée
                                    </span>
                                    <span class="text-xs text-slate-400">Absence sans justificatif</span>
                                </div>
                            </label>
                        </div>
                        @error('statut') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Motif --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Motif</label>
                        <textarea name="motif" rows="4" placeholder="Décrivez le motif de l'absence..."
                                  class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all resize-none @error('motif') border-red-400 bg-red-50 @enderror">{{ old('motif', $absence->motif) }}</textarea>
                        @error('motif') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- Footer Actions --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('directeur.absences.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-100 hover:-translate-y-px transition-all duration-150 no-underline">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold shadow-sm hover:bg-slate-700 hover:-translate-y-px transition-all duration-150">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Enregistrer les modifications
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection