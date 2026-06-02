<template>
  <div class="min-h-screen gradient-bg flex flex-col items-center justify-center px-4 py-12">

    <!-- Logo -->
    <Link :href="route('home')" class="flex items-center gap-2.5 mb-8">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-rose-500 flex items-center justify-center shadow-lg shadow-violet-500/30">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
      </div>
      <span class="font-bold text-xl tracking-tight">
        <span class="text-white">On</span><span class="bg-gradient-to-r from-violet-400 to-rose-400 bg-clip-text text-transparent">leaked</span>
      </span>
    </Link>

    <!-- Card -->
    <div class="w-full max-w-sm glass-card p-8">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
