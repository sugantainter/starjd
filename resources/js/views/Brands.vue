<template>
  <div class="mx-auto max-w-7xl px-4 pt-8 pb-20 md:pb-24">
    <div class="mb-10 text-center">
      <h1 class="text-4xl font-extrabold text-[#1a1a1a] tracking-tight">Discover Brands</h1>
      <p class="mt-3 text-lg text-[#64748b] max-w-2xl mx-auto">Connect with top brands and explore their active campaigns to find your next collaboration.</p>
    </div>

    <!-- Filters Section -->
    <div class="mb-12 sticky top-4 z-20">
      <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-[#e2e8f0] p-4 shadow-xl flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[300px]">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search brands, industries or campaigns..." 
            class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#e2e8f0] focus:border-[#e63946] focus:outline-none focus:ring-4 focus:ring-[#e63946]/10 transition-all bg-white" 
            @keyup.enter="refresh" 
          />
        </div>
        
        <div class="flex flex-wrap gap-2">
          <select v-model="filters.industry" class="rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#e63946] focus:outline-none bg-white text-sm font-medium">
            <option value="">All Industries</option>
            <option v-for="ind in commonIndustries" :key="ind" :value="ind">{{ ind }}</option>
          </select>
          
          <button type="button" class="cursor-link rounded-xl bg-[#e63946] px-6 py-3 text-white font-bold hover:bg-[#c1121f] transition-colors shadow-lg shadow-[#e63946]/20" @click="refresh">Search</button>
          <button type="button" class="cursor-link rounded-xl border border-[#e2e8f0] px-4 py-3 hover:bg-[#f1f5f9] transition-colors font-medium text-[#64748b]" @click="clearFilters">Reset</button>
        </div>
      </div>
    </div>

    <!-- Brands Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <router-link
        v-for="b in list"
        :key="b.id"
        :to="'/brands/' + b.slug"
        class="group bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden hover:border-[#e63946]/40 hover:shadow-2xl transition-all duration-300 flex flex-col"
      >
        <!-- Logo Header -->
        <div class="relative h-48 bg-[#f8fafc] flex items-center justify-center p-8 overflow-hidden">
          <div class="absolute inset-0 opacity-10 blur-xl transition-all duration-500 group-hover:blur-2xl" :style="{ backgroundImage: 'url(' + (b.logo_url || '') + ')', backgroundSize: 'cover', backgroundPosition: 'center' }"></div>
          <img
            :src="b.logo_url || 'https://ui-avatars.com/api?name=' + encodeURIComponent(b.company_name || b.user?.name || '') + '&size=200&background=f1f5f9&color=64748b'"
            :alt="b.company_name"
            class="relative z-10 max-h-full max-w-full object-contain drop-shadow-sm transition-transform duration-500 group-hover:scale-110"
          />
          <div v-if="b.active_campaigns_count > 0" class="absolute top-4 right-4">
             <span class="bg-[#e63946] text-white text-[10px] uppercase font-bold px-3 py-1 rounded-full shadow-lg">
               {{ b.active_campaigns_count }} Active {{ b.active_campaigns_count === 1 ? 'Campaign' : 'Campaigns' }}
             </span>
          </div>
        </div>

        <!-- Brand Info -->
        <div class="p-6 flex-1 flex flex-col">
          <div class="flex items-start justify-between mb-2">
            <h3 class="text-xl font-bold text-[#1a1a1a] group-hover:text-[#e63946] transition-colors line-clamp-1">
              {{ b.company_name || b.user?.name }}
            </h3>
            <svg v-if="b.is_verified || true" class="w-5 h-5 text-blue-500 shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
          </div>

          <p v-if="b.industry" class="text-xs font-bold text-[#94a3b8] uppercase tracking-wider mb-3">{{ b.industry }}</p>
          
          <p class="text-sm text-[#64748b] line-clamp-2 mb-6 flex-1 italic">
            "{{ b.bio || 'Connecting brands with the best creators across India.' }}"
          </p>

          <div class="mt-auto flex items-center justify-between border-t border-[#f1f5f9] pt-4">
            <span class="text-xs font-medium text-[#475569] flex items-center gap-1">
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              {{ b.hq_location || 'India' }}
            </span>
            <span class="text-[#e63946] font-bold text-sm flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              View Profile
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Infinite Scroll Trigger -->
    <div ref="scrollTrigger" class="py-12 flex justify-center">
      <div v-if="loading" class="flex items-center gap-3">
        <div class="w-2 h-2 bg-[#e63946] rounded-full animate-bounce" style="animation-delay: 0s"></div>
        <div class="w-2 h-2 bg-[#e63946] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
        <div class="w-2 h-2 bg-[#e63946] rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        <span class="text-sm font-medium text-[#64748b] ml-2">Loading brands...</span>
      </div>
      <div v-else-if="finished && list.length" class="text-[#94a3b8] font-medium text-sm border-t border-[#e2e8f0] pt-8 w-full text-center">
        End of brand partners
      </div>
      <div v-else-if="!list.length && !loading" class="text-center py-20">
         <div class="w-20 h-20 bg-[#f1f5f9] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
         </div>
         <h3 class="text-xl font-bold text-[#1a1a1a]">No brands found</h3>
         <p class="text-[#64748b] mt-1">Try adjusting your search or filters.</p>
         <button @click="clearFilters" class="mt-6 text-[#e63946] font-bold hover:underline">Clear all filters</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const list = ref([]);
const search = ref('');
const loading = ref(false);
const finished = ref(false);
const page = ref(1);
const scrollTrigger = ref(null);
let observer = null;

const commonIndustries = [
  'Fashion', 'Beauty', 'Tech', 'Food & Beverage', 'Travel', 'Fitness', 'Lifestyle', 'E-commerce', 'Entertainment'
];

const filters = reactive({ industry: '' });

function applyQueryToFilters() {
  const q = route.query;
  if (q.search != null) search.value = q.search;
  if (q.industry != null) filters.industry = q.industry;
}

onMounted(async () => {
  applyQueryToFilters();
  
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
  filters.industry = '';
  refresh();
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
    if (filters.industry) params.industry = filters.industry;
    
    const res = await axios.get('/api/brands', { params });
    const resData = res.data;
    const items = resData.data || (Array.isArray(resData) ? resData : []);
    
    if (items.length === 0) {
      finished.value = true;
    } else {
      list.value = p === 1 ? items : [...list.value, ...items];
      if (items.length < (resData.per_page || 100)) {
        finished.value = true;
      }
    }
  } catch (e) {
    console.error('Failed to load brands', e);
    finished.value = true;
  } finally {
    loading.value = false;
  }
}
</script>
