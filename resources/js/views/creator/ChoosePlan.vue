<template>
  <div class="mx-auto max-w-2xl py-8">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Choose your plan</h1>
    <p class="mt-2 text-[#64748b]">Pay once to access your creator dashboard. Secure payment via PayU.</p>
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
        <div class="mt-2 text-2xl font-bold text-[#10b981]">₹{{ displayPrice(plan) }}</div>
        <div v-if="couponApplied && selected?.id === plan.id" class="mt-1 text-sm text-[#10b981]">Discount applied</div>
        <div class="mt-1 text-sm text-[#64748b]">/ {{ plan.duration }}</div>
      </button>
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

    <!-- Available Coupons -->
    <div v-if="availableCoupons.length" class="mt-6 border-t border-[#e2e8f0] pt-6">
      <p class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-3">Available Discount Codes</p>
      <div class="grid gap-3 sm:grid-cols-2">
        <div v-for="c in availableCoupons" :key="c.id" 
          class="flex flex-col justify-between rounded-xl border border-dashed border-[#10b981]/30 bg-[#10b981]/5 p-4 cursor-pointer hover:bg-[#10b981]/10 transition"
          @click="couponCode = c.code; applyCoupon()"
        >
          <div class="flex items-center justify-between mb-2">
            <span class="font-mono font-bold text-[#10b981]">{{ c.code }}</span>
            <span class="text-[10px] font-bold text-white bg-[#10b981] px-1.5 py-0.5 rounded shadow-sm">
              {{ c.discount_type === 'percent' ? c.discount_value + '%' : '₹' + c.discount_value }} OFF
            </span>
          </div>
          <p class="text-xs text-[#64748b] line-clamp-2">{{ c.description }}</p>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <button
        type="button"
        :disabled="!selected || loading"
        class="cursor-link rounded-xl bg-[#10b981] px-6 py-3 font-semibold text-white transition hover:bg-[#059669] disabled:opacity-50"
        @click="pay"
      >
        {{ loading ? 'Redirecting to PayU…' : 'Pay via PayU & access dashboard' }}
      </button>
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
const availableCoupons = ref([]);

async function loadCoupons() {
  try {
    const { data } = await axios.get('/api/coupons', { params: { applicable_to: 'access' } });
    availableCoupons.value = data || [];
  } catch (e) {
    console.error('Failed to load coupons', e);
  }
}

function displayPrice(plan) {
  if (couponApplied.value && selected.value?.id === plan.id && finalAmount.value != null) return finalAmount.value;
  return plan.price;
}

onMounted(async () => {
  try {
    const res = await axios.get('/api/payment/plans', { withCredentials: true });
    plans.value = res.data || [];
  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) window.location.href = '/login';
    else error.value = 'Could not load plans.';
  }
  loadCoupons();
});

async function applyCoupon() {
  if (!couponCode.value.trim() || !selected.value) return;
  couponError.value = '';
  try {
    const res = await axios.get('/api/payment/coupon/validate', {
      params: { code: couponCode.value.trim(), amount: selected.value.price, applicable_to: 'access' },
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
      type: 'access',
      plan_id: selected.value.id,
      amount: Number(amount),
      coupon_code: couponCode.value.trim() || undefined,
    }, { withCredentials: true });
    
    if (res.data.free && res.data.redirect) {
      window.location.href = res.data.redirect;
      return;
    }

    if (!res.data.payment_url) {
      throw new Error(res.data.message || 'Payment URL missing from server.');
    }

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
