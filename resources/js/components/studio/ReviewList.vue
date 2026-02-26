<template>
  <div class="rounded-2xl border border-[#e2e8f0] bg-white p-6">
    <h3 class="text-lg font-semibold text-[#1a1a1a]">Reviews ({{ reviews.length }})</h3>
    <div v-if="!reviews.length" class="mt-4 text-sm text-[#64748b]">No reviews yet.</div>
    <ul v-else class="mt-4 space-y-4">
      <li
        v-for="r in reviews"
        :key="r.id"
        class="border-b border-[#f1f5f9] pb-4 last:border-0 last:pb-0"
      >
        <div class="flex items-center gap-2">
          <span class="font-medium text-[#1a1a1a]">{{ r.user_name || 'Guest' }}</span>
          <span class="text-amber-500">★ {{ r.rating }}</span>
          <span class="text-xs text-[#64748b]">{{ formatDate(r.created_at) }}</span>
        </div>
        <p v-if="r.comment" class="mt-2 text-sm text-[#64748b]">{{ r.comment }}</p>
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  reviews: { type: Array, default: () => [] },
});

function formatDate(iso) {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>
