<template>
  <PublicLayout title="Leak Check & Digital Footprint">
    <div class="pt-20 pb-20 px-6">
      <div class="max-w-4xl mx-auto">

        <div class="text-center mb-12 fade-up">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-rose-500/20 bg-rose-500/10 text-rose-400 text-xs mb-6">
            <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
            Privacy-first &bull; Zero tracking &bull; Email never stored
          </div>
          <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
            Has your email been <span class="text-rose-400">compromised?</span>
          </h1>
          <p class="text-zinc-400 text-lg max-w-2xl mx-auto">
            Check data breaches and discover your full digital footprint across 120+ platforms. We never store, log, or track your data.
          </p>
        </div>

        <!-- Search bar -->
        <div class="max-w-2xl mx-auto mb-16 fade-up" style="animation-delay:.1s">
          <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:1px;width:1px">
            <input type="text" v-model="honeypot" autocomplete="off" tabindex="-1">
          </div>
          <div class="glass-card rounded-2xl p-2 glow-input transition-all duration-300">
            <form @submit.prevent="checkEmail" class="flex items-center gap-2">
              <div class="flex-1 flex items-center gap-3 px-4">
                <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input v-model="email" type="email" placeholder="Enter your email address…"
                  class="w-full bg-transparent border-none outline-none text-white placeholder-zinc-500 py-3 text-base focus:ring-0" required>
              </div>
              <button type="submit" :disabled="loading"
                class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm">
                <span v-if="!loading">Check Now</span>
                <span v-else class="flex items-center gap-2">
                  <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  Scanning…
                </span>
              </button>
            </form>
          </div>
          <div v-if="error" class="mt-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">{{ error }}</div>
        </div>

        <!-- Results -->
        <div v-if="results" class="fade-up" style="animation-delay:.2s">

          <!-- Score card -->
          <div class="glass-card rounded-2xl p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
              <p class="text-zinc-400 text-sm mb-1">Security Score</p>
              <div class="text-4xl font-bold" :class="scoreColor">
                {{ score }}<span class="text-xl text-zinc-500">/100</span>
              </div>
              <p class="text-sm mt-1 text-zinc-500">{{ scoreLabel }}</p>
            </div>
            <div class="flex gap-2">
              <button @click="downloadCsv" :disabled="csvLoading"
                class="px-4 py-2 bg-white/5 border border-white/10 text-zinc-300 font-semibold rounded-xl hover:bg-white/10 transition-all text-sm flex items-center gap-2 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ csvLoading ? '…' : 'CSV' }}
              </button>
              <button @click="downloadPdf" :disabled="pdfLoading"
                class="px-4 py-2 bg-violet-500 text-white font-semibold rounded-xl hover:bg-violet-400 transition-all text-sm flex items-center gap-2 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ pdfLoading ? 'Generating…' : 'Download PDF' }}
              </button>
            </div>
          </div>

          <!-- Tabs -->
          <div class="flex border-b border-white/10 mb-6">
            <button @click="tab = 'breaches'"
              class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
              :class="tab === 'breaches' ? 'border-rose-400 text-rose-400' : 'border-transparent text-zinc-400 hover:text-white'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
              Data Breaches
              <span v-if="results.breaches?.length" class="px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-xs">{{ results.breaches.length }}</span>
            </button>
            <button @click="tab = 'footprint'"
              class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
              :class="tab === 'footprint' ? 'border-violet-400 text-violet-400' : 'border-transparent text-zinc-400 hover:text-white'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
              Digital Footprint
              <span v-if="results.footprint?.length" class="px-1.5 py-0.5 rounded-full bg-violet-500/20 text-violet-400 text-xs">{{ results.footprint?.length }}</span>
            </button>
          </div>

          <!-- Breaches tab -->
          <div v-show="tab === 'breaches'">
            <div v-if="results.breaches?.length">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div>
                  <p class="font-semibold text-red-400">Email Compromised</p>
                  <p class="text-zinc-500 text-sm">Found in <span class="text-red-400 font-bold">{{ results.breaches.length }}</span> known data breach(es)</p>
                </div>
              </div>
              <div class="space-y-3">
                <div v-for="(b, i) in paginatedBreaches" :key="i" class="glass-card rounded-xl p-5">
                  <div class="flex items-start gap-4">
                    <img v-if="b.logo" :src="b.logo" :alt="b.source" class="w-10 h-10 rounded-lg object-contain bg-white/5 p-1 shrink-0" @error="$event.target.style.display='none'">
                    <div v-else class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center shrink-0 text-red-400 font-bold text-sm">{{ b.source?.charAt(0) }}</div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center justify-between gap-2 mb-1">
                        <h4 class="font-semibold text-white truncate">{{ b.source }}</h4>
                        <div class="flex gap-2 shrink-0">
                          <span v-if="b.password_leaked" class="text-xs px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20">Password exposed</span>
                          <span v-if="b.date" class="text-xs px-2 py-0.5 rounded-full bg-white/5 text-zinc-500 border border-white/10">{{ b.date }}</span>
                        </div>
                      </div>
                      <p class="text-zinc-400 text-sm line-clamp-2">{{ b.description }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="totalBreachPages > 1" class="flex items-center justify-center gap-2 mt-6">
                <button @click="breachPage = Math.max(1, breachPage - 1)" :disabled="breachPage === 1" class="px-3 py-1.5 rounded-lg bg-white/5 text-zinc-400 text-sm disabled:opacity-30 hover:bg-white/10 transition-colors">&larr; Prev</button>
                <span class="text-zinc-500 text-sm">Page {{ breachPage }} of {{ totalBreachPages }}</span>
                <button @click="breachPage = Math.min(totalBreachPages, breachPage + 1)" :disabled="breachPage === totalBreachPages" class="px-3 py-1.5 rounded-lg bg-white/5 text-zinc-400 text-sm disabled:opacity-30 hover:bg-white/10 transition-colors">Next &rarr;</button>
              </div>
            </div>
            <div v-else class="flex flex-col items-center gap-3 py-10">
              <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              </div>
              <p class="font-semibold text-emerald-400 text-lg">No breaches found</p>
              <p class="text-zinc-500 text-sm">Your email does not appear in any known data breach.</p>
            </div>
          </div>

          <!-- Footprint tab -->
          <div v-show="tab === 'footprint'">
            <div v-if="footprintPending" class="flex flex-col items-center gap-4 py-10">
              <div class="w-16 h-16 rounded-full bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-violet-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              </div>
              <p class="font-semibold text-violet-400">Scanning 120+ platforms…</p>
              <p class="text-zinc-500 text-sm">Results will appear automatically, this may take up to 60 seconds.</p>
            </div>
            <div v-else-if="results.footprint?.length">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                  <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                </div>
                <div>
                  <p class="font-semibold text-violet-400">Digital Footprint</p>
                  <p class="text-zinc-500 text-sm">This email is registered on <span class="text-violet-400 font-bold">{{ results.footprint.length }}</span> service(s)</p>
                </div>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <div v-for="(site, i) in results.footprint" :key="i"
                  class="glass-card rounded-xl p-4 flex items-center gap-3 hover:border-violet-500/20 transition-all">
                  <img :src="`https://www.google.com/s2/favicons?sz=32&domain=${site}`" :alt="site" class="w-6 h-6 rounded shrink-0"
                    @error="$event.target.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%238b5cf6\'><circle cx=\'12\' cy=\'12\' r=\'10\'/></svg>'">
                  <span class="text-sm text-zinc-300 truncate">{{ site }}</span>
                </div>
              </div>
            </div>
            <div v-else class="flex flex-col items-center gap-3 py-10">
              <div class="w-16 h-16 rounded-full bg-zinc-500/10 border border-zinc-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z M9 12h6"/></svg>
              </div>
              <p class="font-semibold text-zinc-400">No associated accounts found</p>
              <p class="text-zinc-500 text-sm">We checked 120+ platforms and didn't find this email registered.</p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { apiPost, downloadBlob } from '@/Composables/useApi'

const email     = ref('')
const honeypot  = ref('')
const loading   = ref(false)
const pdfLoading = ref(false)
const csvLoading = ref(false)
const results   = ref(null)
const error     = ref(null)
const tab       = ref('breaches')
const breachPage = ref(1)
const perPage   = 8
const footprintJobId  = ref(null)
const footprintPending = ref(false)
let   pollInterval = null

const score = computed(() => {
  if (!results.value) return 100
  let s = 100
  const b = results.value.breaches ?? []
  s -= Math.min(50, b.length * 15)
  if (b.some(x => x.password_leaked)) s -= 20
  return Math.max(0, s)
})
const scoreColor = computed(() => {
  const s = score.value
  return s >= 80 ? 'text-emerald-400' : s >= 50 ? 'text-amber-400' : 'text-red-400'
})
const scoreLabel = computed(() => {
  const s = score.value
  return s >= 80 ? 'Low Risk — No significant threats detected' : s >= 50 ? 'Medium Risk — Some exposure found' : 'High Risk — Immediate action recommended'
})
const paginatedBreaches = computed(() => {
  if (!results.value?.breaches) return []
  const start = (breachPage.value - 1) * perPage
  return results.value.breaches.slice(start, start + perPage)
})
const totalBreachPages = computed(() => {
  if (!results.value?.breaches) return 1
  return Math.ceil(results.value.breaches.length / perPage)
})

async function checkEmail() {
  loading.value = true; results.value = null; error.value = null
  breachPage.value = 1; tab.value = 'breaches'
  footprintJobId.value = null; footprintPending.value = false
  if (pollInterval) clearInterval(pollInterval)

  try {
    const data = await apiPost('/check-email', { email: email.value, website: honeypot.value })
    if (data.status === 'error') { error.value = data.message; return }
    results.value = data
    if (data.footprint_job_id) {
      footprintJobId.value = data.footprint_job_id
      footprintPending.value = true
      pollInterval = setInterval(pollFootprint, 2000)
    }
  } catch { error.value = 'Connection error. Please try again.' }
  finally { loading.value = false }
}

async function pollFootprint() {
  try {
    const data = await (await fetch(`/footprint-status/${footprintJobId.value}`)).json()
    if (data.status === 'done') {
      clearInterval(pollInterval)
      footprintPending.value = false
      if (results.value) results.value.footprint = data.data ?? []
    } else if (data.status === 'error') {
      clearInterval(pollInterval)
      footprintPending.value = false
    }
  } catch {}
}

async function downloadPdf() {
  pdfLoading.value = true
  try { await downloadBlob('/pdf/leak-check', { email: email.value, results: results.value }, 'onleaked-breach-report.pdf') }
  catch { error.value = 'Could not generate PDF.' }
  finally { pdfLoading.value = false }
}

async function downloadCsv() {
  csvLoading.value = true
  try { await downloadBlob('/csv/leak-check', { email: email.value, results: results.value }, 'onleaked-breach-report.csv') }
  catch { error.value = 'Could not generate CSV.' }
  finally { csvLoading.value = false }
}
</script>
