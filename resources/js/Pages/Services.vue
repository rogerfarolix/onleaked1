<template>
  <PublicLayout title="Services">
    <div class="gradient-bg min-h-screen py-16 px-4">
      <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-xs font-semibold mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Cybersecurity Tools
          </div>
          <h1 class="text-4xl font-bold text-white mb-4">Security Intelligence Suite</h1>
          <p class="text-zinc-400 text-lg max-w-2xl mx-auto">Professional-grade cybersecurity tools to investigate domains, IPs, email headers, SSL certificates, and data breaches — all in one platform.</p>
        </div>

        <!-- Tools grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-20">
          <ToolCard
            href="/leak-check"
            color="rose"
            name="Email Leak Check"
            desc="Discover if an email address has been exposed in known data breaches. Powered by HaveIBeenPwned and LeakCheck APIs."
            cta="Check for leaks"
            badge="Free"
          >
            <template #icon>
              <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/analyze-domain"
            color="amber"
            name="Domain Analysis"
            desc="Full DNS reconnaissance, email security scoring (SPF/DMARC/DKIM), subdomain discovery, and reputation checks."
            cta="Analyze domain"
          >
            <template #icon>
              <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/check-password"
            color="cyan"
            name="Password Breach Check"
            desc="Verify if a password has appeared in public breach datasets. Uses k-anonymity — your password never leaves your browser."
            cta="Check password"
          >
            <template #icon>
              <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/check-ssl"
            color="sky"
            name="SSL Certificate Inspector"
            desc="Inspect TLS certificate details, grade your SSL configuration, check expiry dates, and review Subject Alternative Names."
            cta="Inspect SSL"
          >
            <template #icon>
              <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/check-ip"
            color="indigo"
            name="IP Reputation Intelligence"
            desc="Geolocate any IP, check abuse reports via AbuseIPDB, detect VPN/Tor/proxy usage, and assess threat confidence scores."
            cta="Check IP"
          >
            <template #icon>
              <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/analyze-header"
            color="teal"
            name="Email Header Analyzer"
            desc="Parse raw email headers to trace routing hops, verify SPF/DKIM/DMARC authentication, and detect phishing indicators."
            cta="Analyze headers"
          >
            <template #icon>
              <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
              </svg>
            </template>
          </ToolCard>
        </div>

        <!-- CVE Alerts section -->
        <div class="glass-card p-10 text-center">
          <div class="w-14 h-14 rounded-2xl bg-violet-500/10 flex items-center justify-center mx-auto mb-6">
            <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-white mb-3">CVE Vulnerability Alerts</h2>
          <p class="text-zinc-400 text-base leading-relaxed max-w-xl mx-auto mb-8">
            Subscribe to receive real-time CVE alerts for the technologies you care about — frameworks, CMS, libraries, and more. Get notified by email when critical vulnerabilities are disclosed.
          </p>
          <div class="flex flex-wrap items-center justify-center gap-6 mb-8">
            <div class="flex items-center gap-2 text-sm text-zinc-400">
              <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Real-time CVSS scores
            </div>
            <div class="flex items-center gap-2 text-sm text-zinc-400">
              <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Instant / daily / weekly digest
            </div>
            <div class="flex items-center gap-2 text-sm text-zinc-400">
              <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Technology-specific filtering
            </div>
          </div>
          <Link href="/register" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 text-white font-semibold rounded-xl hover:from-violet-500 hover:to-violet-400 transition-all text-sm">
            Create free account
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ToolCard from '@/Components/ToolCard.vue'
</script>
