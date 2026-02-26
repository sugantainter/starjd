<template>
  <div class="rounded-2xl border border-[#e2e8f0] bg-white p-6">
    <h3 class="text-lg font-semibold text-[#1a1a1a]">Available slots</h3>
    <p v-if="!slots.length" class="mt-4 text-sm text-[#64748b]">No upcoming availability listed. Contact the owner for custom dates.</p>
    <ul v-else class="mt-4 max-h-64 space-y-2 overflow-y-auto">
      <li
        v-for="s in slots"
        :key="s.id"
        class="flex items-center justify-between rounded-lg bg-[#f8fafc] px-3 py-2 text-sm"
      >
        <span>{{ s.date }} {{ s.start_time }} – {{ s.end_time }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  slots: { type: Array, default: () => [] },
});
</script>
