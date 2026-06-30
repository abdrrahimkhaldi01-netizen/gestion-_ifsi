@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Stagiaires</h2>
    </div>
@endsection

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="text-xl font-bold text-slate-800 tracking-tight">Stagiaires</div>
                <div class="text-sm text-slate-500 mt-0.5">
                    {{ $stagiaires->total() }} stagiaire(s) inscrit(s)
                </div>
            </div>
            <a href="{{ route('directeur.stagiaires.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-800 text-white text-sm font-semibold shadow-sm hover:bg-slate-700 hover:-translate-y-px transition-all duration-150 no-underline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Ajouter un stagiaire
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('directeur.stagiaires.index') }}"
              class="flex flex-wrap gap-3 mb-5">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Rechercher par nom ou CIN..."
                   class="flex-1 min-w-[200px] px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 bg-white">

            <select name="filiere_id"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 bg-white">
                <option value="">Toutes les filières</option>
                @foreach($filieres as $f)
                    <option value="{{ $f->id }}" @selected($filiere == $f->id)>{{ $f->nom }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 transition-colors">
                Filtrer
            </button>

            @if($search || $filiere)
                <a href="{{ route('directeur.stagiaires.index') }}"
                   class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-sm font-semibold hover:bg-slate-200 transition-colors no-underline">
                    Réinitialiser
                </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">Nom complet</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">CIN</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">Date naissance</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">Téléphone</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">Tél. responsable</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">Filière</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">Groupe</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stagiaires as $stagiaire)
                        <tr class="hover:bg-slate-50 border-b border-slate-100 last:border-b-0 transition-colors duration-100">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-blue-600">
                                            {{ strtoupper(substr($stagiaire->prenom, 0, 1)) }}{{ strtoupper(substr($stagiaire->nom, 0, 1)) }}
                                        </span>
                                    </div>
                                    <a href="{{ route('directeur.stagiaires.show', $stagiaire) }}"
                                       class="text-sm font-medium text-slate-800 hover:text-blue-600 transition-colors no-underline">
                                        {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500 font-mono">{{ $stagiaire->cin ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($stagiaire->date_naissance)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $stagiaire->telephone ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $stagiaire->responsable_telephone ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($stagiaire->filiere)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-xs font-medium border border-indigo-100">
                                        {{ $stagiaire->filiere->nom }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($stagiaire->groupe)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-xs font-medium">
                                        {{ $stagiaire->groupe->nom }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('directeur.stagiaires.edit', $stagiaire) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold hover:bg-slate-200 hover:-translate-y-px transition-all duration-150 no-underline">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Modifier
                                    </a>

                                    <button type="button"
                                            onclick="confirmDelete({{ $stagiaire->id }}, '{{ addslashes($stagiaire->nom . ' ' . $stagiaire->prenom) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-100 hover:-translate-y-px transition-all duration-150 cursor-pointer">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                        Supprimer
                                    </button>

                                    <form id="delete-form-{{ $stagiaire->id }}"
                                          action="{{ route('directeur.stagiaires.destroy', $stagiaire) }}"
                                          method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 px-4 text-slate-400">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" class="mx-auto mb-2.5 block"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Aucun stagiaire trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($stagiaires->hasPages())
            <div class="mt-5">
                {{ $stagiaires->links() }}
            </div>
        @endif

    </div>
</div>

{{-- Modal الحذف --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
            </div>
            <div>
                <div class="font-bold text-slate-800 text-sm">Confirmer la suppression</div>
                <div id="modal-name" class="text-xs text-slate-500 mt-0.5"></div>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">
            Cette action est irréversible. Êtes-vous sûr de vouloir supprimer ce stagiaire ?
        </p>
        <div class="flex justify-end gap-2">
            <button onclick="closeModal()"
                    class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-colors cursor-pointer">
                Annuler
            </button>
            <button id="confirm-delete-btn"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors cursor-pointer">
                Supprimer
            </button>
        </div>
    </div>
</div>

<script>
    let currentDeleteId = null;

    function confirmDelete(id, name) {
        currentDeleteId = id;
        document.getElementById('modal-name').textContent = name;
        document.getElementById('delete-modal').classList.remove('hidden');
        document.getElementById('delete-modal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('delete-modal').classList.add('hidden');
        document.getElementById('delete-modal').classList.remove('flex');
        currentDeleteId = null;
    }

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (currentDeleteId) {
            document.getElementById('delete-form-' + currentDeleteId).submit();
        }
    });

    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection