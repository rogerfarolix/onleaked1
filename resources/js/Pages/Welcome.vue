<template>
  <PublicLayout title="Cybersecurity Intelligence Platform">

    <!-- Hero -->
    <section class="pt-36 pb-24 px-6">
      <div class="max-w-5xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 text-xs text-zinc-400 mb-8 fade-up">
          <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
          Privacy-first &bull; Zero tracking &bull; Open intelligence
        </div>
        <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6 fade-up" style="animation-delay:.1s">
          Take back control of<br>
          <span class="bg-gradient-to-r from-violet-400 via-rose-400 to-amber-400 bg-clip-text text-transparent">your digital security</span>
        </h1>
        <p class="text-zinc-400 text-xl mb-12 max-w-2xl mx-auto fade-up" style="animation-delay:.2s">
          Six powerful security tools in one platform. Check breaches, audit domains, inspect certificates — all privacy-first.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4 fade-up" style="animation-delay:.3s">
          <Link :href="route('leak-check')" class="px-8 py-3.5 bg-white text-black font-semibold rounded-xl hover:bg-zinc-100 transition-all text-sm">
            Check Your Email
          </Link>
          <Link :href="route('domain.show')" class="px-8 py-3.5 bg-white/5 border border-white/10 text-white font-medium rounded-xl hover:bg-white/10 transition-all text-sm">
            Analyze a Domain
          </Link>
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="py-10 px-6 border-y border-white/5" ref="statsRef">
      <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div v-for="s in stats" :key="s.label">
          <p class="text-3xl font-bold text-white">{{ s.display }}</p>
          <p class="text-sm text-zinc-500 mt-1">{{ s.label }}</p>
        </div>
      </div>
    </section>

    <!-- Tools grid -->
    <section class="py-24 px-6">
      <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
          <p class="text-sm text-violet-400 font-medium mb-3 uppercase tracking-widest">Tools</p>
          <h2 class="text-3xl md:text-4xl font-bold mb-4">Six powerful tools, one platform.</h2>
          <p class="text-zinc-400 max-w-xl mx-auto">No account required for all security checks. Free, instant, privacy-first.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
          <ToolCard v-for="tool in tools" :key="tool.name" v-bind="tool" />
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
          <div v-for="step in steps" :key="step.n" class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-lg font-bold text-zinc-300">{{ step.n }}</div>
            <h3 class="font-semibold">{{ step.title }}</h3>
            <p class="text-zinc-500 text-sm">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Privacy -->
    <section class="py-24 px-6 border-t border-white/5">
      <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1">
          <p class="text-sm text-emerald-400 font-medium mb-3 uppercase tracking-widest">Privacy first</p>
          <h2 class="text-3xl font-bold mb-4">We check your data.<br>We never keep it.</h2>
          <p class="text-zinc-400 leading-relaxed mb-6">Your email or domain exists only in memory during the request. Never written to disk, never logged, never cached beyond the analysis window.</p>
          <ul class="space-y-3">
            <li v-for="item in privacyItems" :key="item" class="flex items-center gap-3 text-sm text-zinc-300">
              <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              {{ item }}
            </li>
          </ul>
        </div>
        <div class="flex-1 flex justify-center">
          <div class="glass-card p-8 w-full max-w-sm text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
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
          <Link :href="route('leak-check')" class="px-8 py-3.5 bg-white text-black font-semibold rounded-xl hover:bg-zinc-100 transition-all text-sm">Check Your Email Now</Link>
          <Link :href="route('services')" class="px-8 py-3.5 bg-white/5 border border-white/10 text-white font-medium rounded-xl hover:bg-white/10 transition-all text-sm">See All Services</Link>
        </div>
      </div>
    </section>

  </PublicLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ToolCard from '@/Components/ToolCard.vue'

const statsRef = ref(null)
const stats = ref([
  { label: 'breached records indexed', target: 15, suffix: 'B+', display: '0' },
  { label: 'platforms scanned',        target: 120, suffix: '+', display: '0' },
  { label: 'security tools',           target: 6,  suffix: '',  display: '0' },
  { label: 'bytes of your data stored',target: 0,  suffix: '',  display: '0' },
])

const tools = [
  { href: '/leak-check',     color: 'rose',   name: 'Leak Check',        desc: 'Verify if your email was exposed in a breach and discover your digital footprint across 120+ platforms.', cta: 'Try it free' },
  { href: '/domain',         color: 'amber',  name: 'Domain Analysis',   desc: 'Complete domain intelligence: DNS records, SPF/DMARC audit, subdomain enumeration and reputation check.', cta: 'Analyze now' },
  { href: '/password-check', color: 'cyan',   name: 'Password Check',    desc: 'Check if a password has been exposed using k-anonymity. Your password is never sent in full.', cta: 'Check password' },
  { href: '/ssl-check',      color: 'sky',    name: 'SSL Inspector',     desc: 'Inspect any domain\'s certificate — issuer, validity, SANs, days until expiry and security grade.', cta: 'Inspect SSL' },
  { href: '/ip-check',       color: 'indigo', name: 'IP Reputation',     desc: 'Geolocation, ISP, ASN lookup and abuse confidence score from global threat intelligence.', cta: 'Check IP' },
  { href: '/register',       color: 'violet', name: 'CVE Alerts',        desc: 'Subscribe to technologies and receive AI-powered email alerts when new CVEs are published.', cta: 'Get started free', badge: 'PRO' },
]

const steps = [
  { n: 1, title: 'Enter any target', desc: 'Email, domain, password, IP address, or raw email headers. No account required.' },
  { n: 2, title: 'We scan open intelligence sources', desc: 'Real-time checks against breach databases, DNS records and certificate transparency logs.' },
  { n: 3, title: 'Get your full security report', desc: 'Instant, detailed results with actionable recommendations and PDF export.' },
]

const privacyItems = [
  'No account needed for security checks',
  'Emails never stored, logged, or cached',
  'Open-source APIs only (XposedOrNot, crt.sh)',
  'Rate limited to prevent abuse',
]

onMounted(() => {
  if (!statsRef.value) return
  const obs = new IntersectionObserver(([entry]) => {
    if (!entry.isIntersecting) return
    obs.disconnect()
    stats.value.forEach(s => {
      if (s.target === 0) { s.display = '0'; return }
      let v = 0
      const step = Math.max(1, Math.ceil(s.target / 40))
      const id = setInterval(() => {
        v = Math.min(v + step, s.target)
        s.display = v + s.suffix
        if (v >= s.target) clearInterval(id)
      }, 30)
    })
  }, { threshold: 0.3 })
  obs.observe(statsRef.value)
})
</script>
