<template>
  <div class="rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-[#1a1a1a]">Book this studio</h3>
    <p v-if="priceLabel" class="mt-1 text-2xl font-bold text-[#e63946]">{{ priceLabel }}</p>

    <div class="mt-4 space-y-3">
      <div>
        <label class="block text-sm font-medium text-[#64748b]">Date</label>
        <input
          v-model="localDate"
          type="date"
          :min="today"
          class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
          @change="onChange"
        />
      </div>
      <div class="grid grid-cols-2 gap-2">
        <div>
          <label class="block text-sm font-medium text-[#64748b]">Start</label>
          <input
            v-model="localStart"
            type="time"
            class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 focus:border-[#e63946] focus:outline-none"
            @change="onChange"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-[#64748b]">End</label>
          <input
            v-model="localEnd"
            type="time"
            class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 focus:border-[#e63946] focus:outline-none"
            @change="onChange"
          />
        </div>
      </div>
    </div>

    <div v-if="breakdown" class="mt-4 rounded-lg bg-[#f8fafc] p-3 text-sm">
      <div class="flex justify-between"><span>Subtotal</span><span>₹{{ breakdown.amount }}</span></div>
      <div v-if="breakdown.discount > 0" class="flex justify-between text-[#10b981]"><span>Discount</span><span>-₹{{ breakdown.discount }}</span></div>
      <div v-if="breakdown.platform_commission > 0" class="flex justify-between text-[#64748b]"><span>Platform fee</span><span>₹{{ breakdown.platform_commission }}</span></div>
      <div class="mt-2 flex justify-between font-semibold"><span>Total</span><span>₹{{ breakdown.amount }}</span></div>
    </div>

    <!-- Available Coupons -->
    <div v-if="availableCoupons.length" class="mt-4 border-t border-[#e2e8f0] pt-4">
      <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider mb-2">🎁 Special Offers for you</p>
      <div class="space-y-2">
        <div v-for="c in availableCoupons" :key="c.id" class="relative overflow-hidden rounded-xl border border-dashed border-[#10b981] bg-[#10b981]/5 p-3 group">
          <div class="flex items-center justify-between">
            <span class="font-mono font-bold text-[#059669] text-sm">{{ c.code }}</span>
            <span class="text-[10px] font-bold text-white bg-[#10b981] px-1.5 py-0.5 rounded shadow-sm">{{ c.discount_type === 'percent' ? c.discount_value + '%' : '₹' + c.discount_value }} OFF</span>
          </div>
          <p class="mt-1 text-[11px] text-[#64748b] leading-tight">{{ c.description }}</p>
        </div>
      </div>
    </div>

    <button
      type="button"
      class="mt-6 w-full rounded-xl bg-[#e63946] py-3 font-medium text-white hover:bg-[#c1121f] disabled:opacity-50"
      :disabled="!canBook || bookingInProgress"
      @click="emit('book')"
    >
      {{ bookingInProgress ? 'Processing…' : (breakdown?.amount === 0 ? 'Confirm Free Booking' : 'Request to book') }}
    </button>
    <p class="mt-2 text-center text-xs text-[#64748b]">Login required to book. Payment collected after confirmation.</p>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  studio: { type: Object, required: true },
  breakdown: { type: Object, default: null },
  canBook: { type: Boolean, default: true },
  bookingInProgress: { type: Boolean, default: false },
  date: { type: String, default: '' },
  startTime: { type: String, default: '' },
  endTime: { type: String, default: '' },
});

const emit = defineEmits(['update:date', 'update:startTime', 'update:endTime', 'book']);

const today = computed(() => new Date().toISOString().slice(0, 10));

const localDate = ref(props.date);
const localStart = ref(props.startTime || '09:00');
const localEnd = ref(props.endTime || '10:00');

watch([localDate, localStart, localEnd], () => {
  emit('update:date', localDate.value);
  emit('update:startTime', localStart.value);
  emit('update:endTime', localEnd.value);
});

watch(() => [props.date, props.startTime, props.endTime], ([d, s, e]) => {
  if (d) localDate.value = d;
  if (s) localStart.value = s;
  if (e) localEnd.value = e;
});

function onChange() {
  emit('update:date', localDate.value);
  emit('update:startTime', localStart.value);
  emit('update:endTime', localEnd.value);
}

const availableCoupons = ref([]);

async function loadCoupons() {
  try {
    const { data } = await axios.get('/api/coupons', { params: { applicable_to: 'booking' } });
    availableCoupons.value = data || [];
  } catch (e) {
    console.error('Failed to load coupons', e);
  }
}

onMounted(loadCoupons);

const priceLabel = computed(() => {
  const s = props.studio;
  if (s.price_per_hour != null) return `₹${s.price_per_hour}/hr`;
  if (s.price_per_day != null) return `₹${s.price_per_day}/day`;
  return null;
});
</script>
