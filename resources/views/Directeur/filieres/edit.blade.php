@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Modifier une filière</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $filiere->nom }}</p> {{-- ✅ مشي titre --}}
            </div>
            <a href="{{ route('directeur.filieres.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 no-underline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Retour
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#854F0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-800">Modifier les informations</span>
            </div>

            <div class="p-6">
                <form action="{{ route('directeur.filieres.update', $filiere) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Nom ✅ --}}
                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', $filiere->nom) }}"
                               placeholder="Ex : Infirmier"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm outline-none focus:border-amber-400 focus:bg-white focus:ring-2 focus:ring-amber-100 transition-all @error('nom') border-red-400 bg-red-50 @enderror">
                        @error('nom')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type ✅ enum صحيح --}}
                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Type</label>
                        <div class="relative">
                            <select name="type"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm outline-none focus:border-amber-400 focus:bg-white focus:ring-2 focus:ring-amber-100 transition-all appearance-none pr-9 @error('type') border-red-400 bg-red-50 @enderror">
                                <option value="">— Choisir —</option>
                                <option value="qualification"         {{ old('type', $filiere->type) == 'qualification'         ? 'selected' : '' }}>Qualification</option>
                                <option value="technicien"            {{ old('type', $filiere->type) == 'technicien'            ? 'selected' : '' }}>Technicien</option>
                                <option value="technicien_specialise" {{ old('type', $filiere->type) == 'technicien_specialise' ? 'selected' : '' }}>Technicien spécialisé</option>
                                {{-- ❌ حذف licence --}}
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Durée فقط ✅ --}}
                    <div class="mb-6">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Durée (ans)</label>
                        <input type="number" name="duree" value="{{ old('duree', $filiere->duree) }}"
                               min="1" max="10" placeholder="Ex : 2"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm outline-none focus:border-amber-400 focus:bg-white focus:ring-2 focus:ring-amber-100 transition-all @error('duree') border-red-400 bg-red-50 @enderror">
                        @error('duree')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ❌ حذف niveau كاملاً --}}

                    <div class="flex items-center justify-end gap-2.5 pt-5 border-t border-gray-100">
                        <a href="{{ route('directeur.filieres.index') }}"
                           class="px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm font-medium text-gray-600 hover:bg-gray-100 no-underline">
                            Annuler
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-amber-600 text-white text-sm font-semibold hover:bg-amber-700 transition-colors border-none cursor-pointer">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Enregistrer les modifications
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection