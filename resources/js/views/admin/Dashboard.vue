<template>
  <div>
    <h1 class="mb-6 text-2xl font-bold text-[#1a1a1a]">Dashboard</h1>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm">
        <p class="text-sm text-[#64748b]">Categories</p>
        <p class="text-2xl font-bold text-[#1a1a1a]">{{ stats.categories ?? 0 }}</p>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm">
        <p class="text-sm text-[#64748b]">Testimonials</p>
        <p class="text-2xl font-bold text-[#1a1a1a]">{{ stats.testimonials ?? 0 }}</p>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm">
        <p class="text-sm text-[#64748b]">FAQs</p>
        <p class="text-2xl font-bold text-[#1a1a1a]">{{ stats.faqs ?? 0 }}</p>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm">
        <p class="text-sm text-[#64748b]">Steps</p>
        <p class="text-2xl font-bold text-[#1a1a1a]">{{ stats.steps ?? 0 }}</p>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm">
        <p class="text-sm text-[#64748b]">Contact Messages</p>
        <p class="text-2xl font-bold text-[#1a1a1a]">{{ stats.contacts ?? 0 }}</p>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm">
        <p class="text-sm text-[#64748b]">Blog Posts</p>
        <p class="text-2xl font-bold text-[#1a1a1a]">{{ stats.posts ?? 0 }}</p>
        <p class="text-xs text-[#64748b]">{{ stats.posts_published ?? 0 }} published</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({});
const loading = ref(true);

onMounted(async () => {
  try {
    const r = await axios.get('/api/admin/dashboard');
    stats.value = r.data;
  } finally {
    loading.value = false;
  }
});
</script>
