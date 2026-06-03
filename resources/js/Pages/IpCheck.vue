<template>
  <PublicLayout title="Réputation IP">
    <div class="gradient-bg min-h-screen py-16 px-4">
      <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 border border-brand/25 text-brand-bright mono-label mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
            AbuseIPDB + GeoIP
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Réputation IP</h1>
          <p class="text-text-muted text-lg">Géolocalisez, consultez les signalements d'abus et évaluez le risque d'une IP</p>
        </div>

        <!-- Form -->
        <div class="glass-card p-8 mb-8">
          <form @submit.prevent="submit">
            <input v-model="honeypot" type="text" name="website" autocomplete="off" class="hidden" tabindex="-1" aria-hidden="true" />
            <div class="flex gap-3">
              <input
                v-model="ip"
                type="text"
                placeholder="192.168.1.1"
                required
                class="flex-1 w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input font-mono"
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
                <span>{{ loading ? 'Vérification…' : 'Vérifier' }}</span>
              </button>
            </div>
            <p v-if="error" class="mt-3 text-red-400 text-sm">{{ error }}</p>
          </form>
        </div>

        <!-- Results -->
        <div v-if="results">

          <!-- Summary header -->
          <div class="glass-card p-6 mb-6 flex flex-wrap items-center gap-4">
            <div class="text-4xl select-none" aria-label="country flag">{{ countryFlag }}</div>
            <div class="flex-1 min-w-0">
              <div class="text-white font-mono font-bold text-xl">{{ results.ip }}</div>
              <div class="text-text-muted text-sm">{{ results.geo?.city }}, {{ results.geo?.country }}</div>
            </div>
            <!-- Abuse confidence -->
            <div class="text-center">
              <div class="text-3xl font-bold"
                :class="{
                  'text-emerald-400': (results.abuse?.abuseConfidenceScore ?? 0) < 25,
                  'text-amber-400': (results.abuse?.abuseConfidenceScore ?? 0) >= 25 && (results.abuse?.abuseConfidenceScore ?? 0) < 75,
                  'text-red-400': (results.abuse?.abuseConfidenceScore ?? 0) >= 75,
                }">
                {{ results.abuse?.abuseConfidenceScore ?? 0 }}%
              </div>
              <div class="text-xs text-text-dim">Score d'abus</div>
            </div>
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

              <!-- Geolocation -->
              <div v-if="activeTab === 'Géolocalisation'">
                <dl class="space-y-3">
                  <div v-for="(label, key) in geoFields" :key="key" class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-32 shrink-0 pt-0.5">{{ label }}</dt>
                    <dd class="text-sm text-text flex-1">
                      <template v-if="typeof results.geo?.[key] === 'boolean'">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                          :class="results.geo[key] ? 'bg-red-500/20 text-red-400' : 'bg-emerald-500/20 text-emerald-400'">
                          {{ results.geo[key] ? 'Oui' : 'Non' }}
                        </span>
                      </template>
                      <template v-else>{{ results.geo?.[key] ?? '—' }}</template>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Abuse Reports -->
              <div v-if="activeTab === 'Signalements d\'abus'">
                <div v-if="results.abuse?.available === false" class="flex items-center gap-3 p-4 bg-surface-2 rounded-md">
                  <svg class="w-5 h-5 text-text-dim shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <p class="text-text-muted text-sm">AbuseIPDB n'est pas configuré. Ajoutez votre clé API pour activer les signalements d'abus.</p>
                </div>
                <dl v-else class="space-y-3">
                  <div v-for="(label, key) in abuseFields" :key="key" class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-40 shrink-0 pt-0.5">{{ label }}</dt>
                    <dd class="text-sm text-text flex-1">
                      <template v-if="typeof results.abuse?.[key] === 'boolean'">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                          :class="results.abuse[key] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                          {{ results.abuse[key] ? 'Oui' : 'Non' }}
                        </span>
                      </template>
                      <template v-else>{{ results.abuse?.[key] ?? '—' }}</template>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Network -->
              <div v-if="activeTab === 'Réseau'">
                <dl class="space-y-3">
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-32 shrink-0 pt-0.5">FAI</dt>
                    <dd class="text-sm text-text flex-1">{{ results.geo?.isp ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-32 shrink-0 pt-0.5">Organisation</dt>
                    <dd class="text-sm text-text flex-1">{{ results.geo?.org ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-32 shrink-0 pt-0.5">ASN</dt>
                    <dd class="text-sm text-text flex-1 font-mono">{{ results.geo?.as ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-32 shrink-0 pt-0.5">Domaine d'abus</dt>
                    <dd class="text-sm text-text flex-1">{{ results.abuse?.domain ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-32 shrink-0 pt-0.5">Type d'usage</dt>
                    <dd class="text-sm text-text flex-1">{{ results.abuse?.usageType ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-text-dim w-32 shrink-0 pt-0.5">Fuseau horaire</dt>
                    <dd class="text-sm text-text flex-1">{{ results.geo?.timezone ?? '—' }}</dd>
                  </div>
                </dl>
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
import { apiPost } from '@/Composables/useApi'

const ip = ref('')
const honeypot = ref('')
const loading = ref(false)
const error = ref(null)
const results = ref(null)
const activeTab = ref('Géolocalisation')

const tabs = ['Géolocalisation', 'Signalements d\'abus', 'Réseau']

const geoFields = {
  country: 'Pays',
  countryCode: 'Code pays',
  regionName: 'Région',
  city: 'Ville',
  proxy: 'Proxy/VPN',
  hosting: 'Hébergement',
}

const abuseFields = {
  abuseConfidenceScore: 'Score de confiance',
  totalReports: 'Signalements totaux',
  usageType: 'Type d\'usage',
  isWhitelisted: 'Liste blanche',
  isTor: 'Nœud de sortie Tor',
}

const countryFlag = computed(() => {
  const code = results.value?.geo?.countryCode
  if (!code || code.length !== 2) return '🌐'
  try {
    return String.fromCodePoint(...[...code.toUpperCase()].map(c => c.codePointAt(0) + 127397))
  } catch {
    return '🌐'
  }
})

async function submit() {
  if (!ip.value.trim()) return
  loading.value = true
  error.value = null
  results.value = null
  try {
    const data = await apiPost('/check-ip', { ip: ip.value.trim(), website: honeypot.value })
    if (data.error) {
      error.value = data.error
    } else {
      results.value = data
      activeTab.value = 'Géolocalisation'
    }
  } catch (e) {
    error.value = 'Une erreur inattendue est survenue. Veuillez réessayer.'
  } finally {
    loading.value = false
  }
}
</script>
