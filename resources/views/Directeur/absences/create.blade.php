@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Nouvelle absence</h2>
@endsection

@section('content')
<div class="py-8">
<div class="max-w-2xl mx-auto px-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="text-xl font-bold text-slate-800 tracking-tight">Enregistrer une absence</div>
            <div class="text-sm text-slate-500 mt-0.5">Remplir les informations de l'absence</div>
        </div>
        <a href="{{ route('directeur.absences.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-200 transition-colors no-underline">
            ← Retour
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">

        @if($errors->any())
        <div class="bg-red-50 border border-red-100 rounded-xl p-4">
            <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('directeur.absences.store') }}" class="space-y-5">
            @csrf

            {{-- Stagiaire --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    Stagiaire <span class="text-red-500">*</span>
                </label>
                <select name="stagiaire_id" required
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Sélectionner --</option>
                    @foreach($stagiaires as $st)
                        <option value="{{ $st->id }}" {{ old('stagiaire_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->nom }} {{ $st->prenom }}
                            @if($st->groupe) — {{ $st->groupe->nom }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    Date d'absence <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date_absence" value="{{ old('date_absence', date('Y-m-d')) }}" required
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    Type <span class="text-red-500">*</span>
                </label>
                <select name="type" required id="typeSelect" onchange="toggleFields()"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Sélectionner --</option>
                    <option value="seance" {{ old('type') === 'seance' ? 'selected' : '' }}>Séance</option>
                    <option value="stage"  {{ old('type') === 'stage'  ? 'selected' : '' }}>Stage</option>
                </select>
            </div>

            {{-- Séance (conditionnelle) --}}
            <div id="seanceField" class="hidden">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Séance</label>
                <select name="seance_id"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Sélectionner --</option>
                    @foreach($seances as $seance)
                        <option value="{{ $seance->id }}" {{ old('seance_id') == $seance->id ? 'selected' : '' }}>
                            {{ $seance->module->nom ?? '—' }} —
                            {{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Justifiée --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Statut</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="justifiee" value="0"
                               {{ old('justifiee', '0') === '0' ? 'checked' : '' }}
                               class="accent-red-500">
                        <span class="text-sm text-slate-700">Injustifiée</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="justifiee" value="1"
                               {{ old('justifiee') === '1' ? 'checked' : '' }}
                               class="accent-green-500">
                        <span class="text-sm text-slate-700">Justifiée</span>
                    </label>
                </div>
            </div>

            {{-- Motif --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Motif</label>
                <textarea name="motif" rows="3" maxlength="500"
                          placeholder="Motif de l'absence (optionnel)..."
                          class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 resize-none focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('motif') }}</textarea>
            </div>

            {{-- Formateur ID (hidden ou select selon ton setup) --}}
            {{-- Si le formateur est l'utilisateur connecté : --}}
            @if(auth()->user()->formateur)
                <input type="hidden" name="formateur_id" value="{{ auth()->user()->formateur->id }}">
            @else
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    Formateur <span class="text-red-500">*</span>
                </label>
                <input type="number" name="formateur_id" value="{{ old('formateur_id') }}"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100"
                       placeholder="ID du formateur">
            </div>
            @endif

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors cursor-pointer">
                    Enregistrer
                </button>
                <a href="{{ route('directeur.absences.index') }}"
                   class="px-6 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition-colors no-underline">
                    Annuler
                </a>
            </div>
        </form>
    </div>

</div>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('typeSelect').value;
    document.getElementById('seanceField').classList.toggle('hidden', type !== 'seance');
}
toggleFields();
</script>
@endsection