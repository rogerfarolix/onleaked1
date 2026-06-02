<template>
  <AppLayout title="Profile">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto space-y-6">

        <!-- Profile info -->
        <div class="glass-card rounded-2xl p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">Profile Information</h2>
          <p class="text-zinc-500 text-sm mb-6">Update your name and email address.</p>

          <div v-if="profileForm.recentlySuccessful" class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">Saved.</div>

          <form @submit.prevent="profileForm.patch('/profile')" class="space-y-4">
            <div>
              <label class="block text-sm text-zinc-400 mb-1.5">Name</label>
              <input v-model="profileForm.name" type="text" required
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': profileForm.errors.name }">
              <p v-if="profileForm.errors.name" class="mt-1 text-xs text-red-400">{{ profileForm.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm text-zinc-400 mb-1.5">Email</label>
              <input v-model="profileForm.email" type="email" required
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': profileForm.errors.email }">
              <p v-if="profileForm.errors.email" class="mt-1 text-xs text-red-400">{{ profileForm.errors.email }}</p>
              <p v-if="user.email !== profileForm.email" class="mt-1 text-xs text-amber-400">Changing your email will require re-verification.</p>
            </div>
            <div class="flex justify-end">
              <button type="submit" :disabled="profileForm.processing"
                class="px-5 py-2.5 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-500 transition-colors text-sm disabled:opacity-60">
                {{ profileForm.processing ? 'Saving…' : 'Save' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Password -->
        <div class="glass-card rounded-2xl p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">Update Password</h2>
          <p class="text-zinc-500 text-sm mb-6">Use a long, unique password to stay secure.</p>

          <div v-if="passwordForm.recentlySuccessful" class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">Password updated.</div>

          <form @submit.prevent="passwordForm.put('/password', { onFinish: () => passwordForm.reset() })" class="space-y-4">
            <div>
              <label class="block text-sm text-zinc-400 mb-1.5">Current Password</label>
              <input v-model="passwordForm.current_password" type="password" required autocomplete="current-password"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': passwordForm.errors.current_password }">
              <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-red-400">{{ passwordForm.errors.current_password }}</p>
            </div>
            <div>
              <label class="block text-sm text-zinc-400 mb-1.5">New Password</label>
              <input v-model="passwordForm.password" type="password" required autocomplete="new-password"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': passwordForm.errors.password }">
              <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-red-400">{{ passwordForm.errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm text-zinc-400 mb-1.5">Confirm Password</label>
              <input v-model="passwordForm.password_confirmation" type="password" required autocomplete="new-password"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50">
            </div>
            <div class="flex justify-end">
              <button type="submit" :disabled="passwordForm.processing"
                class="px-5 py-2.5 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-500 transition-colors text-sm disabled:opacity-60">
                {{ passwordForm.processing ? 'Updating…' : 'Update Password' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Alert preferences -->
        <div class="glass-card rounded-2xl p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">Alert Preferences</h2>
          <p class="text-zinc-500 text-sm mb-6">How often should we send you CVE alert emails?</p>

          <div v-if="alertForm.recentlySuccessful" class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">Preferences saved.</div>

          <form @submit.prevent="alertForm.patch('/profile/alerts')" class="space-y-3">
            <label v-for="opt in alertOptions" :key="opt.value"
              class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-colors"
              :class="alertForm.alert_frequency === opt.value ? 'border-violet-500/40 bg-violet-500/5' : 'border-white/10 hover:border-white/20'">
              <input type="radio" :value="opt.value" v-model="alertForm.alert_frequency" class="mt-0.5 accent-violet-500">
              <div>
                <p class="text-sm font-medium text-white">{{ opt.label }}</p>
                <p class="text-xs text-zinc-500 mt-0.5">{{ opt.desc }}</p>
              </div>
            </label>
            <div class="flex justify-end pt-2">
              <button type="submit" :disabled="alertForm.processing"
                class="px-5 py-2.5 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-500 transition-colors text-sm disabled:opacity-60">
                Save Preferences
              </button>
            </div>
          </form>
        </div>

        <!-- API Keys -->
        <div class="glass-card rounded-2xl p-6 md:p-8">
          <h2 class="text-lg font-semibold text-white mb-1">API Keys</h2>
          <p class="text-zinc-500 text-sm mb-6">Generate keys to access Onleaked tools programmatically.</p>

          <div class="flex gap-3 mb-4">
            <input v-model="newKeyName" type="text" placeholder="Key name (e.g. My Script)"
              class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
            <button @click="createKey" :disabled="!newKeyName.trim() || creatingKey"
              class="px-5 py-2.5 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-500 transition-colors text-sm disabled:opacity-60">
              {{ creatingKey ? '…' : 'Generate' }}
            </button>
          </div>

          <div v-if="newKeyValue" class="mb-4 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5">
            <p class="text-sm text-emerald-400 font-semibold mb-2">Copy now — this key won't be shown again!</p>
            <div class="flex items-center gap-2">
              <code class="flex-1 text-xs font-mono text-zinc-300 bg-black/30 px-3 py-2 rounded-lg break-all">{{ newKeyValue }}</code>
              <button @click="copyKey" class="text-xs px-3 py-2 rounded-lg bg-white/10 text-zinc-300 hover:bg-white/20 transition-colors shrink-0">
                {{ copied ? 'Copied!' : 'Copy' }}
              </button>
            </div>
          </div>

          <div v-if="loadingKeys" class="text-zinc-600 text-sm py-4">Loading…</div>
          <div v-else-if="!keys.length" class="text-zinc-600 text-sm">No API keys yet.</div>
          <div v-else class="space-y-2">
            <div v-for="k in keys" :key="k.id" class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/2">
              <div>
                <p class="text-sm font-medium text-white">{{ k.name }}</p>
                <p class="text-xs text-zinc-500 mt-0.5">
                  Created {{ formatDate(k.created_at) }}
                  <template v-if="k.last_used_at"> · Last used {{ formatDate(k.last_used_at) }}</template>
                </p>
              </div>
              <button @click="revokeKey(k.id)" class="text-xs px-3 py-1.5 rounded-lg border border-red-500/20 text-red-400 hover:bg-red-500/10 transition-colors">
                Revoke
              </button>
            </div>
          </div>
          <div class="mt-4 p-4 rounded-xl border border-white/5 bg-white/2 text-xs text-zinc-500">
            <p class="font-mono mb-1">POST /api/v1/check-email</p>
            <p class="font-mono mb-2">POST /api/v1/analyze-domain</p>
            <p>Send <code class="text-zinc-400">Authorization: Bearer YOUR_KEY</code> with each request.</p>
          </div>
        </div>

        <!-- Delete account -->
        <div class="glass-card rounded-2xl p-6 md:p-8 border-red-500/20 bg-red-500/5">
          <h2 class="text-lg font-semibold text-white mb-1">Delete Account</h2>
          <p class="text-zinc-500 text-sm mb-4">Permanently delete your account and all associated data.</p>
          <button @click="deleteModal = true" class="px-5 py-2.5 bg-red-600/20 border border-red-600/30 text-red-400 font-semibold rounded-xl hover:bg-red-600/30 transition-colors text-sm">
            Delete Account
          </button>
        </div>

      </div>
    </div>

    <!-- Delete confirmation modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="deleteModal = false"></div>
          <div class="relative max-w-md w-full glass-card rounded-2xl p-6">
            <h3 class="font-semibold text-white mb-2">Delete account?</h3>
            <p class="text-zinc-500 text-sm mb-4">This action is permanent and cannot be undone.</p>
            <div class="mb-4">
              <label class="block text-sm text-zinc-400 mb-1.5">Confirm your password</label>
              <input v-model="deleteForm.password" type="password"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-red-500/50"
                :class="{ 'border-red-500/50': deleteForm.errors.password }">
              <p v-if="deleteForm.errors.password" class="mt-1 text-xs text-red-400">{{ deleteForm.errors.password }}</p>
            </div>
            <div class="flex gap-3 justify-end">
              <button @click="deleteModal = false" class="px-4 py-2 bg-white/5 border border-white/10 text-zinc-400 rounded-xl hover:bg-white/10 transition-colors text-sm">Cancel</button>
              <button @click="deleteForm.delete('/profile', { onSuccess: () => deleteModal = false })" :disabled="deleteForm.processing"
                class="px-4 py-2 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-500 transition-colors text-sm disabled:opacity-60">
                {{ deleteForm.processing ? 'Deleting…' : 'Delete Account' }}
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
  { value: 'instant', label: 'Instant',      desc: 'Receive an email as soon as a new CVE is detected' },
  { value: 'daily',   label: 'Daily Digest', desc: 'One email per day summarizing new CVEs' },
  { value: 'weekly',  label: 'Weekly',       desc: 'One email every Monday with the week\'s CVEs' },
  { value: 'never',   label: 'Never',        desc: 'Disable all CVE alert emails' },
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
