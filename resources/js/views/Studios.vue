<template>
  <div class="mx-auto max-w-7xl px-4 py-8">
    <h1 class="text-3xl font-bold text-[#1a1a1a]">Studio Marketplace</h1>
    <p class="mt-2 text-[#64748b]">Book photography, film, podcast, music & event spaces.</p>

    <div class="mt-6 flex flex-wrap items-center gap-2">
      <button
        type="button"
        class="rounded-xl border px-4 py-2 text-sm transition lg:hidden"
        :class="showFilters ? 'border-[#e63946] bg-[#e63946] text-white' : 'border-[#e2e8f0] hover:bg-[#f1f5f9]'"
        @click="showFilters = !showFilters"
      >
        Filters
      </button>
      <select
        v-model="sort"
        class="rounded-xl border border-[#e2e8f0] px-4 py-2 text-sm focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
        @change="load(1)"
      >
        <option value="newest">Newest</option>
        <option value="price_low">Price: Low to High</option>
        <option value="price_high">Price: High to Low</option>
        <option value="rating">Rating</option>
      </select>
      <span class="ml-auto text-sm text-[#64748b]">{{ total }} studios</span>
    </div>

    <div class="mt-6 flex flex-col gap-6 lg:flex-row">
      <div
        v-show="showFilters"
        class="lg:block"
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
        <div v-if="mapVisible" class="mb-4 rounded-xl border border-[#e2e8f0] bg-[#f1f5f9] p-8 text-center text-[#64748b]">
          Map view (toggle off to see list). Integrate your map provider here.
        </div>
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
          <StudioCard v-for="s in list" :key="s.id" :studio="s" />
        </div>

        <div v-if="!list.length && !loading" class="mt-12 text-center text-[#64748b]">No studios found. Try adjusting filters.</div>
        <div v-if="loading" class="mt-12 text-center text-[#64748b]">Loading…</div>

        <div v-if="lastPage > 1" class="mt-8 flex justify-center gap-2">
          <button
            type="button"
            class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm disabled:opacity-50"
            :disabled="currentPage <= 1"
            @click="load(currentPage - 1)"
          >
            Previous
          </button>
          <span class="flex items-center px-4 py-2 text-sm text-[#64748b]">Page {{ currentPage }} of {{ lastPage }}</span>
          <button
            type="button"
            class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm disabled:opacity-50"
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
