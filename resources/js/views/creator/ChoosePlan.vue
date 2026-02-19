<template>
  <div class="mx-auto max-w-2xl py-8">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Choose your plan</h1>
    <p class="mt-2 text-[#64748b]">Pay once to access your creator dashboard.</p>
    <div v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
      <button
        v-for="plan in plans"
        :key="plan.id"
        type="button"
        :disabled="loading"
        class="cursor-link rounded-xl border-2 p-6 text-left transition"
        :class="selected?.id === plan.id ? 'border-[#10b981] bg-[#10b981]/5' : 'border-[#e2e8f0] hover:border-[#10b981]/50'"
        @click="selected = plan"
      >
        <div class="font-semibold text-[#1a1a1a]">{{ plan.name }}</div>
        <div class="mt-2 text-2xl font-bold text-[#10b981]">₹{{ plan.price }}</div>
        <div class="mt-1 text-sm text-[#64748b]">/ {{ plan.duration }}</div>
      </button>
    </div>
    <div class="mt-8">
      <button
        type="button"
        :disabled="!selected || loading"
        class="cursor-link rounded-xl bg-[#10b981] px-6 py-3 font-semibold text-white transition hover:bg-[#059669] disabled:opacity-50"
        @click="pay"
      >
        {{ loading ? 'Processing…' : 'Pay & access dashboard' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const plans = ref([]);
const selected = ref(null);
const loading = ref(false);
const error = ref('');

onMounted(async () => {
  try {
    const res = await axios.get('/api/payment/plans', { withCredentials: true });
    plans.value = res.data || [];
  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) window.location.href = '/login';
    else error.value = 'Could not load plans.';
  }
});

async function pay() {
  if (!selected.value) return;
  loading.value = true;
  error.value = '';
  try {
    const res = await axios.post('/api/payment/pay', { plan_id: selected.value.id, amount: selected.value.price }, { withCredentials: true });
    window.location.href = res.data?.redirect || '/creator/dashboard';
  } catch (e) {
    error.value = e.response?.data?.message || 'Payment failed.';
  } finally {
    loading.value = false;
  }
}
</script>
