<template>
  <div class="mx-auto max-w-[1400px] px-6 pt-12 pb-24 bg-[#f8fafc]">
    <div class="mb-14 text-center">
      <h1 class="text-5xl font-[900] text-[#0f172a] tracking-tight mb-4">Discover Top Creators</h1>
      <p class="text-xl text-[#64748b] max-w-3xl mx-auto font-medium">Connect with vetted influencers and content creators worldwide to amplify your brand's reach.</p>
    </div>

    <!-- Premium Filter Bar -->
    <div class="mb-16 sticky top-6 z-30 px-2">
      <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] border border-white shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-2.5 flex flex-wrap lg:flex-nowrap gap-3 items-center transition-all hover:shadow-[0_25px_60px_rgba(0,0,0,0.08)]">
        <div class="relative flex-1 min-w-[280px] group">
          <span class="absolute left-6 top-1/2 -translate-y-1/2 text-[#94a3b8] group-focus-within:text-[#fc4402] transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input v-model="search" type="text" placeholder="Search creators, niches or categories..." class="w-full pl-16 pr-6 py-4 rounded-[1.5rem] border-transparent focus:border-transparent focus:outline-none focus:ring-0 text-lg font-semibold text-[#1e293b] placeholder-[#94a3b8] bg-transparent" @keyup.enter="refresh" />
        </div>
        
        <div class="flex flex-wrap lg:flex-nowrap gap-2 items-center px-4">
          <div class="h-10 w-[1px] bg-slate-200 mx-2 hidden lg:block"></div>
          
          <div class="flex flex-wrap gap-2">
            <select v-model="filters.state_id" @change="onStateChange" class="premium-select">
              <option :value="''">Any State</option>
              <option v-for="s in states" :key="s.id" :value="String(s.id)">{{ s.name }}</option>
            </select>
            <select v-model="filters.city_id" :disabled="!filters.state_id" class="premium-select disabled:opacity-40">
              <option :value="''">Any City</option>
              <option v-for="c in cities" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
            </select>
            <select v-model="filters.category" class="premium-select">
              <option value="">Categories</option>
              <option v-for="c in filterOptions.categories" :key="c.slug" :value="c.name">{{ c.name }}</option>
            </select>
            <select v-model="filters.platform" class="premium-select">
              <option value="">Platform</option>
              <option v-for="(label, key) in filterOptions.platforms" :key="key" :value="key">{{ label }}</option>
            </select>
          </div>
          
          <div class="flex gap-2 ml-2">
            <button type="button" class="cursor-link rounded-2xl bg-[#0f172a] px-8 py-4 text-white font-[800] hover:bg-[#fc4402] hover:scale-[1.02] active:scale-95 transition-all duration-300 shadow-xl shadow-slate-200" @click="refresh">Search</button>
            <button type="button" class="cursor-link rounded-2xl bg-slate-100 px-6 py-4 text-[#475569] font-[700] hover:bg-slate-200 transition-all" @click="clearFilters">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Premium Horizontal Listing -->
    <div class="flex flex-col gap-6">
      <router-link
        v-for="p in listWithSlug"
        :key="p.id"
        :to="'/creator-profile/' + p.slug"
        class="group relative bg-white rounded-[1.5rem] border border-[#f1f5f9] hover:border-white shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)] transition-all duration-300 overflow-hidden"
      >
        <div class="flex flex-col xl:flex-row items-stretch">
          <!-- Left: Profile Info -->
          <div class="p-6 xl:p-8 flex flex-col md:flex-row items-center gap-6 xl:border-r border-slate-100 xl:min-w-[400px]">
            <div class="relative shrink-0">
               <!-- Premium Avatar with Background -->
               <div class="w-28 h-28 rounded-[1.8rem] p-1 bg-gradient-to-br from-[#fc4402] via-[#ff8a5c] to-[#4f46e5]">
                  <div class="w-full h-full rounded-[1.6rem] overflow-hidden bg-white">
                      <img
                        :src="p.avatar_url || 'https://ui-avatars.com/api?name=' + encodeURIComponent(p.user?.name || '') + '&size=400&background=f1f5f9&color=64748b'"
                        :alt="p.user?.name"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                  </div>
               </div>
               <!-- Action Buttons overlapping avatar -->
               <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 flex items-center bg-white rounded-full shadow-xl p-0.5 gap-0.5 border border-slate-50">
                  <button class="w-8 h-8 rounded-full flex items-center justify-center text-rose-500 hover:bg-rose-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                  <div class="w-[1px] h-4 bg-slate-100"></div>
                  <button class="w-8 h-8 rounded-full flex items-center justify-center text-teal-500 hover:bg-teal-50 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.5 3c1.557 0 3.046.727 4 2.015Q12.454 3 14 3c2.786 0 5.25 2.322 5.25 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0z"/></svg>
                  </button>
               </div>
            </div>

            <div class="flex-1 text-center md:text-left pt-2 md:pt-0">
               <div class="flex items-center justify-center md:justify-start gap-2 mb-1.5">
                  <h3 class="text-2xl font-[900] text-[#0f172a] group-hover:text-[#fc4402] transition-colors leading-tight">{{ p.user?.name }}</h3>
                  <div class="text-[#4f46e5]/80 bg-[#4f46e5]/10 p-0.5 rounded-lg">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 24c6.627 0 12-5.373 12-12S18.627 0 12 0 0 5.373 0 12s5.373 12 12 12z"/><path fill="#fff" d="M18.825 8.257c.214-.23.18-.588-.075-.776a4.83 4.83 0 00-2.834-1.107c-.12-.012-.236.04-.308.136l-3.328 4.41-1.89-1.89a.4.4 0 00-.565 0l-.826.826a.4.4 0 000 .565l2.457 2.457c.4.4 1.05.358 1.4-.083l4.004-5.32c.118-.156.326-.192.485-.098.42.247.785.57 1.08.95.2.257.564.29.81.08l.385-.306z"/></svg>
                  </div>
               </div>
               
               <div class="flex items-center justify-center md:justify-start gap-2 text-[13px] font-bold text-slate-500 mb-2.5 bg-slate-50 w-fit px-3 py-1 rounded-full mx-auto md:mx-0">
                  <span class="text-base">🇮🇳</span>
                  <span>{{ p.city_name || 'Mumbai' }}{{ p.state_name ? ', ' + p.state_name : ', MH' }}</span>
               </div>

               <p class="text-slate-600 text-sm font-medium leading-[1.5] line-clamp-2">
                  {{ p.tagline || p.bio || 'Sustainable fashion lover & content creator sharing conscious wardrobe tips.' }}
               </p>
            </div>
          </div>

          <!-- Middle: Recent Posts Gallery -->
          <div class="flex-1 p-6 xl:p-8 flex flex-col justify-center border-b xl:border-b-0 xl:border-r border-slate-100 bg-[#fafbfc]/50">
             <div class="flex items-center gap-3 overflow-x-auto pb-1 no-scrollbar">
                <template v-if="p.recent_posts && p.recent_posts.length">
                  <div v-for="(post, idx) in p.recent_posts" :key="post.id" class="relative group/post w-24 h-32 xl:w-28 xl:h-36 rounded-xl overflow-hidden shrink-0 shadow-md">
                     <img :src="post.image_url" class="w-full h-full object-cover transition-transform duration-700 group-hover/post:scale-110" />
                  </div>
                </template>
                <template v-else>
                  <!-- Placeholder Gallery -->
                  <div v-for="i in 3" :key="i" class="w-24 h-32 xl:w-28 xl:h-36 rounded-xl overflow-hidden shrink-0 bg-slate-100 border border-slate-100 flex items-center justify-center">
                      <svg class="w-6 h-6 text-slate-200" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                  </div>
                </template>
             </div>
          </div>

          <!-- Right: Stats & Tags -->
          <div class="p-6 xl:p-8 flex flex-col md:flex-row xl:flex-col justify-between gap-6 xl:w-[280px] bg-white">
             <!-- Top: Metrics -->
             <div class="flex flex-wrap items-center gap-6 xl:gap-3">
                <div class="flex flex-col">
                   <span class="text-[22px] font-black text-emerald-500 leading-none mb-0.5">{{ p.engagement_rate || '94' }}%</span>
                   <span class="text-[9px] font-[800] text-slate-400 uppercase tracking-widest leading-none">Real Rate</span>
                </div>
                
                <div class="flex flex-col gap-2.5">
                   <div v-for="sa in p.social_accounts" :key="sa.platform" class="flex items-center gap-2">
                      <div :class="['w-8 h-8 rounded-lg flex items-center justify-center text-white p-1.5 shadow-md', 
                          sa.platform === 'instagram' ? 'bg-gradient-to-tr from-[#f9ce34] via-[#ee2a7b] to-[#6228d7]' : 'bg-black' ]">
                         <img v-if="sa.platform === 'instagram'" src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" class="w-full invert brightness-0" />
                         <svg v-else fill="currentColor" viewBox="0 0 24 24" class="w-full"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.01.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.59-1.01-.01 2.62-.01 5.24-.01 7.86a7.12 7.12 0 011.08 8.44 7.21 7.21 0 01-11.83-1.02 7.13 7.13 0 014.2-10.05v4.09l-.02.46c-.52.29-.9.84-1.01 1.43a2.91 2.91 0 105.12 2.78c.01-4.74.01-9.48.01-14.22-.05-.14-.11-.29-.11-.45-.19-1.63-.5-3.23-.74-4.84.01 0-.01 0-.01-.01z"/></svg>
                      </div>
                      <span class="text-[15px] font-[900] text-[#1e293b]">{{ (sa.followers_count / 1000).toFixed(1) }}K</span>
                   </div>
                   <!-- Mocked default if no accounts -->
                   <template v-if="!p.social_accounts || !p.social_accounts.length">
                      <div class="flex items-center gap-2">
                          <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white p-1.5 bg-gradient-to-tr from-[#f9ce34] via-[#ee2a7b] to-[#6228d7]">
                              <img src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" class="w-full invert brightness-0" />
                          </div>
                          <span class="text-[15px] font-[900] text-[#1e293b]">{{ (p.total_followers / 1000).toFixed(1) || '1.2' }}M</span>
                      </div>
                   </template>
                </div>
             </div>

             <!-- Bottom Tags & Price -->
             <div class="flex flex-col gap-3">
                <div class="flex flex-wrap gap-1.5">
                   <div v-if="p.category" class="tag-pill bg-amber-50 text-amber-600 !px-3 !py-1 text-[10px]">
                      {{ p.category }}
                   </div>
                </div>
                
                <div class="flex items-end justify-between border-t border-slate-50 pt-3">
                   <div class="flex flex-col">
                      <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Price</span>
                      <span class="text-2xl font-[1000] text-[#0f172a]">₹{{ p.min_rate || '5,000' }}</span>
                   </div>
                   <div class="group-hover:translate-x-1.5 transition-transform duration-300">
                      <svg class="w-7 h-7 text-[#fc4402]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                   </div>
                </div>
             </div>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Infinite Scroll Trigger -->
    <div ref="scrollTrigger" class="py-20 flex flex-col items-center">
      <div v-if="loading" class="flex flex-col items-center gap-6">
        <div class="flex gap-2">
          <div class="w-3 h-3 bg-[#fc4402] rounded-full animate-bounce" style="animation-delay: 0s"></div>
          <div class="w-3 h-3 bg-[#fc4402] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
          <div class="w-3 h-3 bg-[#fc4402] rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
        <span class="text-lg font-bold text-[#475569]">Fetching more top talent...</span>
      </div>
      <div v-else-if="finished && list.length" class="bg-white px-10 py-4 rounded-full shadow-lg border border-slate-100 text-slate-400 font-bold text-sm tracking-widest uppercase">
        End of listing
      </div>
      <div v-else-if="!list.length" class="text-center py-20 bg-white rounded-[3rem] w-full border border-[#f1f5f9] shadow-inner">
         <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
            <svg class="w-12 h-12 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
         </div>
         <h3 class="text-3xl font-[900] text-[#0f172a] mb-2">No creators found</h3>
         <p class="text-xl text-[#64748b] font-medium">Try broadening your search or resetting all filters.</p>
         <button @click="clearFilters" class="mt-10 px-10 py-4 bg-[#0f172a] text-white rounded-2xl font-bold hover:bg-[#fc4402] transition-colors shadow-2xl">Reset Filters</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "../../css/app.css";

.premium-select {
  @apply rounded-2xl border-transparent px-6 py-4 focus:border-transparent focus:outline-none bg-slate-100 hover:bg-slate-200 text-sm font-[800] text-[#475569] transition-all cursor-pointer min-w-[140px];
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1.25rem center;
  background-size: 1rem;
  padding-right: 3rem;
}

.tag-pill {
  @apply flex items-center gap-1.5 px-4 py-2 rounded-xl text-[11px] font-[900] uppercase tracking-wider group-hover:scale-105 transition-transform;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
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

const filterOptions = reactive({ categories: [], sub_categories: [], genders: {}, languages: [], platforms: {} });
const availableSubCategories = computed(() => {
    if (!filters.category) return [];
    return filterOptions.sub_categories.filter(sc => sc.category_name === filters.category);
});
const filters = reactive({ category: '', sub_category: '', gender: '', language: '', platform: '', min_rate: '', location: '', price_range: '', state_id: '', city_id: '' });

watch(() => filters.category, () => {
  filters.sub_category = '';
});

const applyQueryToFilters = async () => {
    const p = route.params;
    const q = route.query;
    const segments = Array.isArray(p.paths) ? p.paths : (typeof p.paths === 'string' ? p.paths.split('/') : []);
    
    // Reset path-based filters before re-applying
    filters.category = '';
    filters.state_id = '';
    filters.city_id = '';
    filters.platform = '';

    for (const s of segments) {
      if (!s) continue;
      
      // 1. Try Category
      const cat = filterOptions.categories.find(c => (c.slug && c.slug.toLowerCase() === s) || c.name.toLowerCase() === s);
      if (cat) {
        filters.category = cat.name;
        continue;
      }

      // 2. Try State
      const st = states.value.find(st => (st.slug && st.slug.toLowerCase() === s) || st.name.toLowerCase() === s);
      if (st) {
        filters.state_id = String(st.id);
        continue;
      }

      // 3. Try City (only if state is known)
      if (filters.state_id) {
        try {
          const res = await axios.get('/api/cities', { params: { state_id: filters.state_id } });
          const cities = res.data || [];
          const cityRes = cities.find(ci => (ci.slug && ci.slug.toLowerCase() === s) || ci.name.toLowerCase() === s);
          if (cityRes) {
            filters.city_id = String(cityRes.id);
            continue;
          }
        } catch (err) {
          console.error('Error fetching city for segment:', s, err);
        }
      }

      // 4. Try Platform
      const lowerS = s.toLowerCase();
      if (filterOptions.platforms && (filterOptions.platforms[lowerS] || ['instagram', 'youtube', 'tiktok', 'facebook', 'linkedin', 'twitter', 'pinterest'].includes(lowerS))) {
        filters.platform = lowerS;
        continue;
      }
    }

    // Apply query parameters (if any override)
    if (q.category) filters.category = q.category;
    if (q.gender) filters.gender = q.gender;
    if (q.language) filters.language = q.language;
    if (q.state_id) filters.state_id = String(q.state_id);
    if (q.city_id) filters.city_id = String(q.city_id);
    if (q.platform) filters.platform = q.platform;
    if (q.sort) filters.sort = q.sort;
    if (q.search) search.value = String(q.search);
};

onMounted(async () => {
  const [filtersRes, statesRes] = await Promise.all([
    axios.get('/api/creators/options/filters'),
    axios.get('/api/states'),
  ]);
  filterOptions.categories = filtersRes.data.categories ?? [];
  filterOptions.sub_categories = filtersRes.data.sub_categories ?? [];
  filterOptions.genders = filtersRes.data.genders ?? {};
  filterOptions.languages = filtersRes.data.languages ?? [];
  filterOptions.platforms = filtersRes.data.platforms ?? {};
  states.value = statesRes.data ?? [];
  await applyQueryToFilters();
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

watch(() => route.params, async () => {
  await applyQueryToFilters();
  refresh();
});

function clearFilters() {
  search.value = '';
  filters.category = '';
  filters.sub_category = '';
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

  // Generate SEO Friendly URL Hierarchy
  const segments = [];
  if (filters.category) {
    const cat = filterOptions.categories.find(c => c.name === filters.category);
    segments.push(cat?.slug || filters.category.toLowerCase().replace(/ /g, '-'));
  }
  if (filters.state_id) {
    const state = states.value.find(s => String(s.id) === String(filters.state_id));
    if (state) segments.push(state.slug || state.name.toLowerCase().replace(/ /g, '-'));
  }
  if (filters.city_id) {
    const city = cities.value.find(c => String(c.id) === String(filters.city_id));
    if (city) segments.push(city.slug || city.name.toLowerCase().replace(/ /g, '-'));
  }
  if (filters.platform) {
    segments.push(filters.platform.toLowerCase());
  }

  let fullPath = '/creators';
  if (segments.length > 0) {
    fullPath += '/' + segments.join('/');
  } else if (search.value) {
    fullPath = `/creators/search/${encodeURIComponent(search.value)}`;
  }

  // Handle other query-only filters
  const query = {};
  if (filters.sub_category) query.sub_category = filters.sub_category;
  if (filters.gender) query.gender = filters.gender;
  if (filters.language) query.language = filters.language;
  if (filters.min_rate) query.min_rate = filters.min_rate;

  // Clear if same but keep query if needed
  if (route.path !== fullPath || JSON.stringify(route.query) !== JSON.stringify(query)) {
    router.push({ path: fullPath, query });
    return;
  }

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
    if (filters.sub_category) params.sub_category = filters.sub_category;
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

// Meta logic
const metaTitle = computed(() => {
  const parts = [];
  if (filters.category) parts.push(filters.category);
  else parts.push('Top');
  
  parts.push('Creators');
  
  const locParts = [];
  if (filters.city_id) {
    const city = cities.value.find(c => String(c.id) === String(filters.city_id));
    if (city) locParts.push(city.name);
  }
  if (filters.state_id) {
    const state = states.value.find(s => String(s.id) === String(filters.state_id));
    if (state) locParts.push(state.name);
  }
  
  if (locParts.length) parts.push('in ' + locParts.join(', '));
  else parts.push('in India');

  return `${parts.join(' ')} | StarJD`;
});

const metaDescription = computed(() => {
  const cat = filters.category || 'creative';
  const loc = filters.city_id ? cities.value.find(c => String(c.id) === String(filters.city_id))?.name : 'India';
  return `Find and collaborate with the best ${cat} creators and influencers in ${loc}. Browse vetted profiles, check social media analytics, and get high-performing content on StarJD.`;
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
</script>
