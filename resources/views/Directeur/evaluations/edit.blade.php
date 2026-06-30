@extends('layouts.app')

@section('title', 'Modifier l\'Évaluation')

@section('content')
<div class="page-wrapper">

    <nav class="breadcrumb">
        <a href="{{ route('directeur.evaluations.index') }}">Évaluations</a>
        <span>›</span>
        <span>Modifier</span>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title">Modifier l'Évaluation</h1>
            <p class="page-subtitle">
                <span class="eval-name">{{ $evaluation->nom }}</span>
                @if($evaluation->module)
                    &nbsp;·&nbsp; {{ $evaluation->module->nom }}
                @endif
            </p>
        </div>
        <div class="header-meta">
            <div class="meta-pill">
                <span class="meta-label">Poids</span>
                <span class="meta-value">{{ $evaluation->poids }}%</span>
            </div>
            <div class="meta-pill">
                <span class="meta-label">Sur</span>
                <span class="meta-value">{{ $evaluation->note_sur }} pts</span>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('directeur.evaluations.update', $evaluation) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">

                {{-- Nom --}}
                <div class="form-group full">
                    <label for="nom" class="form-label">Nom de l'évaluation <span class="req">*</span></label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $evaluation->nom) }}"
                        placeholder="Ex: Contrôle continu 1"
                        class="form-input @error('nom') invalid @enderror" autofocus>
                    @error('nom') <p class="err">{{ $message }}</p> @enderror
                </div>

                {{-- Module --}}
                <div class="form-group full">
                    <label for="module_id" class="form-label">Module <span class="req">*</span></label>
                    <select id="module_id" name="module_id"
                        class="form-input form-select @error('module_id') invalid @enderror">
                        <option value="">— Sélectionner un module —</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}"
                                {{ old('module_id', $evaluation->module_id) == $module->id ? 'selected' : '' }}>
                                {{ $module->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('module_id') <p class="err">{{ $message }}</p> @enderror
                </div>

                {{-- Poids --}}
                <div class="form-group">
                    <label for="poids" class="form-label">
                        Poids (%) <span class="req">*</span>
                        <span class="label-hint">Importance relative</span>
                    </label>
                    <div class="input-suffix-wrap">
                        <input type="number" id="poids" name="poids"
                            value="{{ old('poids', $evaluation->poids) }}"
                            placeholder="Ex: 30" step="1" min="0" max="100"
                            class="form-input has-suffix @error('poids') invalid @enderror">
                        <span class="input-suffix">%</span>
                    </div>
                    @error('poids') <p class="err">{{ $message }}</p> @enderror
                </div>

                {{-- Note sur --}}
                <div class="form-group">
                    <label for="note_sur" class="form-label">
                        Notée sur <span class="req">*</span>
                        <span class="label-hint">Barème maximum</span>
                    </label>
                    <div class="input-suffix-wrap">
                        <input type="number" id="note_sur" name="note_sur"
                            value="{{ old('note_sur', $evaluation->note_sur) }}"
                            placeholder="Ex: 20" step="0.5" min="0"
                            class="form-input has-suffix @error('note_sur') invalid @enderror">
                        <span class="input-suffix">pts</span>
                    </div>
                    @error('note_sur') <p class="err">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="form-actions">
                <a href="{{ route('directeur.evaluations.index') }}" class="btn-cancel">Annuler</a>

                <form action="{{ route('directeur.evaluations.destroy', $evaluation) }}" method="POST"
                    onsubmit="return confirm('Supprimer définitivement cette évaluation ?')" style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                        Supprimer
                    </button>
                </form>

                <button type="submit" class="btn-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Sauvegarder
                </button>
            </div>
        </form>
    </div>

</div>

<style>
    .page-wrapper { padding: 2rem; max-width: 680px; margin: 0 auto; font-family: 'Segoe UI', system-ui, sans-serif; }

    .breadcrumb { display: flex; align-items: center; gap: .4rem; font-size: .82rem; color: #6b7280; margin-bottom: 1.2rem; }
    .breadcrumb a { color: #0f766e; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb span { color: #d1d5db; }
    .breadcrumb span:last-child { color: #6b7280; }

    .page-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; flex-wrap: wrap; }
    .page-title { font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 .3rem; }
    .page-subtitle { font-size: .875rem; color: #6b7280; margin: 0; }
    .eval-name { font-weight: 600; color: #374151; }

    .header-meta { display: flex; gap: .5rem; flex-shrink: 0; margin-top: .25rem; }
    .meta-pill { display: flex; flex-direction: column; align-items: center; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: .35rem .75rem; }
    .meta-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; font-weight: 600; }
    .meta-value { font-size: .9rem; font-weight: 700; color: #0f766e; }

    .form-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 2rem; box-shadow: 0 1px 6px rgba(0,0,0,.06); }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 2rem; }
    .form-group { display: flex; flex-direction: column; gap: .35rem; }
    .full { grid-column: 1 / -1; }

    .form-label { font-size: .82rem; font-weight: 600; color: #374151; display: flex; align-items: baseline; gap: .4rem; }
    .req { color: #dc2626; }
    .label-hint { font-size: .75rem; font-weight: 400; color: #9ca3af; }

    .form-input { padding: .65rem .85rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: .9rem; color: #111827; background: #fff; outline: none; width: 100%; box-sizing: border-box; font-family: inherit; transition: border-color .2s, box-shadow .2s; }
    .form-input:focus { border-color: #0f766e; box-shadow: 0 0 0 3px rgba(15,118,110,.12); }
    .form-input.invalid { border-color: #dc2626; }

    .form-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .7rem center; padding-right: 2.2rem; }

    .input-suffix-wrap { position: relative; }
    .input-suffix-wrap .form-input.has-suffix { padding-right: 2.8rem; }
    .input-suffix { position: absolute; right: .85rem; top: 50%; transform: translateY(-50%); font-size: .8rem; font-weight: 600; color: #9ca3af; pointer-events: none; }

    .err { color: #dc2626; font-size: .78rem; margin: 0; }

    .form-actions { display: flex; align-items: center; justify-content: flex-end; gap: .75rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; flex-wrap: wrap; }
    .btn-cancel { padding: .6rem 1.2rem; border-radius: 8px; font-size: .875rem; font-weight: 600; color: #6b7280; text-decoration: none; background: #f9fafb; border: 1px solid #e5e7eb; margin-right: auto; transition: background .15s; }
    .btn-cancel:hover { background: #f1f5f9; }
    .btn-danger { display: inline-flex; align-items: center; gap: .4rem; padding: .6rem 1.1rem; border-radius: 8px; font-size: .875rem; font-weight: 600; color: #dc2626; background: #fff; border: 1.5px solid #fca5a5; cursor: pointer; transition: all .15s; }
    .btn-danger:hover { background: #fef2f2; border-color: #dc2626; }
    .btn-submit { display: inline-flex; align-items: center; gap: .4rem; padding: .6rem 1.3rem; border-radius: 8px; font-size: .875rem; font-weight: 600; color: #fff; background: #0f766e; border: none; cursor: pointer; transition: background .2s; }
    .btn-submit:hover { background: #0d6460; }

    @media (max-width: 520px) {
        .form-grid { grid-template-columns: 1fr; }
        .full { grid-column: 1; }
        .btn-cancel { margin-right: 0; width: 100%; text-align: center; }
    }
</style>
@endsection