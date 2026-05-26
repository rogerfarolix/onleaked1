@extends('layouts.public')

@section('content')
    <!-- Hero + Leak Check -->
    <section id="check" class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto text-center" x-data="leakChecker()">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 text-xs text-zinc-400 mb-8 fade-up">
                <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
                Privacy-first • Zero tracking • Open source APIs
            </div>
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-4 fade-up" style="animation-delay:.1s">
                Has your email been<br>
                <span class="bg-gradient-to-r from-violet-400 via-rose-400 to-amber-400 bg-clip-text text-transparent">compromised?</span>
            </h1>
            <p class="text-zinc-400 text-lg mb-10 max-w-xl mx-auto fade-up" style="animation-delay:.2s">
                Check data breaches and discover your full digital footprint. We never store, log, or track your data.
            </p>

            <!-- Search Bar -->
            <div class="max-w-xl mx-auto fade-up" style="animation-delay:.3s">
                <div class="glass-card rounded-2xl p-2 glow-input transition-all duration-300">
                    <form @submit.prevent="checkEmail" class="flex items-center gap-2">
                        <div class="flex-1 flex items-center gap-3 px-4">
                            <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input x-model="email" type="email" id="leak-check-email" placeholder="Enter your email address..." class="w-full bg-transparent border-none outline-none text-white placeholder-zinc-500 py-3 text-base focus:ring-0" required>
                        </div>
                        <button type="submit" :disabled="loading" class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm">
                            <span x-show="!loading">Check Now</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Scanning...
                            </span>
                        </button>
                    </form>
                </div>
                <template x-if="error">
                    <div class="mt-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm" x-text="error"></div>
                </template>
            </div>

            <!-- Results -->
            <template x-if="results !== null && !error">
                <div class="max-w-4xl mx-auto mt-10 fade-up text-left">

                    <!-- Tabs: Breaches / Footprint -->
                    <div class="flex border-b border-white/10 mb-6">
                        <button @click="tab = 'breaches'" :class="tab === 'breaches' ? 'border-rose-400 text-rose-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            Data Breaches 
                            <span x-show="results.breaches.length" class="ml-1 px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-xs" x-text="results.breaches.length"></span>
                        </button>
                        <button @click="tab = 'footprint'" :class="tab === 'footprint' ? 'border-violet-400 text-violet-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            Digital Footprint 
                            <span x-show="results.footprint && results.footprint.length" class="ml-1 px-1.5 py-0.5 rounded-full bg-violet-500/20 text-violet-400 text-xs" x-text="results.footprint ? results.footprint.length : 0"></span>
                        </button>
                    </div>

                    <!-- TAB: Breaches -->
                    <div x-show="tab === 'breaches'" x-cloak>
                        <template x-if="results.breaches.length > 0">
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-red-400">Email Compromised</p>
                                        <p class="text-zinc-500 text-sm">Found in <span x-text="results.breaches.length" class="text-red-400 font-bold"></span> known data breach(es)</p>
                                    </div>
                                </div>

                                <!-- Paginated breach cards -->
                                <div class="space-y-3">
                                    <template x-for="(breach, i) in paginatedBreaches()" :key="i">
                                        <div class="glass-card rounded-xl p-5 hover:border-white/10 transition-all">
                                            <div class="flex items-start gap-4">
                                                <img x-show="breach.logo" :src="breach.logo" :alt="breach.source" class="w-10 h-10 rounded-lg object-contain bg-white/5 p-1 shrink-0" onerror="this.style.display='none'">
                                                <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center shrink-0 text-red-400 font-bold text-sm" x-show="!breach.logo" x-text="breach.source.charAt(0)"></div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-2 mb-1">
                                                        <h4 class="font-semibold text-white truncate" x-text="breach.source"></h4>
                                                        <div class="flex gap-2 shrink-0">
                                                            <span x-show="breach.password_leaked" class="text-xs px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20">Password exposed</span>
                                                            <span x-show="breach.date" class="text-xs px-2 py-0.5 rounded-full bg-white/5 text-zinc-500 border border-white/10" x-text="breach.date"></span>
                                                        </div>
                                                    </div>
                                                    <p class="text-zinc-400 text-sm line-clamp-2" x-text="breach.description"></p>
                                                    <a x-show="breach.domain" :href="'https://' + breach.domain" target="_blank" class="text-xs text-violet-400 hover:text-violet-300 mt-1 inline-block" x-text="breach.domain"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Pagination -->
                                <div x-show="totalBreachPages() > 1" class="flex items-center justify-center gap-2 mt-6">
                                    <button @click="breachPage = Math.max(1, breachPage - 1)" :disabled="breachPage === 1" class="px-3 py-1.5 rounded-lg bg-white/5 text-zinc-400 text-sm disabled:opacity-30 hover:bg-white/10 transition-colors">&larr; Prev</button>
                                    <span class="text-zinc-500 text-sm">Page <span x-text="breachPage"></span> of <span x-text="totalBreachPages()"></span></span>
                                    <button @click="breachPage = Math.min(totalBreachPages(), breachPage + 1)" :disabled="breachPage === totalBreachPages()" class="px-3 py-1.5 rounded-lg bg-white/5 text-zinc-400 text-sm disabled:opacity-30 hover:bg-white/10 transition-colors">Next &rarr;</button>
                                </div>
                            </div>
                        </template>
                        <template x-if="results.breaches.length === 0">
                            <div class="flex flex-col items-center gap-3 py-10">
                                <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <p class="font-semibold text-emerald-400 text-lg">No breaches found</p>
                                <p class="text-zinc-500 text-sm">Your email does not appear in any known data breach.</p>
                            </div>
                        </template>
                    </div>

                    <!-- TAB: Digital Footprint -->
                    <div x-show="tab === 'footprint'" x-cloak>
                        <template x-if="results.footprint && results.footprint.length > 0">
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 rounded-full bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-violet-400">Digital Footprint</p>
                                        <p class="text-zinc-500 text-sm">This email is registered on <span x-text="results.footprint.length" class="text-violet-400 font-bold"></span> service(s)</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <template x-for="(site, i) in results.footprint" :key="i">
                                        <div class="glass-card rounded-xl p-4 flex items-center gap-3 hover:border-violet-500/20 transition-all">
                                            <img :src="'https://www.google.com/s2/favicons?sz=32&domain=' + site" :alt="site" class="w-6 h-6 rounded shrink-0" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%238b5cf6%22><circle cx=%2212%22 cy=%2212%22 r=%2210%22/></svg>'">
                                            <span class="text-sm text-zinc-300 truncate" x-text="site"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="!results.footprint || results.footprint.length === 0">
                            <div class="flex flex-col items-center gap-3 py-10">
                                <div class="w-16 h-16 rounded-full bg-zinc-500/10 border border-zinc-500/20 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z M9 12h6"/></svg>
                                </div>
                                <p class="font-semibold text-zinc-400">No associated accounts found</p>
                                <p class="text-zinc-500 text-sm">We checked 120+ platforms and didn't find this email registered.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 px-6 border-t border-white/5">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Cybersecurity Intelligence Platform</h2>
                <p class="text-zinc-400 max-w-xl mx-auto">Three powerful services to keep your digital life secure and monitored.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <a href="{{ route('services') }}#alerts" class="glass-card rounded-2xl p-6 hover:border-violet-500/20 transition-all duration-300 group block">
                    <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center mb-4 group-hover:bg-violet-500/20 transition-colors">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Vulnerability Alerts</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">Subscribe to technologies and get AI-powered alerts when new CVEs and advisories are published.</p>
                </a>
                <a href="{{ route('services') }}#leakcheck" class="glass-card rounded-2xl p-6 hover:border-rose-500/20 transition-all duration-300 group block">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center mb-4 group-hover:bg-rose-500/20 transition-colors">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Leak Check & Footprint</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">Verify if your email has been compromised and discover your digital footprint across 120+ sites.</p>
                </a>
                <a href="{{ route('domain.show') }}" class="glass-card rounded-2xl p-6 hover:border-amber-500/20 transition-all duration-300 group block">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center mb-4 group-hover:bg-amber-500/20 transition-colors">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Domain Analysis</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">Complete domain intelligence: DNS, subdomains, reputation and email configuration.</p>
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function leakChecker() {
        return {
            email: '',
            loading: false,
            results: null,
            error: null,
            tab: 'breaches',
            breachPage: 1,
            perPage: 8,
            async checkEmail() {
                this.loading = true;
                this.results = null;
                this.error = null;
                this.breachPage = 1;
                this.tab = 'breaches';
                try {
                    const res = await fetch('{{ route("check-email") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ email: this.email }),
                    });
                    const data = await res.json();
                    if (data.status === 'error') {
                        this.error = data.message;
                    } else {
                        this.results = data;
                    }
                } catch (e) {
                    this.error = 'Connection error. Please try again.';
                } finally {
                    this.loading = false;
                }
            },
            paginatedBreaches() {
                if (!this.results || !this.results.breaches) return [];
                const start = (this.breachPage - 1) * this.perPage;
                return this.results.breaches.slice(start, start + this.perPage);
            },
            totalBreachPages() {
                if (!this.results || !this.results.breaches) return 1;
                return Math.ceil(this.results.breaches.length / this.perPage);
            }
        }
    }
</script>
@endpush
