<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Studio dashboard</h1>
    <p class="mt-1 text-[#64748b]">Welcome back, {{ data?.user?.name }}.</p>
    <div class="mt-6 grid gap-4 sm:grid-cols-2">
      <router-link to="/studio/studios" class="rounded-xl border border-[#e2e8f0] bg-white p-4 transition hover:border-[#e63946]/30 hover:bg-[#e63946]/5">
        <div class="text-sm text-[#64748b]">My studios</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ studioCount }}</div>
        <div class="mt-2 text-sm text-[#e63946]">Manage studios →</div>
      </router-link>
      <router-link to="/studio/bookings" class="rounded-xl border border-[#e2e8f0] bg-white p-4 transition hover:border-[#e63946]/30 hover:bg-[#e63946]/5">
        <div class="text-sm text-[#64748b]">Bookings</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ bookingCount }}</div>
        <div class="mt-2 text-sm text-[#e63946]">View bookings →</div>
      </router-link>
    </div>
    <div class="mt-8">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Get started</h2>
      <p class="mt-2 text-[#64748b]">Add your first studio to start receiving bookings from creators and brands.</p>
      <router-link to="/studio/studios" class="mt-4 inline-block rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f]">Add a studio</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const data = ref(null);
const studioCount = ref(0);
const bookingCount = ref(0);

onMounted(async () => {
  try {
    const [dashboardRes, studiosRes, bookingsRes] = await Promise.all([
      axios.get('/api/studio/dashboard', { withCredentials: true }),
      axios.get('/api/studio-owner/studios', { withCredentials: true }).catch(() => ({ data: { data: [] } })),
      axios.get('/api/studio-owner/bookings', { withCredentials: true }).catch(() => ({ data: { total: 0 } })),
    ]);
    data.value = dashboardRes.data;
    studioCount.value = Array.isArray(studiosRes.data?.data) ? studiosRes.data.data.length : studiosRes.data?.length ?? 0;
    bookingCount.value = bookingsRes.data?.total ?? (Array.isArray(bookingsRes.data?.data) ? bookingsRes.data.data.length : 0);
  } catch (_) {
    data.value = {};
  }
});
</script>
