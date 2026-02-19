<template>
  <div class="min-h-screen bg-[#fafaf9] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <h1 class="text-2xl font-bold text-[#1a1a1a] text-center">Login</h1>
      <p class="mt-2 text-center text-[#6b7280]">Sign in as Creator, Brand, or Admin</p>

      <form @submit.prevent="submit" class="mt-8 space-y-4 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
        <div v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Email</label>
          <input v-model="form.email" type="email" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="you@example.com" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Password</label>
          <input v-model="form.password" type="password" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="••••••••" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">I am a</label>
          <select v-model="form.role" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
            <option value="creator">Creator</option>
            <option value="brand">Brand</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" :disabled="loading" class="cursor-link w-full rounded-xl bg-[#e63946] py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50">Login</button>
      </form>
      <p class="mt-6 text-center text-sm text-[#6b7280]">Don't have an account? <router-link to="/register" class="font-medium text-[#e63946] hover:underline">Register</router-link></p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const form = reactive({ email: '', password: '', role: 'creator' });
const loading = ref(false);
const error = ref('');

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await axios.post('/api/login', form, { headers: token ? { 'X-CSRF-TOKEN': token } : {}, withCredentials: true });
    const redirect = res.data?.redirect || '/';
    window.location.href = redirect;
  } catch (e) {
    error.value = e.response?.data?.message || 'Invalid email or password.';
  } finally {
    loading.value = false;
  }
}
</script>
