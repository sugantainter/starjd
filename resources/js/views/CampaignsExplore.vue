<template>
  <div class="mx-auto max-w-7xl px-4 py-8 md:py-12">
    <div class="mb-8">
      <h1 class="text-3xl font-bold tracking-tight text-[#1a1a1a] md:text-4xl">Explore Campaigns</h1>
      <p class="mt-2 text-lg text-[#64748b]">Find open influencer marketing campaigns and show your interest. Apply to run campaigns with top brands.</p>
    </div>

    <!-- Filters -->
    <div class="rounded-2xl border border-[#e2e8f0] bg-white p-4 shadow-sm md:p-5">
      <div class="flex flex-wrap items-end gap-3">
        <div class="min-w-[200px] flex-1">
          <label class="mb-1 block text-xs font-medium text-[#64748b]">Search</label>
          <input
            v-model="filters.q"
            type="text"
            placeholder="Campaign title or description..."
            class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] placeholder-[#94a3b8] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
            @keyup.enter="load(1)"
          />
        </div>
        <div class="w-40">
          <label class="mb-1 block text-xs font-medium text-[#64748b]">Type</label>
          <select
            v-model="filters.campaign_type"
            class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
          >
            <option value="">All</option>
            <option v-for="t in filterOptions.campaign_types" :key="t" :value="t">{{ typeLabel(t) }}</option>
          </select>
        </div>
        <div class="w-40">
          <label class="mb-1 block text-xs font-medium text-[#64748b]">Niche</label>
          <select
            v-model="filters.niche"
            class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
          >
            <option value="">All</option>
            <option v-for="n in filterOptions.niches" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>
        <div class="w-40">
          <label class="mb-1 block text-xs font-medium text-[#64748b]">Country</label>
          <select
            v-model="filters.country"
            class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
          >
            <option value="">All</option>
            <option v-for="c in filterOptions.countries" :key="c" :value="c">{{ c }}</option>
          </select>
        </div>
        <div class="w-32">
          <label class="mb-1 block text-xs font-medium text-[#64748b]">Min budget (₹)</label>
          <input
            v-model.number="filters.budget_min"
            type="number"
            min="0"
            step="1000"
            placeholder="0"
            class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
          />
        </div>
        <div class="w-32">
          <label class="mb-1 block text-xs font-medium text-[#64748b]">Max budget (₹)</label>
          <input
            v-model.number="filters.budget_max"
            type="number"
            min="0"
            step="1000"
            placeholder="Any"
            class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
          />
        </div>
        <div class="flex gap-2">
          <button
            type="button"
            class="rounded-xl bg-[#e63946] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#c1121f] focus:outline-none focus:ring-2 focus:ring-[#e63946] focus:ring-offset-2"
            @click="load(1)"
          >
            Apply filters
          </button>
          <button
            type="button"
            class="rounded-xl border border-[#e2e8f0] bg-white px-5 py-2.5 text-sm font-medium text-[#64748b] transition hover:bg-[#f8fafc]"
            @click="clearFilters"
          >
            Clear
          </button>
        </div>
      </div>
    </div>

    <!-- Results -->
    <div v-if="loading" class="mt-10 flex justify-center py-16">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent" />
    </div>
    <div v-else-if="!list.length" class="mt-10 rounded-2xl border border-[#e2e8f0] bg-[#f8fafc] p-12 text-center">
      <p class="text-[#64748b]">No open campaigns match your filters.</p>
      <router-link to="/campaign" class="mt-4 inline-block text-[#e63946] font-medium hover:underline">Campaign home</router-link>
    </div>
    <div v-else class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <article
        v-for="c in list"
        :key="c.id"
        class="flex flex-col overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md"
      >
        <div class="flex flex-1 flex-col p-5">
          <div class="flex items-start justify-between gap-2">
            <span class="rounded-full bg-[#e63946]/10 px-2.5 py-1 text-xs font-semibold text-[#e63946]">{{ typeLabel(c.campaign_type) }}</span>
            <span v-if="c.brand?.name" class="text-xs text-[#64748b]">by {{ c.brand.name }}</span>
          </div>
          <h2 class="mt-3 line-clamp-2 text-lg font-bold text-[#1a1a1a]">
            <router-link :to="'/campaigns/' + (c.slug || c.id)" class="hover:text-[#e63946]">{{ c.title }}</router-link>
          </h2>
          <p v-if="c.description" class="mt-2 line-clamp-3 text-sm text-[#64748b]">{{ c.description }}</p>
          <div class="mt-4 flex flex-wrap gap-2">
            <span v-if="c.budget != null && c.budget > 0" class="rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-sm font-medium text-[#475569]">Budget ₹{{ formatNumber(c.budget) }}</span>
            <span v-if="c.max_applications" class="rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-sm text-[#475569]">{{ c.max_applications }} creators</span>
            <span v-if="c.applications_count != null" class="rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-sm text-[#475569]">{{ c.applications_count }} applied</span>
          </div>
          <router-link
            :to="'/campaigns/' + (c.slug || c.id)"
            class="mt-5 inline-flex items-center justify-center rounded-xl bg-[#e63946] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#c1121f]"
          >
            View & show interest
            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </router-link>
        </div>
      </article>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage > 1" class="mt-10 flex flex-wrap items-center justify-center gap-2">
      <button
        type="button"
        class="rounded-lg border border-[#e2e8f0] bg-white px-4 py-2 text-sm font-medium text-[#64748b] disabled:opacity-50"
        :disabled="currentPage <= 1"
        @click="load(currentPage - 1)"
      >
        Previous
      </button>
      <span class="px-4 text-sm text-[#64748b]">Page {{ currentPage }} of {{ lastPage }}</span>
      <button
        type="button"
        class="rounded-lg border border-[#e2e8f0] bg-white px-4 py-2 text-sm font-medium text-[#64748b] disabled:opacity-50"
        :disabled="currentPage >= lastPage"
        @click="load(currentPage + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const list = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
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
  load(1);
}

async function load(page = 1) {
  loading.value = true;
  try {
    const params = { page, per_page: 12 };
    if (filters.q) params.q = filters.q;
    if (filters.campaign_type) params.campaign_type = filters.campaign_type;
    if (filters.niche) params.niche = filters.niche;
    if (filters.country) params.country = filters.country;
    if (filters.budget_min !== '' && filters.budget_min != null) params.budget_min = filters.budget_min;
    if (filters.budget_max !== '' && filters.budget_max != null) params.budget_max = filters.budget_max;
    const res = await axios.get('/api/campaigns', { params });
    const data = res.data;
    list.value = data.data ?? data.campaigns ?? [];
    currentPage.value = data.current_page ?? 1;
    lastPage.value = data.last_page ?? 1;
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  applyQueryToFilters();
  loadFilters();
  load(1);
});

watch(() => route.query, () => {
  applyQueryToFilters();
  load(1);
}, { deep: true });
</script>
