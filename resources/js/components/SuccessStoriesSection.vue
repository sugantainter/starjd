<template>
  <section v-if="stories.length" class="bg-white py-16 md:py-24">
    <div class="mx-auto max-w-6xl px-4 md:px-6">
      <div class="mb-12 flex flex-col items-center justify-between gap-6 md:flex-row md:items-end">
        <div class="max-w-xl text-center md:text-left">
          <h2 class="text-3xl font-black tracking-tight text-[#1a1a1a] md:text-4xl lg:text-5xl">
            Real Stories, <span class="text-[#e63946]">Real Results.</span>
          </h2>
          <p class="mt-4 text-lg text-[#64748b]">
            Discover how StarJD is empowering creators to build brands and helping brands to find their perfect voice.
          </p>
        </div>
        <router-link
          to="/success-stories"
          class="group flex items-center gap-2 font-bold text-[#e63946] transition-colors hover:text-[#c1121f]"
        >
          View all stories
          <svg
            class="h-5 w-5 transition-transform group-hover:translate-x-1"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </router-link>
      </div>

      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <SuccessStoryCard
          v-for="story in stories"
          :key="story.id"
          :story="story"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SuccessStoryCard from './SuccessStoryCard.vue';

const stories = ref([]);

async function loadFeatured() {
  try {
    const { data } = await axios.get('/api/success-stories', {
      params: { featured: 1, limit: 3 }
    });
    stories.value = data.data || [];
  } catch (e) {
    console.error('Failed to load success stories', e);
  }
}

onMounted(loadFeatured);
</script>
