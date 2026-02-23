<template>
  <div class="flex min-h-screen bg-[#f1f5f9]">
    <aside class="w-56 shrink-0 border-r border-[#e2e8f0] bg-white">
      <div class="sticky top-0 flex flex-col p-4">
        <router-link to="/creator/dashboard" class="mb-6 flex items-center gap-2">
          <img src="/logo.png" alt="StarJD" class="h-11 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
          <span class="hidden text-lg font-bold text-[#1a1a1a]">StarJD</span>
          <span class="text-sm font-medium text-[#64748b]">Creator Panel</span>
        </router-link>
        <nav class="space-y-1">
          <router-link to="/creator/dashboard" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#10b981]/10 !text-[#10b981]">Dashboard</router-link>
          <router-link to="/creator/profile" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#10b981]/10 !text-[#10b981]">Profile</router-link>
          <router-link to="/creator/packages" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#10b981]/10 !text-[#10b981]">Packages</router-link>
          <router-link to="/creator/social" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#10b981]/10 !text-[#10b981]">Social Accounts</router-link>
          <router-link to="/creator/collaborations" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#10b981]/10 !text-[#10b981]">Collaborations</router-link>
          <router-link to="/creator/featured" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#10b981]/10 !text-[#10b981]">Featured</router-link>
        </nav>
        <div class="mt-auto pt-6">
          <p class="mb-2 px-3 text-xs text-[#94a3b8]">StarJD powered by Suganta International</p>
          <router-link to="/creators" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]">View my profile</router-link>
          <button type="button" class="w-full rounded-lg px-3 py-2 text-left text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#10b981]" @click="logout">Logout</button>
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
  if (route.path === '/creator/choose-plan') {
    try {
      const me = await axios.get('/api/me', { withCredentials: true });
      if (me.data?.role === 'brand') router.replace('/brand/choose-plan');
    } catch (_) {}
    return;
  }
  try {
    await axios.get('/api/creator/dashboard');
  } catch (e) {
    if (e.response?.status === 402 || e.response?.data?.requires_payment) {
      router.replace('/creator/choose-plan');
      return;
    }
    if (e.response?.status === 401 || e.response?.status === 403) {
      window.location.href = '/login?redirect=' + encodeURIComponent(route.fullPath);
    }
  }
});

function logout() {
  axios.post('/api/logout').catch(() => {}).finally(() => {
    window.location.href = '/login';
  });
}
</script>
