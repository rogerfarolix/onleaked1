<template>
  <AppLayout title="Historique des scans">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-5xl mx-auto">

        <div class="flex items-center justify-between mb-8">
          <div>
            <h1 class="text-2xl font-bold text-white">Historique des scans</h1>
            <p class="text-text-dim text-sm mt-1">Vos scans authentifiés sur l'ensemble des outils</p>
          </div>
          <Link :href="route('dashboard')" class="text-sm text-text-dim hover:text-white transition-colors">&larr; Tableau de bord</Link>
        </div>

        <div v-if="!history.data?.length" class="glass-card rounded-2xl p-16 text-center">
          <svg class="w-10 h-10 text-text-dim mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-text-muted font-medium mb-1">Aucun scan pour l'instant</p>
          <p class="text-text-dim text-sm">Utilisez un outil en étant connecté et votre historique apparaîtra ici.</p>
        </div>

        <div v-else class="glass-card rounded-2xl overflow-hidden">
          <table class="w-full">
            <thead>
              <tr class="border-b border-line">
                <th class="px-6 py-3.5 text-left text-xs font-medium text-text-dim uppercase tracking-wider">Date</th>
                <th class="px-6 py-3.5 text-left text-xs font-medium text-text-dim uppercase tracking-wider">Outil</th>
                <th class="px-6 py-3.5 text-left text-xs font-medium text-text-dim uppercase tracking-wider">Cible</th>
                <th class="px-6 py-3.5"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/4">
              <tr v-for="scan in history.data" :key="scan.id" class="hover:bg-white/2 transition-colors">
                <td class="px-6 py-4 text-xs text-text-dim font-mono whitespace-nowrap">{{ formatDate(scan.created_at) }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                    :class="typeClass(scan.scan_type)">
                    {{ typeLabel(scan.scan_type) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-text font-mono truncate max-w-xs">{{ scan.target }}</td>
                <td class="px-6 py-4 text-right">
                  <button @click="openModal(scan.id)" class="text-xs text-text-dim hover:text-white transition-colors">
                    Voir →
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="history.links?.length > 3" class="px-6 py-4 border-t border-line flex gap-1 flex-wrap">
            <component v-for="link in history.links" :key="link.label"
              :is="link.url ? Link : 'span'"
              :href="link.url"
              v-html="link.label"
              class="px-3 py-1.5 rounded-lg text-xs transition-colors"
              :class="link.active ? 'bg-brand text-white' : link.url ? 'bg-white/5 text-text-muted hover:bg-white/10' : 'text-text-dim cursor-default'"
            />
          </div>
        </div>

      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape="modal.show = false">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="modal.show = false"></div>
          <div class="relative max-w-2xl w-full max-h-[80vh] overflow-y-auto rounded-2xl border border-line bg-surface p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-white text-sm">Détails du scan</h3>
              <button @click="modal.show = false" class="text-text-dim hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <div v-if="modal.loading" class="flex justify-center py-8">
              <svg class="w-5 h-5 animate-spin text-text-dim" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </div>
            <pre v-else-if="modal.data" class="text-xs text-text-muted overflow-x-auto font-mono bg-black/30 rounded-xl p-4 whitespace-pre-wrap">{{ JSON.stringify(modal.data.results, null, 2) }}</pre>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props  = defineProps({ history: { type: Object, default: () => ({ data: [], links: [] }) } })
const modal  = reactive({ show: false, loading: false, data: null })

const typeMap = {
  leak_check: { label: 'Fuite',       cls: 'bg-brand/10 text-brand-bright border border-brand/20' },
  domain:     { label: 'Domaine',     cls: 'bg-brand/10 text-brand-bright border border-brand/20' },
  password:   { label: 'Mot de passe',cls: 'bg-brand/10 text-brand-bright border border-brand/20' },
  ssl:        { label: 'SSL',         cls: 'bg-brand/10 text-brand-bright border border-brand/20' },
  ip:         { label: 'Réputation IP', cls: 'bg-brand/10 text-brand-bright border border-brand/20' },
  header:     { label: 'En-têtes',    cls: 'bg-brand/10 text-brand-bright border border-brand/20' },
}

const typeLabel = (t) => typeMap[t]?.label ?? t
const typeClass = (t) => typeMap[t]?.cls ?? 'bg-zinc-500/10 text-text-muted'
const formatDate = (d) => d ? new Date(d).toLocaleString('fr-FR') : ''

async function openModal(id) {
  modal.show = true; modal.loading = true; modal.data = null
  try {
    const res = await fetch(`/history/${id}`, { headers: { 'Accept': 'application/json' } })
    modal.data = await res.json()
  } catch {}
  finally { modal.loading = false }
}
</script>
