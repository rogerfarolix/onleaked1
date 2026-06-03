<template>
  <AppLayout title="Profil">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto space-y-6">

        <!-- Profile info -->
        <div class="glass-card rounded-lg p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">Informations du profil</h2>
          <p class="text-text-dim text-sm mb-6">Mettez à jour votre nom et votre adresse e-mail.</p>

          <div v-if="profileForm.recentlySuccessful" class="mb-4 p-3 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">Enregistré.</div>

          <form @submit.prevent="profileForm.patch('/profile')" class="space-y-4">
            <div>
              <label class="block text-sm text-text-muted mb-1.5">Nom</label>
              <input v-model="profileForm.name" type="text" required
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input"
                :class="{ 'border-red-500/50': profileForm.errors.name }">
              <p v-if="profileForm.errors.name" class="mt-1 text-xs text-red-400">{{ profileForm.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm text-text-muted mb-1.5">E-mail</label>
              <input v-model="profileForm.email" type="email" required
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input"
                :class="{ 'border-red-500/50': profileForm.errors.email }">
              <p v-if="profileForm.errors.email" class="mt-1 text-xs text-red-400">{{ profileForm.errors.email }}</p>
              <p v-if="user.email !== profileForm.email" class="mt-1 text-xs text-amber-400">Modifier votre e-mail nécessitera une nouvelle vérification.</p>
            </div>
            <div class="flex justify-end">
              <button type="submit" :disabled="profileForm.processing"
                class="px-5 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
                {{ profileForm.processing ? 'Enregistrement…' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Password -->
        <div class="glass-card rounded-lg p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">Modifier le mot de passe</h2>
          <p class="text-text-dim text-sm mb-6">Utilisez un mot de passe long et unique pour rester protégé.</p>

          <div v-if="passwordForm.recentlySuccessful" class="mb-4 p-3 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">Mot de passe mis à jour.</div>

          <form @submit.prevent="passwordForm.put('/password', { onFinish: () => passwordForm.reset() })" class="space-y-4">
            <div>
              <label class="block text-sm text-text-muted mb-1.5">Mot de passe actuel</label>
              <input v-model="passwordForm.current_password" type="password" required autocomplete="current-password"
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input"
                :class="{ 'border-red-500/50': passwordForm.errors.current_password }">
              <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-red-400">{{ passwordForm.errors.current_password }}</p>
            </div>
            <div>
              <label class="block text-sm text-text-muted mb-1.5">Nouveau mot de passe</label>
              <input v-model="passwordForm.password" type="password" required autocomplete="new-password"
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input"
                :class="{ 'border-red-500/50': passwordForm.errors.password }">
              <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-red-400">{{ passwordForm.errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm text-text-muted mb-1.5">Confirmer le mot de passe</label>
              <input v-model="passwordForm.password_confirmation" type="password" required autocomplete="new-password"
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input">
            </div>
            <div class="flex justify-end">
              <button type="submit" :disabled="passwordForm.processing"
                class="px-5 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
                {{ passwordForm.processing ? 'Mise à jour…' : 'Modifier le mot de passe' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Alert preferences -->
        <div class="glass-card rounded-lg p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">Préférences d'alertes</h2>
          <p class="text-text-dim text-sm mb-6">À quelle fréquence devons-nous vous envoyer les alertes CVE par e-mail ?</p>

          <div v-if="alertForm.recentlySuccessful" class="mb-4 p-3 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">Préférences enregistrées.</div>

          <form @submit.prevent="alertForm.patch('/profile/alerts')" class="space-y-3">
            <label v-for="opt in alertOptions" :key="opt.value"
              class="flex items-start gap-3 p-4 rounded-lg border cursor-pointer transition-colors"
              :class="alertForm.alert_frequency === opt.value ? 'border-brand/40 bg-brand/5' : 'border-line hover:border-white/20'">
              <input type="radio" :value="opt.value" v-model="alertForm.alert_frequency" class="mt-0.5 accent-brand">
              <div>
                <p class="text-sm font-medium text-white">{{ opt.label }}</p>
                <p class="text-xs text-text-dim mt-0.5">{{ opt.desc }}</p>
              </div>
            </label>
            <div class="flex justify-end pt-2">
              <button type="submit" :disabled="alertForm.processing"
                class="px-5 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
                Enregistrer les préférences
              </button>
            </div>
          </form>
        </div>

        <!-- API Keys -->
        <div class="glass-card rounded-lg p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">Clés API</h2>
          <p class="text-text-dim text-sm mb-6">Générez des clés pour accéder aux outils Onleaked par programmation.</p>

          <div class="flex gap-3 mb-4">
            <input v-model="newKeyName" type="text" placeholder="Nom de la clé (ex. Mon script)"
              class="flex-1 bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input">
            <button @click="createKey" :disabled="!newKeyName.trim() || creatingKey"
              class="px-5 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
              {{ creatingKey ? '…' : 'Générer' }}
            </button>
          </div>

          <div v-if="newKeyValue" class="mb-4 p-4 rounded-md border border-emerald-500/20 bg-emerald-500/5">
            <p class="text-sm text-emerald-400 font-semibold mb-2">Copiez-la maintenant cette clé ne sera plus affichée !</p>
            <div class="flex items-center gap-2">
              <code class="flex-1 text-xs font-mono text-text bg-black/30 px-3 py-2 rounded-md break-all">{{ newKeyValue }}</code>
              <button @click="copyKey" class="text-xs px-3 py-2 rounded-md bg-white/10 text-text hover:bg-white/20 transition-colors shrink-0">
                {{ copied ? 'Copié !' : 'Copier' }}
              </button>
            </div>
          </div>

          <div v-if="loadingKeys" class="text-text-dim text-sm py-4">Chargement…</div>
          <div v-else-if="!keys.length" class="text-text-dim text-sm">Aucune clé API pour l'instant.</div>
          <div v-else class="space-y-2">
            <div v-for="k in keys" :key="k.id" class="flex items-center justify-between p-4 rounded-lg border border-line bg-white/2">
              <div>
                <p class="text-sm font-medium text-white">{{ k.name }}</p>
                <p class="text-xs text-text-dim mt-0.5">
                  Créée le {{ formatDate(k.created_at) }}
                  <template v-if="k.last_used_at"> · Dernière utilisation {{ formatDate(k.last_used_at) }}</template>
                </p>
              </div>
              <button @click="revokeKey(k.id)" class="text-xs px-3 py-1.5 rounded-md border border-red-500/20 text-red-400 hover:bg-red-500/10 transition-colors">
                Révoquer
              </button>
            </div>
          </div>
          <div class="mt-4 p-4 rounded-lg border border-line bg-white/2 text-xs text-text-dim">
            <p class="font-mono mb-1">POST /api/v1/check-email</p>
            <p class="font-mono mb-2">POST /api/v1/analyze-domain</p>
            <p>Envoyez <code class="text-text-muted">Authorization: Bearer VOTRE_CLE</code> à chaque requête.</p>
          </div>
        </div>

        <!-- Delete account -->
        <div class="glass-card rounded-lg p-6 md:p-8 border-red-500/20 bg-red-500/5">
          <h2 class="text-lg font-semibold text-white mb-1">Supprimer le compte</h2>
          <p class="text-text-dim text-sm mb-4">Supprimez définitivement votre compte et toutes les données associées.</p>
          <button @click="deleteModal = true" class="px-5 py-2.5 bg-red-600/20 border border-red-600/30 text-red-400 font-semibold rounded-md hover:bg-red-600/30 transition-colors text-sm">
            Supprimer le compte
          </button>
        </div>

      </div>
    </div>

    <!-- Delete confirmation modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="deleteModal = false"></div>
          <div class="relative max-w-md w-full glass-card rounded-lg p-6">
            <h3 class="font-semibold text-white mb-2">Supprimer le compte ?</h3>
            <p class="text-text-dim text-sm mb-4">Cette action est définitive et irréversible.</p>
            <div class="mb-4">
              <label class="block text-sm text-text-muted mb-1.5">Confirmez votre mot de passe</label>
              <input v-model="deleteForm.password" type="password"
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-red-500/50"
                :class="{ 'border-red-500/50': deleteForm.errors.password }">
              <p v-if="deleteForm.errors.password" class="mt-1 text-xs text-red-400">{{ deleteForm.errors.password }}</p>
            </div>
            <div class="flex gap-3 justify-end">
              <button @click="deleteModal = false" class="px-4 py-2 bg-white/5 border border-line text-text-muted rounded-md hover:bg-white/10 transition-colors text-sm">Annuler</button>
              <button @click="deleteForm.delete('/profile', { onSuccess: () => deleteModal = false })" :disabled="deleteForm.processing"
                class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-500 transition-colors text-sm disabled:opacity-60">
                {{ deleteForm.processing ? 'Suppression…' : 'Supprimer le compte' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { apiGet, apiPost } from '@/Composables/useApi'

const props = defineProps({ user: { type: Object, required: true } })

const profileForm = useForm({ name: props.user.name, email: props.user.email })
const passwordForm = useForm({ current_password: '', password: '', password_confirmation: '' })
const alertForm   = useForm({ alert_frequency: props.user.alert_frequency ?? 'instant' })
const deleteForm  = useForm({ password: '' })
const deleteModal = ref(false)

const alertOptions = [
  { value: 'instant', label: 'Instantané',          desc: 'Recevez un e-mail dès qu\'une nouvelle CVE est détectée' },
  { value: 'daily',   label: 'Digest quotidien',    desc: 'Un e-mail par jour résumant les nouvelles CVE' },
  { value: 'weekly',  label: 'Hebdomadaire',        desc: 'Un e-mail chaque lundi avec les CVE de la semaine' },
  { value: 'never',   label: 'Jamais',              desc: 'Désactiver tous les e-mails d\'alerte CVE' },
]

const keys       = ref([])
const loadingKeys = ref(true)
const newKeyName = ref('')
const newKeyValue = ref(null)
const creatingKey = ref(false)
const copied     = ref(false)

onMounted(async () => {
  loadingKeys.value = true
  try { keys.value = await apiGet('/profile/api-keys') }
  catch {}
  finally { loadingKeys.value = false }
})

async function createKey() {
  if (!newKeyName.value.trim()) return
  creatingKey.value = true; newKeyValue.value = null
  try {
    const data = await apiPost('/profile/api-keys', { name: newKeyName.value })
    if (data.key) { newKeyValue.value = data.key; newKeyName.value = '' }
    keys.value = await apiGet('/profile/api-keys')
  } catch {}
  finally { creatingKey.value = false }
}

async function revokeKey(id) {
  if (!confirm('Revoke this API key?')) return
  const token = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
  await fetch(`/profile/api-keys/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } })
  keys.value = await apiGet('/profile/api-keys')
}

async function copyKey() {
  await navigator.clipboard.writeText(newKeyValue.value)
  copied.value = true; setTimeout(() => copied.value = false, 2000)
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString() : '—'
</script>
