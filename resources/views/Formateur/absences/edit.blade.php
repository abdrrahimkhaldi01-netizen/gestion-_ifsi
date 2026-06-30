@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Modifier une absence</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Mettre à jour les informations de l'absence</div>
    </div>
    <a href="{{ route('formateur.absences.index') }}"
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
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-[#1a3a5c]">Informations de l'absence</span>
    </div>

    <div class="p-6">
        <form action="{{ route('formateur.absences.update', $absence) }}" method="POST">
            @csrf @method('PUT')

            {{-- Date --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Date</label>
                <input type="date" name="date_absence"
                       value="{{ old('date_absence', $absence->date_absence) }}"
                       class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150">
            </div>

            {{-- Séance --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Séance</label>
                <select name="seance_id"
                        class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9">
                    <option value="">— Choisir une séance —</option>
                    @foreach($seances as $seance)
                        <option value="{{ $seance->id }}" {{ $absence->seance_id == $seance->id ? 'selected' : '' }}>
                            {{ $seance->date_seance }} — {{ $seance->groupe->nom ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Groupe --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Groupe</label>
                <select name="groupe_id" id="groupe_select"
                        class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9">
                    <option value="">— Choisir un groupe —</option>
                    @foreach($groupes as $groupe)
                        <option value="{{ $groupe->id }}" {{ $absence->stagiaire->group_id == $groupe->id ? 'selected' : '' }}>
                            {{ $groupe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Stagiaire --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Stagiaire</label>
                <select name="stagiaire_id" id="stagiaire_select"
                        class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 appearance-none bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235a8aaa' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\")] bg-no-repeat bg-[right_13px_center] pr-9">
                    <option value="{{ $absence->stagiaire_id }}">
                        {{ $absence->stagiaire->nom ?? '' }} {{ $absence->stagiaire->prenom ?? '' }}
                    </option>
                </select>
            </div>

            {{-- Justifiée --}}
            <div class="mb-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="justifiee" value="1"
                               {{ $absence->justifiee ? 'checked' : '' }}
                               class="peer w-4 h-4 rounded border-[1.5px] border-slate-300 bg-slate-50 accent-[#4fa3d1] cursor-pointer">
                    </div>
                    <span class="text-[13.5px] font-medium text-slate-700 group-hover:text-[#1a3a5c] transition-colors">
                        Absence justifiée
                    </span>
                </label>
            </div>

            {{-- Motif --}}
            <div class="mb-6">
                <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Motif</label>
                <textarea name="motif" rows="3"
                          class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 resize-y"
                          placeholder="Motif de l'absence (optionnel)...">{{ old('motif', $absence->motif) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2.5">
                <a href="{{ route('formateur.absences.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold hover:bg-slate-200 transition-all duration-150 no-underline">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#4fa3d1] text-white text-sm font-semibold shadow-md hover:bg-[#3d8ab8] hover:-translate-y-px transition-all duration-150 cursor-pointer font-['Inter'] border-none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Modifier
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    document.getElementById('groupe_select').addEventListener('change', function () {
        const groupeId = this.value;
        const stagiaireSelect = document.getElementById('stagiaire_select');

        stagiaireSelect.innerHTML = '<option value="">Chargement...</option>';

        if (!groupeId) {
            stagiaireSelect.innerHTML = "<option value=''>— Choisir d'abord un groupe —</option>";
            return;
        }

        fetch(`/formateur/stagiaires-by-groupe/${groupeId}`)
            .then(res => res.json())
            .then(stagiaires => {
                stagiaireSelect.innerHTML = '<option value="">— Choisir un stagiaire —</option>';
                stagiaires.forEach(s => {
                    stagiaireSelect.innerHTML += `<option value="${s.id}">${s.nom} ${s.prenom}</option>`;
                });
            });
    });
</script>

@endsection