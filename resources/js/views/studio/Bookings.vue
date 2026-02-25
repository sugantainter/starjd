<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Bookings</h1>
    <p class="mt-1 text-[#64748b]">Bookings for your studios.</p>
    <div v-if="loading" class="mt-6 text-[#64748b]">Loading…</div>
    <div v-else-if="!list.length" class="mt-6 rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]">No bookings yet.</div>
    <ul v-else class="mt-6 space-y-4">
      <li v-for="b in list" :key="b.id" class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <span class="font-medium text-[#1a1a1a]">{{ b.studio?.name }}</span>
          <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusClass(b.status)">{{ b.status }}</span>
        </div>
        <p class="mt-2 text-sm text-[#64748b]">{{ b.date }} · {{ b.start_time }} – {{ b.end_time }}</p>
        <p v-if="b.customer" class="mt-1 text-sm text-[#64748b]">Customer: {{ b.customer.name }}</p>
        <p class="mt-1 text-sm font-medium text-[#1a1a1a]">₹{{ b.amount ?? b.studio_amount }}</p>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const list = ref([]);
const loading = ref(true);

function statusClass(status) {
  const s = (status || '').toLowerCase();
  if (s === 'confirmed') return 'bg-green-100 text-green-800';
  if (s === 'cancelled') return 'bg-red-100 text-red-800';
  if (s === 'pending') return 'bg-amber-100 text-amber-800';
  return 'bg-gray-100 text-gray-800';
}

onMounted(async () => {
  try {
    const res = await axios.get('/api/studio-owner/bookings', { withCredentials: true });
    const d = res.data;
    list.value = Array.isArray(d?.data) ? d.data : (Array.isArray(d) ? d : []);
  } catch (_) {
    list.value = [];
  } finally {
    loading.value = false;
  }
});
</script>
