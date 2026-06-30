@extends('layouts.app')

@section('title', 'Nouvelle Évaluation')

@section('content')
<div class="page-wrapper">

    {{-- Header --}}
    <div class="page-header">
        <h1 class="page-title">Nouvelle Évaluation (CC Auto)</h1>
        <p class="page-subtitle">Les CC sont générés automatiquement selon le module.</p>
    </div>

    <div class="form-card">

        <form action="{{ route('directeur.evaluations.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                {{-- MODULE --}}
                <div class="form-group full">
                    <label class="form-label">Module <span class="req">*</span></label>

                    <select name="module_id" class="form-input @error('module_id') invalid @enderror" required>
                        <option value="">-- Sélectionner un module --</option>

                        @foreach($modules as $module)
                            <option value="{{ $module->id }}">
                                {{ $module->titre }}
                                @if($module->unite)
                                    — {{ $module->unite->titre ?? $module->unite->nom }}
                                @endif
                            </option>
                        @endforeach

                    </select>

                    @error('module_id')
                        <p class="err">{{ $message }}</p>
                    @enderror
                </div>

                {{-- INFO BOX --}}
                <div class="info-box full">
                    <strong>ℹ️ Système automatique :</strong>
                    <p>
                        Les contrôles continus (CC1, CC2, CC3...) sont générés automatiquement
                        selon la durée du module.
                    </p>
                </div>

                {{-- NOTE SUR --}}
                <div class="form-group full">
                    <label class="form-label">Note maximale <span class="req">*</span></label>

                    <div class="input-suffix-wrap">
                        <input type="number"
                               name="note_sur"
                               value="20"
                               min="1"
                               max="100"
                               class="form-input has-suffix @error('note_sur') invalid @enderror"
                               required>
                        <span class="input-suffix">pts</span>
                    </div>

                    @error('note_sur')
                        <p class="err">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="form-actions">
                <a href="{{ route('directeur.evaluations.index') }}" class="btn-cancel">
                    Annuler
                </a>

                <button type="submit" class="btn-submit">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>
</div>

<style>
.page-wrapper { padding: 2rem; max-width: 700px; margin: auto; font-family: 'Segoe UI'; }

.page-header { margin-bottom: 1.5rem; }
.page-title { font-size: 1.6rem; font-weight: 700; }
.page-subtitle { color: #6b7280; font-size: .9rem; }

.form-card { background: white; padding: 2rem; border-radius: 12px; border: 1px solid #e5e7eb; }

.form-grid { display: grid; gap: 1rem; }

.form-group { display: flex; flex-direction: column; gap: .4rem; }

.full { width: 100%; }

.form-label { font-weight: 600; font-size: .85rem; }

.form-input {
    padding: .6rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
}

.form-input:focus {
    border-color: #0f766e;
    outline: none;
}

.input-suffix-wrap { position: relative; }
.has-suffix { padding-right: 2.5rem; }

.input-suffix {
    position: absolute;
    right: .8rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.info-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: .8rem;
    border-radius: 8px;
    font-size: .85rem;
    color: #166534;
}

.form-actions {
    margin-top: 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: .7rem;
}

.btn-cancel {
    padding: .5rem 1rem;
    background: #f3f4f6;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
}

.btn-submit {
    padding: .5rem 1.2rem;
    background: #0f766e;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.btn-submit:hover {
    background: #0d5f5a;
}

.err { color: red; font-size: .8rem; }
</style>

@endsection