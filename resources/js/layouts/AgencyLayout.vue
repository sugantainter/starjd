<template>
  <div class="flex min-h-screen bg-[#f1f5f9]">
    <aside class="w-56 shrink-0 border-r border-[#e2e8f0] bg-white">
      <div class="sticky top-0 flex flex-col p-4">
        <router-link to="/agency/dashboard" class="mb-6 flex items-center gap-2">
          <img src="/logo.png" alt="StarJD" class="h-11 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
          <span class="hidden text-lg font-bold text-[#1a1a1a]">StarJD</span>
          <span class="text-sm font-medium text-[#64748b]">Agency Panel</span>
        </router-link>
        <nav class="space-y-1">
          <router-link to="/agency/dashboard" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#7c3aed]/10 !text-[#7c3aed]">Dashboard</router-link>
        </nav>
        <div class="mt-auto pt-6">
          <p class="mb-2 px-3 text-xs text-[#94a3b8]">StarJD powered by Suganta International</p>
          <button type="button" class="w-full rounded-lg px-3 py-2 text-left text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#7c3aed]" @click="logout">Logout</button>
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
    await axios.get('/api/agency/dashboard', { withCredentials: true });
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
