<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        IFSI — Connexion
    </title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap"
        rel="stylesheet"
    />

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="bg-slate-100 font-['Inter']">

    <div class="min-h-screen flex">

        {{-- LEFT SIDE --}}
        <div class="hidden lg:flex lg:w-1/2 bg-[#1a3a5c] relative overflow-hidden">

            <div class="absolute -top-20 -left-20 w-72 h-72 bg-[#4fa3d1]/20 rounded-full blur-3xl"></div>

            <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#4fa3d1]/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex flex-col justify-center items-center w-full px-16 text-center">

                {{-- LOGO --}}
                <div class="w-28 h-28 rounded-3xl bg-white shadow-2xl flex items-center justify-center p-4 mb-8">

                    <img
                        src="{{ asset('storage/logo/logo.png') }}"
                        alt="IFSI Logo"
                        class="w-full h-full object-contain"
                    >

                </div>

                <h1 class="text-5xl font-bold text-white">
                    IFSI
                </h1>

                <p class="text-[#b8d4e8] text-lg mt-4 max-w-md leading-relaxed">
                    Plateforme moderne de gestion
                    de formation, stagiaires,
                    notes et séances.
                </p>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-10">

            <div class="w-full max-w-md">

                {{-- MOBILE LOGO --}}
                <div class="lg:hidden flex justify-center mb-8">

                    <div class="w-24 h-24 rounded-3xl bg-white shadow-xl flex items-center justify-center p-4 border border-slate-200">

                        <img
                            src="{{ asset('storage/logo/logo.png') }}"
                            alt="IFSI Logo"
                            class="w-full h-full object-contain"
                        >

                    </div>

                </div>

                {{-- CARD --}}
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">

                    {{-- HEADER --}}
                    <div class="px-8 pt-8 pb-6 text-center border-b border-slate-100">

                        <h2 class="text-3xl font-bold text-[#1a3a5c]">
                            Connexion
                        </h2>

                        <p class="text-slate-500 mt-2 text-sm">
                            Connectez-vous à votre compte
                        </p>

                    </div>

                    {{-- FORM --}}
                    <div class="p-8">

                        @if (session('status'))

                            <div class="mb-4 text-sm text-green-600">
                                {{ session('status') }}
                            </div>

                        @endif

                        <form
                            method="POST"
                            action="{{ route('login') }}"
                        >

                            @csrf

                            {{-- EMAIL --}}
                            <div>

                                <label
                                    for="email"
                                    class="block text-sm font-semibold text-[#1a3a5c] mb-2"
                                >
                                    Adresse Email
                                </label>

                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus

                                    class="w-full rounded-xl border border-[#b8d4e8] bg-slate-50 px-4 py-3 text-slate-700 focus:border-[#4fa3d1] focus:ring-2 focus:ring-[#4fa3d1]/20 outline-none transition"
                                    placeholder="exemple@email.com"
                                >

                                @error('email')

                                    <p class="mt-2 text-sm text-red-500">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            {{-- PASSWORD --}}
                            <div class="mt-5">

                                <label
                                    for="password"
                                    class="block text-sm font-semibold text-[#1a3a5c] mb-2"
                                >
                                    Mot de passe
                                </label>

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required

                                    class="w-full rounded-xl border border-[#b8d4e8] bg-slate-50 px-4 py-3 text-slate-700 focus:border-[#4fa3d1] focus:ring-2 focus:ring-[#4fa3d1]/20 outline-none transition"
                                    placeholder="••••••••"
                                >

                                @error('password')

                                    <p class="mt-2 text-sm text-red-500">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            {{-- OPTIONS --}}
                            <div class="flex items-center justify-between mt-5">

                                <label
                                    for="remember_me"
                                    class="flex items-center gap-2 text-sm text-slate-600"
                                >

                                    <input
                                        id="remember_me"
                                        type="checkbox"
                                        name="remember"

                                        class="rounded border-slate-300 text-[#4fa3d1] focus:ring-[#4fa3d1]"
                                    >

                                    Se souvenir de moi

                                </label>

                                @if (Route::has('password.request'))

                                    <a
                                        href="{{ route('password.request') }}"
                                        class="text-sm font-medium text-[#4fa3d1] hover:text-[#1a3a5c] transition"
                                    >
                                        Mot de passe oublié ?
                                    </a>

                                @endif

                            </div>

                            {{-- BUTTON --}}
                            <button
                                type="submit"

                                class="w-full mt-7 bg-[#1a3a5c] hover:bg-[#16324f] text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-[#1a3a5c]/10"
                            >
                                Se connecter
                            </button>

                        </form>

                    </div>

                </div>

                {{-- FOOTER --}}
                <p class="text-center text-sm text-slate-500 mt-6">
                    © {{ date('Y') }} IFSI — Gestion de Formation
                </p>

            </div>

        </div>

    </div>

</body>
</html>