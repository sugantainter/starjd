<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <h1 class="section-title text-3xl font-bold text-[#1a1a1a] md:text-4xl">Video Guides & Success Stories</h1>
        <p class="section-subtitle mt-4 text-[#6b7280]">Influencer marketing tips and how-to guides from experts.</p>
        <div v-if="loading" class="mt-12 text-center text-[#6b7280]">Loading…</div>
        <div v-else-if="!videos.length" class="mt-12 rounded-2xl border border-[#e5e7eb] bg-white p-12 text-center text-[#6b7280]">No videos yet.</div>
        <div v-else class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="video in videos" :key="video.id" class="overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm transition hover:border-[#e63946]/40 hover:shadow-xl">
            <div class="relative aspect-video overflow-hidden bg-[#1a1a1a]">
              <iframe :src="video.embedUrl" :title="video.title" class="absolute inset-0 h-full w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
            <div class="p-5">
              <h2 class="text-lg font-bold text-[#1a1a1a]">{{ video.title }}</h2>
              <p v-if="video.desc" class="mt-2 text-sm text-[#6b7280]">{{ video.desc }}</p>
              <a :href="video.watchUrl" target="_blank" rel="noopener noreferrer" class="cursor-link mt-3 inline-flex items-center gap-2 text-sm font-medium text-[#e63946] hover:underline">Watch on YouTube →</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const videos = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const r = await axios.get('/api/videos');
    videos.value = r.data.videos || [];
  } finally {
    loading.value = false;
  }
});
</script>
