<template>
  <PublicLayout title="Analyseur d'en-têtes d'e-mail">
    <div class="gradient-bg min-h-screen pt-28 pb-16 px-4">
      <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 border border-brand/25 text-brand-bright mono-label mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Analyse forensique d'e-mail
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Analyseur d'en-têtes d'e-mail</h1>
          <p class="text-text-muted text-lg">Retracez le routage d'un e-mail, vérifiez l'authentification SPF/DKIM/DMARC</p>
        </div>

        <!-- Form -->
        <div class="glass-card p-8 mb-8">
          <form @submit.prevent="submit">
            <input v-model="honeypot" type="text" name="website" autocomplete="off" class="hidden" tabindex="-1" aria-hidden="true" />
            <label class="block text-text-muted text-sm mb-2">Collez les en-têtes bruts de l'e-mail</label>
            <textarea
              v-model="headers"
              rows="8"
              placeholder="Received: from mail.example.com…&#10;From: sender@example.com&#10;To: recipient@example.com&#10;Subject: …"
              required
              class="w-full bg-white/5 border border-line rounded-md px-4 py-3 text-white text-xs placeholder-text-dim focus:outline-none focus:border-brand glow-input font-mono resize-y leading-relaxed"
            ></textarea>
            <div class="mt-3 flex justify-end">
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span>{{ loading ? 'Analyse…' : 'Analyser les en-têtes' }}</span>
              </button>
            </div>
            <p v-if="error" class="mt-3 text-red-400 text-sm">{{ error }}</p>
          </form>
        </div>

        <!-- Results -->
        <div v-if="results">

          <!-- Summary banner -->
          <div class="rounded-lg px-5 py-4 mb-6 flex items-center gap-3"
            :class="{
              'bg-emerald-500/10 border border-emerald-500/20': results.authentication?.summary === 'secure',
              'bg-amber-500/10 border border-amber-500/20': results.authentication?.summary === 'partial',
              'bg-red-500/10 border border-red-500/20': results.authentication?.summary === 'suspicious' || !['secure','partial'].includes(results.authentication?.summary),
            }">
            <svg class="w-5 h-5 shrink-0"
              :class="{
                'text-emerald-400': results.authentication?.summary === 'secure',
                'text-amber-400': results.authentication?.summary === 'partial',
                'text-red-400': !['secure','partial'].includes(results.authentication?.summary),
              }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                :d="results.authentication?.summary === 'secure'
                  ? 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
                  : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'"
              />
            </svg>
            <div>
              <div class="font-semibold text-sm"
                :class="{
                  'text-emerald-400': results.authentication?.summary === 'secure',
                  'text-amber-400': results.authentication?.summary === 'partial',
                  'text-red-400': !['secure','partial'].includes(results.authentication?.summary),
                }">
                {{ summaryLabels[results.authentication?.summary] ?? 'Inconnu' }}
              </div>
              <div class="text-xs text-text-muted mt-0.5">Évaluation de l'authentification de l'e-mail</div>
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

              <!-- Authentication -->
              <div v-if="activeTab === 'Authentification'">
                <div class="space-y-3">
                  <div v-for="proto in authProtocols" :key="proto.key" class="flex items-center justify-between bg-surface-2 rounded-md px-4 py-3">
                    <div class="font-semibold text-sm text-text">{{ proto.label }}</div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                      :class="{
                        'bg-emerald-500/20 text-emerald-400': results.authentication?.[proto.key] === 'pass',
                        'bg-red-500/20 text-red-400': results.authentication?.[proto.key] === 'fail',
                        'bg-zinc-500/20 text-text-muted': !results.authentication?.[proto.key] || results.authentication[proto.key] === 'none',
                      }">
                      {{ results.authentication?.[proto.key] ?? 'none' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Routing Path -->
              <div v-if="activeTab === 'Chemin de routage'">
                <div v-if="results.routing?.length" class="space-y-3">
                  <div v-for="(hop, i) in results.routing" :key="i" class="flex gap-4 bg-surface-2 rounded-md p-4">
                    <div class="w-7 h-7 rounded-full bg-brand/20 text-brand-bright text-xs font-bold flex items-center justify-center shrink-0">
                      {{ i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="font-mono text-sm text-text break-all">{{ typeof hop === 'string' ? hop : hop.from ?? hop.by ?? JSON.stringify(hop) }}</div>
                      <div v-if="hop.date" class="text-xs text-text-dim mt-1">{{ hop.date }}</div>
                    </div>
                  </div>
                </div>
                <p v-else class="text-text-dim text-sm">Aucun saut de routage trouvé.</p>
              </div>

              <!-- Key Headers -->
              <div v-if="activeTab === 'En-têtes clés'">
                <div v-if="results.key_headers && Object.keys(results.key_headers).length" class="space-y-3">
                  <div v-for="(value, name) in results.key_headers" :key="name" class="bg-surface-2 rounded-md p-4">
                    <div class="text-xs text-text-dim mb-1">{{ name }}</div>
                    <div class="font-mono text-sm text-text break-all">{{ value }}</div>
                  </div>
                </div>
                <div v-else-if="results.all_headers?.length" class="space-y-2">
                  <div v-for="(hdr, i) in results.all_headers.slice(0, 20)" :key="i" class="bg-surface-2 rounded-lg p-3">
                    <div class="font-mono text-xs text-text break-all">{{ typeof hdr === 'string' ? hdr : `${hdr.name}: ${hdr.value}` }}</div>
                  </div>
                </div>
                <p v-else class="text-text-dim text-sm">Aucun en-tête clé extrait.</p>
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
import { apiPost } from '@/Composables/useApi'

const headers = ref('')
const honeypot = ref('')
const loading = ref(false)
const error = ref(null)
const results = ref(null)
const activeTab = ref('Authentification')

const tabs = ['Authentification', 'Chemin de routage', 'En-têtes clés']

const authProtocols = [
  { key: 'spf', label: 'SPF' },
  { key: 'dkim', label: 'DKIM' },
  { key: 'dmarc', label: 'DMARC' },
]

const summaryLabels = {
  secure: 'Sécurisé',
  partial: 'Partiel',
  suspicious: 'Suspect',
}

async function submit() {
  if (!headers.value.trim()) return
  loading.value = true
  error.value = null
  results.value = null
  try {
    const data = await apiPost('/analyze-header', { headers: headers.value, website: honeypot.value })
    if (data.error) {
      error.value = data.error
    } else {
      results.value = data
      activeTab.value = 'Authentification'
    }
  } catch (e) {
    error.value = 'Une erreur inattendue est survenue. Veuillez réessayer.'
  } finally {
    loading.value = false
  }
}
</script>
