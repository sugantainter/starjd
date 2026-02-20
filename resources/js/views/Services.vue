<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero -->
    <section class="relative overflow-hidden border-b border-[#e5e7eb] bg-gradient-to-br from-[#1a1a1a] via-[#2d2d2d] to-[#1a1a1a] px-4 py-20 md:py-28">
      <div class="relative mx-auto max-w-4xl text-center">
        <span class="inline-block rounded-full bg-[#e63946]/20 px-4 py-1 text-xs font-semibold uppercase tracking-wider text-[#e63946]">What we offer</span>
        <h1 class="mt-4 text-4xl font-bold tracking-tight text-white md:text-5xl">Our Services</h1>
        <p class="mt-5 text-lg text-[#94a3b8] md:text-xl">Explore what we offer. Professional solutions tailored for creators and brands.</p>
      </div>
    </section>

    <!-- Intro strip -->
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-10">
      <div class="mx-auto max-w-4xl text-center">
        <p class="text-[#64748b]">Select a service below to see full details, or get in touch for a custom quote.</p>
      </div>
    </section>

    <!-- Services grid -->
    <section class="px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <div v-if="loading" class="flex justify-center py-20">
          <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent"></div>
        </div>
        <div v-else-if="!services.length" class="rounded-2xl border border-[#e5e7eb] bg-white p-16 text-center shadow-sm">
          <p class="text-[#64748b]">No services yet. Check back soon.</p>
        </div>
        <div v-else class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <router-link
            v-for="s in services"
            :key="s.id"
            :to="'/services/' + s.slug"
            class="group flex flex-col overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm transition hover:border-[#e63946]/40 hover:shadow-xl"
          >
            <div class="aspect-[16/10] overflow-hidden bg-[#f1f5f9]">
              <img v-if="s.image" :src="s.image" :alt="s.name" class="h-full w-full transition duration-300 group-hover:scale-105" :class="(s.image_fit || 'cover') === 'contain' ? 'object-contain' : 'object-cover'" />
              <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#e63946]/10 to-[#1a1a1a]/5">
                <span class="text-4xl font-bold text-[#e63946]/30">{{ s.name.charAt(0) }}</span>
              </div>
            </div>
            <div class="flex flex-1 flex-col p-6">
              <h2 class="text-xl font-bold text-[#1a1a1a] transition group-hover:text-[#e63946]">{{ s.name }}</h2>
              <p v-if="s.short_description" class="mt-2 flex-1 line-clamp-2 text-sm text-[#64748b]">{{ s.short_description }}</p>
              <span class="mt-4 inline-flex items-center text-sm font-medium text-[#e63946]">Learn more <svg class="ml-1 h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg></span>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="border-t border-[#e5e7eb] bg-white px-4 py-16 md:py-20">
      <div class="mx-auto max-w-3xl rounded-2xl bg-gradient-to-br from-[#e63946] to-[#c1121f] px-8 py-12 text-center shadow-xl md:px-12 md:py-16">
        <h2 class="text-2xl font-bold text-white md:text-3xl">Need something custom?</h2>
        <p class="mt-3 text-[#fecaca]">Tell us your goals and we'll tailor a solution for you.</p>
        <router-link to="/contact" class="mt-6 inline-block rounded-xl bg-white px-6 py-3 text-sm font-semibold text-[#e63946] shadow-lg transition hover:bg-[#fafafa]">Get in touch</router-link>
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
