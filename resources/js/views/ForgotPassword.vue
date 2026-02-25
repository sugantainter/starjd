<template>
  <div class="min-h-screen bg-[#fafaf9] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <h1 class="text-2xl font-bold text-[#1a1a1a] text-center">Forgot password?</h1>
      <p class="mt-2 text-center text-[#6b7280]">Enter your email and we’ll send you a link to reset your password.</p>

      <div class="mt-8 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
        <div v-if="success" class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ success }}
        </div>
        <template v-else>
          <div v-if="error" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Email</label>
              <input
                v-model="email"
                type="email"
                required
                autocomplete="email"
                class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                placeholder="you@example.com"
              />
            </div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full cursor-pointer rounded-xl bg-[#e63946] py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50"
            >
              {{ loading ? 'Sending…' : 'Send reset link' }}
            </button>
          </form>
        </template>
      </div>

      <p class="mt-6 text-center text-sm text-[#6b7280]">
        <router-link to="/login" class="font-medium text-[#e63946] hover:underline">Back to sign in</router-link>
      </p>
      <p class="mt-4 text-center text-xs text-[#94a3b8]">StarJD — powered by Suganta International</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const email = ref('');
const loading = ref(false);
const error = ref('');
const success = ref('');

async function submit() {
  loading.value = true;
  error.value = '';
  success.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await axios.post('/api/forgot-password', { email: email.value }, {
      headers: token ? { 'X-CSRF-TOKEN': token } : {},
      withCredentials: true,
    });
    success.value = res.data?.message || 'Check your email for the reset link.';
  } catch (e) {
    error.value = e.response?.data?.message || (e.response?.data?.errors?.email?.[0]) || 'Something went wrong. Try again.';
  } finally {
    loading.value = false;
  }
}
</script>
