<template>
  <div class="mx-auto max-w-7xl px-4 pt-8 pb-20 md:pb-24">
    <div class="mb-10 text-center">
      <h1 class="text-4xl font-extrabold text-[#1a1a1a] tracking-tight">Discover Top Creators</h1>
      <p class="mt-3 text-lg text-[#64748b] max-w-2xl mx-auto">Connect with the world's most talented creators and influencers for your next big project.</p>
    </div>

    <!-- Enhanced Filters -->
    <div class="mb-12 sticky top-4 z-20">
      <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-[#e2e8f0] p-4 shadow-xl flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[240px]">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input v-model="search" type="text" placeholder="Search creators, niches or categories..." class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#e2e8f0] focus:border-[#fc4402] focus:outline-none focus:ring-4 focus:ring-[#fc4402]/10 transition-all bg-white" @keyup.enter="refresh" />
        </div>
        
        <div class="flex flex-wrap gap-2">
          <select v-model="filters.state_id" @change="onStateChange" class="rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#fc4402] focus:outline-none bg-white text-sm font-medium">
            <option :value="''">Any State</option>
            <option v-for="s in states" :key="s.id" :value="String(s.id)">{{ s.name }}</option>
          </select>
          <select v-model="filters.city_id" :disabled="!filters.state_id" class="rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#fc4402] focus:outline-none bg-white text-sm font-medium disabled:opacity-60">
            <option :value="''">Any City</option>
            <option v-for="c in cities" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
          </select>
          <select v-model="filters.category" class="rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#fc4402] focus:outline-none bg-white text-sm font-medium">
            <option value="">All Categories</option>
            <option v-for="c in filterOptions.categories" :key="c" :value="c">{{ c }}</option>
          </select>
          <select v-model="filters.platform" class="rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#fc4402] focus:outline-none bg-white text-sm font-medium">
            <option value="">Any Platform</option>
            <option v-for="(label, key) in filterOptions.platforms" :key="key" :value="key">{{ label }}</option>
          </select>
          <select v-model="filters.language" class="rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#fc4402] focus:outline-none bg-white text-sm font-medium">
            <option value="">Any Language</option>
            <option v-for="lang in filterOptions.languages" :key="lang" :value="lang">{{ lang }}</option>
          </select>
          
          <button type="button" class="cursor-link rounded-xl bg-[#fc4402] px-6 py-3 text-white font-bold hover:bg-[#e63d02] transition-colors shadow-lg shadow-[#fc4402]/20" @click="refresh">Search</button>
          <button type="button" class="cursor-link rounded-xl border border-[#e2e8f0] px-4 py-3 hover:bg-[#f1f5f9] transition-colors font-medium text-[#64748b]" @click="clearFilters">Reset</button>
        </div>
      </div>
    </div>

    <!-- Horizontal Listing -->
    <div class="flex flex-col gap-6">
      <router-link
        v-for="p in listWithSlug"
        :key="p.id"
        :to="'/creators/' + p.slug"
        class="group flex flex-col md:flex-row bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden hover:border-[#fc4402]/40 hover:shadow-2xl transition-all duration-300"
      >
        <!-- Image Section -->
        <div class="relative w-full md:w-72 h-64 md:h-80 overflow-hidden bg-[#f1f5f9] shrink-0">
          <img
            :src="p.avatar_url || 'https://ui-avatars.com/api?name=' + encodeURIComponent(p.user?.name || '') + '&size=400&background=fc4402&color=fff'"
            :alt="p.user?.name"
            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
          />
          <div v-if="p.is_featured" class="absolute top-4 left-4">
             <span class="bg-[#f59e0b] text-white text-[10px] uppercase font-black px-3 py-1 rounded-full shadow-lg">Featured</span>
          </div>
          <div v-if="p.category" class="absolute bottom-4 left-4">
             <span class="bg-black/60 backdrop-blur-md text-white text-xs font-semibold px-3 py-1.5 rounded-lg border border-white/20">{{ p.category }}</span>
          </div>
        </div>

        <!-- Content Section -->
        <div class="flex-1 p-6 md:p-8 flex flex-col">
          <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-2xl font-bold text-[#1a1a1a] group-hover:text-[#fc4402] transition-colors">{{ p.user?.name }}</h3>
                <svg v-if="p.is_verified" class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
              </div>
              <p v-if="p.tagline" class="text-lg text-[#475569] font-medium mb-4 line-clamp-1">{{ p.tagline }}</p>
              
              <div class="flex flex-wrap gap-4 text-sm text-[#64748b]">
                <div v-if="p.location" class="flex items-center gap-1.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  {{ p.location }}
                </div>
                <div v-if="p.language" class="flex items-center gap-1.5">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                   {{ p.language }}
                </div>
                <div v-if="p.platforms?.length" class="flex items-center gap-1.5">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                   {{ p.platforms.join(', ') }}
                </div>
              </div>
            </div>

            <div class="flex flex-col items-start md:items-end shrink-0">
               <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider mb-1">Starting from</div>
               <div class="text-3xl font-black text-[#1a1a1a]">₹{{ p.min_rate || '0' }}</div>
               <div class="text-xs text-[#64748b]">per project</div>
            </div>
          </div>

          <div class="mt-auto pt-6 flex flex-wrap items-center justify-between gap-4">
             <div class="flex -space-x-2 overflow-hidden">
                <div v-for="i in 3" :key="i" class="inline-block h-8 w-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[10px] font-bold text-[#64748b]">
                  <img :src="'https://i.pravatar.cc/100?u=' + i" />
                </div>
                <div class="inline-block h-8 w-8 rounded-full border-2 border-white bg-[#f1f5f9] flex items-center justify-center text-[10px] font-bold text-[#64748b]">
                  +12
                </div>
                <span class="ml-4 self-center text-xs font-medium text-[#64748b]">Collaborations</span>
             </div>
             
             <button class="px-8 py-3 bg-[#1a1a1a] text-white rounded-xl font-bold hover:bg-[#fc4402] transition-all transform group-hover:scale-105 shadow-lg">
                View Full Profile
             </button>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Infinite Scroll Trigger -->
    <div ref="scrollTrigger" class="py-12 flex justify-center">
      <div v-if="loading" class="flex items-center gap-3">
        <div class="w-2 h-2 bg-[#fc4402] rounded-full animate-bounce" style="animation-delay: 0s"></div>
        <div class="w-2 h-2 bg-[#fc4402] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
        <div class="w-2 h-2 bg-[#fc4402] rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        <span class="text-sm font-medium text-[#64748b] ml-2">Loading more creators...</span>
      </div>
      <div v-else-if="finished && list.length" class="text-[#94a3b8] font-medium text-sm">
        You've reached the end of the list.
      </div>
      <div v-else-if="!list.length" class="text-center py-20">
         <div class="w-20 h-20 bg-[#f1f5f9] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
         </div>
         <h3 class="text-xl font-bold text-[#1a1a1a]">No creators found</h3>
         <p class="text-[#64748b] mt-1">Try adjusting your search or filters.</p>
         <button @click="clearFilters" class="mt-6 text-[#fc4402] font-bold hover:underline">Clear all filters</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const list = ref([]);
const listWithSlug = computed(() => (list.value || []).filter((p) => p.slug));
const search = ref('');
const loading = ref(false);
const finished = ref(false);
const page = ref(1);
const scrollTrigger = ref(null);
const states = ref([]);
const cities = ref([]);
let observer = null;

const filterOptions = reactive({ categories: [], genders: {}, languages: [], platforms: {} });
const filters = reactive({ category: '', gender: '', language: '', platform: '', min_rate: '', location: '', price_range: '', state_id: '', city_id: '' });

function applyQueryToFilters() {
  const q = route.query;
  if (q.search != null) search.value = q.search;
  if (q.category != null) filters.category = q.category;
  if (q.gender != null) filters.gender = q.gender;
  if (q.language != null) filters.language = q.language;
  if (q.platform != null) filters.platform = q.platform;
  if (q.location != null) filters.location = q.location;
  if (q.price_range != null) filters.price_range = q.price_range;
  if (q.state_id != null) filters.state_id = String(q.state_id);
  if (q.city_id != null) filters.city_id = String(q.city_id);
  if (q.min_rate != null) filters.min_rate = q.min_rate === '' ? '' : Number(q.min_rate);
}

onMounted(async () => {
  const [filtersRes, statesRes] = await Promise.all([
    axios.get('/api/creators/options/filters'),
    axios.get('/api/states'),
  ]);
  filterOptions.categories = filtersRes.data.categories ?? [];
  filterOptions.genders = filtersRes.data.genders ?? {};
  filterOptions.languages = filtersRes.data.languages ?? [];
  filterOptions.platforms = filtersRes.data.platforms ?? {};
  states.value = statesRes.data ?? [];
  applyQueryToFilters();
  if (filters.state_id) {
    await loadCities(filters.state_id);
  }
  
  // Initialize Infinite Scroll
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !loading.value && !finished.value) {
      loadMore();
    }
  }, { threshold: 0.1 });
  
  if (scrollTrigger.value) {
    observer.observe(scrollTrigger.value);
  }
  
  load(1);
});

