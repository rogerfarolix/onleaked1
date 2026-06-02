<template>
  <PublicLayout title="Domain Analysis">
    <div class="gradient-bg min-h-screen py-16 px-4">
      <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
            Domain Intelligence
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Domain Analysis</h1>
          <p class="text-zinc-400 text-lg">Analyze DNS records, email security, subdomains and reputation</p>
        </div>

        <!-- Form -->
        <div class="glass-card p-8 mb-8">
          <form @submit.prevent="submit">
            <!-- Honeypot -->
            <input v-model="honeypot" type="text" name="website" autocomplete="off" class="hidden" tabindex="-1" aria-hidden="true" />

            <div class="flex gap-3">
              <input
                v-model="domain"
                type="text"
                placeholder="example.com"
                required
                class="flex-1 w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-amber-500/50"
              />
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 text-black font-semibold rounded-xl hover:from-amber-400 hover:to-amber-300 transition-all text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap"
              >
                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span>{{ loading ? 'Analyzing…' : 'Analyze' }}</span>
              </button>
            </div>

            <p v-if="error" class="mt-3 text-red-400 text-sm">{{ error }}</p>
          </form>
        </div>

        <!-- Results -->
        <div v-if="results">

          <!-- Score + actions row -->
          <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
              <div class="text-center">
                <div class="text-4xl font-bold" :class="scoreColor">{{ score }}</div>
                <div class="text-xs text-zinc-500 mt-0.5">Trust Score</div>
              </div>
              <div>
                <div class="text-white font-semibold">{{ domain }}</div>
                <div class="text-zinc-400 text-sm">Domain analysis complete</div>
              </div>
            </div>
            <div class="flex flex-wrap gap-2">
              <button @click="downloadPdf" class="px-4 py-2 bg-white/5 border border-white/10 text-zinc-300 text-sm rounded-xl hover:bg-white/10 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
              </button>
              <button @click="downloadCsv" class="px-4 py-2 bg-white/5 border border-white/10 text-zinc-300 text-sm rounded-xl hover:bg-white/10 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                CSV
              </button>
              <button @click="shareReport" :disabled="sharing" class="px-4 py-2 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm rounded-xl hover:bg-amber-500/20 transition-all flex items-center gap-2 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                {{ sharing ? 'Sharing…' : 'Share' }}
              </button>
            </div>
          </div>

          <!-- Share URL banner -->
          <div v-if="shareUrl" class="glass-card p-4 mb-6 border-amber-500/20 flex items-center gap-3">
            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <span class="text-zinc-300 text-sm flex-1 font-mono break-all">{{ shareUrl }}</span>
            <button @click="copyShare" class="text-amber-400 text-xs hover:text-amber-300">Copy</button>
          </div>

          <!-- Tabs -->
          <div class="glass-card overflow-hidden">
            <div class="flex border-b border-white/6">
              <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                class="px-5 py-3.5 text-sm font-medium transition-colors"
                :class="activeTab === tab ? 'text-amber-400 border-b-2 border-amber-400' : 'text-zinc-400 hover:text-zinc-200'"
              >{{ tab }}</button>
            </div>

            <div class="p-6">

              <!-- DNS Records -->
              <div v-if="activeTab === 'DNS Records'">
                <div v-if="results.dns && Object.keys(results.dns).length">
                  <div v-for="(records, type) in results.dns" :key="type" class="mb-6 last:mb-0">
                    <div class="flex items-center gap-2 mb-3">
                      <span class="text-xs font-bold px-2 py-0.5 rounded bg-amber-500/10 text-amber-400">{{ type }}</span>
                    </div>
                    <div class="space-y-1.5">
                      <div v-for="(rec, i) in (Array.isArray(records) ? records : [records])" :key="i"
                        class="font-mono text-sm text-zinc-300 bg-white/3 rounded-lg px-3 py-2">
                        {{ typeof rec === 'object' ? JSON.stringify(rec) : rec }}
                      </div>
                    </div>
                  </div>
                </div>
                <p v-else class="text-zinc-500 text-sm">No DNS records found.</p>
              </div>

              <!-- Email Security -->
              <div v-if="activeTab === 'Email Security'">
                <div class="grid grid-cols-3 gap-4 mb-6">
                  <div class="bg-white/3 rounded-xl p-4 text-center">
                    <div class="text-xs text-zinc-500 mb-2">MX</div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      :class="results.email_security?.has_mx ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                      {{ results.email_security?.has_mx ? 'Configured' : 'Missing' }}
                    </span>
                  </div>
                  <div class="bg-white/3 rounded-xl p-4 text-center">
                    <div class="text-xs text-zinc-500 mb-2">SPF</div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      :class="results.email_security?.has_spf ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                      {{ results.email_security?.has_spf ? 'Configured' : 'Missing' }}
                    </span>
                  </div>
                  <div class="bg-white/3 rounded-xl p-4 text-center">
                    <div class="text-xs text-zinc-500 mb-2">DMARC</div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      :class="results.email_security?.has_dmarc ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                      {{ results.email_security?.has_dmarc ? 'Configured' : 'Missing' }}
                    </span>
                  </div>
                </div>
                <div v-if="results.email_security?.spf_record" class="mb-4">
                  <div class="text-xs text-zinc-500 mb-1">SPF Record</div>
                  <div class="font-mono text-sm text-zinc-300 bg-white/3 rounded-lg px-3 py-2 break-all">{{ results.email_security.spf_record }}</div>
                </div>
                <div v-if="results.email_security?.dmarc_record">
                  <div class="text-xs text-zinc-500 mb-1">DMARC Record</div>
                  <div class="font-mono text-sm text-zinc-300 bg-white/3 rounded-lg px-3 py-2 break-all">{{ results.email_security.dmarc_record }}</div>
                </div>
              </div>

              <!-- Subdomains -->
              <div v-if="activeTab === 'Subdomains'">
                <div v-if="results.subdomains?.length" class="grid grid-cols-2 gap-2">
                  <div v-for="sub in results.subdomains" :key="sub"
                    class="font-mono text-sm text-zinc-300 bg-white/3 rounded-lg px-3 py-2">
                    {{ sub }}
                  </div>
                </div>
                <p v-else class="text-zinc-500 text-sm">No subdomains discovered.</p>
              </div>

              <!-- Reputation -->
              <div v-if="activeTab === 'Reputation'">
                <div class="flex items-center gap-4 mb-6">
                  <span class="px-3 py-1.5 rounded-full text-sm font-semibold capitalize"
                    :class="{
                      'bg-emerald-500/20 text-emerald-400': results.reputation?.status === 'clean',
                      'bg-amber-500/20 text-amber-400': results.reputation?.status === 'suspicious',
                      'bg-red-500/20 text-red-400': results.reputation?.status === 'malicious',
                    }">
                    {{ results.reputation?.status ?? 'Unknown' }}
                  </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-white/3 rounded-xl p-4">
                    <div class="text-xs text-zinc-500 mb-1">Engines Flagged</div>
                    <div class="text-2xl font-bold" :class="(results.reputation?.engines_flagged ?? 0) > 0 ? 'text-red-400' : 'text-emerald-400'">
                      {{ results.reputation?.engines_flagged ?? 0 }}
                    </div>
                  </div>
                  <div class="bg-white/3 rounded-xl p-4">
                    <div class="text-xs text-zinc-500 mb-1">IOC Hits</div>
                    <div class="text-2xl font-bold" :class="(results.reputation?.ioc_hits ?? 0) > 0 ? 'text-red-400' : 'text-emerald-400'">
                      {{ results.reputation?.ioc_hits ?? 0 }}
                    </div>
                  </div>
                </div>
              </div>

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

