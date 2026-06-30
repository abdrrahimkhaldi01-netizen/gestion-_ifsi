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
{{-- ========================= WHATSAPP ========================= --}}
@if(auth()->user()->role === 'directeur')
<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden mt-5">

    {{-- Card Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#cde4f0]">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#25D366">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-[#1a3a5c]">WhatsApp</span>
            {{-- Status Badge --}}
            <span id="wa-status-badge" class="px-2 py-0.5 rounded-full text-[11px] font-bold border">
                ⏳ Vérification...
            </span>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="p-6">
        <div id="wa-connected" class="hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-green-700">WhatsApp connecté ✅</div>
                        <div class="text-xs text-slate-400">Le serveur fonctionne — envoi de messages activé</div>
                    </div>
                </div>
               <div class="flex items-center gap-2">
    <a href="http://localhost:3000/qr" target="_blank"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition-colors no-underline">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Gérer
    </a>
    <button onclick="disconnectWA()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200 text-xs font-semibold hover:bg-red-100 transition-colors cursor-pointer">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Déconnecter
    </button>
</div>
            </div>
        </div>

        <div id="wa-disconnected" class="hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </div>
                    <div>
                       <div class="text-sm font-bold text-red-700">WhatsApp déconnecté ❌</div>
<div class="text-xs text-slate-400">Scannez le QR Code pour vous connecter</div>
                    </div>
                </div>
                <a href="http://localhost:3000/qr" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#25D366] hover:bg-[#1ebe5d] text-white text-xs font-semibold transition-colors no-underline">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    Connecter WhatsApp
                </a>
            </div>
        </div>

        <div id="wa-loading">
            <div class="flex items-center gap-3 text-slate-400">
                <div class="w-4 h-4 border-2 border-slate-300 border-t-slate-600 rounded-full animate-spin"></div>
                <span class="text-sm">Vérification du statut...</span>
            </div>
        </div>
    </div>
</div>

<script>
async function checkWAStatus() {
    try {
        const res = await fetch('http://localhost:3000/status');
        const data = await res.json();

        document.getElementById('wa-loading').classList.add('hidden');

        if (data.ready) {
            document.getElementById('wa-connected').classList.remove('hidden');
            document.getElementById('wa-disconnected').classList.add('hidden');
            document.getElementById('wa-status-badge').className = 'px-2 py-0.5 rounded-full text-[11px] font-bold border bg-green-50 text-green-700 border-green-200';
            document.getElementById('wa-status-badge').textContent = '🟢 Connecté';
        } else {
            document.getElementById('wa-disconnected').classList.remove('hidden');
            document.getElementById('wa-connected').classList.add('hidden');
            document.getElementById('wa-status-badge').className = 'px-2 py-0.5 rounded-full text-[11px] font-bold border bg-red-50 text-red-700 border-red-200';
            document.getElementById('wa-status-badge').textContent = '🔴 Déconnecté';
        }
    } catch(e) {
        document.getElementById('wa-loading').classList.add('hidden');
        document.getElementById('wa-disconnected').classList.remove('hidden');
        document.getElementById('wa-status-badge').className = 'px-2 py-0.5 rounded-full text-[11px] font-bold border bg-red-50 text-red-700 border-red-200';
        document.getElementById('wa-status-badge').textContent = '🔴 Déconnecté';
    }
}

checkWAStatus();
setInterval(checkWAStatus, 5000);
async function disconnectWA() {
    if (!confirm('Voulez-vous vraiment déconnecter WhatsApp ?')) return;
    try {
        await fetch('http://localhost:3000/disconnect', { method: 'POST' });
        setTimeout(checkWAStatus, 2000);
    } catch(e) {
        alert('Erreur de connexion au serveur');
    }
}
</script>
@endif
@endsection