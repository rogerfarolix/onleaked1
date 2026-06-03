<template>
  <PublicLayout :title="`Rapport de domaine ${domain}`">
    <div class="pt-28 pb-20 px-6">
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
          <div>
            <p class="mono-label text-text-dim mb-1">Rapport partagé</p>
            <h1 class="text-2xl font-bold text-white font-mono">{{ domain }}</h1>
            <p v-if="expiresAt" class="text-xs text-text-dim mt-1">Expire le {{ new Date(expiresAt).toLocaleString('fr-FR') }}</p>
          </div>
          <Link :href="route('domain.show')" class="text-sm text-text-dim hover:text-white transition-colors">
            Analyser un autre domaine →
          </Link>
        </div>

        <!-- Show domain results inline using same structure as DomainAnalysis.vue -->
        <div v-if="results">
          <!-- Score -->
          <div class="glass-card rounded-lg p-5 mb-6">
            <p class="text-text-muted text-sm mb-1">Score de sécurité</p>
            <div class="text-4xl font-bold font-mono" :class="score >= 70 ? 'text-emerald-400' : score >= 40 ? 'text-amber-400' : 'text-red-400'">
              {{ score }}<span class="text-xl text-text-dim">/100</span>
            </div>
          </div>

          <!-- Tabs -->
          <div class="flex border-b border-line mb-6 overflow-x-auto">
            <button v-for="t in tabs" :key="t.id" @click="tab = t.id"
              class="px-5 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap"
              :class="tab === t.id ? 'border-brand-bright text-brand-bright' : 'border-transparent text-text-muted hover:text-white'">
              {{ t.label }}
            </button>
          </div>

          <!-- DNS -->
          <div v-show="tab === 'dns'">
            <div v-if="results.dns && Object.keys(results.dns).length" class="space-y-4">
              <div v-for="(records, type) in results.dns" :key="type" class="glass-card rounded-lg p-5">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-text-dim mb-3">{{ type }}</h3>
                <div class="space-y-1.5">
                  <div v-for="(rec, i) in (Array.isArray(records) ? records : [records])" :key="i"
                    class="font-mono text-sm text-text bg-black/20 rounded-lg px-3 py-2 break-all">
                    {{ typeof rec === 'object' ? JSON.stringify(rec) : rec }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Email Security -->
          <div v-show="tab === 'email'" class="space-y-3">
            <div class="glass-card rounded-lg p-5 flex items-center justify-between">
              <span class="text-sm text-white">Enregistrements MX</span>
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold" :class="results.email_config?.has_mx ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'">{{ results.email_config?.has_mx ? 'Configuré' : 'Absent' }}</span>
            </div>
            <div class="glass-card rounded-lg p-5 flex items-center justify-between">
              <span class="text-sm text-white">SPF</span>
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold" :class="results.email_config?.has_spf ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'">{{ results.email_config?.has_spf ? 'Configuré' : 'Absent' }}</span>
            </div>
            <div class="glass-card rounded-lg p-5 flex items-center justify-between">
              <span class="text-sm text-white">DMARC</span>
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold" :class="results.email_config?.has_dmarc ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'">{{ results.email_config?.has_dmarc ? 'Configuré' : 'Absent' }}</span>
            </div>
          </div>

          <!-- Subdomains -->
          <div v-show="tab === 'subdomains'">
            <div v-if="results.subdomains?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div v-for="(sub, i) in results.subdomains" :key="i" class="glass-card rounded-lg px-4 py-2.5 font-mono text-sm text-text truncate">{{ sub }}</div>
            </div>
            <p v-else class="text-text-dim text-sm text-center py-10">Aucun sous-domaine trouvé.</p>
          </div>

          <!-- Reputation -->
          <div v-show="tab === 'reputation'">
            <div class="glass-card rounded-lg p-6">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                  :class="results.reputation?.status === 'clean' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                </div>
                <div>
                  <h3 class="font-bold text-white">{{ reputationLabels[results.reputation?.status] ?? 'Inconnu' }}</h3>
                  <p class="text-xs text-text-dim">{{ results.reputation?.engines_flagged ?? 0 }} moteur(s) ayant signalé · {{ results.reputation?.ioc_hits ?? 0 }} correspondance(s) IOC</p>
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
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  domain:    { type: String, required: true },
  results:   { type: Object, required: true },
  expiresAt: { type: String, default: null },
})

const tab  = ref('dns')
const tabs = [
  { id: 'dns',        label: 'Enregistrements DNS' },
  { id: 'email',      label: 'Sécurité e-mail' },
  { id: 'subdomains', label: 'Sous-domaines' },
  { id: 'reputation', label: 'Réputation' },
]

const reputationLabels = {
  clean: 'Sain',
  suspicious: 'Suspect',
  malicious: 'Malveillant',
}

const score = computed(() => {
  const r = props.results?.reputation
  if (!r) return 100
  return Math.max(0, Math.min(100, 100 - (r.engines_flagged ?? 0) * 10 - (r.ioc_hits ?? 0) * 15))
})
</script>
