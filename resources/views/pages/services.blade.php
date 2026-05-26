@extends('layouts.public')

@section('title', 'Our Services - ' . config('app.name', 'Onleaked'))

@section('content')
    <!-- Hero -->
    <section class="pt-32 pb-16 px-6 text-center">
        <div class="max-w-3xl mx-auto fade-up">
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                Our <span class="bg-gradient-to-r from-violet-400 to-rose-400 bg-clip-text text-transparent">Services</span>
            </h1>
            <p class="text-zinc-400 text-lg max-w-2xl mx-auto">Three powerful cybersecurity services designed to protect individuals, developers, and businesses.</p>
        </div>
    </section>

    <!-- Service 1: Vulnerability Alerts -->
    <section id="alerts" class="py-16 px-6 border-t border-white/5">
        <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="fade-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-violet-500/20 bg-violet-500/10 text-violet-400 text-xs mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Service 1
                </div>
                <h2 class="text-3xl font-bold mb-4">Vulnerability Alerts</h2>
                <p class="text-zinc-400 leading-relaxed mb-6">Subscribe to the technologies you use (Laravel, React, PostgreSQL, etc.) and receive instant email alerts when new CVEs, advisories, or critical vulnerabilities are published.</p>
                <ul class="space-y-3 text-sm text-zinc-300">
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-violet-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>AI-powered severity analysis and personalized recommendations</li>
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-violet-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Automated scraping of CVE databases and security advisories</li>
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-violet-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Dashboard to manage your subscriptions and view past alerts</li>
                </ul>
                @guest <a href="{{ route('register') }}" class="inline-block mt-8 px-6 py-3 bg-violet-500 text-white font-semibold rounded-xl hover:bg-violet-400 transition-colors">Get Started Free</a> @endguest
            </div>
            <div class="glass-card rounded-2xl p-6 fade-up" style="animation-delay:.2s">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-red-500/5 border border-red-500/10">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-red-500/20 text-red-400">CRITICAL</span>
                        <div><p class="text-sm font-medium text-white">CVE-2026-12345</p><p class="text-xs text-zinc-500">Laravel - Remote Code Execution</p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-500/5 border border-amber-500/10">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-amber-500/20 text-amber-400">HIGH</span>
                        <div><p class="text-sm font-medium text-white">CVE-2026-67890</p><p class="text-xs text-zinc-500">React - Cross-Site Scripting</p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-500/5 border border-blue-500/10">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-blue-500/20 text-blue-400">MEDIUM</span>
                        <div><p class="text-sm font-medium text-white">CVE-2026-11111</p><p class="text-xs text-zinc-500">PostgreSQL - Privilege Escalation</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service 2: Leak Check & Footprint -->
    <section id="leakcheck" class="py-16 px-6 border-t border-white/5">
        <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="glass-card rounded-2xl p-6 fade-up order-2 md:order-1" style="animation-delay:.2s">
                <div class="text-center mb-4">
                    <div class="w-14 h-14 mx-auto rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <p class="text-red-400 font-semibold">Found in 47 breaches</p>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['Adobe','LinkedIn','Dropbox','Canva','Twitter','Wattpad'] as $b)
                    <div class="bg-white/5 rounded-lg p-2 text-center text-xs text-zinc-400">{{ $b }}</div>
                    @endforeach
                </div>
            </div>
            <div class="fade-up order-1 md:order-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-rose-500/20 bg-rose-500/10 text-rose-400 text-xs mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Service 2
                </div>
                <h2 class="text-3xl font-bold mb-4">Leak Check & Digital Footprint</h2>
                <p class="text-zinc-400 leading-relaxed mb-6">Check if your email has been compromised in data breaches and discover your digital presence across 120+ platforms — all without registration or tracking.</p>
                <ul class="space-y-3 text-sm text-zinc-300">
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Zero-storage, privacy-first architecture</li>
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>OSINT-powered footprint: see where your email is registered</li>
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Detailed breach info: dates, logos, password exposure risk</li>
                </ul>
                <a href="/#check" class="inline-block mt-8 px-6 py-3 bg-rose-500 text-white font-semibold rounded-xl hover:bg-rose-400 transition-colors">Check Your Email</a>
            </div>
        </div>
    </section>

    <!-- Service 3: Domain Analysis -->
    <section id="domain" class="py-16 px-6 border-t border-white/5">
        <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="fade-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-amber-500/20 bg-amber-500/10 text-amber-400 text-xs mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    Service 3
                </div>
                <h2 class="text-3xl font-bold mb-4">Domain Analysis</h2>
                <p class="text-zinc-400 leading-relaxed mb-6">Perform a complete security audit of any domain. Retrieve DNS records, check email security configurations (SPF, DKIM, DMARC), discover subdomains, and assess domain reputation.</p>
                <ul class="space-y-3 text-sm text-zinc-300">
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Full DNS record enumeration (A, MX, TXT, NS, AAAA)</li>
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Email security audit: SPF, DMARC pass/fail indicators</li>
                    <li class="flex items-start gap-3"><svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Subdomain discovery via Certificate Transparency logs</li>
                </ul>
                <a href="{{ route('domain.show') }}" class="inline-block mt-8 px-6 py-3 bg-amber-500 text-black font-semibold rounded-xl hover:bg-amber-400 transition-colors">Analyze a Domain</a>
            </div>
            <div class="glass-card rounded-2xl p-6 fade-up" style="animation-delay:.2s">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/5"><span class="text-sm text-zinc-300">MX Records</span><span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/20 text-emerald-400">PASS</span></div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/5"><span class="text-sm text-zinc-300">SPF</span><span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/20 text-emerald-400">PASS</span></div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/5"><span class="text-sm text-zinc-300">DMARC</span><span class="px-2 py-0.5 rounded text-xs font-bold bg-red-500/20 text-red-400">FAIL</span></div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/5"><span class="text-sm text-zinc-300">Reputation</span><span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/20 text-emerald-400">CLEAN</span></div>
                </div>
            </div>
        </div>
    </section>
@endsection
