<template>
  <div class="flex min-h-screen bg-[#f1f5f9]">
    <aside class="w-64 shrink-0 border-r border-[#e2e8f0] bg-white">
      <div class="sticky top-0 flex flex-col p-4">
        <router-link to="/professional/dashboard" class="mb-6 rounded-2xl border border-[#e2e8f0] bg-gradient-to-r from-white to-[#f8fafc] px-4 py-4 shadow-sm transition hover:shadow-md">
          <div class="flex items-center justify-center rounded-xl bg-white/90 px-2 py-2">
            <img src="/logo.png" alt="StarJD" class="h-16 w-auto object-contain drop-shadow-[0_4px_10px_rgba(0,0,0,0.08)]" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-2xl font-bold text-[#1a1a1a]">StarJD</span>
          </div>
          <span class="mt-3 block text-center text-sm font-semibold tracking-[0.08em] text-[#64748b]">Professional Panel</span>
        </router-link>
        <nav class="space-y-1">
          <router-link to="/professional/dashboard" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#f59e0b]/10 !text-[#f59e0b]">Dashboard</router-link>
          <router-link to="/professional/profile" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#f59e0b]/10 !text-[#f59e0b]">Profile</router-link>
          <router-link to="/professional/services" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#f59e0b]/10 !text-[#f59e0b]">My Services</router-link>
          <router-link to="/professional/orders" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#f59e0b]/10 !text-[#f59e0b]">Active Orders</router-link>
          <router-link to="/professional/messages" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#f59e0b]/10 !text-[#f59e0b]">Messages</router-link>
          <router-link to="/professional/earnings" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#f59e0b]/10 !text-[#f59e0b]">Earnings</router-link>
          <router-link to="/professional/support" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#f59e0b]/10 !text-[#f59e0b]">Help & Support</router-link>
        </nav>
        <div class="mt-auto pt-6">
          <p class="mb-2 px-3 text-xs text-[#94a3b8]">StarJD powered by Suganta International</p>
          <button type="button" class="w-full rounded-lg px-3 py-2 text-left text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#f59e0b]" @click="logout">Logout</button>
        </div>
      </div>
    </aside>
    <main class="flex-1 overflow-auto p-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();

onMounted(async () => {
  try {
    const res = await axios.get('/api/me');
    if (!res.data?.has_paid_access && route.path !== '/professional/choose-plan') {
      router.replace('/professional/choose-plan');
      return;
    }
    if (res.data?.role !== 'professional' && res.data?.role !== 'admin') {
      router.replace('/');
      return;
    }
    // Also try calling dashboard to trigger middleware if session is stale or complex rules apply
    await axios.get('/api/professional/dashboard');
  } catch (e) {
    if (e.response?.status === 402 || e.response?.data?.requires_payment) {
      router.replace('/professional/choose-plan');
      return;
    }
    if (e.response?.status === 401 || e.response?.status === 403) {
      window.location.href = '/login?redirect=' + encodeURIComponent(route.fullPath);
    }
  }
});

function logout() {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  axios.post('/api/logout', {}, { withCredentials: true, headers: token ? { 'X-CSRF-TOKEN': token } : {} })
    .then(() => { window.location.href = '/login'; })
    .catch(() => { window.location.href = '/login'; });
}
</script>
