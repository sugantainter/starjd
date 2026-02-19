<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <h1 class="section-title text-3xl font-bold text-[#1a1a1a] md:text-4xl">Blog</h1>
        <p class="section-subtitle mt-4 text-[#6b7280]">Articles, tips, and resources for creators and brands.</p>
        <div v-if="loading" class="mt-12 text-center text-[#6b7280]">Loading…</div>
        <div v-else-if="!posts.length" class="mt-12 rounded-2xl border border-[#e5e7eb] bg-white p-12 text-center text-[#6b7280]">No posts yet.</div>
        <div v-else class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <router-link v-for="post in posts" :key="post.id" :to="'/blog/' + post.slug" class="group overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm transition hover:border-[#e63946]/40 hover:shadow-xl">
            <div v-if="post.image" class="aspect-[16/10] overflow-hidden bg-[#f3f4f6]">
              <img :src="post.image" :alt="post.title" class="h-full w-full object-cover transition group-hover:scale-105" />
            </div>
            <div class="p-5">
              <span v-if="post.category" class="text-xs font-medium uppercase tracking-wider text-[#e63946]">{{ post.category }}</span>
              <h2 class="mt-2 text-lg font-bold text-[#1a1a1a] transition group-hover:text-[#e63946]">{{ post.title }}</h2>
              <p v-if="post.excerpt" class="mt-2 line-clamp-2 text-sm text-[#6b7280]">{{ post.excerpt }}</p>
              <p class="mt-2 text-xs text-[#9ca3af]">{{ post.date }}</p>
            </div>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const posts = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const r = await axios.get('/api/posts');
    posts.value = r.data.posts || [];
  } finally {
    loading.value = false;
  }
});
</script>
