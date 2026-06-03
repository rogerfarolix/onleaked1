<template>
  <Link :href="href"
    class="bg-surface p-7 hover:bg-surface-2 transition-colors group block"
  >
    <div class="w-11 h-11 rounded-md flex items-center justify-center mb-5 transition-colors bg-brand/10 border border-brand/25 group-hover:bg-brand/20">
      <slot name="icon">
        <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
      </slot>
    </div>
    <div class="flex items-center gap-2 mb-2">
      <h3 class="font-semibold text-lg">{{ name }}</h3>
      <span v-if="badge" class="mono-label text-[9px]! px-1.5 py-0.5 rounded bg-brand/20 text-brand-bright font-semibold">{{ badge }}</span>
    </div>
    <p class="text-text-muted text-sm leading-relaxed mb-4">{{ desc }}</p>
    <span class="text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all text-brand-bright">
      {{ cta }}
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href:  { type: String, required: true },
  color: { type: String, default: 'brand' },
  name:  { type: String, required: true },
  desc:  { type: String, required: true },
  cta:   { type: String, required: true },
  badge: { type: String, default: null },
})
</script>
