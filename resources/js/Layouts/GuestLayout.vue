<template>
  <div class="min-h-screen bg-ink flex flex-col items-center justify-center px-4 py-12 relative">
    <div class="grid-backdrop fixed inset-0 pointer-events-none"></div>

    <!-- Logo -->
    <Link :href="route('home')" class="flex items-center gap-2.5 mb-8 relative">
      <div class="w-10 h-10 rounded-md bg-brand flex items-center justify-center ring-1 ring-brand/40">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
      </div>
      <span class="font-bold text-xl tracking-tight text-text">
        On<span class="text-brand-bright">leaked</span>
      </span>
    </Link>

    <!-- Card -->
    <div class="w-full max-w-sm glass-card p-8 relative">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
