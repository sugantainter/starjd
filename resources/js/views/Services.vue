<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-4xl">
        <h1 class="text-3xl font-bold text-[#1a1a1a] md:text-4xl">Our Services</h1>
        <p class="mt-4 text-lg text-[#6b7280]">Explore what we offer. Select a service to learn more.</p>
        <div v-if="loading" class="mt-12 text-center text-[#6b7280]">Loading…</div>
        <div v-else-if="!services.length" class="mt-12 rounded-2xl border border-[#e5e7eb] bg-white p-12 text-center text-[#6b7280]">No services yet.</div>
        <div v-else class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <router-link
            v-for="s in services"
            :key="s.id"
            :to="'/services/' + s.slug"
            class="group block overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm transition hover:border-[#e63946]/40 hover:shadow-xl"
          >
            <h2 class="text-lg font-bold text-[#1a1a1a] transition group-hover:text-[#e63946]">{{ s.name }}</h2>
            <p v-if="s.short_description" class="mt-2 line-clamp-2 text-sm text-[#6b7280]">{{ s.short_description }}</p>
            <span class="mt-3 inline-block text-sm font-medium text-[#e63946]">Learn more →</span>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const r = await axios.get('/api/services');
    services.value = r.data;
  } finally {
    loading.value = false;
  }
});
</script>
