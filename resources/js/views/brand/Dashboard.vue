<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Dashboard</h1>
    <p class="mt-1 text-[#64748b]">Welcome back, {{ data?.user?.name }}.</p>
    <div class="mt-6 grid gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Collaborations</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ data?.collaborations?.length ?? 0 }}</div>
      </div>
    </div>
    <div class="mt-8">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Recent collaborations</h2>
      <div v-if="!data?.collaborations?.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-6 text-center text-[#64748b]">No collaborations yet.</div>
      <ul v-else class="mt-4 space-y-2">
        <li v-for="c in data.collaborations" :key="c.id" class="rounded-xl border border-[#e2e8f0] bg-white p-4">
          <span class="font-medium text-[#1a1a1a]">{{ c.creator?.name }}</span>
          <span class="text-[#64748b]"> – ₹{{ c.amount }} ({{ c.status }})</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const data = ref(null);

onMounted(async () => {
  const res = await axios.get('/api/brand/dashboard', { withCredentials: true });
  data.value = res.data;
});
</script>
