<template>
  <header ref="headerRef" class="sticky top-0 z-50 w-full max-w-[calc(100%-3rem)] mx-auto">
    <nav class="nav-bar nav-bar-bg flex items-center w-full h-auto rounded-[10rem] border-b-0 justify-between px-3 py-2 sm:px-8 sm:py-2">
      <!-- Logo – left -->
      <router-link to="/" class="flex shrink-0 items-center" @click="navMobileOpen = false">
        <img src="/logo.png" alt="StarJD" class="h-11 w-auto object-contain sm:h-12" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
        <span class="hidden text-xl font-bold tracking-tight text-[#1a1a1a]">StarJD</span>
      </router-link>

      <!-- Right: menu toggle (mobile) + links & buttons -->
      <div class="flex items-center gap-6 md:gap-8 ml-auto">
        <!-- Mobile / tablet menu toggle -->
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg border border-[#e5e7eb] bg-white text-[#1a1a1a] transition hover:border-[#e63946] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20 md:hidden"
          aria-label="Toggle menu"
          :aria-expanded="navMobileOpen"
          @click.stop="navMobileOpen = !navMobileOpen"
        >
          <svg v-if="!navMobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Desktop nav -->
        <div class="hidden items-center gap-4 md:flex">
          <router-link to="/" class="text-sm font-semibold transition hover:text-[#e63946]">Home</router-link>
          <router-link to="/about" class="text-sm font-semibold transition hover:text-[#e63946]">About</router-link>
          <router-link to="/contact" class="text-sm font-semibold transition hover:text-[#e63946]">Contact</router-link>
          <router-link to="/blog" class="text-sm font-semibold transition hover:text-[#e63946]">Blog</router-link>
          <div
            ref="servicesRef"
            class="relative"
            @mouseenter="servicesDropdownOpen = true"
            @mouseleave="servicesDropdownOpen = false"
          >
            <button
              type="button"
              class="inline-flex items-center gap-1 text-sm font-semibold transition hover:text-[#e63946]"
              :class="{ 'text-[#e63946]': servicesDropdownOpen }"
              aria-haspopup="true"
              :aria-expanded="servicesDropdownOpen"
              @click="servicesDropdownOpen = !servicesDropdownOpen"
            >
              Services
              <svg class="h-4 w-4 shrink-0 transition-transform" :class="{ 'rotate-180': servicesDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <Transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-y-1"
            >
              <div
                v-show="servicesDropdownOpen"
                class="absolute left-1/2 top-full z-50 mt-2 w-[min(95vw,600px)] -translate-x-1/2 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-xl"
              >
                <div class="flex min-h-[280px]">
                  <!-- Left column: All Services heading + 5 options -->
                  <div class="flex flex-1 flex-col border-r border-[#e5e7eb] py-4">
                    <span class="mb-2 inline-block px-4 text-xs font-semibold uppercase tracking-wide text-[#e63946]">All Services</span>
                    <div class="flex flex-col gap-0.5 px-4">
                      <router-link
                        v-for="(item, idx) in servicesColumn1"
                        :key="'c1-' + idx"
                        :to="'/services/' + item.slug"
                        class="block py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                        @click="servicesDropdownOpen = false"
                      >
                        {{ item.name }}
                      </router-link>
                    </div>
                  </div>
                  <!-- Middle column: 4 options -->
                  <div class="flex flex-1 flex-col border-r border-[#e5e7eb] py-4">
                    <div class="flex flex-col gap-0.5 px-4">
                      <router-link
                        v-for="(item, idx) in servicesColumn2"
                        :key="'c2-' + idx"
                        :to="'/services/' + item.slug"
                        class="block py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                        @click="servicesDropdownOpen = false"
                      >
                        {{ item.name }}
                      </router-link>
                    </div>
                  </div>
                  <!-- Right: case study card with image -->
                  <router-link
                    to="/services"
                    class="services-dropdown-card flex w-[52%] min-w-[200px] shrink-0 flex-col overflow-hidden bg-[#f5f5f4] p-4 transition hover:bg-[#eee]"
                    @click="servicesDropdownOpen = false"
                  >
                    <span class="mb-2 inline-block text-xs font-semibold uppercase tracking-wide text-[#e63946]">Case study</span>
                    <div class="flex flex-1 flex-col">
                      <p class="text-lg font-bold text-[#1a1a1a]">Driving brand love and sales</p>
                      <p class="mt-1.5 text-sm leading-snug text-[#64748b]">Learn how we helped brands launch creator campaigns with millions of impressions.</p>
                    </div>
                    <div class="mt-3 aspect-[16/10] overflow-hidden rounded-lg bg-[#e5e7eb]">
                      <img
                        v-if="servicesDropdownImage"
                        :src="servicesDropdownImage"
                        alt="Case study"
                        class="h-full w-full object-cover"
                      />
                      <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#e63946]/15 to-[#1a1a1a]/10">
                        <span class="text-3xl font-bold text-[#e63946]/40">StarJD</span>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
            </Transition>
          </div>
          <router-link to="/videos" class="text-sm font-semibold transition hover:text-[#e63946]">Videos</router-link>
          <router-link to="/creators" class="text-sm font-semibold transition hover:text-[#e63946]">Discover Creators</router-link>

          <template v-if="displayUser">
            <div class="relative" ref="userMenuRef">
              <button
                type="button"
                class="flex items-center gap-2 rounded-lg border border-[#e5e7eb] bg-white px-4 py-2 text-sm font-medium text-[#1a1a1a] shadow-sm transition hover:border-[#e63946] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                :class="user.role === 'creator' ? 'hover:border-[#10b981] focus:ring-[#10b981]/20' : user.role === 'admin' ? 'hover:border-[#1e293b] focus:ring-[#1e293b]/20' : user.role === 'agency' ? 'hover:border-[#7c3aed] focus:ring-[#7c3aed]/20' : ''"
                @click="userMenuOpen = !userMenuOpen"
              >
                <span class="max-w-[120px] truncate">{{ user.name }}</span>
                <svg class="h-4 w-4 shrink-0 text-[#64748b] transition-transform" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
              </button>
              <Transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div
                  v-show="userMenuOpen"
                  class="absolute right-0 top-full z-50 mt-1 min-w-[200px] rounded-xl border border-[#e2e8f0] bg-white py-1 shadow-lg"
                >
                  <div class="border-b border-[#e2e8f0] px-4 py-2.5">
                    <p class="text-xs font-medium uppercase tracking-wide text-[#64748b]">{{ user.role }}</p>
                    <p class="truncate text-sm font-medium text-[#1a1a1a]">{{ user.name }}</p>
                  </div>
                  <template v-if="displayUser && displayUser.role === 'creator'">
                    <router-link to="/creator/dashboard" class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]" @click="userMenuOpen = false">Creator Dashboard</router-link>
                    <router-link to="/creator/profile" class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]" @click="userMenuOpen = false">My Profile</router-link>
                  </template>
                  <template v-else-if="user.role === 'brand'">
                    <router-link to="/brand/dashboard" class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="userMenuOpen = false">Brand Dashboard</router-link>
                    <router-link to="/brand/creators" class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="userMenuOpen = false">Discover Creators</router-link>
                  </template>
                  <template v-else-if="user.role === 'admin'">
                    <router-link to="/admin" class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]" @click="userMenuOpen = false">Admin Panel</router-link>
                  </template>
                  <template v-else-if="user.role === 'agency'">
                    <router-link to="/agency/dashboard" class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#7c3aed]/5 hover:text-[#7c3aed]" @click="userMenuOpen = false">Agency Dashboard</router-link>
                  </template>
                  <div class="border-t border-[#e2e8f0] pt-1">
                    <button type="button" class="block w-full px-4 py-2.5 text-left text-sm text-[#64748b] transition hover:bg-red-50 hover:text-red-600" @click="logout">Logout</button>
                  </div>
                </div>
              </Transition>
            </div>
          </template>
          <template v-else>
            <router-link to="/login" class="rounded-lg border border-[#e5e7eb] px-4 py-2 text-sm font-medium transition hover:border-[#e63946] hover:text-[#e63946]">Login</router-link>
            <router-link to="/creator-landing" class="join-creator rounded-lg border border-[#e5e7eb] px-4 py-2 text-sm font-medium transition hover:border-[#10b981] hover:bg-[#10b981]/5 hover:text-[#10b981]">Join as Creator</router-link>
            <router-link to="/brand-landing" class="join-brand rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#c1121f]">Join as Brand</router-link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Mobile / tablet dropdown -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-show="navMobileOpen"
        class="absolute left-0 right-0 top-full z-40 border-t border-[#e5e7eb] bg-white shadow-lg md:hidden"
      >
        <div class="mx-auto max-w-6xl px-4 py-4">
          <div class="flex flex-col gap-1">
            <router-link to="/" class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Home</router-link>
            <router-link to="/about" class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">About</router-link>
            <router-link to="/contact" class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Contact</router-link>
            <router-link to="/blog" class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Blog</router-link>
            <router-link to="/services" class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Services</router-link>
            <router-link to="/videos" class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Videos</router-link>
            <router-link to="/creators" class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Discover Creators</router-link>
            <template v-if="displayUser">
              <div class="my-2 border-t border-[#e5e7eb] pt-2">
                <p class="px-4 py-1 text-xs font-medium uppercase text-[#64748b]">{{ user.role }}</p>
                <p class="truncate px-4 py-1 text-sm font-medium text-[#1a1a1a]">{{ user.name }}</p>
              </div>
              <template v-if="displayUser && displayUser.role === 'creator'">
                <router-link to="/creator/dashboard" class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]" @click="navMobileOpen = false">Creator Dashboard</router-link>
                <router-link to="/creator/profile" class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]" @click="navMobileOpen = false">My Profile</router-link>
              </template>
              <template v-else-if="user.role === 'brand'">
                <router-link to="/brand/dashboard" class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Brand Dashboard</router-link>
                <router-link to="/brand/creators" class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]" @click="navMobileOpen = false">Discover Creators</router-link>
              </template>
              <template v-else-if="user.role === 'admin'">
                <router-link to="/admin" class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]" @click="navMobileOpen = false">Admin Panel</router-link>
              </template>
              <template v-else-if="user.role === 'agency'">
                <router-link to="/agency/dashboard" class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#7c3aed]/5 hover:text-[#7c3aed]" @click="navMobileOpen = false">Agency Dashboard</router-link>
              </template>
              <button type="button" class="rounded-lg px-4 py-3 text-left text-sm text-red-600 transition hover:bg-red-50" @click="logout(); navMobileOpen = false">Logout</button>
            </template>
            <template v-else>
              <div class="mt-2 flex flex-col gap-2 border-t border-[#e5e7eb] pt-3">
                <router-link to="/login" class="rounded-lg border border-[#e5e7eb] px-4 py-3 text-center text-sm font-medium transition hover:border-[#e63946] hover:text-[#e63946]" @click="navMobileOpen = false">Login</router-link>
                <router-link to="/creator-landing" class="rounded-lg border border-[#10b981] px-4 py-3 text-center text-sm font-medium text-[#10b981] transition hover:bg-[#10b981]/5" @click="navMobileOpen = false">Join as Creator</router-link>
                <router-link to="/brand-landing" class="rounded-lg bg-[#e63946] px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-[#c1121f]" @click="navMobileOpen = false">Join as Brand</router-link>
              </div>
            </template>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();

