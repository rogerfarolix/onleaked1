<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Onleaked - Cybersecurity SaaS platform. Check if your email has been compromised, track vulnerabilities, and analyze domains.">
    <title>@yield('title', config('app.name', 'Onleaked') . ' - Cybersecurity Intelligence')</title>
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
    </style>
    @stack('styles')
</head>
<body class="gradient-bg text-white min-h-screen antialiased flex flex-col">

    <!-- Navigation -->
    <nav x-data="{ open: false }" class="fixed top-0 w-full z-50 border-b border-white/5 bg-[#09090b]/80 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-rose-500 flex items-center justify-center font-bold text-sm">O</div>
                <span class="font-bold text-lg tracking-tight">Onleaked</span>
            </a>
            
            <!-- Desktop Links -->
            <div class="hidden md:flex items-center gap-8 text-sm text-zinc-400">
                <a href="/#check" class="hover:text-white transition-colors">Leak Check</a>
                <a href="{{ route('domain.show') }}" class="hover:text-white transition-colors">Domain Analysis</a>
                
                <!-- Services Dropdown -->
                <div x-data="{ dropdownOpen: false }" class="relative" @click.outside="dropdownOpen = false">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-1 hover:text-white transition-colors focus:outline-none">
                        Services
                        <svg class="w-4 h-4" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="dropdownOpen" x-transition x-cloak class="absolute top-full mt-2 left-1/2 -translate-x-1/2 w-48 py-2 bg-[#09090b]/90 backdrop-blur-xl border border-white/10 rounded-xl shadow-xl z-50">
                        <a href="{{ route('services') }}" class="block px-4 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/5 transition-colors">Overview</a>
                        <a href="{{ route('services') }}#alerts" class="block px-4 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/5 transition-colors">Vulnerability Alerts</a>
                        <a href="{{ route('services') }}#leakcheck" class="block px-4 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/5 transition-colors">Leak Check</a>
                        <a href="{{ route('domain.show') }}" class="block px-4 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/5 transition-colors">Domain Analysis</a>
                    </div>
                </div>

                <a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a>
            </div>

            <!-- Auth / Actions Desktop -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm text-zinc-400 hover:text-white transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-zinc-400 hover:text-white transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="text-sm px-4 py-2 rounded-lg bg-white text-black font-medium hover:bg-zinc-200 transition-colors">Get Started</a>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-zinc-400 hover:text-white focus:outline-none p-2">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-transition x-cloak class="md:hidden bg-[#09090b] border-t border-white/5">
            <div class="px-6 py-4 flex flex-col gap-4">
                <a href="/#check" class="text-zinc-400 hover:text-white transition-colors">Leak Check</a>
                <a href="{{ route('domain.show') }}" class="text-zinc-400 hover:text-white transition-colors">Domain Analysis</a>
                <a href="{{ route('services') }}" class="text-zinc-400 hover:text-white transition-colors">Services Overview</a>
                <a href="{{ route('contact') }}" class="text-zinc-400 hover:text-white transition-colors">Contact</a>
                <hr class="border-white/5">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-zinc-400 hover:text-white transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-zinc-400 hover:text-white transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="text-white font-medium">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-10 px-6">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-gradient-to-br from-violet-500 to-rose-500 flex items-center justify-center font-bold text-xs">O</div>
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
