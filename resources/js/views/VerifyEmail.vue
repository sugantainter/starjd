<template>
  <div class="min-h-screen bg-[#fafaf9] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <h1 class="text-2xl font-bold text-[#1a1a1a] text-center">Verify your email</h1>
      <p class="mt-2 text-center text-[#6b7280]">
        We sent a 6-digit code to <strong class="text-[#1a1a1a]">{{ email }}</strong>. Enter it below.
      </p>

      <div class="mt-6 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
        <form @submit.prevent="submit" class="space-y-4">
          <div v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
          <div v-if="success" class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ success }}</div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Email</label>
            <input
              v-model="email"
              type="email"
              required
              class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
              placeholder="you@example.com"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Verification code</label>
            <input
              v-model="otp"
              type="text"
              inputmode="numeric"
              maxlength="6"
              autocomplete="one-time-code"
              class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-center text-lg tracking-[0.4em] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
              placeholder="000000"
              @input="otp = otp.replace(/\D/g, '').slice(0, 6)"
            />
            <p class="mt-1 text-xs text-[#64748b]">Code is valid for 10 minutes.</p>
          </div>

          <button
            type="submit"
            :disabled="loading || otp.length !== 6"
            class="w-full cursor-pointer rounded-xl bg-[#e63946] py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50"
          >
            {{ loading ? 'Verifying…' : 'Verify & continue' }}
          </button>

          <p class="text-center text-sm text-[#64748b]">
            Didn't receive the code?
            <button
              type="button"
              :disabled="resendCooldown > 0 || resending"
              class="font-medium text-[#e63946] hover:underline disabled:opacity-50 disabled:no-underline"
              @click="resendOtp"
            >
              {{ resendLabel }}
            </button>
          </p>
        </form>
      </div>

      <p class="mt-6 text-center text-sm text-[#6b7280]">
        <router-link to="/login" class="font-medium text-[#e63946] hover:underline">Back to login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const email = ref('');
const otp = ref('');
const loading = ref(false);
const resending = ref(false);
const resendCooldown = ref(0);
const error = ref('');
const success = ref('');

const resendLabel = computed(() => {
  if (resendCooldown.value > 0) return 'Resend in ' + resendCooldown.value + 's';
  if (resending.value) return 'Sending…';
  return 'Resend code';
});

onMounted(() => {
  const q = route.query.email;
  if (q && typeof q === 'string') email.value = q;
});

async function submit() {
  loading.value = true;
  error.value = '';
  success.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await axios.post('/api/verify-email-otp', { email: email.value, otp: otp.value }, {
      headers: token ? { 'X-CSRF-TOKEN': token } : {},
      withCredentials: true,
    });
    const redirect = res.data && res.data.redirect;
    if (redirect) {
      const path = redirect.startsWith('http') ? new URL(redirect).pathname : (redirect.startsWith('/') ? redirect : '/' + redirect);
      window.location.href = path;
    } else {
      window.location.href = '/';
    }
  } catch (e) {
    const d = e.response && e.response.data;
    error.value = (d && d.message) || (d && d.errors && d.errors.otp && d.errors.otp[0]) || 'Invalid or expired code. Please try again or resend.';
  } finally {
    loading.value = false;
  }
}

async function resendOtp() {
  if (resendCooldown.value > 0 || resending.value || !email.value) return;
  resending.value = true;
  error.value = '';
  success.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    await axios.post('/api/resend-otp', { email: email.value }, {
      headers: token ? { 'X-CSRF-TOKEN': token } : {},
      withCredentials: true,
    });
    success.value = 'A new code has been sent to your email.';
    resendCooldown.value = 60;
    const t = setInterval(() => {
      resendCooldown.value -= 1;
      if (resendCooldown.value <= 0) clearInterval(t);
    }, 1000);
  } catch (e) {
    error.value = (e.response && e.response.data && e.response.data.message) || 'Could not resend code.';
  } finally {
    resending.value = false;
  }
}
</script>
