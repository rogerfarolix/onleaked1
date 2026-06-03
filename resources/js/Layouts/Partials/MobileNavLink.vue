<template>
  <Link :href="href"
    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm transition-colors"
    :class="active ? 'text-white bg-white/5' : 'text-text-muted hover:text-white hover:bg-white/5'"
  >
    <span v-if="color" class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 bg-brand/10 border border-brand/25">
      <slot name="icon">
        <svg class="w-3.5 h-3.5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </slot>
    </span>
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href:   { type: String, required: true },
  color:  { type: String, default: null },
  active: { type: Boolean, default: false },
})
</script>
