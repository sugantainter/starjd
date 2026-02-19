<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Collaborations</h1>
    <p class="mt-1 text-[#64748b]">Your collaboration requests and payments.</p>
    <div v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
    <ul class="mt-6 space-y-4">
      <li v-for="c in list" :key="c.id" class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="flex justify-between items-start">
          <div>
            <div class="font-semibold text-[#1a1a1a]">{{ c.creator?.name }}</div>
            <div class="text-[#e63946] font-medium">₹{{ c.amount }} (platform fee: ₹{{ c.platform_fee }})</div>
            <p v-if="c.brand_notes" class="mt-2 text-sm text-[#64748b]">{{ c.brand_notes }}</p>
            <span class="mt-2 inline-block rounded-full px-2 py-0.5 text-xs bg-[#e2e8f0] text-[#64748b]">{{ c.status }}</span>
          </div>
          <button v-if="c.status === 'accepted' || c.status === 'pending'" type="button" class="cursor-link rounded-lg bg-[#e63946] px-4 py-2 text-sm text-white hover:bg-[#c1121f]" @click="pay(c)">Pay now</button>
        </div>
      </li>
    </ul>
    <div v-if="!list.length" class="mt-6 rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]">No collaborations yet.</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const list = ref([]);
const error = ref('');

onMounted(load);

async function load() {
  const res = await axios.get('/api/collaborations', { withCredentials: true });
  list.value = res.data;
}

async function pay(c) {
  error.value = '';
  try {
    await axios.post('/api/collaborations/' + c.id + '/pay', {}, { withCredentials: true });
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to process payment.';
  }
}
</script>
