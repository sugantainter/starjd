<template>
  <div class="flex min-h-screen bg-[#f1f5f9]">
    <aside class="w-56 shrink-0 border-r border-[#e2e8f0] bg-white">
      <div class="sticky top-0 flex flex-col p-4">
        <router-link to="/studio/dashboard" class="mb-6 flex items-center gap-2">
          <img src="/logo.png" alt="StarJD" class="h-11 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
          <span class="hidden text-lg font-bold text-[#1a1a1a]">StarJD</span>
          <span class="text-sm font-medium text-[#64748b]">Studio Panel</span>
        </router-link>
        <nav class="space-y-1">
          <router-link to="/studio/dashboard" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Dashboard</router-link>
          <router-link to="/studio/studios" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">My Studios</router-link>
          <router-link to="/studio/bookings" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Bookings</router-link>
        </nav>
        <div class="mt-auto pt-6">
          <router-link to="/studios" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]">Browse studios</router-link>
          <button type="button" class="mt-1 w-full rounded-lg px-3 py-2 text-left text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#e63946]" @click="logout">Logout</button>
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
    await axios.get('/api/studio/dashboard', { withCredentials: true });
  } catch (e) {
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
