<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero / header -->
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-12 md:py-16">
      <div class="mx-auto max-w-6xl">
        <h1 class="text-3xl font-bold tracking-tight text-[#1a1a1a] md:text-4xl">Blog</h1>
        <p class="mt-2 text-lg text-[#6b7280]">Articles, tips, and resources for creators and brands.</p>
      </div>
    </section>

    <!-- Posts grid -->
    <section class="px-4 py-12 md:py-16">
      <div class="mx-auto max-w-6xl">
        <div v-if="loading" class="flex justify-center py-20">
          <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent"></div>
        </div>
        <div v-else-if="!posts.length" class="rounded-2xl border border-[#e5e7eb] bg-white p-16 text-center text-[#6b7280] shadow-sm">No posts yet.</div>
        <div v-else class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="post in posts"
            :key="post.id"
            class="group flex flex-col overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm transition hover:border-[#e63946]/30 hover:shadow-lg"
          >
            <router-link :to="'/blog/' + post.slug" class="block flex-1">
              <div v-if="post.image" class="aspect-[16/10] overflow-hidden bg-[#f3f4f6]">
                <img :src="post.image" :alt="post.title" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
              </div>
              <div v-else class="aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-[#e63946]/10 to-[#1a1a1a]/5">
                <span class="text-4xl font-bold text-[#e63946]/30">{{ post.title.charAt(0) }}</span>
              </div>
              <div class="flex flex-1 flex-col p-6">
                <span v-if="post.category" class="text-xs font-semibold uppercase tracking-wider text-[#e63946]">{{ post.category }}</span>
                <h2 class="mt-2 text-xl font-bold leading-snug text-[#1a1a1a] transition group-hover:text-[#e63946]">{{ post.title }}</h2>
                <p v-if="post.excerpt" class="mt-2 flex-1 line-clamp-2 text-sm leading-relaxed text-[#6b7280]">{{ post.excerpt }}</p>
                <p class="mt-4 text-xs font-medium text-[#9ca3af]">{{ post.date }}</p>
              </div>
            </router-link>
            <div class="border-t border-[#e5e7eb] px-6 py-3">
              <router-link :to="'/blog/' + post.slug" class="inline-flex items-center text-sm font-medium text-[#e63946] hover:underline">
                Read more
                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
              </router-link>
            </div>
          </article>
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
