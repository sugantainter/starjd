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
      </form>
      <p class="mt-6 text-center text-sm text-[#6b7280]">Already have an account? <router-link to="/login" class="font-medium text-[#e63946] hover:underline">Login</router-link></p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const form = reactive({ name: '', email: '', password: '', password_confirmation: '', role: 'creator' });
const loading = ref(false);
const error = ref('');

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
