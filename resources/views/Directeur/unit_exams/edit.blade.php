@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Modifier le poids</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">
            {{ $unitExam->unite->nom ?? 'Unité' }} - {{ ucfirst($unitExam->type) }}
        </div>
    </div>

    <form method="POST" action="{{ route('directeur.unit_exams.update', $unitExam) }}"
          class="bg-white border border-[#cde4f0] rounded-xl shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="poids" class="block text-sm font-semibold text-slate-700 mb-1">Poids</label>
            <input id="poids" name="poids" type="number" min="0" max="100" step="0.01"
                   value="{{ old('poids', $unitExam->poids) }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
            @error('poids')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="px-4 py-2 bg-[#1a3a5c] text-white rounded-lg text-sm font-semibold hover:bg-[#132d4a] transition-all">
                Enregistrer
            </button>
            <a href="{{ route('directeur.unit_exams.index') }}"
               class="px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all no-underline">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