onUnmounted(() => {
  if (observer) observer.disconnect();
});

watch(() => route.query, () => {
  applyQueryToFilters();
  refresh();
}, { deep: true });

function clearFilters() {
  search.value = '';
  filters.category = '';
  filters.gender = '';
  filters.language = '';
  filters.platform = '';
  filters.location = '';
  filters.price_range = '';
  filters.min_rate = '';
  filters.state_id = '';
  filters.city_id = '';
  cities.value = [];
  refresh();
}

async function loadCities(stateId) {
  if (!stateId) {
    cities.value = [];
    return;
  }
  const res = await axios.get('/api/cities?state_id=' + stateId);
  cities.value = res.data ?? [];
}

async function onStateChange() {
  filters.city_id = '';
  await loadCities(filters.state_id);
}

function refresh() {
  page.value = 1;
  list.value = [];
  finished.value = false;
  load(1);
}

async function loadMore() {
  if (loading.value || finished.value) return;
  page.value++;
  load(page.value);
}

async function load(p = 1) {
  loading.value = true;
  try {
    const params = { page: p };
    if (search.value) params.search = search.value;
    if (filters.category) params.category = filters.category;
    if (filters.gender) params.gender = filters.gender;
    if (filters.language) params.language = filters.language;
    if (filters.platform) params.platform = filters.platform;
    if (filters.location) params.location = filters.location;
    if (filters.price_range) params.price_range = filters.price_range;
    if (filters.min_rate !== '' && filters.min_rate != null) params.min_rate = filters.min_rate;
    if (filters.state_id) params.state_id = filters.state_id;
    if (filters.city_id) params.city_id = filters.city_id;
    
    const res = await axios.get('/api/creators', { params });
    const resData = res.data;
    const items = resData.data?.data || resData.data || (Array.isArray(resData) ? resData : []);
    
    if (items.length === 0) {
      finished.value = true;
    } else {
      list.value = p === 1 ? items : [...list.value, ...items];
      // If we got fewer items than expected page size, assume finished
      if (items.length < (resData.per_page || 12)) {
        finished.value = true;
      }
    }
  } catch (e) {
    console.error('Failed to load creators', e);
    finished.value = true;
  } finally {
    loading.value = false;
  }
}
</script>
