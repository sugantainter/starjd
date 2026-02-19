<template>
  <div class="mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl font-bold text-[#1a1a1a]">Discover Creators</h1>
    <p class="mt-2 text-[#64748b]">Find creators to collaborate with.</p>
    <div class="mt-6 flex flex-wrap gap-2">
      <input v-model="search" type="text" placeholder="Keywords, niches or categories..." class="min-w-[200px] flex-1 rounded-xl border border-[#e2e8f0] px-4 py-2 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" @keyup.enter="load(1)" />
      <select v-model="filters.category" class="rounded-xl border border-[#e2e8f0] px-4 py-2 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
        <option value="">Category</option>
        <option v-for="c in filterOptions.categories" :key="c" :value="c">{{ c }}</option>
      </select>
      <select v-model="filters.gender" class="rounded-xl border border-[#e2e8f0] px-4 py-2 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
        <option value="">Gender</option>
        <option v-for="(label, key) in filterOptions.genders" :key="key" :value="key">{{ label }}</option>
      </select>
      <select v-model="filters.language" class="rounded-xl border border-[#e2e8f0] px-4 py-2 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
        <option value="">Language</option>
        <option v-for="lang in filterOptions.languages" :key="lang" :value="lang">{{ lang }}</option>
      </select>
      <select v-model="filters.platform" class="rounded-xl border border-[#e2e8f0] px-4 py-2 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
        <option value="">Platform</option>
        <option v-for="(label, key) in filterOptions.platforms" :key="key" :value="key">{{ label }}</option>
      </select>
      <input v-model.number="filters.min_rate" type="number" min="0" step="100" placeholder="Min price (₹)" class="w-32 rounded-xl border border-[#e2e8f0] px-4 py-2 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
      <button type="button" class="cursor-link rounded-xl bg-[#e63946] px-4 py-2 text-white hover:bg-[#c1121f]" @click="load(1)">Search</button>
      <button type="button" class="cursor-link rounded-xl border border-[#e2e8f0] px-4 py-2 hover:bg-[#f1f5f9]" @click="clearFilters">Clear all</button>
    </div>
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <router-link
        v-for="p in list"
        :key="p.id"
        :to="'/creators/' + p.slug"
        class="group flex flex-col overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md"
      >
        <div class="relative aspect-[4/5] shrink-0 overflow-hidden bg-[#f1f5f9]">
          <img
            :src="p.avatar_url || 'https://ui-avatars.com/api?name=' + encodeURIComponent(p.user?.name || '') + '&size=400&background=e63946&color=fff'"
            :alt="p.user?.name"
            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
          />
          <span v-if="p.is_featured" class="absolute left-2 top-2 rounded-full bg-[#f59e0b] px-2 py-0.5 text-xs font-medium text-white">Featured</span>
          <span v-if="p.category" class="absolute bottom-2 left-2 rounded bg-gray-800/80 px-2 py-0.5 text-xs font-medium text-white">{{ p.category }}</span>
        </div>
        <div class="flex flex-1 flex-col p-4">
          <h3 class="font-semibold text-[#1a1a1a]">{{ p.user?.name }}</h3>
          <p v-if="p.tagline" class="mt-1 line-clamp-2 text-sm text-[#64748b]">{{ p.tagline }}</p>
          <p v-if="p.min_rate != null" class="mt-2 text-base font-semibold text-[#e63946]">From ₹{{ p.min_rate }}/project</p>
          <span class="mt-4 inline-block rounded-lg bg-[#e63946] px-4 py-2 text-center text-sm font-medium text-white transition group-hover:bg-[#c1121f]">View profile</span>
        </div>
      </router-link>
    </div>
    <div v-if="!list.length && !loading" class="mt-12 text-center text-[#64748b]">No creators found.</div>
    <div v-if="loading" class="mt-12 text-center text-[#64748b]">Loading…</div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const list = ref([]);
const search = ref('');
const loading = ref(false);
const filterOptions = reactive({ categories: [], genders: {}, languages: [], platforms: {} });
const filters = reactive({ category: '', gender: '', language: '', platform: '', min_rate: '' });

function applyQueryToFilters() {
  const q = route.query;
  if (q.search != null) search.value = q.search;
  if (q.category != null) filters.category = q.category;
  if (q.gender != null) filters.gender = q.gender;
  if (q.language != null) filters.language = q.language;
  if (q.platform != null) filters.platform = q.platform;
  if (q.min_rate != null) filters.min_rate = q.min_rate === '' ? '' : Number(q.min_rate);
}

onMounted(async () => {
  const res = await axios.get('/api/creators/options/filters');
  filterOptions.categories = res.data.categories ?? [];
  filterOptions.genders = res.data.genders ?? {};
  filterOptions.languages = res.data.languages ?? [];
  filterOptions.platforms = res.data.platforms ?? {};
  applyQueryToFilters();
  load(1);
});

watch(() => route.query, () => {
  applyQueryToFilters();
  load(1);
}, { deep: true });

function clearFilters() {
  search.value = '';
  filters.category = '';
  filters.gender = '';
  filters.language = '';
  filters.platform = '';
  filters.min_rate = '';
  load(1);
}

async function load(page = 1) {
  loading.value = true;
  try {
    const params = { page };
    if (search.value) params.search = search.value;
    if (filters.category) params.category = filters.category;
    if (filters.gender) params.gender = filters.gender;
    if (filters.language) params.language = filters.language;
    if (filters.platform) params.platform = filters.platform;
    if (filters.min_rate !== '' && filters.min_rate != null) params.min_rate = filters.min_rate;
    const res = await axios.get('/api/creators', { params });
    const data = res.data?.data ?? res.data;
    list.value = Array.isArray(data) ? data : (data && data.data) ? data.data : [];
  } finally {
    loading.value = false;
  }
}
</script>
