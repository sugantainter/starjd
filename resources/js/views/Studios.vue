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
          class="relative mb-4 overflow-hidden rounded-2xl border border-[#e2e8f0] bg-[#f1f5f9]"
          style="min-height: 420px;"
        >
          <div ref="mapContainer" class="h-full min-h-[420px] w-full rounded-2xl"></div>
          <button
            type="button"
            :disabled="myLocationLoading"
            class="absolute right-3 top-3 z-[1000] flex items-center gap-2 rounded-xl border border-[#e2e8f0] bg-white px-3 py-2 text-sm font-medium text-[#1a1a1a] shadow-md transition hover:border-[#0ea5e9] hover:bg-[#f0f9ff] hover:text-[#0369a1] disabled:opacity-60"
            title="Center on my location"
            @click="centerOnMyLocation"
          >
            <svg v-if="myLocationLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <svg v-else class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ myLocationLoading ? 'Getting…' : 'My location' }}
          </button>
          <p
            v-if="studiosWithCoords.length === 0 && !loading"
            class="absolute inset-0 flex items-center justify-center bg-[#f8fafc]/90 text-sm text-[#64748b]"
          >
            No studios with location on this page. Add latitude/longitude to studios to see them on the map.
          </p>
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
import { ref, reactive, onMounted, watch, computed, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import StudioCard from '../components/studio/StudioCard.vue';
import FilterSidebar from '../components/studio/FilterSidebar.vue';

const route = useRoute();
const list = ref([]);
const loading = ref(false);
const showFilters = ref(false);
const mapVisible = ref(false);
const mapContainer = ref(null);
const mapInstance = ref(null);
const markersLayer = ref(null);
const myLocationMarker = ref(null);
const myLocationLoading = ref(false);
const categories = ref([]);
const amenities = ref([]);
const sort = ref('newest');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const studiosWithCoords = computed(() =>
  list.value.filter((s) => s.latitude != null && s.longitude != null && !Number.isNaN(s.latitude) && !Number.isNaN(s.longitude))
);

const defaultCenter = [20.5937, 78.9629];
const defaultZoom = 5;

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

function initMap() {
  if (!mapContainer.value || mapInstance.value) return;
  const map = L.map(mapContainer.value, {
    center: defaultCenter,
    zoom: defaultZoom,
    scrollWheelZoom: true,
  });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(map);
  mapInstance.value = map;
  markersLayer.value = L.layerGroup().addTo(map);
  updateMapMarkers();
}

function updateMapMarkers() {
  const map = mapInstance.value;
  const layer = markersLayer.value;
  if (!map || !layer) return;
  layer.clearLayers();
  const studios = studiosWithCoords.value;
  if (studios.length === 0) return;
  const bounds = [];
  const icon = L.divIcon({
    className: 'studio-marker',
    html: '<span style="background:#e63946;width:14px;height:14px;border-radius:50%;display:block;border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.3)"></span>',
    iconSize: [14, 14],
    iconAnchor: [7, 7],
  });
  studios.forEach((s) => {
    const lat = Number(s.latitude);
    const lng = Number(s.longitude);
    const marker = L.marker([lat, lng], { icon }).addTo(layer);
    const url = '/studios/' + (s.slug || s.id);
    const name = (s.name || 'Studio').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const price = s.price_per_hour != null ? `₹${s.price_per_hour}/hr` : s.price_per_day != null ? `₹${s.price_per_day}/day` : '';
    marker.bindPopup(
      `<div class="min-w-[160px]"><a href="${url}" class="font-semibold text-[#1a1a1a] hover:text-[#e63946]">${name}</a>${s.city ? `<p class="mt-0.5 text-xs text-[#64748b]">${String(s.city).replace(/</g, '&lt;')}</p>` : ''}${price ? `<p class="mt-1 text-sm font-medium text-[#e63946]">${price}</p>` : ''}<a href="${url}" class="mt-2 inline-block text-sm text-[#e63946] hover:underline">View details →</a></div>`,
      { maxWidth: 280 }
    );
    bounds.push([lat, lng]);
  });
  if (bounds.length === 1) {
    map.setView(bounds[0], 12);
  } else if (bounds.length > 1) {
    map.fitBounds(bounds, { padding: [24, 24], maxZoom: 14 });
  }
}

function removeMap() {
  if (myLocationMarker.value) {
    mapInstance.value?.removeLayer(myLocationMarker.value);
    myLocationMarker.value = null;
  }
  if (mapInstance.value) {
    mapInstance.value.remove();
    mapInstance.value = null;
    markersLayer.value = null;
  }
}

function centerOnMyLocation() {
  if (!navigator.geolocation || !mapInstance.value) return;
  myLocationLoading.value = true;
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      myLocationLoading.value = false;
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;
      mapInstance.value.setView([lat, lng], 14);
      if (myLocationMarker.value) mapInstance.value.removeLayer(myLocationMarker.value);
      const blueIcon = L.divIcon({
        className: 'my-location-marker',
        html: '<span style="background:#0ea5e9;width:16px;height:16px;border-radius:50%;display:block;border:3px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.3)"></span>',
        iconSize: [16, 16],
        iconAnchor: [8, 8],
      });
      myLocationMarker.value = L.marker([lat, lng], { icon: blueIcon }).addTo(mapInstance.value);
      myLocationMarker.value.bindTooltip('You are here', { permanent: false, direction: 'top' });
    },
    () => {
      myLocationLoading.value = false;
    },
    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
  );
}

watch(mapVisible, (visible) => {
  if (visible) {
    nextTick(() => {
      initMap();
    });
  } else {
    removeMap();
  }
});

watch(studiosWithCoords, () => {
  if (mapVisible.value && mapInstance.value) updateMapMarkers();
}, { deep: true });

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

<style scoped>
:deep(.studio-marker),
:deep(.my-location-marker) {
  background: transparent !important;
  border: none !important;
}
</style>
