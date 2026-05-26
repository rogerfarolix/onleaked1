@extends('layouts.public')

@section('title', 'Onleaked — Cybersecurity Intelligence Platform')

@section('content')
    <!-- Hero -->
    <section class="pt-36 pb-24 px-6">
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 text-xs text-zinc-400 mb-8 fade-up">
                <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
                Privacy-first &bull; Zero tracking &bull; Open source APIs
            </div>

            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6 fade-up" style="animation-delay:.1s">
                Take back control of<br>
                <span class="bg-linear-to-r from-violet-400 via-rose-400 to-amber-400 bg-clip-text text-transparent">your digital security</span>
            </h1>

            <p class="text-zinc-400 text-xl mb-12 max-w-2xl mx-auto fade-up" style="animation-delay:.2s">
                Monitor breaches, audit your domain, and track vulnerabilities — all in one privacy-first platform. We never store your data.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 fade-up" style="animation-delay:.3s">
                <a href="{{ route('leak-check') }}" class="px-8 py-3.5 bg-white text-black font-semibold rounded-xl hover:bg-zinc-100 transition-all duration-200 text-sm">
                    Check Your Email
                </a>
                <a href="{{ route('domain.show') }}" class="px-8 py-3.5 bg-white/5 border border-white/10 text-white font-medium rounded-xl hover:bg-white/10 transition-all duration-200 text-sm">
                    Analyze a Domain
                </a>
            </div>
        </div>
    </section>

    <!-- Stats bar -->
    <section class="py-10 px-6 border-y border-white/5">
        <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-3xl font-bold text-white">120+</p>
                <p class="text-sm text-zinc-500 mt-1">platforms scanned</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-white">0</p>
                <p class="text-sm text-zinc-500 mt-1">bytes stored</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-white">Real-time</p>
                <p class="text-sm text-zinc-500 mt-1">CVE alerts</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-white">Free</p>
                <p class="text-sm text-zinc-500 mt-1">to use</p>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <p class="text-sm text-violet-400 font-medium mb-3 uppercase tracking-widest">Services</p>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Everything you need to stay secure</h2>
                <p class="text-zinc-400 max-w-xl mx-auto">Three powerful tools, one platform. No account required for the security checks.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Leak Check -->
                <a href="{{ route('leak-check') }}" class="glass-card rounded-2xl p-7 hover:border-rose-500/20 transition-all duration-300 group block">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center mb-5 group-hover:bg-rose-500/20 transition-colors">
                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Leak Check & Footprint</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed mb-4">Verify if your email was exposed in a breach and discover your digital footprint across 120+ platforms.</p>
                    <span class="text-rose-400 text-sm font-medium group-hover:gap-2 flex items-center gap-1 transition-all">
                        Try it free
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>

                <!-- Domain Analysis -->
                <a href="{{ route('domain.show') }}" class="glass-card rounded-2xl p-7 hover:border-amber-500/20 transition-all duration-300 group block">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center mb-5 group-hover:bg-amber-500/20 transition-colors">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Domain Analysis</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed mb-4">Complete domain intelligence: DNS records, SPF/DMARC audit, subdomain enumeration and reputation check.</p>
                    <span class="text-amber-400 text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                        Analyze now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>

                <!-- Vulnerability Alerts -->
                <a href="{{ route('register') }}" class="glass-card rounded-2xl p-7 hover:border-violet-500/20 transition-all duration-300 group block">
                    <div class="w-12 h-12 rounded-xl bg-violet-500/10 flex items-center justify-center mb-5 group-hover:bg-violet-500/20 transition-colors">
                        <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Vulnerability Alerts</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed mb-4">Subscribe to technologies and receive AI-powered email alerts when new CVEs and advisories are published.</p>
                    <span class="text-violet-400 text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                        Get started free
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-24 px-6 border-t border-white/5">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <p class="text-sm text-violet-400 font-medium mb-3 uppercase tracking-widest">How it works</p>
                <h2 class="text-3xl md:text-4xl font-bold">Simple. Fast. Private.</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-10 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-lg font-bold text-zinc-300">1</div>
                    <h3 class="font-semibold">Enter your email or domain</h3>
                    <p class="text-zinc-500 text-sm">No account required. Your data is never stored or logged.</p>
                </div>
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-lg font-bold text-zinc-300">2</div>
                    <h3 class="font-semibold">We scan open intelligence sources</h3>
                    <p class="text-zinc-500 text-sm">Real-time checks against breach databases, DNS records and certificate transparency logs.</p>
                </div>
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-lg font-bold text-zinc-300">3</div>
                    <h3 class="font-semibold">Get your full security report</h3>
                    <p class="text-zinc-500 text-sm">Instant, detailed results with actionable recommendations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Privacy section -->
    <section class="py-24 px-6 border-t border-white/5">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-12">
            <div class="flex-1">
                <p class="text-sm text-emerald-400 font-medium mb-3 uppercase tracking-widest">Privacy first</p>
                <h2 class="text-3xl font-bold mb-4">We check your data.<br>We never keep it.</h2>
                <p class="text-zinc-400 leading-relaxed mb-6">
                    Your email address exists only in memory during the request. It is never written to disk, logged, or sent to third parties beyond the breach-checking APIs. Every request is ephemeral by design.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        No account needed for security checks
                    </li>
                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Emails never stored, logged, or cached
                    </li>
                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Open-source APIs only (XposedOrNot, crt.sh)
                    </li>
                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Rate limited to prevent abuse
                    </li>
                </ul>
            </div>
            <div class="flex-1 flex justify-center">
                <div class="glass-card rounded-2xl p-8 w-full max-w-sm text-center">
                    <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-white mb-1">Zero data retention</p>
                    <p class="text-zinc-500 text-sm">Your queries leave no trace on our servers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 px-6 border-t border-white/5">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to check your exposure?</h2>
            <p class="text-zinc-400 mb-8">Free, instant, and completely private. No signup required.</p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('leak-check') }}" class="px-8 py-3.5 bg-white text-black font-semibold rounded-xl hover:bg-zinc-100 transition-all duration-200 text-sm">
                    Check Your Email Now
                </a>
                <a href="{{ route('services') }}" class="px-8 py-3.5 bg-white/5 border border-white/10 text-white font-medium rounded-xl hover:bg-white/10 transition-all duration-200 text-sm">
                    See All Services
                </a>
            </div>
        </div>
    </section>
@endsection
