<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Agency Dashboard</h1>
    <p class="mt-1 text-[#64748b]">Welcome back, {{ data?.user?.name }}.</p>
    <div class="mt-6 rounded-xl border border-[#e2e8f0] bg-white p-6">
      <p class="text-[#64748b]">{{ data?.message ?? 'Your agency account is under review. You will be notified once approved.' }}</p>
      <div v-if="data?.user" class="mt-4 pt-4 border-t border-[#e2e8f0]">
        <p class="text-sm text-[#64748b]">Logged in as <span class="font-medium text-[#1a1a1a]">{{ data.user.email }}</span></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const data = ref(null);

onMounted(async () => {
  try {
    const res = await axios.get('/api/agency/dashboard', { withCredentials: true });
    data.value = res.data;
  } catch (_) {
    data.value = { message: 'Unable to load dashboard.' };
  }
});
</script>
