<template>
  <PublicLayout title="Analyse de domaine">
    <div class="gradient-bg min-h-screen pt-28 pb-16 px-4">
      <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 border border-brand/25 text-brand-bright mono-label mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
            Renseignement de domaine
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Analyse de domaine</h1>
          <p class="text-text-muted text-lg">Analysez les enregistrements DNS, la sécurité e-mail, les sous-domaines et la réputation</p>
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
                placeholder="exemple.com"
                required
                class="flex-1 w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input"
              />
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright ring-1 ring-brand/40 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap"
              >
                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span>{{ loading ? 'Analyse…' : 'Analyser' }}</span>
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
                <div class="text-4xl font-bold font-mono" :class="scoreColor">{{ score }}</div>
                <div class="text-xs text-text-dim mt-0.5">Score de confiance</div>
              </div>
              <div>
                <div class="text-white font-semibold">{{ domain }}</div>
                <div class="text-text-muted text-sm">Analyse du domaine terminée</div>
              </div>
            </div>
            <div class="flex flex-wrap gap-2">
              <button @click="downloadPdf" class="px-4 py-2 bg-white/5 border border-line text-text text-sm rounded-md hover:bg-white/10 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
              </button>
              <button @click="downloadCsv" class="px-4 py-2 bg-white/5 border border-line text-text text-sm rounded-md hover:bg-white/10 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                CSV
              </button>
              <button @click="shareReport" :disabled="sharing" class="px-4 py-2 bg-brand/10 border border-brand/25 text-brand-bright text-sm rounded-md hover:bg-brand/20 transition-colors flex items-center gap-2 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                {{ sharing ? 'Partage…' : 'Partager' }}
              </button>
            </div>
          </div>

          <!-- Share URL banner -->
          <div v-if="shareUrl" class="glass-card p-4 mb-6 border-brand/25 flex items-center gap-3">
            <svg class="w-4 h-4 text-brand-bright shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <span class="text-text text-sm flex-1 font-mono break-all">{{ shareUrl }}</span>
            <button @click="copyShare" class="text-brand-bright text-xs hover:text-white">Copier</button>
          </div>

          <!-- Tabs -->
          <div class="glass-card overflow-hidden">
            <div class="flex border-b border-line">
              <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                class="px-5 py-3.5 text-sm font-medium transition-colors"
                :class="activeTab === tab ? 'text-brand-bright border-b-2 border-brand-bright' : 'text-text-muted hover:text-text'"
              >{{ tab }}</button>
            </div>

            <div class="p-6">

              <!-- DNS Records -->
              <div v-if="activeTab === 'Enregistrements DNS'">
                <div v-if="results.dns && Object.keys(results.dns).length">
                  <div v-for="(records, type) in results.dns" :key="type" class="mb-6 last:mb-0">
                    <div class="flex items-center gap-2 mb-3">
                      <span class="text-xs font-bold px-2 py-0.5 rounded bg-brand/15 text-brand-bright font-mono">{{ type }}</span>
                    </div>
                    <div class="space-y-1.5">
                      <div v-for="(rec, i) in (Array.isArray(records) ? records : [records])" :key="i"
                        class="font-mono text-sm text-text bg-surface-2 rounded-md px-3 py-2">
                        {{ typeof rec === 'object' ? JSON.stringify(rec) : rec }}
                      </div>
                    </div>
                  </div>
                </div>
                <p v-else class="text-text-dim text-sm">Aucun enregistrement DNS trouvé.</p>
              </div>

              <!-- Email Security -->
              <div v-if="activeTab === 'Sécurité e-mail'">
                <div class="grid grid-cols-3 gap-4 mb-6">
                  <div class="bg-surface-2 rounded-md p-4 text-center">
                    <div class="text-xs text-text-dim mb-2 font-mono">MX</div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      :class="results.email_security?.has_mx ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                      {{ results.email_security?.has_mx ? 'Configuré' : 'Absent' }}
                    </span>
                  </div>
                  <div class="bg-surface-2 rounded-md p-4 text-center">
                    <div class="text-xs text-text-dim mb-2 font-mono">SPF</div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      :class="results.email_security?.has_spf ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                      {{ results.email_security?.has_spf ? 'Configuré' : 'Absent' }}
                    </span>
                  </div>
                  <div class="bg-surface-2 rounded-md p-4 text-center">
                    <div class="text-xs text-text-dim mb-2 font-mono">DMARC</div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      :class="results.email_security?.has_dmarc ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                      {{ results.email_security?.has_dmarc ? 'Configuré' : 'Absent' }}
                    </span>
                  </div>
                </div>
                <div v-if="results.email_security?.spf_record" class="mb-4">
                  <div class="text-xs text-text-dim mb-1">Enregistrement SPF</div>
                  <div class="font-mono text-sm text-text bg-surface-2 rounded-md px-3 py-2 break-all">{{ results.email_security.spf_record }}</div>
                </div>
                <div v-if="results.email_security?.dmarc_record">
                  <div class="text-xs text-text-dim mb-1">Enregistrement DMARC</div>
                  <div class="font-mono text-sm text-text bg-surface-2 rounded-md px-3 py-2 break-all">{{ results.email_security.dmarc_record }}</div>
                </div>
              </div>

              <!-- Subdomains -->
              <div v-if="activeTab === 'Sous-domaines'">
                <div v-if="results.subdomains?.length" class="grid grid-cols-2 gap-2">
                  <div v-for="sub in results.subdomains" :key="sub"
                    class="font-mono text-sm text-text bg-surface-2 rounded-md px-3 py-2">
                    {{ sub }}
                  </div>
                </div>
                <p v-else class="text-text-dim text-sm">Aucun sous-domaine découvert.</p>
              </div>

              <!-- Reputation -->
              <div v-if="activeTab === 'Réputation'">
                <div class="flex items-center gap-4 mb-6">
                  <span class="px-3 py-1.5 rounded-full text-sm font-semibold"
                    :class="{
                      'bg-emerald-500/20 text-emerald-400': results.reputation?.status === 'clean',
                      'bg-amber-500/20 text-amber-400': results.reputation?.status === 'suspicious',
                      'bg-red-500/20 text-red-400': results.reputation?.status === 'malicious',
                    }">
                    {{ reputationLabels[results.reputation?.status] ?? 'Inconnu' }}
                  </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-surface-2 rounded-md p-4">
                    <div class="text-xs text-text-dim mb-1">Moteurs ayant signalé</div>
                    <div class="text-2xl font-bold font-mono" :class="(results.reputation?.engines_flagged ?? 0) > 0 ? 'text-red-400' : 'text-emerald-400'">
                      {{ results.reputation?.engines_flagged ?? 0 }}
                    </div>
                  </div>
                  <div class="bg-surface-2 rounded-md p-4">
                    <div class="text-xs text-text-dim mb-1">Correspondances IOC</div>
                    <div class="text-2xl font-bold font-mono" :class="(results.reputation?.ioc_hits ?? 0) > 0 ? 'text-red-400' : 'text-emerald-400'">
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
const activeTab = ref('Enregistrements DNS')
const sharing = ref(false)
const shareUrl = ref(null)

const tabs = ['Enregistrements DNS', 'Sécurité e-mail', 'Sous-domaines', 'Réputation']

const reputationLabels = {
  clean: 'Sain',
  suspicious: 'Suspect',
  malicious: 'Malveillant',
}

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
      activeTab.value = 'Enregistrements DNS'
    }
  } catch (e) {
    error.value = 'Une erreur inattendue est survenue. Veuillez réessayer.'
  } finally {
    loading.value = false
  }
}

async function downloadPdf() {
  await downloadBlob('/pdf/domain', { domain: domain.value, results: results.value }, 'onleaked-rapport-domaine.pdf')
}

async function downloadCsv() {
  await downloadBlob('/csv/domain', { domain: domain.value, results: results.value }, 'onleaked-rapport-domaine.csv')
}

async function shareReport() {
  sharing.value = true
  try {
    const data = await apiPost('/results/store', { domain: domain.value, results: results.value })
    shareUrl.value = data.url ?? null
  } catch (e) {
    // échec silencieux
  } finally {
    sharing.value = false
  }
}

function copyShare() {
  if (shareUrl.value) navigator.clipboard.writeText(shareUrl.value)
}
</script>
