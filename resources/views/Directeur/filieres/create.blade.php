@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Ajouter une filière</h1>
                <p class="text-sm text-gray-500">Créer une nouvelle filière</p>
            </div>
            <a href="{{ route('directeur.filieres.index') }}"
               class="px-4 py-2 rounded-lg border bg-white text-sm text-gray-600 hover:bg-gray-50 no-underline">
                Retour
            </a>
        </div>

        <div class="bg-white border rounded-xl overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('directeur.filieres.store') }}">
                    @csrf

                    {{-- Nom ✅ مشي titre --}}
                    <div class="mb-4">
                        <label class="text-xs font-medium text-gray-500">Nom de la filière</label>
                        <input type="text" name="nom"
                               class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-50 text-sm @error('nom') border-red-400 @enderror"
                               value="{{ old('nom') }}"
                               placeholder="Ex: Infirmier, Technicien de laboratoire...">
                        @error('nom')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type ✅ enum صحيح --}}
                    <div class="mb-4">
                        <label class="text-xs font-medium text-gray-500">Type</label>
                        <select name="type"
                                class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-50 text-sm @error('type') border-red-400 @enderror">
                            <option value="">-- choisir --</option>
                            <option value="qualification"         {{ old('type') === 'qualification'         ? 'selected' : '' }}>Qualification</option>
                            <option value="technicien"            {{ old('type') === 'technicien'            ? 'selected' : '' }}>Technicien</option>
                            <option value="technicien_specialise" {{ old('type') === 'technicien_specialise' ? 'selected' : '' }}>Technicien spécialisé</option>
                            {{-- ❌ حذف licence_professionnelle --}}
                        </select>
                        @error('type')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Durée --}}
                    <div class="mb-6">
                        <label class="text-xs font-medium text-gray-500">Durée (ans)</label>
                        <input type="number" name="duree" min="1" max="5"
                               class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-50 text-sm @error('duree') border-red-400 @enderror"
                               value="{{ old('duree') }}"
                               placeholder="Ex: 2">
                        @error('duree')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ❌ حذف niveau -- ما كاينش في migration --}}

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('directeur.filieres.index') }}"
                           class="px-4 py-2 border rounded-lg bg-gray-50 text-sm text-gray-600 no-underline hover:bg-gray-100">
                            Annuler
                        </a>
                        <button type="submit"
                                class="px-5 py-2 bg-[#185FA5] text-white rounded-lg text-sm font-medium hover:bg-[#0C447C] transition-colors">
                            Enregistrer
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection