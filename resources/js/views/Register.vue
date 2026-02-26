<template>
  <div class="min-h-screen bg-[#fafaf9] py-12 px-4 flex items-center justify-center">
    <div class="w-full max-w-lg">
      <h1 class="text-2xl font-bold text-[#1a1a1a] text-center">Create your account</h1>
      <p class="mt-2 text-center text-[#6b7280]">Join StarJD — choose how you want to use the platform.</p>

      <!-- Role selection (checkboxes - single choice) -->
      <div class="mt-6">
        <p class="mb-3 text-sm font-medium text-[#1a1a1a]">I want to join as</p>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
          <label
            v-for="opt in roleOptions"
            :key="opt.value"
            class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
            :class="form.role === opt.value ? 'border-[#e63946] bg-[#e63946]/5' : 'border-[#e5e7eb] bg-white hover:border-[#e63946]/50'"
          >
            <input
              v-model="form.role"
              type="radio"
              :value="opt.value"
              name="role"
              class="h-4 w-4 border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]"
            />
            <span class="font-medium text-[#1a1a1a]">{{ opt.label }}</span>
          </label>
        </div>
      </div>

      <!-- Social login first -->
      <div class="mt-6 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
        <p class="mb-4 text-center text-sm font-medium text-[#64748b]">Continue with</p>
        <div class="grid grid-cols-2 gap-3">
          <a
            :href="googleRegisterUrl"
            class="flex items-center justify-center gap-2 rounded-xl border border-[#e5e7eb] bg-white py-3.5 font-medium text-[#1a1a1a] shadow-sm transition hover:border-[#e63946]/30 hover:bg-[#fafafa]"
          >
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google
          </a>
          <a
            :href="facebookRegisterUrl"
            class="flex items-center justify-center gap-2 rounded-xl border border-[#e5e7eb] bg-[#1877F2] py-3.5 font-medium text-white shadow-sm transition hover:bg-[#166fe5]"
          >
            <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook
          </a>
        </div>

        <div class="relative my-5">
          <span class="absolute inset-0 flex items-center"><span class="w-full border-t border-[#e5e7eb]"></span></span>
          <span class="relative flex justify-center bg-white px-2 text-xs uppercase tracking-wider text-[#9ca3af]">or sign up with email</span>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <div v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label>
            <input v-model="form.name" type="text" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="Your name" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Email</label>
            <input v-model="form.email" type="email" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="you@example.com" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Password</label>
            <input v-model="form.password" type="password" required minlength="8" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="••••••••" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Confirm password</label>
            <input v-model="form.password_confirmation" type="password" required minlength="8" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="••••••••" />
          </div>
          <button type="submit" :disabled="loading" class="w-full cursor-pointer rounded-xl bg-[#e63946] py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50">Create account</button>
        </form>
      </div>

      <p class="mt-6 text-center text-sm text-[#6b7280]">Already have an account? <router-link to="/login" class="font-medium text-[#e63946] hover:underline">Login</router-link></p>

      <!-- Role descriptions below -->
      <section class="mt-10 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1a1a1a] mb-4">What each role is for</h2>
        <div class="space-y-4">
          <div class="flex gap-4 rounded-xl border border-[#f1f5f9] bg-[#fafafa] p-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#e63946]/10 text-[#e63946]">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
              <h3 class="font-semibold text-[#1a1a1a]">Creator</h3>
              <p class="mt-1 text-sm text-[#64748b]">Content creators & influencers. Create a profile, set your packages, get discovered by brands, and earn from collaborations.</p>
            </div>
          </div>
          <div class="flex gap-4 rounded-xl border border-[#f1f5f9] bg-[#fafafa] p-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#0ea5e9]/10 text-[#0ea5e9]">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
              <h3 class="font-semibold text-[#1a1a1a]">Brand</h3>
              <p class="mt-1 text-sm text-[#64748b]">Businesses & marketers. Discover creators, run campaigns, and collaborate on content that fits your brand.</p>
            </div>
          </div>
          <div class="flex gap-4 rounded-xl border border-[#f1f5f9] bg-[#fafafa] p-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#8b5cf6]/10 text-[#8b5cf6]">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
              <h3 class="font-semibold text-[#1a1a1a]">Studio Owner</h3>
              <p class="mt-1 text-sm text-[#64748b]">List your space — photography, film, podcast, music or event studios. Set availability, pricing, and earn when creators & brands book.</p>
            </div>
          </div>
          <div class="flex gap-4 rounded-xl border border-[#f1f5f9] bg-[#fafafa] p-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#10b981]/10 text-[#10b981]">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <div>
              <h3 class="font-semibold text-[#1a1a1a]">Customer</h3>
              <p class="mt-1 text-sm text-[#64748b]">Explore and book studios. Browse spaces by category, location & price — then book and pay securely. No brand or creator setup needed.</p>
            </div>
          </div>
        </div>
      </section>

      <p class="mt-6 text-center text-xs text-[#94a3b8]">StarJD — powered by Suganta International</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const form = reactive({ name: '', email: '', password: '', password_confirmation: '', role: 'creator' });
const loading = ref(false);
const error = ref('');

const roleOptions = [
  { value: 'creator', label: 'Creator' },
  { value: 'brand', label: 'Brand' },
  { value: 'studio_owner', label: 'Studio Owner' },
  { value: 'customer', label: 'Customer' },
];

const googleRegisterUrl = computed(() => {
  const base = `${window.location.origin}/auth/google/redirect`;
  return `${base}?role=${encodeURIComponent(form.role)}`;
});

const facebookRegisterUrl = computed(() => {
  const base = `${window.location.origin}/auth/facebook/redirect`;
  return `${base}?role=${encodeURIComponent(form.role)}`;
});

onMounted(() => {
  const type = route.query.type;
  if (['brand', 'creator', 'studio_owner', 'customer'].includes(type)) form.role = type;
});

function registerEndpoint() {
  if (form.role === 'studio_owner') return '/api/register/studio-owner';
  if (form.role === 'customer') return '/api/register/customer';
  return '/api/register';
}

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await axios.post(registerEndpoint(), form, { headers: token ? { 'X-CSRF-TOKEN': token } : {}, withCredentials: true });
    const data = res.data;
    if (data && data.needs_verification && data.email) {
      window.location.href = '/verify-email?email=' + encodeURIComponent(data.email);
      return;
    }
    const raw = data?.redirect || '/';
    const path = raw.startsWith('http') ? new URL(raw).pathname : (raw.startsWith('/') ? raw : `/${raw}`);
    window.location.href = path;
  } catch (e) {
    error.value = e.response?.data?.message || (e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(' ') : 'Registration failed.');
  } finally {
    loading.value = false;
  }
}
</script>
