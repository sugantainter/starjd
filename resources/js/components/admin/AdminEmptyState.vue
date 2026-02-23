<template>
  <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-[#e2e8f0] bg-[#fafbfc] px-6 py-16 text-center">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f1f5f9] text-[#94a3b8]">
      <slot name="icon">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.5m-2.5 0H6" /></svg>
      </slot>
    </div>
    <h3 class="mt-4 text-base font-semibold text-[#1a1a1a]">{{ title }}</h3>
    <p class="mt-2 max-w-sm text-sm text-[#64748b]">{{ description }}</p>
    <div v-if="$slots.action" class="mt-6">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: 'No items yet' },
  description: { type: String, default: 'Get started by adding your first item.' },
});
</script>
