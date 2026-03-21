<template>
  <div class="mx-auto max-w-7xl px-4 py-8 md:py-16">
    <div class="mb-12">
      <h1 class="text-5xl font-black tracking-tight text-[#1a1a1a] mb-4">Marketplace Campaigns</h1>
      <p class="text-xl text-[#64748b] max-w-3xl">Apply to the world's most exciting influencer marketing campaigns. Work with top brands and showcase your creative talent.</p>
    </div>

    <!-- Enhanced Filters -->
    <div class="rounded-3xl border border-[#e2e8f0] bg-white p-6 md:p-8 shadow-xl mb-12 sticky top-4 z-20">
      <div class="flex flex-wrap items-end gap-4">
        <div class="min-w-[280px] flex-1">
          <label class="mb-2 block text-xs font-black uppercase tracking-widest text-[#94a3b8]">Search Campaigns</label>
          <div class="relative">
            <input
              v-model="filters.q"
              type="text"
              placeholder="Campaign title, brand or keywords..."
              class="w-full rounded-2xl border border-[#e2e8f0] pl-12 pr-4 py-4 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-4 focus:ring-[#e63946]/10 transition-all"
              @keyup.enter="refresh"
            />
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
        </div>
        <div class="w-48">
          <label class="mb-2 block text-xs font-black uppercase tracking-widest text-[#94a3b8]">Platform</label>
          <select
            v-model="filters.campaign_type"
            class="w-full rounded-2xl border border-[#e2e8f0] px-4 py-4 text-[#1a1a1a] font-bold focus:border-[#e63946] focus:outline-none appearance-none bg-no-repeat bg-[right_1rem_center]"
            style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23CBD5E1%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-size: .65em auto;"
          >
            <option value="">All Platforms</option>
            <option v-for="t in filterOptions.campaign_types" :key="t" :value="t">{{ typeLabel(t) }}</option>
          </select>
        </div>
        <div class="flex gap-3">
          <button
            type="button"
            class="rounded-2xl bg-[#e63946] px-8 py-4 text-sm font-black text-white shadow-xl shadow-[#e63946]/30 transition hover:bg-[#c1121f] transform hover:scale-105"
            @click="refresh"
          >
            Update Results
          </button>
          <button
            type="button"
            class="rounded-2xl border border-[#e2e8f0] bg-white px-6 py-4 text-sm font-bold text-[#64748b] transition hover:bg-[#f8fafc]"
            @click="clearFilters"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Campaigns List -->
    <div class="flex flex-col gap-8">
      <article
        v-for="c in list"
        :key="c.id"
        class="group flex flex-col md:flex-row overflow-hidden rounded-3xl border border-[#e2e8f0] bg-white hover:border-[#e63946]/40 hover:shadow-2xl transition-all duration-500"
      >
        <!-- Campaign Image/Brand -->
        <div class="w-full md:w-80 h-64 md:h-auto overflow-hidden bg-gradient-to-br from-[#f8fafc] to-[#f1f5f9] shrink-0 relative flex items-center justify-center p-8">
           <div class="text-center">
              <div class="w-24 h-24 bg-white rounded-3xl shadow-xl flex items-center justify-center mx-auto mb-4 border border-[#e2e8f0] group-hover:scale-110 transition-transform duration-500">
                 <span class="text-3xl font-black text-[#e63946]">{{ (c.brand?.name || 'C').charAt(0) }}</span>
              </div>
              <p class="text-sm font-black text-[#1a1a1a] uppercase tracking-widest">{{ c.brand?.name || 'StarJD Partner' }}</p>
           </div>
           <div class="absolute top-4 left-4">
              <span class="bg-black text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-tighter">{{ typeLabel(c.campaign_type) }}</span>
           </div>
        </div>

        <!-- Campaign Stats & Info -->
        <div class="flex-1 p-8 md:p-10 flex flex-col">
          <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-6">
            <div class="flex-1">
              <h2 class="text-3xl font-black text-[#1a1a1a] mb-3 group-hover:text-[#e63946] transition-colors leading-tight">
                <router-link :to="'/campaigns/' + (c.slug || c.id)">{{ c.title }}</router-link>
              </h2>
              <p v-if="c.description" class="text-[#64748b] text-lg line-clamp-2 leading-relaxed">{{ c.description }}</p>
            </div>
            
            <div class="flex flex-col items-start md:items-end shrink-0">
               <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">Total Budget</div>
               <div class="text-4xl font-black text-[#1a1a1a]">₹{{ formatNumber(c.budget || 0) }}</div>
            </div>
          </div>

          <div class="mt-auto flex flex-wrap items-center justify-between gap-6 pt-8 border-t border-[#f1f5f9]">
            <div class="flex flex-wrap gap-8">
               <div class="flex flex-col">
                  <span class="text-[10px] font-bold text-[#94a3b8] uppercase mb-1">Open Slots</span>
                  <span class="text-lg font-black text-[#1a1a1a]">{{ c.max_applications || 'Unlimited' }}</span>
               </div>
               <div class="flex flex-col">
                  <span class="text-[10px] font-bold text-[#94a3b8] uppercase mb-1">Applied</span>
                  <span class="text-lg font-black text-[#e63946]">{{ c.applications_count || 0 }}</span>
               </div>
               <div class="flex flex-col">
                  <span class="text-[10px] font-bold text-[#94a3b8] uppercase mb-1">Niche</span>
                  <span class="text-lg font-black text-[#1a1a1a] capitalize">{{ c.niche || 'General' }}</span>
               </div>
            </div>

            <router-link
              :to="'/campaigns/' + (c.slug || c.id)"
              class="inline-flex items-center justify-center rounded-2xl bg-[#1a1a1a] px-8 py-4 text-sm font-black text-white transition hover:bg-[#e63946] shadow-xl shadow-black/10 group-hover:scale-105"
            >
              Apply Now
              <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </router-link>
          </div>
        </div>
      </article>
    </div>

    <!-- Infinite Scroll Trigger -->
    <div ref="scrollTrigger" class="py-20 flex justify-center">
      <div v-if="loading" class="flex items-center gap-3">
        <div class="w-2 h-2 bg-[#e63946] rounded-full animate-bounce" style="animation-delay: 0s"></div>
        <div class="w-2 h-2 bg-[#e63946] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
        <div class="w-2 h-2 bg-[#e63946] rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
      </div>
      <div v-else-if="finished && list.length" class="text-[#cbd5e1] font-black uppercase tracking-widest text-xs">
        All campaigns loaded
      </div>
      <div v-else-if="!list.length" class="text-center py-20 bg-[#f8fafc] rounded-3xl border border-[#e2e8f0] w-full">
         <p class="text-[#64748b] text-lg font-medium">No campaigns match your current filters.</p>
         <button @click="clearFilters" class="mt-6 text-[#e63946] font-black hover:underline uppercase tracking-widest text-sm">Clear everything</button>
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
const loading = ref(false);
const finished = ref(false);
const page = ref(1);
const scrollTrigger = ref(null);
let observer = null;

