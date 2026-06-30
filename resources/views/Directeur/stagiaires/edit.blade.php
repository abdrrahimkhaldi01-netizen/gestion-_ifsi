@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Modifier un stagiaire</h2>
@endsection

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-6">

        {{-- Breadcrumb --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('directeur.stagiaires.index') }}"
                   class="text-slate-400 hover:text-slate-600 transition-colors duration-150 no-underline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <span class="text-sm text-slate-400">Stagiaires</span>
                <span class="text-sm text-slate-300">/</span>
                <span class="text-sm text-slate-600 font-medium">Modifier</span>
            </div>
            <div class="text-xl font-bold text-slate-800 tracking-tight">Modifier le stagiaire</div>
            <div class="text-sm text-slate-500 mt-0.5">
                <span class="font-medium text-slate-700">{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</span>
            </div>
        </div>

        {{-- Erreurs globales --}}
        @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                <div class="font-semibold mb-1 flex items-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Veuillez corriger les erreurs suivantes :
                </div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <form action="{{ route('directeur.stagiaires.update', $stagiaire) }}"
                  method="POST"
                  autocomplete="off"
                  novalidate
                  id="form-stagiaire">
                @csrf
                @method('PUT')

                {{-- بيانات الغروب لـ JavaScript --}}
                <script id="groupes-data" type="application/json">
                    {!! json_encode(
                        $groupes->map(fn($g) => [
                            'id'         => $g->id,
                            'nom'        => $g->nom,
                            'filiere_id' => $g->niveau?->filiere?->id ?? $g->filiere_id ?? null,
                        ])
                    ) !!}
                </script>

                <div class="p-6 space-y-5">

                    {{-- Section : Identité --}}
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Identité
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="nom" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Nom <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="nom" name="nom"
                                       value="{{ old('nom', $stagiaire->nom) }}"
                                       maxlength="255"
                                       class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('nom') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('nom')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div>
                                <label for="prenom" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Prénom <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="prenom" name="prenom"
                                       value="{{ old('prenom', $stagiaire->prenom) }}"
                                       maxlength="255"
                                       class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('prenom') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('prenom')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="date_naissance" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Date de naissance <span class="text-red-400">*</span>
                                </label>
                                <input type="date" id="date_naissance" name="date_naissance"
                                       value="{{ old('date_naissance', \Carbon\Carbon::parse($stagiaire->date_naissance)->format('Y-m-d')) }}"
                                       max="{{ now()->subYears(15)->format('Y-m-d') }}"
                                       min="1900-01-01"
                                       class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('date_naissance') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('date_naissance')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div>
                                <label for="cin" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    CIN <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="cin" name="cin"
                                       value="{{ old('cin', $stagiaire->cin) }}"
                                       maxlength="20"
                                       style="text-transform: uppercase;"
                                       class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 font-mono placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('cin') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('cin')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- Section : Contact --}}
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/></svg>
                            Contact
                        </div>

                        <div class="mb-4">
                            <label for="adresse" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Adresse</label>
                            <input type="text" id="adresse" name="adresse"
                                   value="{{ old('adresse', $stagiaire->adresse) }}"
                                   maxlength="500"
                                   placeholder="Ex: 12 Rue Hassan II, Casablanca"
                                   class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('adresse') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                            @error('adresse')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="telephone" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone"
                                       value="{{ old('telephone', $stagiaire->telephone) }}"
                                       placeholder="06XXXXXXXX" maxlength="20"
                                       class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('telephone') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('telephone')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div>
                                <label for="responsable_telephone" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tél. responsable</label>
                                <input type="tel" id="responsable_telephone" name="responsable_telephone"
                                       value="{{ old('responsable_telephone', $stagiaire->responsable_telephone) }}"
                                       placeholder="06XXXXXXXX" maxlength="20"
                                       class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('responsable_telephone') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('responsable_telephone')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- Section : Formation --}}
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
                            Formation
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Filière --}}
                            <div>
                                <label for="filiere_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Filière <span class="text-red-400">*</span>
                                </label>
                                <select id="filiere_id" name="filiere_id"
                                        class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('filiere_id') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                    <option value="">— Choisir une filière —</option>
                                    @foreach($filieres as $filiere)
                                        <option value="{{ $filiere->id }}"
                                            {{ old('filiere_id', $stagiaire->filiere_id) == $filiere->id ? 'selected' : '' }}>
                                            {{ $filiere->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('filiere_id')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Groupe --}}
                            <div>
                                <label for="group_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                    Groupe <span class="text-red-400">*</span>
                                </label>
                                <select id="group_id" name="group_id"
                                        class="w-full border rounded-xl px-3 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent transition-all {{ $errors->has('group_id') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                    <option value="">— Choisir un groupe —</option>
                                </select>
                                @error('group_id')
                                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">
                        <span class="text-red-400">*</span> Champs obligatoires
                    </span>
                    <div class="flex gap-3">
                        <a href="{{ route('directeur.stagiaires.index') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-100 hover:-translate-y-px transition-all duration-150 no-underline">
                            Annuler
                        </a>
                        <button type="submit" id="submit-btn"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold shadow-sm hover:bg-slate-700 hover:-translate-y-px transition-all duration-150 disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg id="submit-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <span id="submit-text">Enregistrer les modifications</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    const groupes      = JSON.parse(document.getElementById('groupes-data').textContent);
    const filieresSel  = document.getElementById('filiere_id');
    const groupesSel   = document.getElementById('group_id');

    // القيمة الحالية للغروب (old() أو قيمة الـ stagiaire)
    const currentGroupId = "{{ old('group_id', $stagiaire->group_id) }}";

    function filterGroupes(filiereId) {
        groupesSel.innerHTML = '';

        if (!filiereId) {
            groupesSel.disabled = true;
            groupesSel.innerHTML = '<option value="">— Choisir d\'abord une filière —</option>';
            return;
        }

        const filtered = groupes.filter(g => String(g.filiere_id) === String(filiereId));

        if (filtered.length === 0) {
            groupesSel.disabled = true;
            groupesSel.innerHTML = '<option value="">Aucun groupe disponible</option>';
            return;
        }

        groupesSel.disabled = false;
        groupesSel.innerHTML = '<option value="">— Choisir un groupe —</option>';

        filtered.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.nom;
            if (String(g.id) === String(currentGroupId)) opt.selected = true;
            groupesSel.appendChild(opt);
        });
    }

    // عند تغيير الفلير
    filieresSel.addEventListener('change', function () {
        filterGroupes(this.value);
    });

    // تهيئة عند تحميل الصفحة — يعرض الغروب المرتبط بالفلير الحالي
    filterGroupes(filieresSel.value);

    // CIN uppercase
    document.getElementById('cin').addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });

    // Anti double-submit
    document.getElementById('form-stagiaire').addEventListener('submit', function () {
        const btn  = document.getElementById('submit-btn');
        const icon = document.getElementById('submit-icon');
        const text = document.getElementById('submit-text');
        btn.disabled = true;
        icon.innerHTML = '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>';
        text.textContent = 'Enregistrement...';
    });
</script>
@endsection