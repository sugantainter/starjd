<template>
  <div class="flex min-h-screen bg-[#f1f5f9]">
    <aside class="w-64 shrink-0 border-r border-[#e2e8f0] bg-white">
      <div class="sticky top-0 flex flex-col p-4">
        <router-link to="/brand/dashboard" class="mb-6 rounded-2xl border border-[#e2e8f0] bg-gradient-to-r from-white to-[#f8fafc] px-4 py-4 shadow-sm transition hover:shadow-md">
          <div class="flex items-center justify-center rounded-xl bg-white/90 px-2 py-2">
            <img src="/logo.png" alt="StarJD" class="h-14 w-44 object-contain drop-shadow-[0_4px_10px_rgba(0,0,0,0.08)]" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-2xl font-bold text-[#1a1a1a]">StarJD</span>
          </div>
          <span class="mt-3 block text-center text-sm font-semibold tracking-[0.08em] text-[#64748b]">Brand Panel</span>
        </router-link>
        <nav class="space-y-1">
          <router-link to="/brand/dashboard" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Dashboard</router-link>
          <router-link to="/brand/profile" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Profile</router-link>
          <router-link to="/brand/creators" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Discover Creators</router-link>
          <router-link to="/brand/post-campaign" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Post Campaign</router-link>
          <router-link to="/brand/collaborations" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Collaborations</router-link>
          <router-link to="/brand/messages" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Messages</router-link>
        </nav>
        <div class="mt-auto pt-6">
          <p class="mb-2 px-3 text-xs text-[#94a3b8]">StarJD powered by Suganta International</p>
          <button type="button" class="w-full rounded-lg px-3 py-2 text-left text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#e63946]" @click="logout">Logout</button>
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
  if (route.path === '/brand/choose-plan') {
    try {
      const me = await axios.get('/api/me', { withCredentials: true });
      if (me.data?.role === 'creator') router.replace('/creator/choose-plan');
    } catch (_) {}
    return;
  }
  try {
    await axios.get('/api/brand/dashboard');
  } catch (e) {
    if (e.response?.status === 402 || e.response?.data?.requires_payment) {
      router.replace('/brand/choose-plan');
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
