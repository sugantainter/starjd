<template>
  <div
    class="min-h-screen bg-[#fafaf9] text-[#1a1a1a]"
    :class="{ 'cursor-none': cursorVisible }"
  >
    <!-- Custom cursor (desktop only) -->
    <div
      v-show="cursorVisible"
      class="custom-cursor pointer-events-none fixed left-0 top-0 z-[9999]"
    >
      <div
        class="cursor-dot absolute h-2 w-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#e63946]"
        :style="{ left: `${mouse.x}px`, top: `${mouse.y}px` }"
      />
      <div
        class="cursor-ring absolute h-8 w-8 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-[#e63946]/60 transition-all duration-150"
        :class="{ 'cursor-ring-hover': cursorHover }"
        :style="{ left: `${ring.x}px`, top: `${ring.y}px` }"
      />
    </div>

    <header class="sticky top-0 z-50 border-b border-[#e5e7eb] bg-white/90 backdrop-blur-md">
      <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 md:px-6">
        <router-link to="/" class="flex items-center">
            <img src="/logo.jpeg" alt="StarJD" class="h-8 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-xl font-bold tracking-tight text-[#1a1a1a]">StarJD</span>
        </router-link>
        <div class="hidden items-center gap-3 md:flex">
          <router-link to="/" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">Home</router-link>
          <router-link to="/about" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">About</router-link>
          <router-link to="/contact" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">Contact</router-link>
          <router-link to="/#how-it-works" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">How It Works</router-link>
          <router-link to="/#featured" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">Featured</router-link>
          <router-link to="/blog" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">Blog</router-link>
          <router-link to="/services" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">Services</router-link>
          <router-link to="/videos" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">Videos</router-link>
          <router-link to="/creators" class="cursor-link text-sm text-[#6b7280] transition hover:text-[#e63946]">Discover Creators</router-link>

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
            <router-link to="/login" class="cursor-link rounded-lg border border-[#e5e7eb] px-4 py-2 text-sm font-medium transition hover:border-[#e63946] hover:text-[#e63946]">Login</router-link>
            <router-link to="/creator-landing" class="cursor-link join-creator rounded-lg border border-[#e5e7eb] px-4 py-2 text-sm font-medium transition hover:border-[#10b981] hover:bg-[#10b981]/5 hover:text-[#10b981]">Join as Creator</router-link>
            <router-link to="/brand-landing" class="cursor-link join-brand rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#c1121f]">Join as Brand</router-link>
          </template>
        </div>
      </nav>
    </header>

    <main>
      <router-view />
    </main>

    <footer class="border-t border-[#e5e7eb] bg-white px-4 py-12">
      <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-10 md:flex-row md:items-start md:justify-between">
          <div>
            <img src="/logo.jpeg" alt="StarJD" class="h-8 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-xl font-bold text-[#1a1a1a]">StarJD</span>
            <p class="mt-2 max-w-xs text-sm text-[#6b7280]">Connect with creators. Build your brand.</p>
          </div>
          <div class="flex flex-wrap gap-8">
            <div>
              <h4 class="font-semibold text-[#1a1a1a]">Resources</h4>
              <ul class="mt-2 space-y-2 text-sm text-[#6b7280]">
                <li><router-link to="/#how-it-works" class="cursor-link transition hover:text-[#e63946]">How It Works</router-link></li>
                <li><router-link to="/#featured" class="cursor-link transition hover:text-[#e63946]">Featured Creators</router-link></li>
                <li><router-link to="/#faq" class="cursor-link transition hover:text-[#e63946]">FAQ</router-link></li>
                <li><router-link to="/blog" class="cursor-link transition hover:text-[#e63946]">Blog</router-link></li>
                <li><router-link to="/services" class="cursor-link transition hover:text-[#e63946]">Services</router-link></li>
                <li><router-link to="/about" class="cursor-link transition hover:text-[#e63946]">About</router-link></li>
                <li><router-link to="/contact" class="cursor-link transition hover:text-[#e63946]">Contact</router-link></li>
              </ul>
            </div>
            <div>
              <h4 class="font-semibold text-[#1a1a1a]">Join</h4>
              <ul class="mt-2 space-y-2 text-sm text-[#6b7280]">
                <li><router-link to="/creator-landing" class="cursor-link transition hover:text-[#10b981]">Join as Creator</router-link></li>
                <li><router-link to="/brand-landing" class="cursor-link transition hover:text-[#e63946]">Join as Brand</router-link></li>
              </ul>
            </div>
            <div>
              <h4 class="font-semibold text-[#1a1a1a]">Legal</h4>
              <ul class="mt-2 space-y-2 text-sm text-[#6b7280]">
                <li><a href="#" class="cursor-link transition hover:text-[#e63946]">Privacy</a></li>
                <li><a href="#" class="cursor-link transition hover:text-[#e63946]">Terms</a></li>
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
const cursorVisible = ref(false);
const cursorHover = ref(false);
const mouse = ref({ x: 0, y: 0 });
const ring = ref({ x: 0, y: 0 });
let raf = null;

function onMouseMove(e) {
  mouse.value = { x: e.clientX, y: e.clientY };
  if (!cursorVisible.value) cursorVisible.value = true;
  if (!raf) raf = requestAnimationFrame(updateRing);
}

function updateRing() {
  raf = null;
  const dx = mouse.value.x - ring.value.x;
  const dy = mouse.value.y - ring.value.y;
  ring.value = { x: ring.value.x + dx * 0.15, y: ring.value.y + dy * 0.15 };
  if (Math.abs(dx) > 0.5 || Math.abs(dy) > 0.5) raf = requestAnimationFrame(updateRing);
}

function onMouseEnter() { cursorHover.value = true; }
function onMouseLeave() { cursorHover.value = false; }

function logout() {
  axios.post('/api/logout', {}, { withCredentials: true }).catch(() => {}).finally(() => {
    user.value = null;
    window.location.href = '/';
  });
}

function onClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) userMenuOpen.value = false;
}

onMounted(() => {
  axios.get('/api/me', { withCredentials: true }).then((r) => { user.value = r.data; }).catch(() => { user.value = null; });
  document.addEventListener('click', onClickOutside);
  if (typeof window === 'undefined') return;
  if (window.matchMedia('(pointer: fine)').matches) {
    window.addEventListener('mousemove', onMouseMove);
    document.querySelectorAll('.cursor-link, a, button').forEach((el) => {
      el.addEventListener('mouseenter', onMouseEnter);
      el.addEventListener('mouseleave', onMouseLeave);
    });
  }
});

onUnmounted(() => {
  document.removeEventListener('click', onClickOutside);
  if (typeof window === 'undefined') return;
  window.removeEventListener('mousemove', onMouseMove);
  document.querySelectorAll('.cursor-link, a, button').forEach((el) => {
    el.removeEventListener('mouseenter', onMouseEnter);
    el.removeEventListener('mouseleave', onMouseLeave);
  });
  if (raf) cancelAnimationFrame(raf);
});
</script>
