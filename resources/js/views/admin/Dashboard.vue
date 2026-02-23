<template>
  <div>
    <header class="mb-8">
      <h1 class="text-2xl font-bold tracking-tight text-[#1a1a1a]">Dashboard</h1>
      <p class="mt-1 text-sm text-[#64748b]">Overview of your CMS and site content</p>
    </header>

    <div v-if="loading" class="space-y-8">
      <div class="h-6 w-48 animate-pulse rounded bg-[#e2e8f0]" />
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="i in 8" :key="i" class="rounded-xl border border-[#e2e8f0] bg-white p-5">
          <div class="h-4 w-24 animate-pulse rounded bg-[#e2e8f0]" />
          <div class="mt-3 h-8 w-16 animate-pulse rounded bg-[#e2e8f0]" />
        </div>
      </div>
    </div>

    <div v-else class="space-y-8">
      <!-- Content & CMS -->
      <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-[#94a3b8]">Content &amp; CMS</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <router-link to="/admin/pages" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Pages</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.pages ?? 0 }}</p>
            <p class="mt-0.5 text-xs text-[#94a3b8]">{{ stats.pages_published ?? 0 }} published</p>
          </router-link>
          <router-link to="/admin/posts" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Blog Posts</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.posts ?? 0 }}</p>
            <p class="mt-0.5 text-xs text-[#94a3b8]">{{ stats.posts_published ?? 0 }} published</p>
          </router-link>
          <router-link to="/admin/videos" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Video Guides</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.videos ?? 0 }}</p>
          </router-link>
        </div>
      </section>

      <!-- Locations -->
      <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-[#94a3b8]">Locations</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <router-link to="/admin/states" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">States</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.states ?? 0 }}</p>
          </router-link>
          <router-link to="/admin/cities" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Cities</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.cities ?? 0 }}</p>
          </router-link>
        </div>
      </section>

      <!-- Site building -->
      <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-[#94a3b8]">Site building</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <router-link to="/admin/categories" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Categories</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.categories ?? 0 }}</p>
          </router-link>
          <router-link to="/admin/testimonials" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Testimonials</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.testimonials ?? 0 }}</p>
          </router-link>
          <router-link to="/admin/faqs" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">FAQs</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.faqs ?? 0 }}</p>
          </router-link>
          <router-link to="/admin/steps" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Steps</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.steps ?? 0 }}</p>
          </router-link>
          <router-link to="/admin/partners" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Partners</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.partners ?? 0 }}</p>
          </router-link>
          <router-link to="/admin/services" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Services</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.services ?? 0 }}</p>
          </router-link>
        </div>
      </section>

      <!-- Inbox -->
      <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-[#94a3b8]">Inbox</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <router-link to="/admin/contacts" class="group rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md">
            <p class="text-sm font-medium text-[#64748b]">Contact Messages</p>
            <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ stats.contacts ?? 0 }}</p>
          </router-link>
        </div>
      </section>
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
