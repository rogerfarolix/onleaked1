<template>
  <PublicLayout title="Inspecteur SSL">
    <div class="gradient-bg min-h-screen pt-28 pb-16 px-4">
      <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 border border-brand/25 text-brand-bright mono-label mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Inspecteur TLS/SSL
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Inspecteur de certificat SSL</h1>
          <p class="text-text-muted text-lg">Inspectez les certificats TLS, leur note et leur configuration de sécurité</p>
        </div>

        <!-- Form -->
        <div class="glass-card p-8 mb-8">
          <form @submit.prevent="submit">
            <input v-model="honeypot" type="text" name="website" autocomplete="off" class="hidden" tabindex="-1" aria-hidden="true" />
            <div class="flex gap-3">
              <input
                v-model="domain"
                type="text"
                placeholder="example.com"
                required
                class="flex-1 w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input"
              />
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap"
              >
                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span>{{ loading ? 'Inspection…' : 'Inspecter' }}</span>
              </button>
            </div>
            <p v-if="error" class="mt-3 text-red-400 text-sm">{{ error }}</p>
          </form>
        </div>

        <!-- Results -->
        <div v-if="results">

          <!-- Grade + summary row -->
          <div class="flex flex-wrap items-center gap-6 mb-6">
            <!-- Grade badge -->
            <div class="flex items-center justify-center w-20 h-20 rounded-lg text-4xl font-black"
              :class="{
                'bg-emerald-500/20 text-emerald-400': results.ssl?.grade === 'A' || results.ssl?.grade === 'A+',
                'bg-amber-500/20 text-amber-400': results.ssl?.grade === 'B',
                'bg-orange-500/20 text-orange-400': results.ssl?.grade === 'C',
                'bg-red-500/20 text-red-400': results.ssl?.grade === 'F' || !results.ssl?.connected,
              }">
              {{ results.ssl?.grade ?? 'F' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-white font-semibold text-lg">{{ results.domain }}</div>
              <div class="text-text-muted text-sm">{{ results.ssl?.issuer_org ?? 'Émetteur inconnu' }}</div>
              <!-- Days remaining -->
              <div class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold"
                :class="{
                  'text-emerald-400': (results.ssl?.days_left ?? 0) > 30,
                  'text-amber-400': (results.ssl?.days_left ?? 0) <= 30 && (results.ssl?.days_left ?? 0) > 7,
                  'text-red-400': (results.ssl?.days_left ?? 0) <= 7,
                }">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ results.ssl?.days_left ?? 0 }} jours restants
              </div>
            </div>
            <button @click="downloadPdf" class="px-4 py-2 bg-white/5 border border-line text-text text-sm rounded-md hover:bg-white/10 transition-colors flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              PDF
            </button>
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

              <!-- Certificate -->
              <div v-if="activeTab === 'Certificat'">
                <dl class="space-y-3">
                  <div v-for="(label, key) in certFields" :key="key" class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-36 shrink-0 pt-0.5">{{ label }}</dt>
                    <dd class="text-sm text-text font-mono flex-1 break-all">{{ results.ssl?.[key] ?? '—' }}</dd>
                  </div>
                </dl>
              </div>

              <!-- SANs -->
              <div v-if="activeTab === 'SANs'">
                <div v-if="results.ssl?.sans?.length" class="grid grid-cols-2 gap-2">
                  <div v-for="san in results.ssl.sans" :key="san"
                    class="font-mono text-xs text-text bg-surface-2 rounded-lg px-3 py-2">
                    {{ san }}
                  </div>
                </div>
                <p v-else class="text-text-dim text-sm">Aucun Subject Alternative Name trouvé.</p>
              </div>

              <!-- Recommendations -->
              <div v-if="activeTab === 'Recommandations'">
                <ul class="space-y-3">
                  <li v-if="!results.ssl?.connected" class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Impossible de se connecter au serveur. Vérifiez que le domaine dispose bien d'un SSL configuré.
                  </li>
                  <li v-if="(results.ssl?.days_left ?? 0) <= 30 && (results.ssl?.days_left ?? 0) > 0" class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Le certificat expire dans {{ results.ssl.days_left }} jours renouvelez-le bientôt.
                  </li>
                  <li v-if="(results.ssl?.days_left ?? 0) <= 0" class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Le certificat a expiré. Renouvelez-le immédiatement.
                  </li>
                  <li v-if="results.ssl?.grade !== 'A' && results.ssl?.grade !== 'A+'" class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-brand-bright mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Améliorez votre configuration SSL pour atteindre la note A (désactivez les anciennes versions de TLS et les chiffrements faibles).
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Activez HSTS (HTTP Strict Transport Security) avec un max-age élevé.
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Utilisez le renouvellement automatique des certificats (ex. Let's Encrypt avec Certbot).
                  </li>
                </ul>
              </div>

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

const domain = ref('')
const honeypot = ref('')
const loading = ref(false)
const error = ref(null)
const results = ref(null)
const activeTab = ref('Certificat')

const tabs = ['Certificat', 'SANs', 'Recommandations']

const certFields = {
  subject_cn: 'CN du sujet',
  issuer_cn: 'CN de l\'émetteur',
  issuer_org: 'Organisation émettrice',
  valid_from: 'Valide depuis',
  valid_to: 'Valide jusqu\'au',
  days_left: 'Jours restants',
  serial: 'Numéro de série',
  grade: 'Note',
}

async function submit() {
  if (!domain.value.trim()) return
  loading.value = true
  error.value = null
  results.value = null
  try {
    const data = await apiPost('/check-ssl', { domain: domain.value.trim(), website: honeypot.value })
    if (data.error) {
      error.value = data.error
    } else {
      results.value = data
      activeTab.value = 'Certificat'
    }
  } catch (e) {
    error.value = 'Une erreur inattendue est survenue. Veuillez réessayer.'
  } finally {
    loading.value = false
  }
}

async function downloadPdf() {
  await downloadBlob('/pdf/ssl-check', { domain: results.value.domain, ssl: results.value.ssl }, 'onleaked-ssl-report.pdf')
}
</script>
