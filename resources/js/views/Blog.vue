<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero Header -->
    <section class="bg-white border-b border-[#e5e7eb] px-4 py-20 md:py-32">
      <div class="mx-auto max-w-7xl px-4 text-center">
        <span class="inline-block rounded-full bg-[#e63946]/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-[#e63946] mb-6">StarJD Insights</span>
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter text-[#1a1a1a] mb-8">The Creative Blog</h1>
        <p class="text-xl text-[#64748b] max-w-2xl mx-auto leading-relaxed">Master the art of influencer marketing with our curated collection of insights, trends, and success strategies.</p>
        <p v-if="route.query.category || route.params.category" class="mt-8 text-sm font-black text-[#e63946] uppercase tracking-widest">Filtering by: {{ categoryLabel }}</p>
      </div>
    </section>

    <!-- Main Content Grid -->
    <section class="px-4 py-12 md:py-24 -mt-12">
      <div class="mx-auto max-w-7xl px-4">
        <div class="grid gap-10 lg:grid-cols-[1fr_320px]">
          <!-- Left: Posts List (9 Parts) -->
          <div class="min-w-0">
            <div v-if="loading && !posts.length" class="flex justify-center py-20 bg-white rounded-3xl border border-[#e5e7eb] shadow-xl">
               <div class="w-12 h-12 border-4 border-[#e63946] border-t-transparent rounded-full animate-spin"></div>
            </div>
            
            <div v-else-if="!posts.length && finished" class="rounded-3xl border border-[#e5e7eb] bg-white p-20 text-center shadow-xl text-[#64748b] font-bold text-lg">
               No articles found.
            </div>

            <div v-else class="flex flex-col gap-10">
              <article
                v-for="post in filteredPosts"
                :key="post.id"
                class="group flex flex-col md:flex-row overflow-hidden rounded-3xl border border-[#e5e7eb] bg-white shadow-xl transition-all duration-500 hover:border-[#e63946]/30 hover:shadow-2xl hover:-translate-y-1"
              >
                <!-- Feature Image -->
                <div class="w-full md:w-80 h-64 md:h-auto shrink-0 overflow-hidden bg-[#f3f4f6] relative">
                   <img v-if="post.image" :src="post.image" :alt="post.title" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" />
                   <div v-else class="h-full w-full flex items-center justify-center bg-gradient-to-br from-[#e63946] to-[#1a1a1a]">
                     <span class="text-6xl font-black text-white/20">{{ post.title.charAt(0) }}</span>
                   </div>
                   <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent md:hidden"></div>
                </div>

                <!-- Content Area -->
                <div class="flex-1 p-8 md:p-10 flex flex-col">
                  <div class="mb-6">
                     <div class="flex items-center gap-3 mb-4">
                        <span v-if="post.category" class="text-[10px] font-black uppercase tracking-[0.2em] text-[#e63946]">{{ post.category }}</span>
                        <span class="w-1 h-1 bg-[#cbd5e1] rounded-full"></span>
                        <span class="text-xs font-bold text-[#94a3b8]">{{ post.date }}</span>
                     </div>
                     <h2 class="text-2xl font-black text-[#1a1a1a] mb-4 group-hover:text-[#e63946] transition-colors leading-tight">{{ post.title }}</h2>
                     <p v-if="post.excerpt" class="text-[#64748b] text-base leading-relaxed line-clamp-2">{{ post.excerpt }}</p>
                  </div>

                  <div class="mt-auto pt-6 border-t border-[#f1f5f9] flex items-center justify-between">
                     <router-link :to="'/blog/' + post.slug" class="inline-flex items-center gap-2 text-xs font-black text-[#1a1a1a] group-hover:text-[#e63946] transition-colors uppercase tracking-widest">
                        Read Story
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                     </router-link>
                  </div>
                </div>
              </article>
            </div>

            <!-- Infinite scroll -->
            <div v-if="posts.length" ref="scrollTrigger" class="flex justify-center py-12">
              <div v-if="loadingMore" class="flex items-center gap-3">
                <div class="h-2 w-2 animate-bounce rounded-full bg-[#e63946]" style="animation-delay: 0s"></div>
                <div class="h-2 w-2 animate-bounce rounded-full bg-[#e63946]" style="animation-delay: 0.2s"></div>
                <div class="h-2 w-2 animate-bounce rounded-full bg-[#e63946]" style="animation-delay: 0.4s"></div>
                <span class="text-sm font-medium text-[#64748b]">Loading more stories…</span>
              </div>
              <p v-else-if="finished" class="text-sm font-bold uppercase tracking-widest text-[#cbd5e1]">You’ve read it all</p>
            </div>
          </div>

          <!-- Right: Sidebar (3 Parts) -->
          <aside class="space-y-8 lg:sticky lg:top-6 lg:self-start">
            <!-- Search Box -->
            <div class="rounded-3xl border border-[#e5e7eb] bg-white p-6 shadow-xl space-y-4">
              <h3 class="text-sm font-black uppercase tracking-widest text-[#1a1a1a]">Search Insights</h3>
              <div class="relative">
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Keywords..."
                  class="w-full px-5 py-4 pr-14 rounded-2xl bg-[#fafaf9] border border-[#e5e7eb] focus:outline-none focus:ring-2 focus:ring-[#e63946] focus:bg-white transition-all text-sm"
                  @keyup.enter="router.push({ path: '/blog', query: { search: searchTerm } })"
                />
                <button 
                  @click="router.push({ path: '/blog', query: { search: searchTerm } })"
                  class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-[#e63946] text-white rounded-xl shadow-lg shadow-[#e63946]/20 transition-all hover:bg-[#c1121f] transform hover:scale-105 active:scale-95"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1116.5 9a7.5 7.5 0 010 7.5z" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Categories Card (Optional but looks good) -->
            <div v-if="blogCategories && blogCategories.length" class="rounded-3xl border border-[#e5e7eb] bg-white p-6 shadow-xl">
               <h3 class="text-sm font-black uppercase tracking-widest text-[#1a1a1a] mb-6">Explore Topics</h3>
               <div class="flex flex-wrap gap-2">
                  <router-link 
                    v-for="cat in blogCategories" 
                    :key="cat.slug"
                    :to="'/blog/category/' + cat.slug"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all border border-[#f1f5f9] hover:border-[#e63946] hover:text-[#e63946] hover:shadow-lg shadow-[#e63946]/5"
                    :class="(route.query.category === cat.slug || route.params.category === cat.slug) ? 'bg-[#e63946] text-white border-[#e63946]' : 'bg-[#fafaf9] text-[#64748b]'"
                  >
                    {{ cat.label }}
                  </router-link>
               </div>
            </div>
          </aside>
        </div>
      </div>
    </section>

    <!-- Professional Newsletter CTA -->
    <section class="max-w-5xl mx-auto px-4 py-24">
       <div class="bg-black rounded-3xl p-12 md:p-20 text-center relative overflow-hidden shadow-2xl">
          <div class="absolute inset-0 opacity-20">
             <div class="absolute top-0 right-0 w-96 h-96 bg-[#e63946] rounded-full blur-[120px]"></div>
          </div>
          <div class="relative z-10">
             <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Stay ahead of the curve</h2>
             <p class="text-[#94a3b8] text-xl max-w-xl mx-auto mb-10">Get the best of StarJD Insights delivered to your inbox once a week. No spam, just strategies.</p>
             <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" placeholder="Enter your email" class="flex-1 px-6 py-4 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:border-white transition-all" />
                <button class="px-8 py-4 bg-[#e63946] text-white font-black rounded-2xl shadow-xl shadow-[#e63946]/20 transition-all hover:bg-[#c1121f] transform hover:scale-105">Subscribe</button>
             </div>
          </div>
       </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const posts = ref([]);
const loading = ref(true);
const loadingMore = ref(false);
const finished = ref(false);
const page = ref(1);
// Sync search term with URL query
watch(() => route.query.search, (newVal) => {
  searchTerm.value = newVal || '';
  refresh();
});
const scrollTrigger = ref(null);
const resolvedCategoryLabel = ref('');
const blogCategories = ref([]);
let observer = null;

const searchTerm = ref('');
// We'll mostly rely on server-side search now, 
// but we keep this computed to ensure the UI reacts 
// if the user clears the input locally without hitting enter.
const filteredPosts = computed(() => {
  if (!searchTerm.value && !route.query.search) return posts.value;
  const term = searchTerm.value.toLowerCase();
  return posts.value.filter(p =>
    (p.title && p.title.toLowerCase().includes(term)) ||
    (p.excerpt && p.excerpt.toLowerCase().includes(term))
  );
});

function setupScrollObserver() {
  if (!observer) return;
  observer.disconnect();
  if (scrollTrigger.value) observer.observe(scrollTrigger.value);
}

const PER_PAGE = 12;

function slugify(text) {
  if (!text) return '';
  return String(text)
    .trim()
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w-]+/g, '');
}

const categoryLabel = computed(() => {
  const cat = route.query.category || route.params.category;
  if (!cat) return '';
  if (posts.value.length) return posts.value[0]?.category || cat.replace(/-/g, ' ');
  return cat.replace(/-/g, ' ');
});

async function resolveCategoryFilter() {
  const q = route.query.category || route.params.category;
  resolvedCategoryLabel.value = '';
  if (q == null || q === '') return;
  try {
    const { data } = await axios.get('/api/posts/categories');
    const slug = String(q).toLowerCase();
    const found = data.categories?.find(
      (c) => c.slug === slug || slugify(c.label) === slug
    );
    resolvedCategoryLabel.value = found?.label || '';
  } catch {
    resolvedCategoryLabel.value = '';
  }
}

function refresh() {
  posts.value = [];
  page.value = 1;
  finished.value = false;
  load(1, false);
}

async function load(p = 1, append = false) {
  if (append && (loadingMore.value || finished.value)) return;
  if (append) loadingMore.value = true;
  else loading.value = true;

  try {
    const params = { page: p, per_page: PER_PAGE };
    if (resolvedCategoryLabel.value) {
      params.category = resolvedCategoryLabel.value;
    }
    if (route.query.search) {
      params.search = route.query.search;
    }

    const r = await axios.get('/api/posts', { params });
    let items = r.data.posts || [];
    const current = r.data.current_page ?? p;
    const last = r.data.last_page ?? p;
    const perPage = r.data.per_page ?? PER_PAGE;

    // Legacy ?category=slug links: filter client-side if slug not in /categories
    const qCat = route.query.category || route.params.category;
    if (qCat != null && qCat !== '' && !resolvedCategoryLabel.value) {
      const want = String(qCat).toLowerCase();
      items = items.filter((post) => slugify(post.category) === want);
    }

    if (append) {
      posts.value = [...posts.value, ...items];
    } else {
      posts.value = items;
    }

    page.value = current;
    finished.value = current >= last || (r.data.posts || []).length === 0 || (r.data.posts || []).length < perPage;
  } catch {
    if (!append) posts.value = [];
    finished.value = true;
  } finally {
    if (append) loadingMore.value = false;
    else loading.value = false;
  }
}

function loadMore() {
  if (loading.value || loadingMore.value || finished.value) return;
  load(page.value + 1, true);
}

onMounted(async () => {
  observer = new IntersectionObserver(
    (entries) => {
      if (entries[0]?.isIntersecting) loadMore();
    },
    { threshold: 0.1 }
  );

  if (route.query.search) {
    searchTerm.value = route.query.search;
  }
  
  // Fetch categories for sidebar
  try {
    const catRes = await axios.get('/api/posts/categories');
    blogCategories.value = catRes.data.categories || [];
  } catch (e) {
    console.error('Failed to fetch blog categories', e);
  }

  await resolveCategoryFilter();
  await load(1, false);
  await nextTick();
  setupScrollObserver();
});

onUnmounted(() => {
  if (observer) observer.disconnect();
});

watch(
  () => [route.query.category, route.params.category],
  async () => {
    await resolveCategoryFilter();
    refresh();
  }
);

watch(
  () => posts.value.length,
  async () => {
    await nextTick();
    setupScrollObserver();
  }
);
</script>
