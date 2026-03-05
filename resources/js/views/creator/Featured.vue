<template>
  <div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Get Featured</h1>
    <p class="mt-1 text-[#64748b]">Featured creators appear first in Discover and on the homepage. Choose a duration and pay to go featured. Secure payment via PayU.</p>

    <div v-if="status?.is_featured && status?.featured_until" class="mt-6 rounded-xl border border-[#10b981] bg-[#ecfdf5] p-4">
      <p class="text-sm font-medium text-[#059669]">You are featured until {{ formatDate(status.featured_until) }}.</p>
      <p class="mt-1 text-sm text-[#64748b]">Extend your featured period by selecting a plan below.</p>
    </div>

    <div v-if="error" class="mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>

    <div class="mt-8">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Choose a plan</h2>
      <div class="mt-4 grid gap-4 sm:grid-cols-3">
        <button
          v-for="plan in plans"
          :key="plan.id"
          type="button"
          :disabled="loading"
          class="cursor-link rounded-xl border-2 p-6 text-left transition"
          :class="selected?.id === plan.id ? 'border-[#f59e0b] bg-[#fffbeb]' : 'border-[#e2e8f0] bg-white hover:border-[#f59e0b]/50'"
          @click="selected = plan"
        >
          <div class="font-semibold text-[#1a1a1a]">{{ plan.name }}</div>
          <div class="mt-2 text-2xl font-bold text-[#f59e0b]">₹{{ displayPrice(plan) }}</div>
          <div v-if="couponApplied && selected?.id === plan.id" class="mt-1 text-sm text-[#10b981]">Discount applied</div>
          <div class="mt-1 text-sm text-[#64748b]">{{ plan.duration_days }} days</div>
          <p class="mt-3 text-xs text-[#64748b]">Your profile appears at the top of Discover for this period.</p>
        </button>
      </div>
    </div>

    <div class="mt-6 flex flex-wrap items-end gap-2">
      <div class="min-w-[180px] flex-1">
        <label class="mb-1 block text-sm font-medium text-[#64748b]">Coupon code (optional)</label>
        <input v-model="couponCode" type="text" placeholder="e.g. SAVE20" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
      </div>
      <button type="button" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm hover:bg-[#f1f5f9]" @click="applyCoupon">Apply</button>
    </div>
    <p v-if="couponError" class="mt-1 text-sm text-red-600">{{ couponError }}</p>
    <p v-if="couponApplied" class="mt-1 text-sm text-[#10b981]">Coupon applied. Pay ₹{{ finalAmount }}</p>

    <div class="mt-8 flex flex-wrap items-center gap-4">
      <button
        type="button"
        :disabled="!selected || loading"
        class="cursor-link rounded-xl bg-[#f59e0b] px-6 py-3 font-semibold text-white transition hover:bg-[#d97706] disabled:opacity-50"
        @click="pay"
      >
        {{ loading ? 'Redirecting to PayU…' : 'Pay & get featured' }}
      </button>
      <router-link to="/creator/dashboard" class="cursor-link rounded-xl border border-[#e2e8f0] px-6 py-3 text-sm font-medium text-[#64748b] hover:bg-[#f1f5f9]">Back to dashboard</router-link>
    </div>
    <form ref="payuForm" method="post" :action="payuUrl" class="hidden">
      <input v-for="(val, key) in payuParams" :key="key" :name="key" :value="val" type="hidden" />
    </form>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from 'vue';
import axios from 'axios';

const plans = ref([]);
const status = ref(null);
const selected = ref(null);
const loading = ref(false);
const error = ref('');
const couponCode = ref('');
const couponError = ref('');
const couponApplied = ref(false);
const finalAmount = ref(null);
const payuForm = ref(null);
const payuUrl = ref('');
const payuParams = ref({});

function displayPrice(plan) {
  if (couponApplied.value && selected.value?.id === plan.id && finalAmount.value != null) return finalAmount.value;
  return plan.price;
}

function formatDate(iso) {
  if (!iso) return '';
  return new Date(iso).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

onMounted(async () => {
  try {
    const [plansRes, statusRes] = await Promise.all([
      axios.get('/api/creator/featured-plans', { withCredentials: true }),
      axios.get('/api/creator/featured/status', { withCredentials: true }),
    ]);
    plans.value = plansRes.data ?? [];
    status.value = statusRes.data ?? null;
  } catch (e) {
    error.value = 'Could not load plans.';
  }
});

async function applyCoupon() {
  if (!couponCode.value.trim() || !selected.value) return;
  couponError.value = '';
  try {
    const res = await axios.get('/api/payment/coupon/validate', {
      params: { code: couponCode.value.trim(), amount: selected.value.price, applicable_to: 'featured' },
      withCredentials: true,
    });
    if (res.data?.valid && res.data?.data) {
      couponApplied.value = true;
      finalAmount.value = res.data.data.final_amount;
      couponError.value = '';
    }
  } catch (e) {
    couponApplied.value = false;
    finalAmount.value = null;
    couponError.value = e.response?.data?.message || 'Invalid coupon';
  }
}

async function pay() {
  if (!selected.value) return;
  loading.value = true;
  error.value = '';
  const amount = couponApplied.value && finalAmount.value != null ? finalAmount.value : selected.value.price;
  try {
    const res = await axios.post('/api/payment/payu/create', {
      type: 'featured',
      plan_id: selected.value.id,
      amount: Number(amount),
      coupon_code: couponCode.value.trim() || undefined,
    }, { withCredentials: true });
    payuUrl.value = res.data.payment_url;
    payuParams.value = res.data.params || {};
    nextTick(() => {
      if (payuForm.value) payuForm.value.submit();
    });
  } catch (e) {
    error.value = e.response?.data?.message || 'Payment failed.';
    loading.value = false;
  }
}
</script>
