<template>
  <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4" @click.self="$emit('close')">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
      <h3 class="text-lg font-semibold text-[#1a1a1a]">{{ title }}</h3>
      <p class="mt-2 text-sm text-[#64748b]">{{ message }}</p>
      <div class="mt-6 flex justify-end gap-2">
        <button type="button" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm font-medium text-[#64748b] hover:bg-[#f1f5f9]" @click="$emit('close')">
          {{ cancelLabel }}
        </button>
        <button type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700" @click="$emit('confirm')">
          {{ confirmLabel }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: 'Confirm' },
  message: { type: String, default: 'Are you sure?' },
  confirmLabel: { type: String, default: 'Delete' },
  cancelLabel: { type: String, default: 'Cancel' },
});
defineEmits(['close', 'confirm']);
</script>
