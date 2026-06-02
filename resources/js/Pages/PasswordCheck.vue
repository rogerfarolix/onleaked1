<template>
  <PublicLayout title="Password Breach Check">
    <div class="gradient-bg min-h-screen py-16 px-4">
      <div class="max-w-2xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            HaveIBeenPwned
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Password Breach Check</h1>
          <p class="text-zinc-400 text-lg">Check if your password has appeared in known data breaches</p>
        </div>

        <!-- Privacy notice -->
        <div class="glass-card p-4 mb-6 flex items-start gap-3 border-cyan-500/10">
          <svg class="w-4 h-4 text-cyan-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-zinc-400 text-xs leading-relaxed">Your password is hashed locally using k-anonymity — only the first 5 characters of the SHA-1 hash are sent. Your actual password never leaves your browser.</p>
        </div>

        <!-- Form -->
        <div class="glass-card p-8 mb-8">
          <form @submit.prevent="submit">
            <input v-model="honeypot" type="text" name="website" autocomplete="off" class="hidden" tabindex="-1" aria-hidden="true" />
            <label class="block text-zinc-400 text-sm mb-2">Password to check</label>
            <div class="flex gap-3">
              <input
                v-model="password"
                type="text"
                placeholder="Enter a password to check…"
                required
                class="flex-1 w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-cyan-500/50 font-mono"
              />
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold rounded-xl hover:from-cyan-500 hover:to-cyan-400 transition-all text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap"
              >
                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span>{{ loading ? 'Checking…' : 'Check' }}</span>
              </button>
            </div>
            <p v-if="error" class="mt-3 text-red-400 text-sm">{{ error }}</p>
          </form>
        </div>

        <!-- Results -->
        <div v-if="results">
          <div class="glass-card p-8">

            <!-- Count display -->
            <div class="text-center mb-8">
              <div class="text-7xl font-black mb-3"
                :class="{
                  'text-emerald-400': results.risk === 0,
                  'text-amber-400': results.risk === 1,
                  'text-orange-400': results.risk === 2,
                  'text-red-400': results.risk === 3,
                }">
                {{ results.count?.toLocaleString() ?? 0 }}
              </div>
              <div class="text-zinc-400 text-sm mb-4">times found in breaches</div>

              <!-- Risk badge -->
              <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold"
                :class="{
                  'bg-emerald-500/20 text-emerald-400': results.risk === 0,
                  'bg-amber-500/20 text-amber-400': results.risk === 1,
                  'bg-orange-500/20 text-orange-400': results.risk === 2,
                  'bg-red-500/20 text-red-400': results.risk === 3,
                }">
                <span class="w-2 h-2 rounded-full"
                  :class="{
                    'bg-emerald-400': results.risk === 0,
                    'bg-amber-400': results.risk === 1,
                    'bg-orange-400': results.risk === 2,
                    'bg-red-400': results.risk === 3,
                  }"></span>
                {{ results.risk_label ?? 'Unknown' }}
              </span>
            </div>

            <!-- Recommendations -->
            <div class="border-t border-white/6 pt-6">
              <h3 class="text-sm font-semibold text-zinc-300 mb-4">Recommendations</h3>
              <ul class="space-y-3">
                <template v-if="results.risk === 0">
                  <li class="flex items-start gap-2.5 text-sm text-zinc-400">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    This password has not appeared in known breaches. Good choice!
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-zinc-400">
                    <svg class="w-4 h-4 text-cyan-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Still use a unique password per account and enable 2FA wherever possible.
                  </li>
                </template>
                <template v-else>
                  <li class="flex items-start gap-2.5 text-sm text-zinc-400">
                    <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Change this password immediately on all accounts where it is used.
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-zinc-400">
                    <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Use a password manager to generate and store unique, strong passwords.
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-zinc-400">
                    <svg class="w-4 h-4 text-cyan-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Enable two-factor authentication (2FA) on all critical accounts.
                  </li>
                </template>
              </ul>
            </div>

            <!-- PDF export -->
            <div class="mt-6 pt-6 border-t border-white/6">
              <button @click="downloadPdf" class="w-full py-2.5 bg-white/5 border border-white/10 text-zinc-300 text-sm rounded-xl hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF Report
              </button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { apiPost, downloadBlob } from '@/Composables/useApi'

const password = ref('')
const honeypot = ref('')
const loading = ref(false)
const error = ref(null)
const results = ref(null)

async function submit() {
  if (!password.value.trim()) return
  loading.value = true
  error.value = null
  results.value = null
  try {
    const data = await apiPost('/check-password', { password: password.value, website: honeypot.value })
    if (data.error) {
      error.value = data.error
    } else {
      results.value = data
    }
  } catch (e) {
    error.value = 'An unexpected error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}

async function downloadPdf() {
  await downloadBlob(
    '/pdf/password-check',
    { count: results.value.count, risk: results.value.risk, risk_label: results.value.risk_label },
    'onleaked-password-report.pdf'
  )
}
</script>
