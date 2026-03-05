<template>
  <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-[#1a1a1a] sm:text-3xl">Studio Marketplace</h1>
        <p class="mt-1 text-sm text-[#64748b] sm:mt-2 sm:text-base">
          Book photography, film, podcast, music &amp; event spaces.
        </p>
      </div>
      <div class="mt-2 flex items-center gap-2 text-xs text-[#64748b] sm:mt-0 sm:text-sm">
        <span v-if="!mapVisible" class="hidden sm:inline">Browse curated spaces in a responsive grid.</span>
        <span v-else class="hidden sm:inline">Pan and zoom the map to explore nearby studios.</span>
        <span class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-[#e2e8f0]">
          <span class="h-2 w-2 rounded-full" :class="mapVisible ? 'bg-[#22c55e]' : 'bg-[#e5e7eb]'"></span>
          <span>{{ total }} studios</span>
        </span>
      </div>
    </div>

    <div class="mt-5 flex flex-col gap-3 rounded-2xl bg-white/80 p-3 shadow-sm ring-1 ring-[#e5e7eb]/60 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:p-4">
      <div class="flex flex-wrap items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-xl border border-[#e2e8f0] px-4 py-2 text-sm font-medium text-[#1f2937] transition hover:border-[#e63946] hover:bg-[#fef2f2] hover:text-[#b91c1c] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20 lg:hidden"
          :class="showFilters ? 'border-[#e63946] bg-[#e63946] text-white hover:bg-[#b91c1c] hover:text-white' : ''"
          @click="showFilters = !showFilters"
        >
          <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M4 6h16M6 12h12M10 18h8"
            />
          </svg>
          <span>Filters</span>
        </button>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-xl border border-[#e2e8f0] px-3 py-2 text-sm font-medium text-[#1f2937] transition hover:border-[#0ea5e9] hover:bg-[#f0f9ff] hover:text-[#0369a1]"
          @click="mapVisible = !mapVisible"
        >
          <span class="inline-flex h-2 w-2 rounded-full" :class="mapVisible ? 'bg-[#0ea5e9]' : 'bg-[#e5e7eb]'"></span>
          <span class="hidden xs:inline">{{ mapVisible ? 'Show list view' : 'Show map view' }}</span>
          <span class="xs:hidden">{{ mapVisible ? 'List view' : 'Map view' }}</span>
        </button>
      </div>
      <div class="flex flex-wrap items-center gap-2 sm:justify-end">
        <select
          v-model="sort"
          class="min-w-[180px] rounded-xl border border-[#e2e8f0] bg-white px-3 py-2 text-sm text-[#111827] shadow-sm focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
          @change="load(1)"
        >
          <option value="newest">Newest</option>
          <option value="price_low">Price: Low to High</option>
          <option value="price_high">Price: High to Low</option>
          <option value="rating">Rating</option>
        </select>
        <span class="text-sm text-[#64748b] sm:pl-1">{{ total }} {{ total === 1 ? 'studio' : 'studios' }}</span>
      </div>
    </div>

    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">
      <div
        v-show="showFilters"
        class="lg:block lg:w-72 lg:flex-none"
        :class="{ 'hidden': !showFilters }"
      >
        <FilterSidebar
          :categories="categories"
          :amenities="amenities"
          :model="filters"
          @update:category="filters.category = $event"
          @update:city="filters.city = $event"
          @update:min_price="filters.min_price = $event"
          @update:max_price="filters.max_price = $event"
          @update:featured="filters.featured = $event"
          @update:rating="filters.rating = $event"
          @update:amenities="filters.amenities = $event"
          @apply="load(1)"
          @clear="clearFilters(); load(1)"
        />
      </div>

      <div class="min-w-0 flex-1">
        <div
          v-if="mapVisible"
          class="mb-4 rounded-2xl border border-dashed border-[#cbd5f5] bg-[#eff6ff] p-6 text-center text-sm text-[#1d4ed8] sm:p-8"
        >
          Map view is enabled. Integrate your preferred map provider (Google Maps, Mapbox, etc.) here to show studios by
          location.
        </div>
        <div
          v-else
          class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:gap-6 xl:grid-cols-3"
        >
          <StudioCard v-for="s in list" :key="s.id" :studio="s" />
        </div>

        <div
          v-if="!list.length && !loading"
          class="mt-12 rounded-2xl border border-dashed border-[#e2e8f0] bg-white/80 p-8 text-center text-[#64748b]"
        >
          No studios found. Try adjusting filters or widening your search.
        </div>
        <div v-if="loading" class="mt-12 text-center text-sm text-[#64748b]">Loading…</div>

        <div
          v-if="lastPage > 1"
          class="mt-8 flex flex-wrap items-center justify-center gap-2 text-sm"
        >
          <button
            type="button"
            class="rounded-lg border border-[#e2e8f0] bg-white px-4 py-2 text-sm font-medium text-[#111827] shadow-sm transition hover:border-[#e63946] hover:text-[#e63946] disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="currentPage <= 1"
            @click="load(currentPage - 1)"
          >
            Previous
          </button>
          <span class="flex items-center px-4 py-2 text-sm text-[#64748b]">
            Page
            <span class="mx-1 inline-flex h-7 min-w-[2.25rem] items-center justify-center rounded-full bg-[#f3f4f6] px-2 text-sm font-medium text-[#111827]">
              {{ currentPage }}
            </span>
            of {{ lastPage }}
          </span>
          <button
            type="button"
            class="rounded-lg border border-[#e2e8f0] bg-white px-4 py-2 text-sm font-medium text-[#111827] shadow-sm transition hover:border-[#e63946] hover:text-[#e63946] disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="currentPage >= lastPage"
            @click="load(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import StudioCard from '../components/studio/StudioCard.vue';
import FilterSidebar from '../components/studio/FilterSidebar.vue';

const route = useRoute();
const list = ref([]);
const loading = ref(false);
const showFilters = ref(false);
const mapVisible = ref(false);
const categories = ref([]);
const amenities = ref([]);
const sort = ref('newest');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const filters = reactive({
  category: '',
  city: '',
  min_price: '',
  max_price: '',
  amenities: [],
  featured: false,
  rating: '',
});

function clearFilters() {
  filters.category = '';
  filters.city = '';
  filters.min_price = '';
  filters.max_price = '';
  filters.amenities = [];
  filters.featured = false;
  filters.rating = '';
}

function buildParams(page = 1) {
  const params = { page, sort: sort.value };
  if (filters.category) params.category = filters.category;
  if (filters.city) params.city = filters.city;
  if (filters.min_price !== '' && filters.min_price != null) params.min_price = filters.min_price;
  if (filters.max_price !== '' && filters.max_price != null) params.max_price = filters.max_price;
  if (filters.featured) params.featured = 1;
  if (filters.rating !== '' && filters.rating != null) params.rating = filters.rating;
  if (filters.amenities?.length) params.amenities = filters.amenities.join(',');
  return params;
}

async function load(page = 1) {
  loading.value = true;
  try {
    const res = await axios.get('/api/studios', { params: buildParams(page) });
    const data = res.data?.data ?? res.data;
    list.value = Array.isArray(data) ? data : (data && data.data) ? data.data : [];
    currentPage.value = res.data?.current_page ?? 1;
    lastPage.value = res.data?.last_page ?? 1;
    total.value = res.data?.total ?? list.value.length;
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  const [catRes, amRes] = await Promise.all([
    axios.get('/api/studios/categories'),
    axios.get('/api/amenities'),
  ]);
  categories.value = catRes.data ?? [];
  amenities.value = amRes.data ?? [];
  load(1);
});

watch([sort], () => load(1));
watch(() => route.query, () => load(route.query.page ? Number(route.query.page) : 1), { deep: true });
</script>
