<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(false);
const error = ref('');
const success = ref('');

const email = ref('');
const reason = ref('');
const confirmText = ref('');

onMounted(async () => {
  try {
    const res = await axios.get('/api/me', { withCredentials: true });
    email.value = res.data?.email ?? '';
  } catch (e) {
    // If not authenticated, redirect to login
    window.location.href = '/login';
  }
});

async function submitDelete() {
  error.value = '';
  success.value = '';

  if (confirmText.value.toLowerCase().trim() !== 'delete my account') {
    error.value = 'Please type "delete my account" to confirm.';
    return;
  }

  if (!window.confirm('This will permanently delete your account and data. Are you sure?')) {
    return;
  }

  loading.value = true;
  try {
    const headers = {};
    if (reason.value) {
      headers['X-Delete-Reason'] = reason.value.slice(0, 500);
    }
    if (email.value) {
      headers['X-Delete-Email'] = email.value;
    }

    await axios.delete('/api/account', {
      withCredentials: true,
      headers,
    });
    success.value = 'Your account has been deleted. You will be logged out.';
    // Small delay so user sees the message, then reload to clear state
    setTimeout(() => {
      window.location.href = '/';
    }, 1500);
  } catch (e) {
    console.error(e);
    error.value =
      e.response?.data?.message ||
      'Unable to delete your account right now. Please try again.';
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-slate-900 mb-4">
      Delete your account
    </h1>
    <p class="text-sm text-slate-600 mb-6">
      Deleting your account is permanent. Your profile, campaigns, bookings, and
      other associated data may be removed and you won’t be able to recover it.
      If you just want to stop emails or notifications, you can change your
      settings instead.
    </p>

    <div class="space-y-4 bg-slate-50 border border-slate-200 rounded-lg p-4">
      <div v-if="email">
        <label class="block text-sm font-medium text-slate-700 mb-1">
          Account email
        </label>
        <input
          :value="email"
          type="email"
          class="mt-1 block w-full rounded-md border-slate-200 bg-slate-100 text-slate-700 shadow-sm text-sm cursor-not-allowed"
          readonly
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          Why are you leaving? (optional)
        </label>
        <textarea
          v-model="reason"
          rows="3"
          class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
          placeholder="Your feedback helps us improve StarJD."
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          Type <span class="font-mono">'delete my account'</span> to confirm
        </label>
        <input
          v-model="confirmText"
          type="text"
          class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
          placeholder="delete my account"
        />
      </div>

      <p class="text-xs text-red-600">
        This action cannot be undone. You will be logged out immediately.
      </p>

      <div class="pt-2 flex items-center gap-3">
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-60"
          :disabled="loading"
          @click="submitDelete"
        >
          <span v-if="loading">Deleting…</span>
          <span v-else>Delete my account</span>
        </button>
        <a href="/" class="text-sm text-slate-500 hover:text-slate-700">
          Cancel and go back
        </a>
      </div>

      <p v-if="error" class="text-sm text-red-600 mt-2">
        {{ error }}
      </p>
      <p v-if="success" class="text-sm text-emerald-600 mt-2">
        {{ success }}
      </p>
    </div>
  </div>
</template>

