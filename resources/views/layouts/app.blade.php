<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IFSI — {{ config('app.name', 'Gestion de Formation') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen font-['Inter']">

@php
    $u    = auth()->user();
    $role = $u->role;

    $profileRoute = match($role) {
        'formateur' => route('formateur.profile.show'),
        'encadrant' => route('encadrant.profile.show'),
        'directeur' => route('directeur.profile.show'),
        default     => '#',
    };

    $sb = function(bool $a) {
        return 'flex items-center gap-2.5 w-full px-3 py-2.5 rounded-xl text-[13px] no-underline mb-0.5 transition-all duration-150 '
            . ($a ? 'bg-white/15 text-white font-semibold' : 'text-slate-300 font-normal hover:bg-white/10 hover:text-white');
    };

    $si = function(bool $a) {
        return 'w-[16px] h-[16px] flex-shrink-0 ' . ($a ? 'stroke-white' : 'stroke-slate-400');
    };

    $tb = function(bool $a) {
        return 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12.5px] no-underline transition-all duration-150 whitespace-nowrap '
            . ($a ? 'bg-blue-50 text-blue-800 font-semibold' : 'text-slate-500 font-medium hover:bg-slate-100 hover:text-slate-800');
    };

    $svgs = [
        'dashboard'  => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'calendar'   => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'notes'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'absences'   => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
        'users'      => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user'       => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'book'       => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
        'activity'   => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'stages'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>',
        'students'   => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
        'modules'    => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        'groups'     => '<circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><circle cx="17" cy="9" r="3"/><path d="M21 21v-2a3 3 0 0 0-2-2.83"/>',
        'unite'      => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/><line x1="12" y1="7" x2="12" y2="17"/>',
        'evaluation' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'niveau'     => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'semestre'   => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8" y2="18"/>',
        'pfe'        => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
        'exam'       => '<path d="M9 11l3 3L22 4"/><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/>',
        'annee'      => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    ];

    $sidebarItem = function(string $href, string $label, string $svgKey, bool $active) use ($sb, $si, $svgs) {
        $cls  = $sb($active);
        $iCls = $si($active);
        $path = $svgs[$svgKey];
        return <<<HTML
        <a href="{$href}" class="{$cls}">
            <svg class="{$iCls}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{$path}</svg>
            {$label}
        </a>
        HTML;
    };

    $topTab = function(string $href, string $svgKey, string $label, bool $active) use ($tb, $svgs) {
        $cls  = $tb($active);
        $path = $svgs[$svgKey];
        return <<<HTML
        <a href="{$href}" class="{$cls}">
            <svg class="w-[13px] h-[13px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{$path}</svg>
            {$label}
        </a>
        HTML;
    };
@endphp

{{-- ======================== SIDEBAR ======================== --}}
<aside class="fixed top-0 left-0 bottom-0 w-[210px] flex flex-col z-50"
       style="background: linear-gradient(180deg, #0f2942 0%, #1a3a5c 60%, #1e4570 100%);">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10 flex-shrink-0">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden bg-white/10 p-1">
    <img src="{{ asset('storage/logo/logo.png') }}" alt="IFSI"
         class="w-full h-full object-contain"
         style="filter: brightness(0) invert(1);"
         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <span style="display:none"
          class="w-full h-full flex items-center justify-center text-white font-bold text-sm">IF</span>
</div>
        <div>
            <div class="text-white font-bold text-[15px] leading-tight tracking-wide">IFSI</div>
            <div class="text-slate-400 text-[10.5px] leading-tight mt-0.5">Gestion de Formation</div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 py-3 px-2.5 overflow-y-auto"
         style="scrollbar-width:thin; scrollbar-color:#1e4570 transparent;">

        @if($role === 'directeur')
            {!! $sidebarItem(route('directeur.annees_scolaires.index'), 'Années scolaires', 'annee',      request()->routeIs('directeur.annees_scolaires*')) !!}
            {!! $sidebarItem(route('directeur.semestres.index'),        'Semestres',        'semestre',   request()->routeIs('directeur.semestres*')) !!}
             {!! $sidebarItem(route('directeur.unites.index'), 'Unités', 'unite', request()->routeIs('directeur.unites*')) !!}
            {!! $sidebarItem(route('directeur.seances.index'),          'Séances',          'calendar',   request()->routeIs('directeur.seances*')) !!}
            {!! $sidebarItem(route('directeur.notes.index'),            'Les notes',        'notes',      request()->routeIs('directeur.notes*')) !!}
            {!! $sidebarItem(route('directeur.absences.index'),         'Absences',         'absences',   request()->routeIs('directeur.absences*')) !!}
            {!! $sidebarItem(route('directeur.avancement.index'),       'Avancement',       'activity',   request()->routeIs('directeur.avancement*')) !!}
            
        
        @elseif($role === 'formateur')
            {!! $sidebarItem(route('formateur.dashboard'),       'Tableau de bord', 'dashboard', request()->routeIs('formateur.dashboard')) !!}
            {!! $sidebarItem(route('formateur.seances.index'),   'Séances',         'calendar',  request()->routeIs('formateur.seances*')) !!}
            {!! $sidebarItem(route('formateur.notes.index'),     'Les notes',       'notes',     request()->routeIs('formateur.notes*')) !!}
            {!! $sidebarItem(route('formateur.absences.index'),  'Absences',        'absences',  request()->routeIs('formateur.absences*')) !!}
            {!! $sidebarItem(route('formateur.profile.show'),    'Mon compte',      'user',      request()->routeIs('formateur.profile*')) !!}

        @elseif($role === 'encadrant')
            {!! $sidebarItem(route('encadrant.stages.index'),    'Stages',          'stages',    request()->routeIs('encadrant.stages*')) !!}
            {!! $sidebarItem(route('encadrant.profile.show'),    'Mon compte',      'user',      request()->routeIs('encadrant.profile*')) !!}
        @endif

    </nav>

    {{-- Logout --}}
    <div class="p-3 border-t border-white/10 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2.5 w-full px-3 py-2 rounded-xl text-[12.5px] text-red-400 hover:bg-red-500/10 transition-all duration-150 cursor-pointer border-none bg-transparent text-left font-['Inter']">
                <svg class="w-4 h-4 flex-shrink-0 stroke-red-400" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Déconnexion
            </button>
        </form>
    </div>

</aside>

{{-- ======================== TOPBAR ======================== --}}
<header class="fixed top-0 left-[210px] right-0 bg-white border-b border-slate-200 z-40 flex items-center px-5 gap-4 shadow-sm h-16">

    {{-- Tabs --}}
    <nav class="flex items-center gap-0.5 flex-1 justify-center flex-wrap">

        @if($role === 'directeur')
            {!! $topTab(route('directeur.dashboard'),               'dashboard', 'Dashboard',    request()->routeIs('directeur.dashboard')) !!}
            {!! $topTab(route('directeur.formateurs.index'),        'users',     'Formateurs',   request()->routeIs('directeur.formateurs*')) !!}
            {!! $topTab(route('directeur.stagiaires.index'),        'students',  'Stagiaires',   request()->routeIs('directeur.stagiaires*')) !!}
            {!! $topTab(route('directeur.groupes.index'),           'groups',    'Groupes',      request()->routeIs('directeur.groupes*')) !!}
            {!! $topTab(route('directeur.filieres.index'),          'book',      'Filières',     request()->routeIs('directeur.filieres*')) !!}
            {!! $topTab(route('directeur.modules.index'),           'modules',   'Modules',      request()->routeIs('directeur.modules*')) !!}
            
            {!! $topTab(route('directeur.profile.show'),            'user',      'Mon compte',   request()->routeIs('directeur.profile*')) !!}

        @elseif($role === 'formateur')
            {!! $topTab(route('formateur.dashboard'),       'dashboard', 'Dashboard',  request()->routeIs('formateur.dashboard')) !!}
            {!! $topTab(route('formateur.seances.index'),   'calendar',  'Séances',    request()->routeIs('formateur.seances*')) !!}
            {!! $topTab(route('formateur.notes.index'),     'notes',     'Notes',      request()->routeIs('formateur.notes*')) !!}
            {!! $topTab(route('formateur.absences.index'),  'absences',  'Absences',   request()->routeIs('formateur.absences*')) !!}
            {!! $topTab(route('formateur.profile.show'),    'user',      'Mon compte', request()->routeIs('formateur.profile*')) !!}

        @elseif($role === 'encadrant')
            {!! $topTab(route('encadrant.stages.index'),   'stages',   'Stages',     request()->routeIs('encadrant.stages*')) !!}
            {!! $topTab(route('encadrant.profile.show'),   'user',     'Mon compte', request()->routeIs('encadrant.profile*')) !!}
        @endif

    </nav>

    {{-- Greeting + Avatar --}}
    <div class="flex items-center gap-2.5 flex-shrink-0">
        <div class="text-right hidden sm:block">
            <div class="text-[13px] font-semibold text-slate-800 whitespace-nowrap">
                Bonjour{{ $role === 'directeur' ? ' M.' : '' }} {{ $u->nom }} !
            </div>
            <div class="text-[10.5px] text-slate-400 capitalize">{{ $u->role }}</div>
        </div>
        <a href="{{ $profileRoute }}"
           title="{{ $u->prenom }} {{ $u->nom }}"
           class="w-9 h-9 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white text-xs font-bold hover:bg-blue-500 transition-colors duration-150 no-underline flex-shrink-0 shadow-sm">
            {{ strtoupper(substr($u->prenom,0,1).substr($u->nom,0,1)) }}
        </a>
    </div>

</header>

{{-- ======================== MAIN ======================== --}}
<div class="ml-[210px] min-h-screen pt-16">
    <div class="p-6 px-7">

        @foreach([
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'error'   => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
            'info'    => 'bg-blue-50 border-blue-200 text-blue-800'
        ] as $type => $cls)
            @if(session($type))
                <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-[13px] font-medium mb-4 border {{ $cls }}">
                    @if($type === 'success')
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    @elseif($type === 'error')
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    @elseif($type === 'warning')
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    @else
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    @endif
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        @if($errors->any())
            <div class="flex flex-col gap-1.5 px-4 py-3 rounded-xl text-[13px] font-medium mb-4 bg-red-50 border border-red-200 text-red-800">
                <div class="flex items-center gap-2 font-bold">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Veuillez corriger les erreurs suivantes :
                </div>
                <ul class="pl-6 m-0 text-[12.5px] list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </div>

    <footer class="text-center text-xs text-slate-400 py-4 px-8 border-t border-slate-200 mt-6">
        © {{ date('Y') }} IFSI — Système de Gestion de Formation
    </footer>
</div>

</body>
</html>
