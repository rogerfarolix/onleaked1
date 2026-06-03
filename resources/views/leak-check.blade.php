@extends('layouts.public')

@section('title', 'Leak Check & Digital Footprint Onleaked')

@section('content')
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto text-center" x-data="leakChecker()">

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 text-xs text-zinc-400 mb-8 fade-up">
                <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
                Privacy-first &bull; Zero tracking &bull; Email never stored
            </div>

            <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-4 fade-up" style="animation-delay:.1s">
                Has your email been<br>
                <span class="bg-linear-to-r from-violet-400 via-rose-400 to-amber-400 bg-clip-text text-transparent">compromised?</span>
            </h1>
            <p class="text-zinc-400 text-lg mb-10 max-w-xl mx-auto fade-up" style="animation-delay:.2s">
                Check data breaches and discover your full digital footprint across 120+ platforms. We never store, log, or track your data.
            </p>

            <!-- Search bar -->
            <div class="max-w-xl mx-auto fade-up" style="animation-delay:.3s">
                {{-- Honeypot: visually hidden, bots fill it, humans don't --}}
                <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:1px;width:1px">
                    <label for="lc-website">Leave this field empty</label>
                    <input type="text" name="website" id="lc-website" x-model="honeypot" autocomplete="off" tabindex="-1">
                </div>
                <div class="glass-card rounded-2xl p-2 glow-input transition-all duration-300">
                    <form @submit.prevent="checkEmail" class="flex items-center gap-2">
                        <div class="flex-1 flex items-center gap-3 px-4">
                            <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input
                                x-model="email"
                                type="email"
                                placeholder="Enter your email address..."
                                class="w-full bg-transparent border-none outline-none text-white placeholder-zinc-500 py-3 text-base focus:ring-0"
                                required
                            >
                        </div>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm"
                        >
                            <span x-show="!loading">Check Now</span>
                            <span x-show="loading" x-cloak class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
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

                    <!-- Security Score Card -->
                    <div class="glass-card rounded-2xl p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-5">
                            <!-- SVG Gauge -->
                            <div class="relative w-20 h-20 shrink-0">
                                <svg class="w-20 h-20 -rotate-90" viewBox="0 0 72 72">
                                    <circle cx="36" cy="36" r="30" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="6"/>
                                    <circle cx="36" cy="36" r="30" fill="none" stroke-width="6" stroke-linecap="round"
                                        :stroke="scoreColor() === 'emerald' ? '#34d399' : (scoreColor() === 'amber' ? '#fbbf24' : '#f87171')"
                                        :style="'stroke-dasharray: 188.5; stroke-dashoffset: ' + gaugeOffset()">
                                    </circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center flex-col">
                                    <span class="text-base font-bold leading-none"
                                        :class="{'text-emerald-400':scoreColor()==='emerald','text-amber-400':scoreColor()==='amber','text-red-400':scoreColor()==='red'}"
                                        x-text="computeScore()"></span>
                                    <span class="text-[9px] text-zinc-500 leading-none mt-0.5">/100</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-zinc-400 text-sm mb-1">Security Score</p>
                                <p class="text-sm mt-1 text-zinc-400" x-text="scoreLabel()"></p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="downloadCsv()" :disabled="csvLoading"
                                class="px-4 py-2 bg-white/5 border border-white/10 text-zinc-300 font-semibold rounded-xl hover:bg-white/10 transition-all text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span x-show="!csvLoading">CSV</span>
                                <span x-show="csvLoading">...</span>
                            </button>
                            <button @click="downloadPdf()" :disabled="pdfLoading"
                                class="px-4 py-2 bg-violet-500 text-white font-semibold rounded-xl hover:bg-violet-400 transition-all text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span x-show="!pdfLoading">Download PDF</span>
                                <span x-show="pdfLoading">Generating…</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-white/10 mb-6">
                        <button
                            @click="tab = 'breaches'"
                            :class="tab === 'breaches' ? 'border-rose-400 text-rose-400' : 'border-transparent text-zinc-400 hover:text-white'"
                            class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Data Breaches
                            <span x-show="results.breaches.length" class="ml-1 px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-xs" x-text="results.breaches.length"></span>
                        </button>
                        <button
                            @click="tab = 'footprint'"
                            :class="tab === 'footprint' ? 'border-violet-400 text-violet-400' : 'border-transparent text-zinc-400 hover:text-white'"
                            class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
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
                                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-red-400">Email Compromised</p>
                                        <p class="text-zinc-500 text-sm">Found in <span x-text="results.breaches.length" class="text-red-400 font-bold"></span> known data breach(es)</p>
                                    </div>
                                </div>

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
                                                    <a x-show="breach.domain" :href="'https://' + breach.domain" target="_blank" rel="noopener noreferrer" class="text-xs text-violet-400 hover:text-violet-300 mt-1 inline-block" x-text="breach.domain"></a>
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
                                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-emerald-400 text-lg">No breaches found</p>
                                <p class="text-zinc-500 text-sm">Your email does not appear in any known data breach.</p>
                            </div>
                        </template>
                    </div>

                    <!-- TAB: Digital Footprint -->
                    <div x-show="tab === 'footprint'" x-cloak>
                        <!-- Scanning spinner -->
                        <template x-if="footprintPending">
                            <div class="flex flex-col items-center gap-4 py-10">
                                <div class="w-16 h-16 rounded-full bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-violet-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-violet-400">Scanning 120+ platforms…</p>
                                <p class="text-zinc-500 text-sm">This may take up to 60 seconds. Results will appear automatically.</p>
                            </div>
                        </template>
                        <template x-if="!footprintPending && results.footprint && results.footprint.length > 0">
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 rounded-full bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-violet-400">Digital Footprint</p>
                                        <p class="text-zinc-500 text-sm">This email is registered on <span x-text="results.footprint.length" class="text-violet-400 font-bold"></span> service(s)</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <template x-for="(site, i) in results.footprint" :key="i">
                                        <div class="glass-card rounded-xl p-4 flex items-center gap-3 hover:border-violet-500/20 transition-all">
                                            <img
                                                :src="'https://www.google.com/s2/favicons?sz=32&domain=' + site"
                                                :alt="site"
                                                class="w-6 h-6 rounded shrink-0"
                                                onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%238b5cf6%22><circle cx=%2212%22 cy=%2212%22 r=%2210%22/></svg>'"
                                            >
                                            <span class="text-sm text-zinc-300 truncate" x-text="site"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="!footprintPending && (!results.footprint || results.footprint.length === 0)">
                            <div class="flex flex-col items-center gap-3 py-10">
                                <div class="w-16 h-16 rounded-full bg-zinc-500/10 border border-zinc-500/20 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z M9 12h6"/>
                                    </svg>
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
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
    function leakChecker() {
        return {
            email: '',
            honeypot: '',
            loading: false,
            pdfLoading: false,
            csvLoading: false,
            results: null,
            error: null,
            tab: 'breaches',
            breachPage: 1,
            perPage: 8,
            footprintJobId: null,
            footprintPollInterval: null,
            get footprintPending() {
                return this.footprintJobId !== null && this.results !== null;
            },
            computeScore() {
                let score = 100;
                const b = this.results?.breaches || [];
                score -= Math.min(50, b.length * 15);
                if (b.some(x => x.password_leaked)) score -= 20;
                return Math.max(0, score);
            },
            gaugeOffset() {
                return 188.5 - (188.5 * this.computeScore() / 100);
            },
            scoreColor() {
                const s = this.computeScore();
                return s >= 80 ? 'emerald' : (s >= 50 ? 'amber' : 'red');
            },
            scoreLabel() {
                const s = this.computeScore();
                return s >= 80 ? 'Low Risk No significant threats detected' : (s >= 50 ? 'Medium Risk Some exposure found' : 'High Risk Immediate action recommended');
            },
            async downloadPdf() {
                this.pdfLoading = true;
                try {
                    const res = await fetch('{{ route("pdf.leak-check") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ email: this.email, results: this.results }),
                    });
                    if (!res.ok) throw new Error('Failed');
                    const blob = await res.blob();
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a'); a.href = url; a.download = 'onleaked-breach-report.pdf'; a.click();
                    URL.revokeObjectURL(url);
                } catch { this.error = 'Could not generate PDF. Please try again.'; }
                finally { this.pdfLoading = false; }
            },
            async downloadCsv() {
                this.csvLoading = true;
                try {
                    const res = await fetch('{{ route("csv.leak-check") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ email: this.email, results: this.results }),
                    });
                    if (!res.ok) throw new Error('Failed');
                    const blob = await res.blob();
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a'); a.href = url; a.download = 'onleaked-breach-report.csv'; a.click();
                    URL.revokeObjectURL(url);
                } catch { this.error = 'Could not generate CSV. Please try again.'; }
                finally { this.csvLoading = false; }
            },
            async checkEmail() {
                this.loading = true;
                this.results = null;
                this.error = null;
                this.breachPage = 1;
                this.tab = 'breaches';
                this.footprintJobId = null;
                if (this.footprintPollInterval) { clearInterval(this.footprintPollInterval); this.footprintPollInterval = null; }
                try {
                    const res = await fetch('{{ route("check-email") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ email: this.email, website: this.honeypot }),
                    });
                    const data = await res.json();
                    if (data.status === 'error') {
                        this.error = data.message;
                    } else {
                        this.results = data;
                        if (data.footprint_job_id) {
                            this.footprintJobId = data.footprint_job_id;
                            this.pollFootprint(data.footprint_job_id);
                        }
                    }
                } catch { this.error = 'Connection error. Please try again.'; }
                finally { this.loading = false; }
            },
            pollFootprint(jobId) {
                this.footprintPollInterval = setInterval(async () => {
                    try {
                        const res = await fetch(`/footprint-status/${jobId}`);
                        const data = await res.json();
                        if (data.status === 'done') {
                            clearInterval(this.footprintPollInterval);
                            this.footprintPollInterval = null;
                            this.footprintJobId = null;
                            if (this.results) this.results.footprint = data.data || [];
                        } else if (data.status === 'error') {
                            clearInterval(this.footprintPollInterval);
                            this.footprintPollInterval = null;
                            this.footprintJobId = null;
                            if (this.results) this.results.footprint = [];
                        }
                    } catch { /* keep polling */ }
                }, 2000);
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
