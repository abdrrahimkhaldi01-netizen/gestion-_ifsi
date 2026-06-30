@extends('layouts.app')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Mon compte</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Informations personnelles et sécurité</div>
    </div>
</div>

{{-- ========================= INFORMATIONS ========================= --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden mb-5">

    {{-- Card Header --}}
    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-[#cde4f0]">
        <div class="w-8 h-8 rounded-lg bg-[#ddeef8] flex items-center justify-center flex-shrink-0">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a3a5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-[#1a3a5c]">Informations du compte</span>
    </div>

    {{-- Card Body --}}
    <div class="p-6">

        {{-- Avatar + nom --}}
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
            <div class="w-14 h-14 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
            </div>
            <div>
                <div class="text-lg font-bold text-[#1a3a5c]">{{ $user->prenom }} {{ $user->nom }}</div>
                <div class="text-sm text-[#5a8aaa]">{{ ucfirst($user->role) }}</div>
            </div>
        </div>

        {{-- Info grid --}}
        <div class="grid grid-cols-2 gap-5 mb-5">

            {{-- Nom --}}
            <div>
                <div class="text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Nom</div>
                <div class="flex items-center gap-2.5 px-4 py-3 bg-[#f0f7fc] border border-[#cde4f0] rounded-lg">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#5a8aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span class="text-[14px] font-semibold text-[#1a3a5c]">{{ $user->nom }}</span>
                </div>
            </div>

            {{-- Prénom --}}
            <div>
                <div class="text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Prénom</div>
                <div class="flex items-center gap-2.5 px-4 py-3 bg-[#f0f7fc] border border-[#cde4f0] rounded-lg">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#5a8aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span class="text-[14px] font-semibold text-[#1a3a5c]">{{ $user->prenom }}</span>
                </div>
            </div>

            {{-- Email --}}
            <div class="col-span-2">
                <div class="text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">Email</div>
                <div class="flex items-center gap-2.5 px-4 py-3 bg-[#f0f7fc] border border-[#cde4f0] rounded-lg">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#5a8aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span class="text-[14px] font-semibold text-[#1a3a5c]">{{ $user->email }}</span>
                </div>
            </div>

        </div>

        {{-- Notice --}}
        <div class="flex items-start gap-3 px-4 py-3.5 rounded-lg bg-blue-50 border border-blue-200">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-0.5">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p class="text-[13px] text-blue-800">
                Vous ne pouvez pas modifier les informations personnelles. Seul le mot de passe peut être changé.
            </p>
        </div>

    </div>
</div>

{{-- ========================= MOT DE PASSE ========================= --}}
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">

    {{-- Card Header --}}
    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-[#cde4f0]">
        <div class="w-8 h-8 rounded-lg bg-[#ddeef8] flex items-center justify-center flex-shrink-0">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a3a5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-[#1a3a5c]">Changer le mot de passe</span>
    </div>

    {{-- Card Body --}}
    <div class="p-6">
        <form method="POST" action="{{ route($user->role . '.profile.password') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-5">

                {{-- Nouveau mot de passe --}}
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Nouveau mot de passe
                    </label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150 @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div>
                    <label class="block text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider mb-1.5">
                        Confirmer le mot de passe
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3 py-2.5 rounded-lg border-[1.5px] border-slate-200 bg-slate-50 text-[13.5px] text-slate-800 font-['Inter'] outline-none focus:border-[#4fa3d1] focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,163,209,0.12)] transition-all duration-150">
                </div>

            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#1a3a5c] text-white text-sm font-semibold shadow-md hover:bg-[#132d4a] hover:-translate-y-px transition-all duration-150 cursor-pointer font-['Inter'] border-none">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Modifier le mot de passe
            </button>

        </form>
    </div>

</div>

@endsection