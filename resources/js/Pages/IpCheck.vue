<template>
  <PublicLayout title="IP Reputation Intelligence">
    <div class="gradient-bg min-h-screen py-16 px-4">
      <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
            AbuseIPDB + GeoIP
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">IP Reputation Intelligence</h1>
          <p class="text-zinc-400 text-lg">Geolocate, check abuse reports, and assess IP risk</p>
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
                class="flex-1 w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-indigo-500/50 font-mono"
              />
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-semibold rounded-xl hover:from-indigo-500 hover:to-indigo-400 transition-all text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap"
              >
                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span>{{ loading ? 'Checking…' : 'Check IP' }}</span>
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
              <div class="text-zinc-400 text-sm">{{ results.geo?.city }}, {{ results.geo?.country }}</div>
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
              <div class="text-xs text-zinc-500">Abuse Score</div>
            </div>
          </div>

          <!-- Tabs -->
          <div class="glass-card overflow-hidden">
            <div class="flex border-b border-white/6">
              <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                class="px-5 py-3.5 text-sm font-medium transition-colors"
                :class="activeTab === tab ? 'text-indigo-400 border-b-2 border-indigo-400' : 'text-zinc-400 hover:text-zinc-200'"
              >{{ tab }}</button>
            </div>

            <div class="p-6">

              <!-- Geolocation -->
              <div v-if="activeTab === 'Geolocation'">
                <dl class="space-y-3">
                  <div v-for="(label, key) in geoFields" :key="key" class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-32 shrink-0 pt-0.5">{{ label }}</dt>
                    <dd class="text-sm text-zinc-200 flex-1">
                      <template v-if="typeof results.geo?.[key] === 'boolean'">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                          :class="results.geo[key] ? 'bg-red-500/20 text-red-400' : 'bg-emerald-500/20 text-emerald-400'">
                          {{ results.geo[key] ? 'Yes' : 'No' }}
                        </span>
                      </template>
                      <template v-else>{{ results.geo?.[key] ?? '—' }}</template>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Abuse Reports -->
              <div v-if="activeTab === 'Abuse Reports'">
                <div v-if="results.abuse?.available === false" class="flex items-center gap-3 p-4 bg-white/3 rounded-xl">
                  <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <p class="text-zinc-400 text-sm">AbuseIPDB is not configured. Add your API key to enable abuse reporting.</p>
                </div>
                <dl v-else class="space-y-3">
                  <div v-for="(label, key) in abuseFields" :key="key" class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-40 shrink-0 pt-0.5">{{ label }}</dt>
                    <dd class="text-sm text-zinc-200 flex-1">
                      <template v-if="typeof results.abuse?.[key] === 'boolean'">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                          :class="results.abuse[key] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
                          {{ results.abuse[key] ? 'Yes' : 'No' }}
                        </span>
                      </template>
                      <template v-else>{{ results.abuse?.[key] ?? '—' }}</template>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Network -->
              <div v-if="activeTab === 'Network'">
                <dl class="space-y-3">
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-32 shrink-0 pt-0.5">ISP</dt>
                    <dd class="text-sm text-zinc-200 flex-1">{{ results.geo?.isp ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-32 shrink-0 pt-0.5">Organisation</dt>
                    <dd class="text-sm text-zinc-200 flex-1">{{ results.geo?.org ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-32 shrink-0 pt-0.5">ASN</dt>
                    <dd class="text-sm text-zinc-200 flex-1 font-mono">{{ results.geo?.as ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-32 shrink-0 pt-0.5">Abuse Domain</dt>
                    <dd class="text-sm text-zinc-200 flex-1">{{ results.abuse?.domain ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-32 shrink-0 pt-0.5">Usage Type</dt>
                    <dd class="text-sm text-zinc-200 flex-1">{{ results.abuse?.usageType ?? '—' }}</dd>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <dt class="text-xs text-zinc-500 w-32 shrink-0 pt-0.5">Timezone</dt>
                    <dd class="text-sm text-zinc-200 flex-1">{{ results.geo?.timezone ?? '—' }}</dd>
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
const activeTab = ref('Geolocation')

const tabs = ['Geolocation', 'Abuse Reports', 'Network']

const geoFields = {
  country: 'Country',
  countryCode: 'Country Code',
  regionName: 'Region',
  city: 'City',
  proxy: 'Proxy/VPN',
  hosting: 'Hosting',
}

const abuseFields = {
  abuseConfidenceScore: 'Confidence Score',
  totalReports: 'Total Reports',
  usageType: 'Usage Type',
  isWhitelisted: 'Whitelisted',
  isTor: 'Tor Exit Node',
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
      activeTab.value = 'Geolocation'
    }
  } catch (e) {
    error.value = 'An unexpected error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
