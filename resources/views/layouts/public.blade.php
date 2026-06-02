<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Onleaked - Cybersecurity SaaS platform. Check if your email has been compromised, track vulnerabilities, and analyze domains.">
    <title>@yield('title', config('app.name', 'Onleaked') . ' - Cybersecurity Intelligence')</title>
    {{-- Apply saved theme before render to prevent flash --}}
    <script nonce="{{ $cspNonce }}">if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(120, 119, 198, 0.15), transparent),
                        radial-gradient(ellipse 60% 40% at 80% 50%, rgba(255, 56, 56, 0.08), transparent),
                        #09090b;
        }
        .glass-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.06); }
        .glow-input:focus-within { box-shadow: 0 0 0 1px rgba(139,92,246,0.5), 0 0 30px -5px rgba(139,92,246,0.3); }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:.4;transform:scale(1)} 50%{opacity:1;transform:scale(1.2)} }
        .fade-up { animation: fadeUp .6s ease-out both; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        [x-cloak] { display: none !important; }

        /* Light mode */
        html:not(.dark) body { background: #f8fafc; color: #0f172a; }
        html:not(.dark) .gradient-bg {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(120,119,198,0.08), transparent), #f8fafc;
        }
        html:not(.dark) .glass-card { background: rgba(255,255,255,0.9) !important; border-color: rgba(0,0,0,0.08) !important; color: #0f172a; }
        html:not(.dark) footer { background: #f1f5f9; border-color: rgba(0,0,0,0.06) !important; }
        html:not(.dark) .text-white { color: #111827 !important; }
        html:not(.dark) .text-zinc-400 { color: #4b5563 !important; }
        html:not(.dark) .text-zinc-500 { color: #6b7280 !important; }
        html:not(.dark) .text-zinc-300 { color: #374151 !important; }
        html:not(.dark) input { color: #111827 !important; }
        html:not(.dark) input::placeholder { color: #9ca3af !important; }
    </style>
    @stack('styles')
</head>
<body class="gradient-bg text-white min-h-screen antialiased flex flex-col">

    @php $currentRoute = Route::currentRouteName(); @endphp

    <!-- Navigation — theme state lives HERE, not on <html> -->
    <nav x-data="{
             open: false,
             scrolled: false,
             dark: localStorage.getItem('theme') !== 'light',
             toggle() {
                 this.dark = !this.dark;
                 localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                 document.documentElement.classList.toggle('dark', this.dark);
             }
         }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
         :class="scrolled ? 'border-white/10 bg-[#09090b]/95 shadow-xl shadow-black/20' : 'border-transparent bg-[#09090b]/60'"
         class="fixed top-0 w-full z-50 border-b backdrop-blur-xl transition-all duration-300">

        <div class="max-w-6xl mx-auto px-6 h-[68px] flex items-center justify-between gap-6">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-9 h-9 rounded-xl bg-linear-to-br from-violet-600 to-rose-500 flex items-center justify-center shadow-lg shadow-violet-500/30 group-hover:shadow-violet-500/50 transition-shadow">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <span class="font-bold text-[17px] tracking-tight">
                    <span class="text-white">On</span><span class="bg-linear-to-r from-violet-400 to-rose-400 bg-clip-text text-transparent">leaked</span>
                </span>
            </a>

            <!-- Desktop nav links -->
            <div class="hidden md:flex items-center gap-1 text-sm">

                <a href="{{ route('home') }}"
                   class="px-3 py-2 rounded-lg transition-colors {{ $currentRoute === 'home' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    Home
                </a>

                <!-- Tools dropdown — isolated scope so it doesn't conflict with nav scope -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false" @keydown.escape.window="open = false">
                    <button @click="open = !open"
                            :class="open ? 'text-white bg-white/5' : ''"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-150 {{ in_array($currentRoute, ['leak-check','domain.show','services','password-check','ssl-check','ip-check','header-check']) ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                        Tools
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
                         style="background:#0c0c0f; border:1px solid rgba(255,255,255,0.08);"
                         class="absolute top-[calc(100%+10px)] left-1/2 -translate-x-1/2 w-[300px] rounded-2xl shadow-2xl z-60 overflow-hidden">

                        {{-- Pointer triangle --}}
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 rotate-45"
                             style="background:#0c0c0f; border-top:1px solid rgba(255,255,255,0.08); border-left:1px solid rgba(255,255,255,0.08);"></div>

                        <div class="p-1.5 pt-3">
                            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Security Tools</p>

                            <a href="{{ route('leak-check') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group {{ $currentRoute === 'leak-check' ? 'bg-white/5' : 'hover:bg-white/5' }}">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center shrink-0 group-hover:bg-rose-500/15 transition-colors">
                                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">Leak Check</div>
                                    <div class="text-xs text-zinc-600 mt-0.5">Email in 120+ breach databases</div>
                                </div>
                                @if($currentRoute === 'leak-check')
                                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-rose-400 shrink-0"></div>
                                @endif
                            </a>

                            <a href="{{ route('domain.show') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group {{ $currentRoute === 'domain.show' ? 'bg-white/5' : 'hover:bg-white/5' }}">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0 group-hover:bg-amber-500/15 transition-colors">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">Domain Analysis</div>
                                    <div class="text-xs text-zinc-600 mt-0.5">DNS, SPF, DMARC & reputation</div>
                                </div>
                                @if($currentRoute === 'domain.show')
                                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></div>
                                @endif
                            </a>

                            <a href="{{ route('register') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group hover:bg-white/5">
                                <div class="w-8 h-8 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center shrink-0 group-hover:bg-violet-500/15 transition-colors">
                                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">CVE Alerts</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-violet-500/20 text-violet-400 font-semibold leading-none">PRO</span>
                                    </div>
                                    <div class="text-xs text-zinc-600 mt-0.5">Real-time vulnerability alerts</div>
                                </div>
                            </a>

                            <a href="{{ route('password-check') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group {{ $currentRoute === 'password-check' ? 'bg-white/5' : 'hover:bg-white/5' }}">
                                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0 group-hover:bg-cyan-500/15 transition-colors">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">Password Check</div>
                                    <div class="text-xs text-zinc-600 mt-0.5">Check password breach count</div>
                                </div>
                            </a>

                            <a href="{{ route('ssl-check') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group {{ $currentRoute === 'ssl-check' ? 'bg-white/5' : 'hover:bg-white/5' }}">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-center shrink-0 group-hover:bg-sky-500/15 transition-colors">
                                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">SSL Inspector</div>
                                    <div class="text-xs text-zinc-600 mt-0.5">Certificate details & grade</div>
                                </div>
                            </a>

                            <a href="{{ route('ip-check') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group {{ $currentRoute === 'ip-check' ? 'bg-white/5' : 'hover:bg-white/5' }}">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0 group-hover:bg-indigo-500/15 transition-colors">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">IP Reputation</div>
                                    <div class="text-xs text-zinc-600 mt-0.5">Geolocation & abuse score</div>
                                </div>
                            </a>

                            <a href="{{ route('header-check') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group {{ $currentRoute === 'header-check' ? 'bg-white/5' : 'hover:bg-white/5' }}">
                                <div class="w-8 h-8 rounded-lg bg-teal-500/10 border border-teal-500/20 flex items-center justify-center shrink-0 group-hover:bg-teal-500/15 transition-colors">
                                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">Header Analyzer</div>
                                    <div class="text-xs text-zinc-600 mt-0.5">SPF, DKIM, DMARC parsing</div>
                                </div>
                            </a>
                        </div>

                        <div class="mx-3 my-1 border-t border-white/5"></div>

                        <div class="p-1.5">
                            <a href="{{ route('services') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-zinc-500 hover:text-zinc-200 hover:bg-white/5 transition-all duration-150 group">
                                <span>All tools overview</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('contact') }}"
                   class="px-3 py-2 rounded-lg transition-colors {{ $currentRoute === 'contact' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    Contact
                </a>
            </div>

            <!-- Desktop right actions -->
            <div class="hidden md:flex items-center gap-2 shrink-0">
                <!-- Theme toggle — uses nav's dark/toggle scope -->
                <button @click="toggle()" :title="dark ? 'Light mode' : 'Dark mode'"
                    class="w-9 h-9 rounded-lg flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/5 transition-colors">
                    <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <div class="w-px h-5 bg-white/10"></div>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-sm px-3 py-2 rounded-lg text-zinc-400 hover:text-white hover:bg-white/5 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm px-3 py-2 rounded-lg text-zinc-400 hover:text-white transition-colors">
                        Log in
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm px-4 py-2 rounded-xl bg-linear-to-r from-violet-600 to-violet-500 text-white font-medium hover:from-violet-500 hover:to-violet-400 transition-all shadow-lg shadow-violet-500/20 hover:shadow-violet-500/40">
                        Get Started
                    </a>
                @endauth
            </div>

            <!-- Mobile: theme + hamburger -->
            <div class="md:hidden flex items-center gap-1">
                <button @click="toggle()"
                    class="w-9 h-9 rounded-lg flex items-center justify-center text-zinc-500 hover:text-white transition-colors">
                    <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
                <button @click="open = !open"
                    class="w-9 h-9 rounded-lg flex items-center justify-center text-zinc-400 hover:text-white hover:bg-white/5 transition-colors">
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
             class="md:hidden border-t border-white/5 bg-[#09090b]/98">
            <div class="px-4 py-4 space-y-1">

                <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Navigation</p>

                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'home' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Home
                </a>
                <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'contact' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Contact
                </a>

                <p class="px-3 pt-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Tools</p>

                <a href="{{ route('leak-check') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'leak-check' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-7 h-7 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    Leak Check
                </a>
                <a href="{{ route('domain.show') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'domain.show' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                    </span>
                    Domain Analysis
                </a>
                <a href="{{ route('register') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors text-zinc-400 hover:text-white hover:bg-white/5">
                    <span class="w-7 h-7 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </span>
                    CVE Alerts
                </a>
                <a href="{{ route('password-check') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'password-check' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-7 h-7 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    Password Check
                </a>
                <a href="{{ route('ssl-check') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'ssl-check' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-7 h-7 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    SSL Inspector
                </a>
                <a href="{{ route('ip-check') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'ip-check' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-7 h-7 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                    </span>
                    IP Reputation
                </a>
                <a href="{{ route('header-check') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $currentRoute === 'header-check' ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    <span class="w-7 h-7 rounded-lg bg-teal-500/10 border border-teal-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    Header Analyzer
                </a>

                <div class="pt-3 pb-1 border-t border-white/5 mt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-zinc-400 hover:text-white hover:bg-white/5 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <div class="flex gap-2 px-1 pt-1">
                            <a href="{{ route('login') }}" class="flex-1 text-center text-sm px-4 py-2.5 rounded-xl border border-white/10 text-zinc-300 hover:bg-white/5 transition-colors">
                                Log in
                            </a>
                            <a href="{{ route('register') }}" class="flex-1 text-center text-sm px-4 py-2.5 rounded-xl bg-linear-to-r from-violet-600 to-violet-500 text-white font-medium hover:from-violet-500 hover:to-violet-400 transition-all">
                                Get Started
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
    <footer class="border-t border-white/5 py-10 px-6">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-linear-to-br from-violet-500 to-rose-500 flex items-center justify-center font-bold text-xs">O</div>
                <span class="text-sm text-zinc-500">&copy; {{ date('Y') }} Onleaked by Nealix. All rights reserved.</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-zinc-500">
                <a href="/" class="hover:text-white transition-colors">Home</a>
                <a href="{{ route('services') }}" class="hover:text-white transition-colors">Services</a>
                <a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
