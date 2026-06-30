```php
@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
            Détails du stagiaire
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Consultation complète des notes et résultats
        </p>
    </div>

    <a href="{{ route('directeur.notes.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold shadow-sm hover:bg-slate-50 transition">

        <svg width="15" height="15" viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>

        Retour
    </a>

</div>

{{-- PROFILE CARD --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">

    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">

        <div>
            <h2 class="text-lg font-bold text-slate-800">
                {{ $note->stagiaire->nom ?? '—' }}
                {{ $note->stagiaire->prenom ?? '' }}
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                {{ $note->stagiaire->groupe?->nom ?? '—' }}
                •
                {{ $note->stagiaire->groupe?->niveau?->filiere?->nom ?? '—' }}
            </p>
        </div>

        @if($note->statut === 'validee')

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                Validée
            </span>

        @else

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                En attente
            </span>

        @endif

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-6">

        {{-- MODULE --}}
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">

            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-500 mb-2">
                Module
            </div>

            <div class="text-sm font-semibold text-slate-800">
                {{ $note->module->titre ?? '—' }}
            </div>

        </div>

        {{-- MOYENNE --}}
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">

            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-500 mb-2">
                Moyenne
            </div>

            <div>
                @if($note->moyenne)

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                        {{ $note->moyenne >= 10
                            ? 'bg-green-50 text-green-700 border border-green-200'
                            : 'bg-red-50 text-red-700 border border-red-200' }}">

                        {{ $note->moyenne }}/20

                    </span>

                @else

                    <span class="text-slate-400">—</span>

                @endif
            </div>

        </div>

    </div>

</div>

{{-- NOTES CARD --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">

    <div class="px-6 py-4 border-b border-slate-100">

        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">
            Notes obtenues
        </h3>

    </div>

    <div class="p-6">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            @foreach([
                'CC1' => $note->cc1,
                'CC2' => $note->cc2,
                'CC3' => $note->cc3,
                'Examen' => $note->examen_final,
            ] as $label => $value)

                <div class="rounded-xl border border-slate-100 bg-slate-50 p-5 text-center">

                    <div class="text-[11px] uppercase tracking-wider font-bold text-slate-500 mb-3">
                        {{ $label }}
                    </div>

                    <div class="text-2xl font-bold text-slate-800">

                        {{ $value ?? '—' }}

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>

{{-- INFORMATIONS --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    <div class="px-6 py-4 border-b border-slate-100">

        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">
            Informations supplémentaires
        </h3>

    </div>

    <div class="divide-y divide-slate-100">

        <div class="flex items-center justify-between px-6 py-4">

            <span class="text-sm text-slate-500">
                Date de soumission
            </span>

            <span class="text-sm font-medium text-slate-800">
                {{ $note->created_at?->format('d/m/Y H:i') }}
            </span>

        </div>

        @if($note->validee_at)

        <div class="flex items-center justify-between px-6 py-4">

            <span class="text-sm text-slate-500">
                Date de validation
            </span>

            <span class="text-sm font-medium text-slate-800">
                {{ \Carbon\Carbon::parse($note->validee_at)->format('d/m/Y H:i') }}
            </span>

        </div>

        @endif

    </div>

</div>

@endsection
```
