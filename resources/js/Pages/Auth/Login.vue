<template>
  <GuestLayout title="Connexion">
    <div class="gradient-bg min-h-screen flex items-center justify-center px-4 py-12">
      <div class="w-full max-w-md">

        <!-- Logo / brand -->
        <div class="text-center mb-8">
          <Link href="/" class="inline-flex items-center gap-2 mb-6">
            <div class="w-8 h-8 rounded-md bg-brand flex items-center justify-center ring-1 ring-brand/40">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-white font-bold text-lg">On<span class="text-brand-bright">leaked</span></span>
          </Link>
          <h1 class="text-2xl font-bold text-white mb-1">Bon retour</h1>
          <p class="text-text-muted text-sm">Connectez-vous à votre compte</p>
        </div>

        <!-- Flash status (e.g. password reset success) -->
        <div v-if="status" class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
          {{ status }}
        </div>

        <div class="glass-card p-8">
          <form @submit.prevent="submit" class="space-y-5">

            <!-- Email -->
            <div>
              <label class="block text-text-muted text-sm mb-1.5">Adresse e-mail</label>
              <input
                v-model="form.email"
                type="email"
                autocomplete="username"
                required
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input"
                :class="{ 'border-red-500/50': form.errors.email }"
                placeholder="you@example.com"
              />
              <p v-if="form.errors.email" class="mt-1.5 text-red-400 text-xs">{{ form.errors.email }}</p>
            </div>

            <!-- Password -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="text-text-muted text-sm">Mot de passe</label>
                <Link href="/forgot-password" class="text-xs text-brand-bright hover:text-white transition-colors">Mot de passe oublié ?</Link>
              </div>
              <input
                v-model="form.password"
                type="password"
                autocomplete="current-password"
                required
                class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input"
                :class="{ 'border-red-500/50': form.errors.password }"
                placeholder="••••••••"
              />
              <p v-if="form.errors.password" class="mt-1.5 text-red-400 text-xs">{{ form.errors.password }}</p>
            </div>

            <!-- Remember me -->
            <label class="flex items-center gap-3 cursor-pointer">
              <input v-model="form.remember" type="checkbox" class="w-4 h-4 rounded border-line bg-white/5 text-brand focus:ring-brand/30" />
              <span class="text-text-muted text-sm">Se souvenir de moi</span>
            </label>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
              </svg>
              <span>{{ form.processing ? 'Connexion…' : 'Se connecter' }}</span>
            </button>

          </form>
        </div>

        <!-- Register link -->
        <p class="text-center text-text-dim text-sm mt-6">
          Pas encore de compte ?
          <Link href="/register" class="text-brand-bright hover:text-white transition-colors ml-1">Créez-en un gratuitement</Link>
        </p>

      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const page = usePage()
const status = computed(() => page.props.flash?.status ?? null)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>
