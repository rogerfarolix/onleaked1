<template>
  <PublicLayout title="Mot de passe compromis">
    <div class="gradient-bg min-h-screen pt-28 pb-16 px-4">
      <div class="max-w-2xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 border border-brand/25 text-brand-bright mono-label mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            HaveIBeenPwned
          </div>
          <h1 class="text-4xl font-bold text-white mb-3">Mot de passe compromis</h1>
          <p class="text-text-muted text-lg">Vérifiez si votre mot de passe figure dans des fuites de données connues</p>
        </div>

        <!-- Privacy notice -->
        <div class="glass-card p-4 mb-6 flex items-start gap-3 border-brand/20">
          <svg class="w-4 h-4 text-brand-bright mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-text-muted text-xs leading-relaxed">Votre mot de passe est haché localement via le k-anonymat seuls les 5 premiers caractères de l'empreinte SHA-1 sont envoyés. Votre mot de passe réel ne quitte jamais votre navigateur.</p>
        </div>

        <!-- Form -->
        <div class="glass-card p-8 mb-8">
          <form @submit.prevent="submit">
            <input v-model="honeypot" type="text" name="website" autocomplete="off" class="hidden" tabindex="-1" aria-hidden="true" />
            <label class="block text-text-muted text-sm mb-2">Mot de passe à vérifier</label>
            <div class="flex gap-3">
              <input
                v-model="password"
                type="text"
                placeholder="Saisissez un mot de passe à vérifier…"
                required
                class="flex-1 w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input font-mono"
              />
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors text-sm ring-1 ring-brand/40 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap"
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
              <div class="text-text-muted text-sm mb-4">fois trouvé dans des fuites</div>

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
                {{ results.risk_label ?? 'Inconnu' }}
              </span>
            </div>

            <!-- Recommendations -->
            <div class="border-t border-line pt-6">
              <h3 class="text-sm font-semibold text-text mb-4">Recommandations</h3>
              <ul class="space-y-3">
                <template v-if="results.risk === 0">
                  <li class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ce mot de passe n'apparaît dans aucune fuite connue. Bon choix !
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-brand-bright mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Utilisez tout de même un mot de passe unique par compte et activez la 2FA dès que possible.
                  </li>
                </template>
                <template v-else>
                  <li class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Changez ce mot de passe immédiatement sur tous les comptes où il est utilisé.
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Utilisez un gestionnaire de mots de passe pour générer et stocker des mots de passe uniques et robustes.
                  </li>
                  <li class="flex items-start gap-2.5 text-sm text-text-muted">
                    <svg class="w-4 h-4 text-brand-bright mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Activez l'authentification à deux facteurs (2FA) sur tous les comptes critiques.
                  </li>
                </template>
              </ul>
            </div>

            <!-- PDF export -->
            <div class="mt-6 pt-6 border-t border-line">
              <button @click="downloadPdf" class="w-full py-2.5 bg-white/5 border border-line text-text text-sm rounded-md hover:bg-white/10 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le rapport PDF
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
    error.value = 'Une erreur inattendue est survenue. Veuillez réessayer.'
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
