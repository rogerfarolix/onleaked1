<template>
  <GuestLayout title="Réinitialiser le mot de passe">
    <h2 class="text-xl font-bold text-white text-center mb-2">Mot de passe oublié ?</h2>
    <p class="text-text-dim text-sm text-center mb-6">Saisissez votre e-mail et nous vous enverrons un lien de réinitialisation.</p>

    <div v-if="form.recentlySuccessful || status" class="mb-4 p-3 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
      {{ status ?? 'Un lien de réinitialisation a été envoyé à votre e-mail.' }}
    </div>

    <form @submit.prevent="form.post('/forgot-password')" class="space-y-4">
      <div>
        <label class="block text-sm text-text-muted mb-1.5">Adresse e-mail</label>
        <input v-model="form.email" type="email" required autofocus
          class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input"
          :class="{ 'border-red-500/50': form.errors.email }">
        <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
      </div>
      <button type="submit" :disabled="form.processing"
        class="w-full py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
        {{ form.processing ? 'Envoi…' : 'Envoyer le lien' }}
      </button>
    </form>

    <p class="text-center text-sm text-text-dim mt-4">
      Vous vous souvenez de votre mot de passe ?
      <Link :href="route('login')" class="text-brand-bright hover:text-white">Connexion</Link>
    </p>
  </GuestLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Link, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const page   = usePage()
const status = computed(() => page.props.flash?.status)
const form   = useForm({ email: '' })
</script>
