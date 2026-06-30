@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Mon compte</div>
        <div class="page-subtitle">
            Informations personnelles et sécurité
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- INFORMATIONS --}}
{{-- ========================= --}}
<div class="c-card" style="margin-bottom:20px;">

    <div class="c-card-header">

        <span class="c-card-title">

            <svg width="16" height="16"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 style="display:inline;vertical-align:middle;margin-right:6px;">

                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>

            </svg>

            Informations du compte

        </span>

    </div>

    <div class="c-card-body">

        <div style="
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
        ">

            {{-- NOM --}}
            <div>

                <div class="ifsi-label">
                    Nom
                </div>

                <div style="
                    margin-top:6px;
                    padding:12px 14px;
                    background:#f8fafc;
                    border:1px solid #e2e8f0;
                    border-radius:12px;
                    color:#0f172a;
                    font-weight:500;
                ">
                    {{ $user->nom }}
                </div>

            </div>

            {{-- PRENOM --}}
            <div>

                <div class="ifsi-label">
                    Prénom
                </div>

                <div style="
                    margin-top:6px;
                    padding:12px 14px;
                    background:#f8fafc;
                    border:1px solid #e2e8f0;
                    border-radius:12px;
                    color:#0f172a;
                    font-weight:500;
                ">
                    {{ $user->prenom }}
                </div>

            </div>

            {{-- EMAIL --}}
            <div style="grid-column: span 2;">

                <div class="ifsi-label">
                    Email
                </div>

                <div style="
                    margin-top:6px;
                    padding:12px 14px;
                    background:#f8fafc;
                    border:1px solid #e2e8f0;
                    border-radius:12px;
                    color:#0f172a;
                    font-weight:500;
                ">
                    {{ $user->email }}
                </div>

            </div>

        </div>

        {{-- MESSAGE --}}
        <div style="
            margin-top:20px;
            padding:14px 16px;
            border-radius:12px;
            background:#eff6ff;
            border:1px solid #bfdbfe;
            color:#1e3a8a;
            font-size:14px;
        ">
            Vous ne pouvez pas modifier les informations personnelles.
            Seul le mot de passe peut être changé.
        </div>

    </div>
</div>

{{-- ========================= --}}
{{-- MOT DE PASSE --}}
{{-- ========================= --}}
<div class="c-card">

    <div class="c-card-header">

        <span class="c-card-title">

            <svg width="16"
                 height="16"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 style="display:inline;vertical-align:middle;margin-right:6px;">

                <rect x="3"
                      y="11"
                      width="18"
                      height="11"
                      rx="2"
                      ry="2"/>

                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>

            </svg>

            Changer le mot de passe

        </span>

    </div>

    <div class="c-card-body">

        <form method="POST"
              action="{{ route($user->role . '.profile.password') }}">

            @csrf
            @method('PUT')

            <div style="
                display:grid;
                grid-template-columns:1fr 1fr;
                gap:16px;
            ">

                {{-- PASSWORD --}}
                <div>

                    <label class="ifsi-label">
                        Nouveau mot de passe
                    </label>

                    <input class="ifsi-input"
                           type="password"
                           name="password"
                           required>

                    @error('password')
                        <p style="
                            color:#b91c1c;
                            font-size:12px;
                            margin-top:4px;
                        ">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- CONFIRMATION --}}
                <div>

                    <label class="ifsi-label">
                        Confirmer le mot de passe
                    </label>

                    <input class="ifsi-input"
                           type="password"
                           name="password_confirmation"
                           required>

                </div>

            </div>

            <div style="margin-top:20px;">

                <button type="submit"
                        class="btn btn-primary">

                    <svg width="14"
                         height="14"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round">

                        <rect x="3"
                              y="11"
                              width="18"
                              height="11"
                              rx="2"
                              ry="2"/>

                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>

                    </svg>

                    Modifier le mot de passe

                </button>

            </div>

        </form>

    </div>

</div>

@endsection