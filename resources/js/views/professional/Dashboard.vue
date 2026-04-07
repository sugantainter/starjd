<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const stats = ref({
  listings_count: 0,
  active_orders_count: 0,
  total_earnings: 0
});
const recentOrders = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await axios.get('/api/professional/dashboard');
    stats.value = res.data.stats;
    recentOrders.value = res.data.recent_orders;
  } catch (err) {
    if (err.response?.status === 402 || err.response?.data?.requires_payment) {
      router.replace('/professional/choose-plan');
      return;
    }
    console.error('Failed to fetch dashboard data', err);
  } finally {
    loading.value = false;
  }
});

function formatCurrency(amt) {
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amt || 0);
}
</script>

<template>
  <div v-if="!loading">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Professional Dashboard</h1>
    <p class="mt-1 text-[#64748b]">Maximize your reach and manage your professional services.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Active Orders</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.active_orders_count }}</div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Total Earnings</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ formatCurrency(stats.total_earnings) }}</div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Services Listed</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.listings_count }}</div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Response Rate</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">100%</div>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Recent Orders</h2>
      <div v-if="!recentOrders.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-8 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#f1f5f9] text-[#64748b]">
          <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 11-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </div>
        <h3 class="mt-4 text-lg font-medium text-[#1a1a1a]">No recent orders</h3>
        <p class="mt-1 text-sm text-[#64748b]">Share your profile to get your first order!</p>
        <button class="mt-6 rounded-xl bg-[#f59e0b] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#d97706]">Share Profile</button>
      </div>
      <div v-else class="mt-4 overflow-hidden rounded-xl border border-[#e2e8f0] bg-white">
        <table class="w-full text-left text-sm">
           <thead>
             <tr class="bg-[#f8fafc] border-b border-[#e2e8f0]">
               <th class="px-6 py-4 font-semibold">Order</th>
               <th class="px-6 py-4 font-semibold">Client</th>
               <th class="px-6 py-4 font-semibold">Amount</th>
               <th class="px-6 py-4 font-semibold">Status</th>
             </tr>
           </thead>
           <tbody>
             <tr v-for="order in recentOrders" :key="order.id" class="border-b border-[#f1f5f9]">
               <td class="px-6 py-4 font-medium">{{ order.listing?.title }}</td>
               <td class="px-6 py-4">{{ order.buyer?.name }}</td>
               <td class="px-6 py-4 font-semibold text-[#1a1a1a]">{{ formatCurrency(order.amount) }}</td>
               <td class="px-6 py-4">
                 <span class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize bg-amber-100 text-amber-800">{{ order.status }}</span>
               </td>
             </tr>
           </tbody>
        </table>
      </div>
    </div>
  </div>
  <div v-else class="flex h-96 items-center justify-center">
    <div class="h-8 w-8 animate-spin rounded-full border-4 border-[#f59e0b] border-t-transparent"></div>
  </div>
</template>
