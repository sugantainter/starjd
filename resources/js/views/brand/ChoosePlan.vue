<template>
  <div class="mx-auto max-w-2xl py-8">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Choose your plan</h1>
    <p class="mt-2 text-[#64748b]">Pay once to access your brand dashboard and collaborate with creators.</p>
    <div v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
      <button
        v-for="plan in plans"
        :key="plan.id"
        type="button"
        :disabled="loading"
        class="cursor-link rounded-xl border-2 p-6 text-left transition"
        :class="selected?.id === plan.id ? 'border-[#e63946] bg-[#e63946]/5' : 'border-[#e2e8f0] hover:border-[#e63946]/50'"
        @click="selected = plan"
      >
        <div class="font-semibold text-[#1a1a1a]">{{ plan.name }}</div>
        <div class="mt-2 text-2xl font-bold text-[#e63946]">₹{{ plan.price }}</div>
        <div class="mt-1 text-sm text-[#64748b]">/ {{ plan.duration }}</div>
      </button>
    </div>
    <div class="mt-8">
      <button
        type="button"
        :disabled="!selected || loading"
        class="cursor-link rounded-xl bg-[#e63946] px-6 py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50"
        @click="pay"
      >
        {{ loading ? 'Processing…' : 'Pay & access dashboard' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const plans = ref([]);
const selected = ref(null);
const loading = ref(false);
const error = ref('');

onMounted(async () => {
  try {
    const res = await axios.get('/api/payment/plans', { withCredentials: true });
    plans.value = res.data || [];
  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) {
      window.location.href = '/login';
      return;
    }
    error.value = 'Could not load plans.';
  }
});

async function pay() {
  if (!selected.value) return;
  loading.value = true;
  error.value = '';
  try {
    const res = await axios.post('/api/payment/pay', {
      plan_id: selected.value.id,
      amount: selected.value.price,
    }, { withCredentials: true });
    const redirect = res.data?.redirect || '/brand/dashboard';
    window.location.href = redirect;
  } catch (e) {
    error.value = e.response?.data?.message || 'Payment failed.';
  } finally {
    loading.value = false;
  }
}
</script>
