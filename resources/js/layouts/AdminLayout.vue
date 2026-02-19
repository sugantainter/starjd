<template>
  <div class="flex min-h-screen bg-[#f1f5f9]">
    <aside class="w-56 shrink-0 border-r border-[#e2e8f0] bg-white">
      <div class="sticky top-0 flex flex-col p-4">
        <router-link to="/admin" class="mb-6 flex items-center gap-2">
          <img src="/logo.jpeg" alt="StarJD" class="h-8 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
          <span class="hidden text-lg font-bold text-[#1a1a1a]">StarJD</span>
          <span class="text-sm font-medium text-[#64748b]">Admin</span>
        </router-link>
        <nav class="space-y-1">
          <router-link to="/admin" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Dashboard</router-link>
          <router-link to="/admin/categories" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Categories</router-link>
          <router-link to="/admin/testimonials" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Testimonials</router-link>
          <router-link to="/admin/faqs" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">FAQs</router-link>
          <router-link to="/admin/steps" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Steps</router-link>
          <router-link to="/admin/contacts" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Contacts</router-link>
          <router-link to="/admin/posts" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Blog Posts</router-link>
          <router-link to="/admin/videos" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Video Guides</router-link>
          <router-link to="/admin/hero" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Hero Section</router-link>
          <router-link to="/admin/partners" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Partners</router-link>
        </nav>
        <div class="mt-auto pt-6">
          <a href="/" class="block rounded-lg px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]">View Site</a>
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
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

onMounted(async () => {
  try {
    await axios.get('/api/admin/user');
  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) {
      router.replace('/login?redirect=' + encodeURIComponent(router.currentRoute.value.fullPath));
    }
  }
});

function logout() {
  axios.post('/api/logout').catch(() => {}).finally(() => {
    window.location.href = '/login';
  });
}
</script>
