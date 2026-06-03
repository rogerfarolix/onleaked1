<template>
  <GuestLayout title="Vérifier l'e-mail">
    <div class="text-center mb-6">
      <div class="w-14 h-14 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <h2 class="text-xl font-bold text-white mb-2">Vérifiez votre e-mail</h2>
      <p class="text-text-dim text-sm">Merci pour votre inscription. Cliquez sur le lien dans l'e-mail que nous venons de vous envoyer.</p>
    </div>

    <div v-if="status === 'verification-link-sent'" class="mb-4 p-3 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm text-center">
      Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
    </div>

    <div class="space-y-3">
      <form @submit.prevent="resendForm.post('/email/verification-notification')">
        <button type="submit" :disabled="resendForm.processing"
          class="w-full py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
          {{ resendForm.processing ? 'Envoi…' : 'Renvoyer l\'e-mail de vérification' }}
        </button>
      </form>
      <form @submit.prevent="logoutForm.post('/logout')">
        <button type="submit" class="w-full py-2.5 bg-white/5 border border-line text-text-muted rounded-md hover:bg-white/10 transition-colors text-sm">
          Déconnexion
        </button>
      </form>
    </div>
  </GuestLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const page       = usePage()
const status     = computed(() => page.props.flash?.status)
const resendForm = useForm({})
const logoutForm = useForm({})
</script>