const servicesColumn1 = [
  { name: 'Video Production', slug: 'video-production' },
  { name: 'Podcast & Interview', slug: 'podcast-interview' },
  { name: 'Professional Model Portfolio', slug: 'professional-model-portfolio' },
  { name: 'Live Streaming', slug: 'live-streaming' },
  { name: 'Advertisement', slug: 'advertisement' },
];
const servicesColumn2 = [
  { name: 'Professional Photography', slug: 'professional-photography' },
  { name: 'Video Editing', slug: 'video-editing' },
  { name: 'Studio for Rentals', slug: 'studio-for-rentals' },
  { name: 'Kid Portfolio Shoot', slug: 'kid-portfolio-shoot' },
];

const user = ref(null);
// On login/register pages always show logged-out UI so logout redirect looks correct
const displayUser = computed(() => {
  if (route.path === '/login' || route.path === '/register') return null;
  return user.value;
});
const userMenuOpen = ref(false);
const userMenuRef = ref(null);
const navMobileOpen = ref(false);
const headerRef = ref(null);
const servicesDropdownOpen = ref(false);
const servicesRef = ref(null);
const services = ref([]);
const servicesLoading = ref(false);
const servicesDropdownImage = ref('');

function logout() {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  axios.post('/api/logout', {}, { withCredentials: true, headers: token ? { 'X-CSRF-TOKEN': token } : {} })
    .then(() => { user.value = null; window.location.href = '/login'; })
    .catch(() => { user.value = null; window.location.href = '/login'; });
}

function onClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) userMenuOpen.value = false;
  if (servicesRef.value && !servicesRef.value.contains(e.target)) servicesDropdownOpen.value = false;
  if (navMobileOpen.value && headerRef.value && !headerRef.value.contains(e.target)) navMobileOpen.value = false;
}

onMounted(() => {
  axios.get('/api/me', { withCredentials: true }).then((r) => { user.value = r.data; }).catch(() => { user.value = null; });
  servicesLoading.value = true;
  axios.get('/api/services').then((r) => {
    services.value = r.data || [];
    const first = (r.data || [])[0];
    if (first && first.image) servicesDropdownImage.value = first.image;
  }).catch(() => { services.value = []; }).finally(() => { servicesLoading.value = false; });
  document.addEventListener('click', onClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', onClickOutside);
});
</script>

<style scoped>
.nav-bar {
  z-index: 1;
  position: relative;
  width: 100%;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}
.nav-bar-bg {
  background: linear-gradient(
    135deg,
    rgba(230, 229, 229, 0.55) 0%,
    rgba(255, 255, 255, 0.25) 50%,
    rgba(230, 229, 229, 0.4) 100%
  );
  backdrop-filter: blur(8px);
}
</style>
