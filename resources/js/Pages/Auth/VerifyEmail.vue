<template>
  <GuestLayout title="Verify Email">
    <div class="text-center mb-6">
      <div class="w-14 h-14 rounded-full bg-violet-500/10 border border-violet-500/20 flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <h2 class="text-xl font-bold text-white mb-2">Verify your email</h2>
      <p class="text-zinc-500 text-sm">Thanks for signing up. Please click the link in the email we just sent you.</p>
    </div>

    <div v-if="status === 'verification-link-sent'" class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm text-center">
      A new verification link has been sent to your email address.
    </div>

    <div class="space-y-3">
      <form @submit.prevent="resendForm.post('/email/verification-notification')">
        <button type="submit" :disabled="resendForm.processing"
          class="w-full py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 text-white font-semibold rounded-xl hover:from-violet-500 hover:to-violet-400 transition-all text-sm disabled:opacity-60">
          {{ resendForm.processing ? 'Sending…' : 'Resend Verification Email' }}
        </button>
      </form>
      <form @submit.prevent="logoutForm.post('/logout')">
        <button type="submit" class="w-full py-2.5 bg-white/5 border border-white/10 text-zinc-400 rounded-xl hover:bg-white/10 transition-all text-sm">
          Log Out
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
