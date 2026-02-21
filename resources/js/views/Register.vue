<template>
  <div class="min-h-screen bg-[#fafaf9] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <h1 class="text-2xl font-bold text-[#1a1a1a] text-center">Register</h1>
      <p class="mt-2 text-center text-[#6b7280]">Join as Creator or Brand</p>

      <form @submit.prevent="submit" class="mt-8 space-y-4 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
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
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" required minlength="8" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="••••••••" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">I am a</label>
          <select v-model="form.role" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
            <option value="creator">Creator</option>
            <option value="brand">Brand</option>
          </select>
        </div>
        <button type="submit" :disabled="loading" class="cursor-link w-full rounded-xl bg-[#e63946] py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50">Create account</button>
        <div class="relative my-4">
          <span class="absolute inset-0 flex items-center"><span class="w-full border-t border-[#e5e7eb]"></span></span>
          <span class="relative flex justify-center text-xs uppercase tracking-wider text-[#9ca3af]">or register with</span>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <a
            :href="googleRegisterUrl"
            class="flex items-center justify-center gap-2 rounded-xl border border-[#e5e7eb] bg-white py-3 font-medium text-[#1a1a1a] transition hover:bg-[#f9fafb]"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            Google
          </a>
          <a
            :href="facebookRegisterUrl"
            class="flex items-center justify-center gap-2 rounded-xl border border-[#e5e7eb] bg-[#1877F2] py-3 font-medium text-white transition hover:bg-[#166fe5]"
          >
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
          </a>
        </div>
      </form>
      <p class="mt-6 text-center text-sm text-[#6b7280]">Already have an account? <router-link to="/login" class="font-medium text-[#e63946] hover:underline">Login</router-link></p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const form = reactive({ name: '', email: '', password: '', password_confirmation: '', role: 'creator' });
const loading = ref(false);
const error = ref('');

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
  if (type === 'brand' || type === 'creator') form.role = type;
});

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    await axios.post('/api/register', form, { headers: token ? { 'X-CSRF-TOKEN': token } : {}, withCredentials: true });
    router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message || (e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(' ') : 'Registration failed.');
  } finally {
    loading.value = false;
  }
}
</script>
