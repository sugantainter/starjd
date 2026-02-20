<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <template v-if="post">
      <!-- Article header -->
      <article class="border-b border-[#e5e7eb] bg-white">
        <div class="mx-auto max-w-3xl px-4 pt-10 pb-6 md:pt-14 md:pb-8">
          <span v-if="post.category" class="text-sm font-semibold uppercase tracking-wider text-[#e63946]">{{ post.category }}</span>
          <h1 class="mt-2 text-3xl font-bold leading-tight text-[#1a1a1a] md:text-4xl lg:text-[2.75rem]">{{ post.title }}</h1>
          <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-[#6b7280]">
            <span v-if="post.author">Author: {{ post.author }}</span>
            <span v-if="post.updated_at">|</span>
            <span v-if="post.updated_at">Updated: {{ post.updated_at }}</span>
            <span v-if="!post.updated_at && post.date">|</span>
            <span v-if="post.date">{{ post.date }}</span>
          </div>
        </div>

        <!-- Featured image -->
        <div v-if="post.image" class="mx-auto max-w-4xl px-4 pb-8">
          <div class="overflow-hidden rounded-xl bg-[#f3f4f6] shadow-sm">
            <img :src="post.image" :alt="post.title" class="h-full w-full object-cover" />
          </div>
        </div>

        <!-- Main content -->
        <div class="mx-auto max-w-3xl px-4 pb-12 md:pb-16">
          <div class="prose prose-lg max-w-none text-[#374151] post-body prose-headings:font-bold prose-headings:text-[#1a1a1a] prose-headings:mt-10 prose-headings:mb-4 prose-a:text-[#e63946] prose-a:no-underline hover:prose-a:underline prose-ul:my-4 prose-ol:my-4 prose-li:my-1" v-html="renderedBody"></div>

          <!-- Tags -->
          <div v-if="post.category_tags && post.category_tags.length" class="mt-10 border-t border-[#e5e7eb] pt-8">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-[#9ca3af]">Tags</h3>
            <div class="mt-3 flex flex-wrap gap-2">
              <span v-for="tag in post.category_tags" :key="tag" class="rounded-full bg-[#f1f5f9] px-3 py-1 text-sm text-[#64748b]">{{ tag }}</span>
            </div>
          </div>
        </div>
      </article>

      <!-- Related Posts -->
      <section v-if="relatedPosts.length" class="border-t border-[#e5e7eb] bg-[#f8fafc] px-4 py-12 md:py-16">
        <div class="mx-auto max-w-6xl">
          <h2 class="text-xl font-bold text-[#1a1a1a] md:text-2xl">Related Posts</h2>
          <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <router-link
              v-for="p in relatedPosts"
              :key="p.id"
              :to="'/blog/' + p.slug"
              class="group flex flex-col overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md"
            >
              <div v-if="p.image" class="aspect-[16/10] overflow-hidden bg-[#f3f4f6]">
                <img :src="p.image" :alt="p.title" class="h-full w-full object-cover transition group-hover:scale-105" />
              </div>
              <div class="flex flex-1 flex-col p-4">
                <span v-if="p.category" class="text-xs font-semibold uppercase tracking-wider text-[#e63946]">{{ p.category }}</span>
                <h3 class="mt-1 text-base font-bold text-[#1a1a1a] line-clamp-2 transition group-hover:text-[#e63946]">{{ p.title }}</h3>
                <p class="mt-2 text-xs text-[#9ca3af]">{{ p.date }}</p>
              </div>
            </router-link>
          </div>
        </div>
      </section>

      <!-- Back to blog -->
      <section class="border-t border-[#e5e7eb] bg-white px-4 py-8">
        <div class="mx-auto max-w-3xl">
          <router-link to="/blog" class="inline-flex items-center text-sm font-medium text-[#64748b] transition hover:text-[#e63946]">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Blog
          </router-link>
        </div>
      </section>
    </template>
    <div v-else-if="loading" class="flex min-h-[50vh] items-center justify-center">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent"></div>
    </div>
    <div v-else class="px-4 py-24 text-center text-[#6b7280]">Post not found.</div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const post = ref(null);
const relatedPosts = ref([]);
const loading = ref(true);

const renderedBody = computed(() => {
  if (!post.value?.body) return '';
  const b = String(post.value.body).trim();
  if (!b) return '';
  if (b.startsWith('<')) return b;
  return b
    .split(/\r?\n\r?\n+/)
    .map((p) => '<p>' + p.replace(/\r?\n/g, '<br/>') + '</p>')
    .join('');
});

function updateDocumentMeta() {
  if (!post.value) return;
  const title = post.value.meta_title || post.value.title;
  const description = post.value.meta_description || post.value.excerpt || '';
  document.title = title ? `${title} | Starjd` : document.title;
  let meta = document.querySelector('meta[name="description"]');
  if (!meta) {
    meta = document.createElement('meta');
    meta.name = 'description';
    document.head.appendChild(meta);
  }
  meta.setAttribute('content', description);
}

watch(post, updateDocumentMeta, { immediate: true });

onMounted(async () => {
  try {
    const [postRes, listRes] = await Promise.all([
      axios.get('/api/posts/' + encodeURIComponent(route.params.slug)),
      axios.get('/api/posts'),
    ]);
    post.value = postRes.data;
    const all = listRes.data.posts || [];
    relatedPosts.value = all.filter((p) => p.slug !== post.value?.slug).slice(0, 3);
  } catch {
    post.value = null;
    relatedPosts.value = [];
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.post-body :deep(p) {
  margin-bottom: 1.25em;
  line-height: 1.75;
}
.post-body :deep(p:last-child) {
  margin-bottom: 0;
}
.post-body :deep(br) {
  display: block;
  content: '';
  margin-top: 0.25em;
}
.post-body :deep(h2) {
  font-size: 1.5rem;
  margin-top: 2rem;
}
.post-body :deep(h3) {
  font-size: 1.25rem;
  margin-top: 1.5rem;
}
.post-body :deep(ul),
.post-body :deep(ol) {
  padding-left: 1.5em;
}
.prose :deep(.youtube-embed) {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  margin: 1.5rem 0;
}
.prose :deep(.youtube-embed iframe) {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>
