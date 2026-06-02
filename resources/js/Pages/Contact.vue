<template>
  <PublicLayout title="Contact">
    <div class="gradient-bg min-h-screen py-16 px-4">
      <div class="max-w-2xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-xs font-semibold mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Get in touch
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Contact Us</h1>
          <p class="text-zinc-400 text-lg">Questions, partnerships, or security disclosures — we're here.</p>
        </div>

        <!-- Contact card -->
        <div class="glass-card p-10 mb-6">
          <div class="flex items-start gap-5 mb-8">
            <div class="w-12 h-12 rounded-xl bg-violet-500/10 flex items-center justify-center shrink-0">
              <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div>
              <h2 class="text-white font-semibold text-lg mb-1">Nealix</h2>
              <p class="text-zinc-400 text-sm leading-relaxed">Cybersecurity SaaS solutions for professionals and organizations. Helping you stay ahead of threats with actionable intelligence.</p>
            </div>
          </div>

          <div class="space-y-5">
            <!-- Website -->
            <div class="flex items-center gap-4 p-4 bg-white/3 rounded-xl">
              <div class="w-9 h-9 rounded-lg bg-amber-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
              </div>
              <div>
                <div class="text-xs text-zinc-500 mb-0.5">Website</div>
                <a href="https://onleaked.com" target="_blank" rel="noopener noreferrer" class="text-white text-sm hover:text-violet-400 transition-colors">onleaked.com</a>
              </div>
            </div>

            <!-- Email -->
            <div class="flex items-center gap-4 p-4 bg-white/3 rounded-xl">
              <div class="w-9 h-9 rounded-lg bg-violet-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <div class="text-xs text-zinc-500 mb-0.5">Email</div>
                <a href="mailto:contact@nealix.com" class="text-white text-sm hover:text-violet-400 transition-colors">contact@nealix.com</a>
              </div>
            </div>

            <!-- Security disclosure -->
            <div class="flex items-center gap-4 p-4 bg-white/3 rounded-xl">
              <div class="w-9 h-9 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              </div>
              <div>
                <div class="text-xs text-zinc-500 mb-0.5">Security Disclosures</div>
                <a href="mailto:security@nealix.com" class="text-white text-sm hover:text-emerald-400 transition-colors">security@nealix.com</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Response time note -->
        <div class="glass-card p-6 flex items-start gap-3">
          <svg class="w-4 h-4 text-zinc-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-zinc-400 text-sm leading-relaxed">We typically respond within 1–2 business days. For urgent security disclosures, please mark your email subject with <span class="text-zinc-200 font-mono text-xs bg-white/5 px-1.5 py-0.5 rounded">[SECURITY]</span> for priority handling.</p>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
</script>
