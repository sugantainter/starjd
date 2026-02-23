<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <template v-if="post">
      <!-- Article header (full width) -->
      <header class="border-b border-[#e5e7eb] bg-white">
        <div class="mx-auto max-w-6xl px-4 pt-10 pb-6 md:pt-14 md:pb-8">
          <span v-if="post.category" class="text-sm font-semibold uppercase tracking-wider text-[#e63946]">{{ post.category }}</span>
          <h1 class="mt-2 text-3xl font-bold leading-tight text-[#1a1a1a] md:text-4xl lg:text-[2.75rem]">{{ post.title }}</h1>
          <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-[#6b7280]">
            <span v-if="post.author">Author: {{ post.author }}</span>
            <template v-if="post.updated_at || post.date">
              <span v-if="post.author">|</span>
              <span v-if="post.updated_at">Updated: {{ post.updated_at }}</span>
              <span v-else-if="post.date">{{ post.date }}</span>
            </template>
          </div>
        </div>
        <div v-if="post.image" class="mx-auto max-w-6xl px-4 pb-8">
          <div class="overflow-hidden rounded-xl bg-[#f3f4f6] shadow-sm">
            <img :src="post.image" :alt="post.title" class="h-full w-full object-cover" />
          </div>
        </div>
      </header>

      <!-- Two columns: main content (left) + sidebar (right) -->
      <div class="mx-auto max-w-6xl px-4 py-8 md:py-12">
        <div class="grid gap-10 lg:grid-cols-[1fr_320px]">
          <!-- Left: main content -->
          <div class="min-w-0">
            <article class="rounded-xl border border-[#e5e7eb] bg-white p-6 shadow-sm md:p-8">
              <div
                class="prose prose-lg max-w-none text-left text-[#374151] post-body prose-headings:font-bold prose-headings:text-[#1a1a1a] prose-headings:mt-10 prose-headings:mb-4 prose-a:text-[#e63946] prose-a:no-underline hover:prose-a:underline prose-ul:my-4 prose-ol:my-4 prose-li:my-1"
                v-html="renderedBody"
              ></div>

              <!-- Tags -->
              <div v-if="post.category_tags && post.category_tags.length" class="mt-10 border-t border-[#e5e7eb] pt-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-[#9ca3af]">Tags</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                  <span v-for="tag in post.category_tags" :key="tag" class="rounded-full bg-[#f1f5f9] px-3 py-1 text-sm text-[#64748b]">{{ tag }}</span>
                </div>
              </div>

              <!-- Share, Like, Comment, View -->
              <div class="mt-10 flex flex-wrap items-center gap-6 border-t border-[#e5e7eb] pt-6">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="text-sm font-medium text-[#6b7280]">Share:</span>
                  <button
                    type="button"
                    aria-label="Share on WhatsApp"
                    class="rounded-full p-2 text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#25D366]"
                    @click="share('whatsapp')"
                  >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
                  </button>
                  <button
                    type="button"
                    aria-label="Share on Facebook"
                    class="rounded-full p-2 text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1877F2]"
                    @click="share('facebook')"
                  >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg>
                  </button>
                  <button
                    type="button"
                    aria-label="Share on Instagram"
                    class="rounded-full p-2 text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#E4405F]"
                    @click="share('instagram')"
                  >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" /></svg>
                  </button>
                  <button
                    type="button"
                    aria-label="Share on Twitter"
                    class="rounded-full p-2 text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#e63946]"
                    @click="share('twitter')"
                  >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                  </button>
                  <button
                    type="button"
                    aria-label="Share on LinkedIn"
                    class="rounded-full p-2 text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#e63946]"
                    @click="share('linkedin')"
                  >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" /></svg>
                  </button>
                  <button
                    type="button"
                    aria-label="Copy link"
                    class="rounded-full p-2 text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#e63946]"
                    :title="copyDone ? 'Copied!' : 'Copy link'"
                    @click="copyLink"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l2-2m-2 2l2 2m2-2l2-2m2 2l-2 2" /></svg>
                  </button>
                </div>
                <div class="flex items-center gap-6 text-sm text-[#6b7280]">
                  <button
                    type="button"
                    class="flex items-center gap-1.5 transition hover:text-[#e63946]"
                    :class="{ 'text-[#e63946]': liked }"
                    @click="toggleLike"
                  >
                    <svg class="h-5 w-5" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    <span>{{ likeCount }}</span>
                  </button>
                  <button type="button" class="flex items-center gap-1.5 transition hover:text-[#e63946]" @click="focusComments">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    <span>{{ commentCount }}</span>
                  </button>
                  <span class="flex items-center gap-1.5">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <span>{{ viewCount }}</span> views
                  </span>
                </div>
              </div>
            </article>

            <!-- Back to blog -->
            <div class="mt-6">
              <router-link to="/blog" class="inline-flex items-center text-sm font-medium text-[#64748b] transition hover:text-[#e63946]">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Back to Blog
              </router-link>
            </div>
          </div>

          <!-- Right: sidebar - Trending & News -->
          <aside class="space-y-8 lg:sticky lg:top-6 lg:self-start">
            <!-- Trending -->
            <div class="rounded-xl border border-[#e5e7eb] bg-white p-5 shadow-sm">
              <h2 class="text-lg font-bold text-[#1a1a1a]">Trending</h2>
              <ul class="mt-4 space-y-3">
                <li v-for="p in trendingPosts" :key="p.id">
                  <router-link :to="'/blog/' + p.slug" class="group flex gap-3">
                    <div v-if="p.image" class="h-14 w-20 shrink-0 overflow-hidden rounded-lg bg-[#f3f4f6]">
                      <img :src="p.image" :alt="p.title" class="h-full w-full object-cover transition group-hover:scale-105" />
                    </div>
                    <div v-else class="flex h-14 w-20 shrink-0 items-center justify-center rounded-lg bg-[#e63946]/10 text-lg font-bold text-[#e63946]/60">{{ p.title.charAt(0) }}</div>
                    <div class="min-w-0 flex-1">
                      <h3 class="line-clamp-2 text-sm font-semibold text-[#1a1a1a] transition group-hover:text-[#e63946]">{{ p.title }}</h3>
                      <p class="mt-0.5 text-xs text-[#9ca3af]">{{ p.date }}</p>
                    </div>
                  </router-link>
                </li>
              </ul>
              <router-link v-if="trendingPosts.length" to="/blog" class="mt-4 inline-block text-sm font-medium text-[#e63946] hover:underline">View all</router-link>
            </div>

            <!-- News -->
            <div class="rounded-xl border border-[#e5e7eb] bg-white p-5 shadow-sm">
              <h2 class="text-lg font-bold text-[#1a1a1a]">News</h2>
              <ul class="mt-4 space-y-3">
                <li v-for="p in newsPosts" :key="p.id">
                  <router-link :to="'/blog/' + p.slug" class="group flex gap-3">
                    <div v-if="p.image" class="h-14 w-20 shrink-0 overflow-hidden rounded-lg bg-[#f3f4f6]">
                      <img :src="p.image" :alt="p.title" class="h-full w-full object-cover transition group-hover:scale-105" />
                    </div>
                    <div v-else class="flex h-14 w-20 shrink-0 items-center justify-center rounded-lg bg-[#1a1a1a]/5 text-lg font-bold text-[#1a1a1a]/50">{{ p.title.charAt(0) }}</div>
                    <div class="min-w-0 flex-1">
                      <h3 class="line-clamp-2 text-sm font-semibold text-[#1a1a1a] transition group-hover:text-[#e63946]">{{ p.title }}</h3>
                      <p class="mt-0.5 text-xs text-[#9ca3af]">{{ p.date }}</p>
                    </div>
                  </router-link>
                </li>
              </ul>
              <router-link v-if="newsPosts.length" to="/blog" class="mt-4 inline-block text-sm font-medium text-[#e63946] hover:underline">View all</router-link>
            </div>
          </aside>
        </div>
      </div>

      <!-- Related Posts (full width below) -->
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
const allPosts = ref([]);
const loading = ref(true);
const liked = ref(false);
const copyDone = ref(false);
const likeCount = ref(0);
const commentCount = ref(0);
const viewCount = ref(0);

const trendingPosts = computed(() => {
  const other = allPosts.value.filter((p) => p.slug !== post.value?.slug);
  return other.slice(0, 5);
});
const newsPosts = computed(() => {
  const other = allPosts.value.filter((p) => p.slug !== post.value?.slug);
  return other.slice(5, 10);
});

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

function share(platform) {
  const url = window.location.href;
  const encodedUrl = encodeURIComponent(url);
  const text = encodeURIComponent(post.value?.title || '');
  if (platform === 'whatsapp') window.open(`https://wa.me/?text=${text}%20${encodedUrl}`, '_blank', 'noopener,noreferrer');
  if (platform === 'facebook') window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`, '_blank', 'noopener,noreferrer');
  if (platform === 'instagram') {
    navigator.clipboard?.writeText(url).then(() => { copyDone.value = true; setTimeout(() => (copyDone.value = false), 2000); });
    window.open('https://www.instagram.com/', '_blank', 'noopener,noreferrer');
  }
  if (platform === 'twitter') window.open(`https://twitter.com/intent/tweet?url=${encodedUrl}&text=${text}`, '_blank', 'noopener,noreferrer');
  if (platform === 'linkedin') window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`, '_blank', 'noopener,noreferrer');
}

function copyLink() {
  const url = window.location.href;
  navigator.clipboard?.writeText(url).then(() => {
    copyDone.value = true;
    setTimeout(() => (copyDone.value = false), 2000);
  });
}

function toggleLike() {
  liked.value = !liked.value;
  if (liked.value) likeCount.value++;
  else if (likeCount.value > 0) likeCount.value--;
}

function focusComments() {
  commentCount.value++;
}

watch(post, updateDocumentMeta, { immediate: true });

async function loadPost() {
  const slug = route.params.slug;
  if (!slug) return;
  loading.value = true;
  post.value = null;
  try {
    const [postRes, listRes] = await Promise.all([
      axios.get('/api/posts/' + encodeURIComponent(slug)),
      axios.get('/api/posts'),
    ]);
    post.value = postRes.data;
    const list = listRes.data.posts || [];
    allPosts.value = list;
    relatedPosts.value = list.filter((p) => p.slug !== post.value?.slug).slice(0, 3);
    viewCount.value = post.value?.view_count ?? 0;
  } catch {
    post.value = null;
    relatedPosts.value = [];
    allPosts.value = [];
  } finally {
    loading.value = false;
  }
}

watch(
  () => route.params.slug,
  (newSlug, oldSlug) => {
    if (newSlug && newSlug !== oldSlug) {
      loadPost();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  },
);

onMounted(loadPost);
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