const domain = ref('')
const honeypot = ref('')
const loading = ref(false)
const error = ref(null)
const results = ref(null)
const activeTab = ref('DNS Records')
const sharing = ref(false)
const shareUrl = ref(null)

const tabs = ['DNS Records', 'Email Security', 'Subdomains', 'Reputation']

const score = computed(() => {
  if (!results.value?.reputation) return 100
  const flagged = results.value.reputation.engines_flagged ?? 0
  const ioc = results.value.reputation.ioc_hits ?? 0
  return Math.max(0, Math.min(100, 100 - flagged * 10 - ioc * 15))
})

const scoreColor = computed(() => {
  if (score.value >= 80) return 'text-emerald-400'
  if (score.value >= 50) return 'text-amber-400'
  return 'text-red-400'
})

async function submit() {
  if (!domain.value.trim()) return
  loading.value = true
  error.value = null
  results.value = null
  shareUrl.value = null
  try {
    const data = await apiPost('/analyze-domain', { domain: domain.value.trim(), website: honeypot.value })
    if (data.error) {
      error.value = data.error
    } else {
      results.value = data
      activeTab.value = 'DNS Records'
    }
  } catch (e) {
    error.value = 'An unexpected error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}

async function downloadPdf() {
  await downloadBlob('/pdf/domain', { domain: domain.value, results: results.value }, 'onleaked-domain-report.pdf')
}

async function downloadCsv() {
  await downloadBlob('/csv/domain', { domain: domain.value, results: results.value }, 'onleaked-domain-report.csv')
}

async function shareReport() {
  sharing.value = true
  try {
    const data = await apiPost('/results/store', { domain: domain.value, results: results.value })
    shareUrl.value = data.url ?? null
  } catch (e) {
    // silent fail
  } finally {
    sharing.value = false
  }
}

function copyShare() {
  if (shareUrl.value) navigator.clipboard.writeText(shareUrl.value)
}
</script>