const filterOptions = reactive({ campaign_types: [], niches: [], countries: [] });
const filters = reactive({
  q: '',
  campaign_type: '',
  niche: '',
  country: '',
  budget_min: '',
  budget_max: '',
});

function applyQueryToFilters() {
  if (route.query.niche != null) filters.niche = route.query.niche;
  if (route.query.country != null) filters.country = route.query.country;
  if (route.query.campaign_type != null) filters.campaign_type = route.query.campaign_type;
}

function typeLabel(type) {
  const map = { instagram: 'Instagram', tiktok: 'TikTok', ugc: 'UGC', youtube: 'YouTube' };
  return type ? (map[type] || type) : '';
}

function formatNumber(n) {
  if (n >= 100000) return (n / 100000).toFixed(1) + 'L';
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
  return n;
}

async function loadFilters() {
  try {
    const res = await axios.get('/api/campaigns/filters');
    filterOptions.campaign_types = (res.data.campaign_types && res.data.campaign_types.length) ? res.data.campaign_types : ['instagram', 'tiktok', 'ugc', 'youtube'];
    filterOptions.niches = res.data.niches ?? [];
    filterOptions.countries = res.data.countries ?? [];
  } catch (_) {
    filterOptions.campaign_types = ['instagram', 'tiktok', 'ugc', 'youtube'];
  }
}

function clearFilters() {
  filters.q = '';
  filters.campaign_type = '';
  filters.niche = '';
  filters.country = '';
  filters.budget_min = '';
  filters.budget_max = '';
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
    const params = { page: p, per_page: 12 };
    if (filters.q) params.q = filters.q;
    if (filters.campaign_type) params.campaign_type = filters.campaign_type;
    if (filters.niche) params.niche = filters.niche;
    if (filters.country) params.country = filters.country;
    if (filters.budget_min !== '' && filters.budget_min != null) params.budget_min = filters.budget_min;
    if (filters.budget_max !== '' && filters.budget_max != null) params.budget_max = filters.budget_max;
    
    const res = await axios.get('/api/campaigns', { params });
    const data = res.data;
    const items = data.data ?? data.campaigns ?? [];
    
    if (items.length === 0) {
      finished.value = true;
    } else {
      list.value = p === 1 ? items : [...list.value, ...items];
      if (items.length < (data.per_page || 12)) {
        finished.value = true;
      }
    }
  } catch (e) {
    console.error('Failed to load campaigns', e);
    finished.value = true;
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  applyQueryToFilters();
  loadFilters();
  
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
</script>
