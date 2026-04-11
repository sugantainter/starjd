<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero Header -->
    <section class="bg-white border-b border-[#e5e7eb] px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl text-center">
        <span class="inline-block rounded-full bg-[#e63946]/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-[#e63946] mb-6">Success Stories</span>
        <h1 class="text-4xl md:text-6xl font-black tracking-tight text-[#1a1a1a] mb-6">Real impact, <span class="text-[#e63946]">measured.</span></h1>
        <p class="text-lg text-[#64748b] max-w-2xl mx-auto leading-relaxed">Discovery how creators, brands, and professionals are scaling their reach and impact with StarJD.</p>
      </div>
    </section>

    <!-- Role Filter -->
    <section class="sticky top-[73px] z-30 bg-white/80 backdrop-blur-md border-b border-[#e5e7eb] py-4">
      <div class="mx-auto max-w-6xl px-4 overflow-x-auto no-scrollbar">
        <div class="flex items-center justify-center gap-2 md:gap-4 min-w-max">
          <button
            v-for="role in rolesWithAll"
            :key="role.slug"
            @click="setRole(role.slug)"
            class="px-4 py-2 rounded-full text-sm font-bold transition-all"
            :class="activeRole === role.slug ? 'bg-[#e63946] text-white shadow-lg' : 'bg-transparent text-[#64748b] hover:text-[#e63946] hover:bg-[#e63946]/5'"
          >
            {{ role.name }}
          </button>
        </div>
      </div>
    </section>

    <!-- Stories Grid -->
    <section class="px-4 py-12 md:py-20">
      <div class="mx-auto max-w-6xl">
        <div v-if="loading && !stories.length" class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="i in 6" :key="i" class="animate-pulse rounded-2xl bg-white p-4 border border-[#e2e8f0]">
            <div class="aspect-[16/10] bg-slate-100 rounded-xl mb-4"></div>
            <div class="h-4 bg-slate-100 rounded w-1/4 mb-3"></div>
            <div class="h-6 bg-slate-100 rounded w-3/4 mb-4"></div>
            <div class="flex gap-2 items-center">
              <div class="w-8 h-8 rounded-full bg-slate-100"></div>
              <div class="h-4 bg-slate-100 rounded w-1/2"></div>
            </div>
          </div>
        </div>

        <div v-else-if="!stories.length && finished" class="text-center py-20">
          <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-[#e63946]/5 text-[#e63946]">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-[#1a1a1a]">No stories found</h3>
          <p class="mt-2 text-[#64748b]">We haven't shared any success stories for this category yet.</p>
          <button @click="setRole('all')" class="mt-6 font-bold text-[#e63946] hover:underline">View all stories</button>
        </div>

        <div v-else>
          <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <SuccessStoryCard
              v-for="story in stories"
              :key="story.id"
              :story="story"
            />
          </div>

          <!-- Pagination / Loading more -->
          <div v-if="stories.length" ref="scrollTrigger" class="mt-16 flex justify-center">
            <div v-if="loadingMore" class="flex items-center gap-2">
              <div class="h-2 w-2 animate-bounce rounded-full bg-[#e63946]"></div>
              <div class="h-2 w-2 animate-bounce rounded-full bg-[#e63946]" style="animation-delay: 0.2s"></div>
              <div class="h-2 w-2 animate-bounce rounded-full bg-[#e63946]" style="animation-delay: 0.4s"></div>
            </div>
            <p v-else-if="finished" class="text-sm font-bold tracking-widest text-[#cbd5e1] uppercase">End of stories</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import SuccessStoryCard from '../components/SuccessStoryCard.vue';

const route = useRoute();
const router = useRouter();
const stories = ref([]);
const roles = ref([]);
const loading = ref(true);
const loadingMore = ref(false);
const finished = ref(false);
const page = ref(1);
const scrollTrigger = ref(null);
let observer = null;

const activeRole = computed(() => route.query.role || 'all');

const rolesWithAll = computed(() => [
  { name: 'All Stories', slug: 'all' },
  ...roles.value
]);

async function loadRoles() {
  try {
    const { data } = await axios.get('/api/success-stories/roles');
    roles.value = data;
  } catch (e) {
    console.error('Failed to load roles', e);
  }
}

async function load(p = 1, append = false) {
  if (append && (loadingMore.value || finished.value)) return;
  
  if (append) loadingMore.value = true;
  else loading.value = true;

  try {
    const params = {
      page: p,
      limit: 12,
    };
    
    if (activeRole.value !== 'all') {
      params.role_slug = activeRole.value;
    }

    const { data } = await axios.get('/api/success-stories', { params });
    const items = data.data || [];
    
    if (append) {
      stories.value = [...stories.value, ...items];
    } else {
      stories.value = items;
    }

    page.value = data.current_page;
    finished.value = data.current_page >= data.last_page || items.length === 0;
  } catch (e) {
    console.error('Failed to load stories', e);
    finished.value = true;
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
}

// Meta logic
const metaTitle = computed(() => {
  if (activeRole.value !== 'all') {
    const role = roles.value.find(r => r.slug === activeRole.value);
    return `${role?.name || 'Creator'} Success Stories | StarJD`;
  }
  return 'Creator & Brand Success Stories | Scaling with StarJD';
});

const metaDescription = computed(() => {
  if (activeRole.value !== 'all') {
    const role = roles.value.find(r => r.slug === activeRole.value);
    return `Discover inspiring ${role?.name || 'creator'} success stories on StarJD. See how professionals are scaling their reach and building their brands with real results.`;
  }
  return `Discover how creators, brands, and professionals are scaling their reach and impact using StarJD. See real results and success case studies from our community.`;
});

useHead({
  title: metaTitle,
  meta: [
    { name: 'description', content: metaDescription },
    { property: 'og:title', content: metaTitle },
    { property: 'og:description', content: metaDescription },
    { property: 'og:image', content: (window.location.origin + '/logo.png') },
    { property: 'og:type', content: 'website' }
  ]
});

function setRole(slug) {
  router.push({
    path: '/success-stories',
    query: slug === 'all' ? {} : { role: slug }
  });
}

function loadMore() {
  if (loading.value || loadingMore.value || finished.value) return;
  load(page.value + 1, true);
}

function setupObserver() {
  if (observer) observer.disconnect();
  observer = new IntersectionObserver((entries) => {
    if (entries[0]?.isIntersecting) loadMore();
  }, { threshold: 0.1 });
  
  if (scrollTrigger.value) observer.observe(scrollTrigger.value);
}

onMounted(async () => {
  await loadRoles();
  await load(1, false);
  await nextTick();
  setupObserver();
});

onUnmounted(() => {
  if (observer) observer.disconnect();
});

watch(() => route.query.role, () => {
  page.value = 1;
  stories.value = [];
  finished.value = false;
  load(1, false);
});

watch(() => stories.value.length, async () => {
  await nextTick();
  setupObserver();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
