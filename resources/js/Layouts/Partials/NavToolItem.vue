<template>
  <Link :href="href"
    class="flex items-center gap-3 px-3 py-2.5 rounded-md transition-all duration-150 group"
    :class="active ? 'bg-white/5' : 'hover:bg-white/5'"
  >
    <div class="w-8 h-8 rounded-md flex items-center justify-center shrink-0 transition-colors bg-brand/10 border border-brand/25 group-hover:bg-brand/20">
      <svg class="w-4 h-4 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <slot />
      </svg>
    </div>
    <div class="min-w-0 flex-1">
      <div class="flex items-center gap-2">
        <span class="text-sm font-medium text-text group-hover:text-white transition-colors">{{ label }}</span>
        <span v-if="badge" class="mono-label text-[9px]! px-1.5 py-0.5 rounded bg-brand/20 text-brand-bright font-semibold leading-none">{{ badge }}</span>
      </div>
      <div class="text-xs text-text-dim mt-0.5">{{ desc }}</div>
    </div>
    <div v-if="active" class="ml-auto w-1.5 h-1.5 rounded-full shrink-0 bg-brand-bright"></div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href:   { type: String, required: true },
  active: { type: Boolean, default: false },
  label:  { type: String, required: true },
  desc:   { type: String, default: '' },
  badge:  { type: String, default: null },
})
</script>
