<template>
  <div class="min-h-screen bg-[#fafaf9] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <h1 class="text-2xl font-bold text-[#1a1a1a] text-center">Set new password</h1>
      <p class="mt-2 text-center text-[#6b7280]">Enter your new password below.</p>

      <div class="mt-8 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
        <div v-if="success" class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">
          {{ success }}
          <router-link to="/login" class="mt-2 block font-medium text-[#e63946] hover:underline">Sign in</router-link>
        </div>
        <template v-else>
          <div v-if="error" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Email</label>
              <input
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                placeholder="you@example.com"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">New password</label>
              <input
                v-model="form.password"
                type="password"
                required
                minlength="8"
                autocomplete="new-password"
                class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                placeholder="••••••••"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Confirm password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                required
                minlength="8"
                autocomplete="new-password"
                class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                placeholder="••••••••"
              />
            </div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full cursor-pointer rounded-xl bg-[#e63946] py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50"
            >
              {{ loading ? 'Resetting…' : 'Reset password' }}
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
import { ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const form = reactive({
  email: '',
  password: '',
  password_confirmation: '',
  token: '',
});
const loading = ref(false);
const error = ref('');
const success = ref('');

onMounted(() => {
  form.token = route.query.token || '';
  form.email = route.query.email || '';
});

async function submit() {
  loading.value = true;
  error.value = '';
  success.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    await axios.post('/api/reset-password', { ...form }, {
      headers: token ? { 'X-CSRF-TOKEN': token } : {},
      withCredentials: true,
    });
    success.value = 'Password has been reset. You can now sign in.';
  } catch (e) {
    error.value = e.response?.data?.message || (e.response?.data?.errors?.email?.[0]) || 'Reset failed. The link may have expired.';
  } finally {
    loading.value = false;
  }
}
</script>
