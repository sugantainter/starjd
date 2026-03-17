<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const loading = ref(true);
const listings = ref([]);
const categories = ref([]);
const selectedCategory = ref(route.query.category || '');

onMounted(async () => {
  await fetchCategories();
  await fetchData();
});

async function fetchCategories() {
  try {
    const res = await axios.get('/api/services');
    categories.value = res.data;
  } catch (e) {
    console.error('Failed to load categories', e);
  }
}

async function fetchData() {
  loading.value = true;
  try {
    const params = {};
    if (selectedCategory.value) {
      if (typeof selectedCategory.value === 'number') {
        params.service_id = selectedCategory.value;
      } else {
        params.category = selectedCategory.value;
      }
    }
    const res = await axios.get('/api/gigs', { params });
    listings.value = res.data.data;
  } catch (e) {
    console.error('Failed to load gigs', e);
  } finally {
    loading.value = false;
  }
}

watch(selectedCategory, () => {
  fetchData();
});

function formatCurrency(amt) {
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amt || 0);
}
</script>

<template>
  <div class="min-h-screen bg-[#fafafa]">
    <!-- Marketplace Header -->
    <div class="bg-white border-b border-[#e2e8f0] px-4 py-12">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-[#1a1a1a] mb-4">Professional Marketplace</h1>
        <p class="text-[#64748b] text-lg max-w-2xl">Find the perfect professional for your next project. From digital marketing to high-end content creation.</p>
        
        <!-- Quick Filters -->
        <div class="flex flex-wrap gap-3 mt-8">
           <button 
             @click="selectedCategory = ''"
             :class="['px-5 py-2 rounded-full text-sm font-bold border transition-all', !selectedCategory ? 'bg-[#1a1a1a] text-white border-[#1a1a1a]' : 'bg-white text-[#64748b] border-[#e2e8f0] hover:border-[#1a1a1a]']">
             All Services
           </button>
           <button 
             v-for="cat in categories" :key="cat.id"
             @click="selectedCategory = cat.id"
             :class="['px-5 py-2 rounded-full text-sm font-bold border transition-all', selectedCategory == cat.id ? 'bg-[#1a1a1a] text-white border-[#1a1a1a]' : 'bg-white text-[#64748b] border-[#e2e8f0] hover:border-[#1a1a1a]']">
             {{ cat.name }}
           </button>
        </div>
      </div>
    </div>

    <!-- Listings Grid -->
    <div class="max-w-7xl mx-auto px-4 py-12">
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div v-for="i in 8" :key="i" class="h-80 bg-white rounded-2xl border border-[#e2e8f0] animate-pulse"></div>
      </div>

      <div v-else-if="!listings.length" class="text-center py-20 bg-white rounded-3xl border border-[#e2e8f0]">
        <div class="mx-auto w-24 h-24 bg-[#f8fafc] rounded-full flex items-center justify-center mb-6">
          <svg class="w-12 h-12 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-[#1a1a1a]">No services found</h2>
        <p class="text-[#64748b] mt-2">Try adjusting your filters or searching for something else.</p>
        <button @click="selectedCategory = ''" class="mt-8 px-8 py-3 bg-[#f59e0b] text-white font-bold rounded-xl">View All Services</button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
         <router-link v-for="listing in listings" :key="listing.id" :to="'/gigs/' + listing.slug" class="group bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="aspect-video bg-[#f1f5f9] overflow-hidden">
               <img v-if="listing.gallery?.[0]" :src="listing.gallery[0]" class="w-full h-full object-cover group-hover:scale-105 transition-all" />
               <div v-else class="w-full h-full flex items-center justify-center text-[#cbd5e1]">
                 <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
               </div>
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-3">
                 <div class="h-6 w-6 rounded-full bg-[#f1f5f9] overflow-hidden border border-[#e2e8f0]">
                    <img v-if="listing.user?.avatar_url" :src="listing.user.avatar_url" class="w-full h-full object-cover" />
                 </div>
                 <span class="text-xs font-bold text-[#1a1a1a]">{{ listing.user?.name }}</span>
              </div>
              <h3 class="text-sm font-medium text-[#1a1a1a] line-clamp-2 leading-tight group-hover:text-[#f59e0b] transition-colors mb-4 h-10">{{ listing.title }}</h3>
              
              <div class="flex items-center gap-1 text-[#f59e0b] mb-4">
                 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                 <span class="text-xs font-bold">5.0</span>
                 <span class="text-[10px] text-[#94a3b8]">(124)</span>
              </div>

              <div class="pt-4 border-t border-[#f1f5f9] flex items-center justify-between">
                 <svg class="w-5 h-5 text-[#94a3b8] hover:text-[#e63946] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                 <div class="text-right">
                    <div class="text-[10px] font-bold text-[#b5b5b5] uppercase">Starting at</div>
                    <div class="text-lg font-bold text-[#1a1a1a]">{{ formatCurrency(listing.pricing_tiers?.[0]?.price) }}</div>
                 </div>
              </div>
            </div>
         </router-link>
      </div>
    </div>
  </div>
</template>
