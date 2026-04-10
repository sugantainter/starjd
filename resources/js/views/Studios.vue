<template>
  <div class="mx-auto max-w-7xl px-4 py-12 sm:py-20">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between mb-12">
      <div class="max-w-3xl">
        <h1 class="text-5xl font-black tracking-tight text-[#1a1a1a] mb-4">Studio Marketplace</h1>
        <p class="text-xl text-[#64748b] leading-relaxed">
          Book the world's finest photography, film, podcast, and music production spaces. Curated for professionals.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <div class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 shadow-xl ring-1 ring-[#e2e8f0]">
          <span class="h-2.5 w-2.5 rounded-full" :class="mapVisible ? 'bg-[#22c55e]' : 'bg-[#e5e7eb]'"></span>
          <span class="text-sm font-black text-[#1a1a1a]">{{ total }} Studios Available</span>
        </div>
      </div>
    </div>

    <!-- Main Toolbar -->
    <div class="sticky top-4 z-30 mb-10 flex flex-col gap-4 rounded-3xl bg-white/80 backdrop-blur-xl p-4 shadow-2xl border border-white/20 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-wrap items-center gap-3">
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-2xl border border-[#e2e8f0] px-6 py-3 text-sm font-black text-[#1a1a1a] transition hover:bg-[#e63946] hover:text-white lg:hidden"
          :class="showFilters ? 'bg-[#e63946] text-white border-none' : 'bg-white'"
          @click="showFilters = !showFilters"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M6 12h12M10 18h8"/></svg>
          Filters
        </button>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-2xl border border-[#e2e8f0] bg-white px-6 py-3 text-sm font-black text-[#1a1a1a] transition hover:border-black hover:bg-black hover:text-white shadow-sm"
          @click="mapVisible = !mapVisible"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.483V8.416a2 2 0 011.553-1.943L9 5m0 15l4.553-2.276A2 2 0 0115 11.237V5.517c0-1.108-.917-2.02-2.028-1.9L9 5m0 15V5"/></svg>
          {{ mapVisible ? 'Switch to List' : 'Switch to Map' }}
        </button>
      </div>
      <div class="flex flex-wrap items-center gap-4">
        <div class="relative">
          <select
            v-model="sort"
            class="min-w-[200px] appearance-none rounded-2xl border border-[#e2e8f0] bg-white px-5 py-3 text-sm font-bold text-[#1a1a1a] shadow-sm focus:outline-none focus:ring-4 focus:ring-black/5"
            @change="refresh"
          >
            <option value="newest">Sort: Newest First</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="rating">Top Rated</option>
          </select>
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
      <!-- Filter Sidebar -->
      <div
        v-show="showFilters"
        class="lg:block lg:w-80 lg:flex-none sticky top-28"
        :class="{ 'hidden': !showFilters }"
      >
        <div class="rounded-3xl border border-[#e2e8f0] bg-white p-6 shadow-xl">
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
            @apply="refresh"
            @clear="clearFilters(); refresh()"
          />
        </div>
      </div>

      <div class="min-w-0 flex-1">
        <!-- Map View -->
        <div
          v-if="mapVisible"
          class="relative mb-4 overflow-hidden rounded-3xl border border-[#e2e8f0] bg-[#f1f5f9] shadow-2xl"
          style="min-height: 600px;"
        >
          <div ref="mapContainer" class="h-full min-h-[600px] w-full rounded-3xl"></div>
          <button
            type="button"
            :disabled="myLocationLoading"
            class="absolute right-6 top-6 z-[1000] flex items-center gap-2 rounded-2xl border border-[#e2e8f0] bg-white px-5 py-3 text-sm font-black text-[#1a1a1a] shadow-2xl transition hover:bg-black hover:text-white disabled:opacity-60"
            @click="centerOnMyLocation"
          >
            <svg v-if="myLocationLoading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ myLocationLoading ? 'Locating...' : 'Near Me' }}
          </button>
        </div>

        <!-- List View -->
        <div
          v-else
          class="flex flex-col gap-8"
        >
          <StudioCard v-for="s in list" :key="s.id" :studio="s" />
        </div>

        <!-- Empty State -->
        <div
          v-if="!list.length && !loading"
          class="mt-12 rounded-3xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc] p-20 text-center"
        >
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
             <svg class="w-10 h-10 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          </div>
          <h2 class="text-2xl font-black text-[#1a1a1a]">No studios found</h2>
          <p class="text-[#64748b] mt-2 text-lg">We couldn't find any studios matching your criteria.</p>
          <button @click="clearFilters(); refresh()" class="mt-8 px-10 py-4 bg-black text-white font-black rounded-2xl shadow-xl shadow-black/20 transform hover:scale-105 transition-transform">Reset Filters</button>
        </div>

        <!-- Infinite Scroll Trigger -->
        <div ref="scrollTrigger" class="py-20 flex justify-center">
          <div v-if="loading" class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 bg-black rounded-full animate-bounce" style="animation-delay: 0s"></div>
            <div class="w-2.5 h-2.5 bg-black rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="w-2.5 h-2.5 bg-black rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
          </div>
          <div v-else-if="finished && list.length" class="text-xs font-black text-[#cbd5e1] uppercase tracking-[0.2em]">
            Expertly Curated for You
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, watch, computed, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import StudioCard from '../components/studio/StudioCard.vue';
import FilterSidebar from '../components/studio/FilterSidebar.vue';

const route = useRoute();
const router = useRouter();
const list = ref([]);
const loading = ref(false);
const finished = ref(false);
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
const page = ref(1);
const total = ref(0);
const scrollTrigger = ref(null);
let observer = null;

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

function applyQueryToFilters() {
  const q = route.query;
  const p = route.params;

  if (p.category != null) filters.category = p.category;
  if (p.city != null) filters.city = p.city;
  if (p.state != null && !p.city) filters.city = p.state;

  if (q.category != null) filters.category = q.category;
  if (q.city != null) filters.city = q.city;
}

function clearFilters() {
  filters.category = '';
  filters.city = '';
  filters.min_price = '';
  filters.max_price = '';
  filters.amenities = [];
  filters.featured = false;
  filters.rating = '';
}

function buildParams(p = 1) {
  const params = { page: p, sort: sort.value };
  if (filters.category) params.category = filters.category;
  if (filters.city) params.city = filters.city;
  if (filters.min_price !== '' && filters.min_price != null) params.min_price = filters.min_price;
  if (filters.max_price !== '' && filters.max_price != null) params.max_price = filters.max_price;
  if (filters.featured) params.featured = 1;
  if (filters.rating !== '' && filters.rating != null) params.rating = filters.rating;
  if (filters.amenities?.length) params.amenities = filters.amenities.join(',');
  return params;
}

function refresh() {
  page.value = 1;
  list.value = [];
  finished.value = false;

  let path = '/studios';
  if (filters.category) {
    path = `/studios/category/${filters.category.toLowerCase().replace(/ /g, '-')}`;
  } else if (filters.city) {
    path = `/studios/location/${filters.city.toLowerCase().replace(/ /g, '-')}`;
  }

  if (route.path !== path) {
    router.push(path);
    return;
  }

  load(1);
}

async function loadMore() {
  if (loading.value || finished.value || mapVisible.value) return;
  page.value++;
  load(page.value);
}

async function load(p = 1) {
  loading.value = true;
  try {
    const res = await axios.get('/api/studios', { params: buildParams(p) });
    const resData = res.data;
    const items = resData.data?.data || resData.data || (Array.isArray(resData) ? resData : []);
    
    if (items.length === 0) {
      finished.value = true;
    } else {
      list.value = p === 1 ? items : [...list.value, ...items];
      if (items.length < (resData.per_page || 12)) {
        finished.value = true;
      }
    }
    
    total.value = resData.total || list.value.length;
  } catch (e) {
    console.error('Failed to load studios', e);
    finished.value = true;
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
    attribution: '&copy; OpenStreetMap',
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
  categories.value = catRes.data || [];
  amenities.value = amRes.data || [];
  applyQueryToFilters();
  
  // Initialize Infinite Scroll
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !loading.value && !finished.value && !mapVisible.value) {
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
  removeMap();
});

watch([sort], () => refresh());
watch(() => [route.query, route.params], () => {
  applyQueryToFilters();
  refresh();
}, { deep: true });
</script>

<style scoped>
:deep(.studio-marker),
:deep(.my-location-marker) {
  background: transparent !important;
  border: none !important;
}
</style>
