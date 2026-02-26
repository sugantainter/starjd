<template>
  <div class="flex min-h-screen bg-[#f8fafc]">
    <aside class="w-64 shrink-0 border-r border-[#e2e8f0] bg-white shadow-sm">
      <div class="sticky top-0 flex h-screen flex-col">
        <div class="border-b border-[#e2e8f0] px-4 py-5">
          <router-link to="/admin" class="flex items-center gap-2">
            <img src="/logo.png" alt="StarJD" class="h-10 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-lg font-bold text-[#1a1a1a]">StarJD</span>
            <span class="text-xs font-semibold uppercase tracking-wider text-[#64748b]">Admin</span>
          </router-link>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4">
          <router-link
            to="/admin"
            class="mb-1 flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]"
            active-class="!bg-[#e63946]/10 !text-[#e63946]"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" /></svg>
            Dashboard
          </router-link>

          <!-- Content -->
          <div class="mt-5">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.content = !navOpen.content">
              <span>Content &amp; CMS</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.content }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.content" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/pages" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Pages
              </router-link>
              <router-link to="/admin/posts" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6m4-4v12m-3-2h6" /></svg>
                Blog Posts
              </router-link>
              <router-link to="/admin/videos" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                Video Guides
              </router-link>
            </div>
          </div>

          <!-- Locations -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.locations = !navOpen.locations">
              <span>Locations</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.locations }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.locations" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/states" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                States
              </router-link>
              <router-link to="/admin/cities" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                Cities
              </router-link>
            </div>
          </div>

          <!-- Site building -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.site = !navOpen.site">
              <span>Site building</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.site }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.site" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/categories" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                Categories
              </router-link>
              <router-link to="/admin/hero" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" /></svg>
                Hero Section
              </router-link>
              <router-link to="/admin/testimonials" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Testimonials</router-link>
              <router-link to="/admin/faqs" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">FAQs</router-link>
              <router-link to="/admin/steps" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Steps</router-link>
              <router-link to="/admin/partners" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Partners</router-link>
              <router-link to="/admin/services" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Services</router-link>
            </div>
          </div>

          <!-- Studio marketplace -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.studios = !navOpen.studios">
              <span>Studio marketplace</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.studios }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.studios" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/studios" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                Studios
              </router-link>
            </div>
          </div>

          <!-- Inbox -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.inbox = !navOpen.inbox">
              <span>Inbox</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.inbox }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.inbox" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/contacts" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                Contacts
              </router-link>
            </div>
          </div>
        </nav>
        <div class="border-t border-[#e2e8f0] px-3 py-4">
          <p class="mb-2 px-3 text-xs text-[#94a3b8]">StarJD powered by Suganta International</p>
          <a href="/" class="mb-1 flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]">View Site</a>
          <button type="button" class="w-full rounded-lg px-3 py-2 text-left text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#e63946]" @click="logout">Logout</button>
        </div>
      </div>
    </aside>
    <main class="min-w-0 flex-1 overflow-auto p-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { reactive, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const navOpen = reactive({ content: true, locations: true, site: true, studios: true, inbox: true });

onMounted(async () => {
  try {
    await axios.get('/api/admin/user');
  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) {
      router.replace('/login?redirect=' + encodeURIComponent(router.currentRoute.value.fullPath));
    }
  }
});

watch(() => route.path, (path) => {
  if (path.startsWith('/admin/pages') || path.startsWith('/admin/posts') || path.startsWith('/admin/videos')) navOpen.content = true;
  if (path.startsWith('/admin/states') || path.startsWith('/admin/cities')) navOpen.locations = true;
  if (path.startsWith('/admin/categories') || path.startsWith('/admin/hero') || path.startsWith('/admin/testimonials') || path.startsWith('/admin/faqs') || path.startsWith('/admin/steps') || path.startsWith('/admin/partners') || path.startsWith('/admin/services')) navOpen.site = true;
  if (path.startsWith('/admin/studios')) navOpen.studios = true;
  if (path.startsWith('/admin/contacts')) navOpen.inbox = true;
}, { immediate: true });

function logout() {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  axios.post('/api/logout', {}, { withCredentials: true, headers: token ? { 'X-CSRF-TOKEN': token } : {} })
    .then(() => { window.location.href = '/login'; })
    .catch(() => { window.location.href = '/login'; });
}
</script>
