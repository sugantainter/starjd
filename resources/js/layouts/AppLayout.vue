<template>
  <div class="min-h-screen bg-[#fafaf9] text-[#1a1a1a]">
    <header ref="headerRef" class="sticky top-0 z-50 border-b border-[#e5e7eb] bg-white/90 backdrop-blur-md">
      <nav class="relative mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:py-4 md:px-6">
        <router-link to="/" class="flex items-center" @click="navMobileOpen = false">
            <img src="/logo.png" alt="StarJD" class="h-11 w-auto object-contain sm:h-12" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-xl font-bold tracking-tight text-[#1a1a1a]">StarJD</span>
        </router-link>

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
          <router-link to="/" class="text-sm text-[#6b7280] transition hover:text-[#e63946]">Home</router-link>
          <router-link to="/about" class="text-sm text-[#6b7280] transition hover:text-[#e63946]">About</router-link>
          <router-link to="/contact" class="text-sm text-[#6b7280] transition hover:text-[#e63946]">Contact</router-link>
          <router-link to="/blog" class="text-sm text-[#6b7280] transition hover:text-[#e63946]">Blog</router-link>
          <router-link to="/services" class="text-sm text-[#6b7280] transition hover:text-[#e63946]">Services</router-link>
          <router-link to="/videos" class="text-sm text-[#6b7280] transition hover:text-[#e63946]">Videos</router-link>
          <router-link to="/creators" class="text-sm text-[#6b7280] transition hover:text-[#e63946]">Discover Creators</router-link>

          <template v-if="user">
            <div class="relative" ref="userMenuRef">
              <button
                type="button"
                class="flex items-center gap-2 rounded-lg border border-[#e5e7eb] bg-white px-4 py-2 text-sm font-medium text-[#1a1a1a] shadow-sm transition hover:border-[#e63946] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                :class="user.role === 'creator' ? 'hover:border-[#10b981] focus:ring-[#10b981]/20' : user.role === 'admin' ? 'hover:border-[#1e293b] focus:ring-[#1e293b]/20' : ''"
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
                  <template v-if="user.role === 'creator'">
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
              <template v-if="user">
                <div class="my-2 border-t border-[#e5e7eb] pt-2">
                  <p class="px-4 py-1 text-xs font-medium uppercase text-[#64748b]">{{ user.role }}</p>
                  <p class="truncate px-4 py-1 text-sm font-medium text-[#1a1a1a]">{{ user.name }}</p>
                </div>
                <template v-if="user.role === 'creator'">
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

    <main>
      <router-view />
    </main>

    <footer class="border-t border-[#e5e7eb] bg-white px-4 py-12">
      <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-10 md:flex-row md:items-start md:justify-between">
          <div>
            <img src="/logo.png" alt="StarJD" class="h-11 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-xl font-bold text-[#1a1a1a]">StarJD</span>
            <p class="mt-2 max-w-xs text-sm text-[#6b7280]">Connect with creators. Build your brand.</p>
          </div>
          <div class="flex flex-wrap gap-8">
            <div>
              <h4 class="font-semibold text-[#1a1a1a]">Resources</h4>
              <ul class="mt-2 space-y-2 text-sm text-[#6b7280]">
                <li><router-link to="/#faq" class="transition hover:text-[#e63946]">FAQ</router-link></li>
                <li><router-link to="/blog" class="transition hover:text-[#e63946]">Blog</router-link></li>
                <li><router-link to="/services" class="transition hover:text-[#e63946]">Services</router-link></li>
                <li><router-link to="/about" class="transition hover:text-[#e63946]">About</router-link></li>
                <li><router-link to="/contact" class="transition hover:text-[#e63946]">Contact</router-link></li>
              </ul>
            </div>
            <div>
              <h4 class="font-semibold text-[#1a1a1a]">Join</h4>
              <ul class="mt-2 space-y-2 text-sm text-[#6b7280]">
                <li><router-link to="/creator-landing" class="transition hover:text-[#10b981]">Join as Creator</router-link></li>
                <li><router-link to="/brand-landing" class="transition hover:text-[#e63946]">Join as Brand</router-link></li>
              </ul>
            </div>
            <div>
              <h4 class="font-semibold text-[#1a1a1a]">Legal</h4>
              <ul class="mt-2 space-y-2 text-sm text-[#6b7280]">
                <li><router-link to="/privacy" class="transition hover:text-[#e63946]">Privacy</router-link></li>
                <li><router-link to="/terms" class="transition hover:text-[#e63946]">Terms</router-link></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="mt-10 border-t border-[#e5e7eb] pt-8 text-center text-sm text-[#6b7280]">© {{ new Date().getFullYear() }} StarJD.</div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const user = ref(null);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);
const navMobileOpen = ref(false);
const headerRef = ref(null);
function logout() {
  axios.post('/api/logout', {}, { withCredentials: true }).catch(() => {}).finally(() => {
    user.value = null;
    window.location.href = '/';
  });
}

function onClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) userMenuOpen.value = false;
  if (navMobileOpen.value && headerRef.value && !headerRef.value.contains(e.target)) navMobileOpen.value = false;
}

onMounted(() => {
  axios.get('/api/me', { withCredentials: true }).then((r) => { user.value = r.data; }).catch(() => { user.value = null; });
  document.addEventListener('click', onClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', onClickOutside);
});
</script>
