<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <article v-if="post" class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-3xl">
        <span v-if="post.category" class="text-sm font-medium uppercase tracking-wider text-[#e63946]">{{ post.category }}</span>
        <h1 class="mt-2 text-3xl font-bold text-[#1a1a1a] md:text-4xl">{{ post.title }}</h1>
        <p class="mt-4 text-[#6b7280]">{{ post.date }}<span v-if="post.author"> · {{ post.author }}</span></p>
        <div v-if="post.image" class="mt-8 overflow-hidden rounded-2xl bg-[#f3f4f6]">
          <img :src="post.image" :alt="post.title" class="h-full w-full object-cover" />
        </div>
        <div class="prose prose-lg mt-8 max-w-none text-[#1a1a1a]" v-html="renderedBody"></div>
      </div>
    </article>
    <div v-else-if="loading" class="px-4 py-24 text-center text-[#6b7280]">Loading…</div>
    <div v-else class="px-4 py-24 text-center text-[#6b7280]">Post not found.</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const post = ref(null);
const loading = ref(true);

const renderedBody = computed(() => {
  if (!post.value?.body) return '';
  const b = post.value.body;
  if (b.startsWith('<')) return b;
  return b
    .split(/\n\n+/)
    .map((p) => '<p>' + p.replace(/\n/g, '<br/>') + '</p>')
    .join('');
});

onMounted(async () => {
  try {
    const r = await axios.get('/api/posts/' + encodeURIComponent(route.params.slug));
    post.value = r.data;
  } catch {
    post.value = null;
  } finally {
    loading.value = false;
  }
});
</script>
