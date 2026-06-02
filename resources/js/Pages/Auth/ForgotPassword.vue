<template>
  <GuestLayout title="Reset Password">
    <h2 class="text-xl font-bold text-white text-center mb-2">Forgot your password?</h2>
    <p class="text-zinc-500 text-sm text-center mb-6">Enter your email and we'll send you a reset link.</p>

    <div v-if="form.recentlySuccessful || status" class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
      {{ status ?? 'Password reset link sent to your email.' }}
    </div>

    <form @submit.prevent="form.post('/forgot-password')" class="space-y-4">
      <div>
        <label class="block text-sm text-zinc-400 mb-1.5">Email address</label>
        <input v-model="form.email" type="email" required autofocus
          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50"
          :class="{ 'border-red-500/50': form.errors.email }">
        <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
      </div>
      <button type="submit" :disabled="form.processing"
        class="w-full py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 text-white font-semibold rounded-xl hover:from-violet-500 hover:to-violet-400 transition-all text-sm disabled:opacity-60">
        {{ form.processing ? 'Sending…' : 'Send Reset Link' }}
      </button>
    </form>

    <p class="text-center text-sm text-zinc-500 mt-4">
      Remember your password?
      <Link :href="route('login')" class="text-violet-400 hover:text-violet-300">Log in</Link>
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
