<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Onleaked plateforme de cybersécurité. Vérifiez si votre e-mail a fuité, analysez vos domaines et suivez les vulnérabilités.">
    <title>@yield('title', config('app.name', 'Onleaked') . ' Renseignement cybersécurité')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900|jetbrains-mono:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="bg-ink text-text min-h-screen antialiased flex flex-col">

    @php $currentRoute = Route::currentRouteName(); @endphp

    <nav x-data="{ open: false, scrolled: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
         :class="scrolled ? 'border-line bg-ink/95 shadow-lg shadow-black/40' : 'border-transparent bg-ink/70'"
         class="fixed top-0 w-full z-50 border-b backdrop-blur-md transition-all duration-300">

        <div class="max-w-6xl mx-auto px-6 h-[68px] flex items-center justify-between gap-6">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-9 h-9 rounded-md bg-brand flex items-center justify-center ring-1 ring-brand/40 group-hover:ring-brand transition-all">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <span class="font-bold text-[17px] tracking-tight text-text">
                    On<span class="text-brand-bright">leaked</span>
                </span>
            </a>

            <!-- Desktop nav links -->
            <div class="hidden md:flex items-center gap-1 text-sm">

                <a href="{{ route('home') }}"
                   class="px-3 py-2 rounded-md transition-colors {{ $currentRoute === 'home' ? 'text-text bg-white/5' : 'text-text-muted hover:text-text hover:bg-white/5' }}">
                    Accueil
                </a>

                <!-- Tools dropdown -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false" @keydown.escape.window="open = false">
                    <button @click="open = !open"
                            :class="open ? 'text-text bg-white/5' : ''"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-md transition-all duration-150 {{ in_array($currentRoute, ['leak-check','domain.show','services','password-check','ssl-check','ip-check','header-check']) ? 'text-text bg-white/5' : 'text-text-muted hover:text-text hover:bg-white/5' }}">
                        Outils
                        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         x-cloak
                         class="absolute top-[calc(100%+10px)] left-1/2 -translate-x-1/2 w-[300px] rounded-lg shadow-2xl z-60 overflow-hidden bg-surface border border-line">

                        <div class="p-1.5 pt-3">
                            <p class="px-3 pb-2 mono-label text-text-dim">Outils de sécurité</p>

                            @php
                                $tools = [
                                    ['leak-check', 'Vérification de fuite', 'E-mail dans 120+ bases de fuites', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                                    ['domain.show', 'Analyse de domaine', 'DNS, SPF, DMARC & réputation', 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9'],
                                    ['password-check', 'Mot de passe compromis', 'Compte de fuites par k-anonymat', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                                    ['ssl-check', 'Inspecteur SSL', 'Détails du certificat & note', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                                    ['ip-check', 'Réputation IP', 'Géolocalisation & score d\'abus', 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9'],
                                    ['header-check', 'Analyseur d\'en-têtes', 'SPF, DKIM, DMARC', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                ];
                            @endphp

                            @foreach($tools as [$route, $name, $desc, $icon])
                                <a href="{{ route($route) }}"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-md transition-all duration-150 group {{ $currentRoute === $route ? 'bg-white/5' : 'hover:bg-white/5' }}">
                                    <div class="w-8 h-8 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center shrink-0 group-hover:bg-brand/20 transition-colors">
                                        <svg class="w-4 h-4 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-text group-hover:text-white transition-colors">{{ $name }}</div>
                                        <div class="text-xs text-text-dim mt-0.5">{{ $desc }}</div>
                                    </div>
                                    @if($currentRoute === $route)
                                        <div class="ml-auto w-1.5 h-1.5 rounded-full bg-brand-bright shrink-0"></div>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        <div class="mx-3 my-1 border-t border-line"></div>

                        <div class="p-1.5">
                            <a href="{{ route('services') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-md text-xs text-text-muted hover:text-text hover:bg-white/5 transition-all duration-150 group">
                                <span>Tous les outils</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('contact') }}"
                   class="px-3 py-2 rounded-md transition-colors {{ $currentRoute === 'contact' ? 'text-text bg-white/5' : 'text-text-muted hover:text-text hover:bg-white/5' }}">
                    Contact
                </a>
            </div>

            <!-- Desktop right actions -->
            <div class="hidden md:flex items-center gap-2 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-sm px-3 py-2 rounded-md text-text-muted hover:text-text hover:bg-white/5 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Tableau de bord
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm px-3 py-2 rounded-md text-text-muted hover:text-text transition-colors">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm px-4 py-2 rounded-md bg-brand text-white font-medium hover:bg-brand-bright transition-colors ring-1 ring-brand/40">
                        Commencer
                    </a>
                @endauth
            </div>

            <!-- Mobile hamburger -->
            <div class="md:hidden flex items-center gap-1">
                <button @click="open = !open"
                    class="w-9 h-9 rounded-md flex items-center justify-center text-text-muted hover:text-text hover:bg-white/5 transition-colors">
                    <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="md:hidden border-t border-line bg-ink">
            <div class="px-4 py-4 space-y-1">

                <p class="px-3 pt-1 pb-2 mono-label text-text-dim">Navigation</p>

                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm transition-colors {{ $currentRoute === 'home' ? 'text-text bg-white/5' : 'text-text-muted hover:text-text hover:bg-white/5' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Accueil
                </a>
                <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm transition-colors {{ $currentRoute === 'contact' ? 'text-text bg-white/5' : 'text-text-muted hover:text-text hover:bg-white/5' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Contact
                </a>

                <p class="px-3 pt-3 pb-2 mono-label text-text-dim">Outils</p>

                @foreach($tools as [$route, $name, $desc, $icon])
                    <a href="{{ route($route) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm transition-colors {{ $currentRoute === $route ? 'text-text bg-white/5' : 'text-text-muted hover:text-text hover:bg-white/5' }}">
                        <span class="w-7 h-7 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                        </span>
                        {{ $name }}
                    </a>
                @endforeach

                <div class="pt-3 pb-1 border-t border-line mt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm text-text-muted hover:text-text hover:bg-white/5 transition-colors">
                            Tableau de bord
                        </a>
                    @else
                        <div class="flex gap-2 px-1 pt-1">
                            <a href="{{ route('login') }}" class="flex-1 text-center text-sm px-4 py-2.5 rounded-md border border-line text-text hover:bg-white/5 transition-colors">
                                Connexion
                            </a>
                            <a href="{{ route('register') }}" class="flex-1 text-center text-sm px-4 py-2.5 rounded-md bg-brand text-white font-medium hover:bg-brand-bright transition-colors">
                                Commencer
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-line py-10 px-6 mt-auto">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-brand flex items-center justify-center font-bold text-xs text-white">O</div>
                <span class="text-sm text-text-dim">&copy; {{ date('Y') }} Onleaked par Nealix. Tous droits réservés.</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-text-dim">
                <a href="{{ route('home') }}" class="hover:text-text transition-colors">Accueil</a>
                <a href="{{ route('services') }}" class="hover:text-text transition-colors">Services</a>
                <a href="{{ route('contact') }}" class="hover:text-text transition-colors">Contact</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
