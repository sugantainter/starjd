<template>
  <div class="min-h-screen bg-[#fafafa]">
    <!-- Professional Marketplace Header -->
    <div class="bg-white border-b border-[#e2e8f0] px-4 py-16">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div class="max-w-2xl">
            <h1 class="text-5xl font-black text-[#1a1a1a] mb-6 tracking-tight">Professional Services</h1>
            <p class="text-[#64748b] text-xl leading-relaxed">Find the perfect professional for your next project. From high-end content creation to digital marketing experts.</p>
          </div>
          <div class="flex items-center gap-3 bg-[#f8fafc] p-2 rounded-2xl border border-[#e2e8f0]">
             <div class="px-4 py-2 bg-white rounded-xl shadow-sm text-sm font-bold text-[#1a1a1a] border border-[#e2e8f0]">
                {{ listings.length }} Listings
             </div>
             <div class="px-4 py-2 text-sm font-medium text-[#64748b]">
                Updated today
             </div>
          </div>
        </div>
        
        <!-- Category Chips -->
        <div class="flex flex-wrap gap-3 mt-12 pb-2 overflow-x-auto no-scrollbar">
           <button 
             @click="handleCategoryClick('')"
             :class="['px-6 py-3 rounded-xl text-sm font-bold border transition-all whitespace-nowrap', !selectedCategorySlug ? 'bg-[#1a1a1a] text-white border-[#1a1a1a] shadow-lg shadow-black/20' : 'bg-white text-[#64748b] border-[#e2e8f0] hover:border-[#1a1a1a] hover:text-[#1a1a1a]']">
             All Marketplace
           </button>
           <button 
             v-for="cat in categories" :key="cat.id"
             @click="handleCategoryClick(cat.slug || cat.id)"
             :class="['px-6 py-3 rounded-xl text-sm font-bold border transition-all whitespace-nowrap', selectedCategorySlug == (cat.slug || cat.id) ? 'bg-[#1a1a1a] text-white border-[#1a1a1a] shadow-lg shadow-black/20' : 'bg-white text-[#64748b] border-[#e2e8f0] hover:border-[#1a1a1a] hover:text-[#1a1a1a]']">
             {{ cat.name }}
           </button>
        </div>
      </div>
    </div>

    <!-- Listings Container -->
    <div class="max-w-7xl mx-auto px-4 py-12">
      <div class="flex flex-col gap-8">
         <router-link 
            v-for="listing in listings" 
            :key="listing.id" 
            :to="'/gigs/' + listing.slug" 
            class="group bg-white rounded-3xl border border-[#e2e8f0] overflow-hidden flex flex-col md:flex-row hover:border-black/20 hover:shadow-2xl transition-all duration-500"
         >
            <!-- Preview Image -->
            <div class="w-full md:w-96 h-64 md:h-80 overflow-hidden bg-[#f1f5f9] shrink-0 relative">
               <img
                 v-if="listing.gallery?.[0]"
                 :src="listing.gallery[0]"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                 alt=""
                 @error="onGigImageError"
               />
               <div v-else class="w-full h-full flex items-center justify-center text-[#cbd5e1]">
                 <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
               </div>
               <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>

            <!-- Gig Info -->
            <div class="flex-1 p-8 flex flex-col">
              <div class="flex items-center gap-3 mb-4">
                 <div class="h-10 w-10 rounded-full bg-[#f1f5f9] overflow-hidden border-2 border-white shadow-sm">
                    <img
                      v-if="listing.user?.avatar_url"
                      :src="listing.user.avatar_url"
                      class="w-full h-full object-cover"
                      alt=""
                      @error="onGigImageError"
                    />
                 </div>
                 <div>
                    <span class="text-sm font-black text-[#1a1a1a]">{{ listing.user?.name }}</span>
                    <div class="text-[10px] text-[#94a3b8] font-bold uppercase tracking-widest">Top Rated Professional</div>
                 </div>
              </div>

              <h3 class="text-2xl font-bold text-[#1a1a1a] mb-4 group-hover:text-black transition-colors leading-tight">{{ listing.title }}</h3>
              
              <div class="flex flex-wrap gap-2 mb-6">
                 <span class="px-3 py-1 bg-[#f1f5f9] text-[#475569] text-xs font-bold rounded-lg">{{ listing.category?.name || 'Professional' }}</span>
                 <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-lg flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Identity Verified
                 </span>
              </div>

              <div class="flex items-center gap-4 mt-auto pt-6 border-t border-[#f1f5f9]">
                 <div class="flex items-center gap-1.5 text-[#f59e0b]">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="text-sm font-black">5.0</span>
                    <span class="text-xs text-[#94a3b8] font-medium">(120+ Reviews)</span>
                 </div>
                 
                 <div class="ml-auto text-right">
                    <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Starting Price</div>
                    <div class="text-3xl font-black text-[#1a1a1a]">{{ formatCurrency(listing.pricing_tiers?.[0]?.price) }}</div>
                 </div>
              </div>
            </div>

            <!-- Action Area -->
            <div class="md:w-20 bg-black flex md:flex-col items-center justify-center p-4 md:p-0">
               <svg class="w-8 h-8 text-white transition-transform group-hover:translate-x-1 md:group-hover:translate-x-0 md:group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
         </router-link>
      </div>

      <!-- Infinite Scroll Trigger -->
      <div ref="scrollTrigger" class="py-20 flex justify-center">
        <div v-if="loading" class="flex items-center gap-3">
           <div class="w-3 h-3 bg-black rounded-full animate-pulse"></div>
           <div class="w-3 h-3 bg-black rounded-full animate-pulse [animation-delay:0.2s]"></div>
           <div class="w-3 h-3 bg-black rounded-full animate-pulse [animation-delay:0.4s]"></div>
        </div>
        <div v-else-if="finished && listings.length" class="text-sm font-bold text-[#cbd5e1] tracking-widest uppercase">
           End of Marketplace
        </div>
        <div v-else-if="!listings.length" class="text-center py-20 bg-white rounded-3xl border border-[#e2e8f0] w-full">
          <div class="mx-auto w-24 h-24 bg-[#f8fafc] rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <h2 class="text-2xl font-bold text-[#1a1a1a]">No services found</h2>
          <p class="text-[#64748b] mt-2">Try adjusting your filters or searching for something else.</p>
          <button @click="handleCategoryClick('')" class="mt-8 px-8 py-4 bg-black text-white font-bold rounded-2xl shadow-xl shadow-black/20">View All Services</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
/** When signed URLs expire or paths were wrong */
const GIG_IMAGE_FALLBACK =
  'https://images.unsplash.com/photo-1560439514-4e9645039924?w=800&h=500&fit=crop';

function onGigImageError(e) {
  const el = e?.target;
  if (!el || el.dataset.gigImgFallback === '1') return;
  el.dataset.gigImgFallback = '1';
  el.src = GIG_IMAGE_FALLBACK;
}

const loading = ref(true);
const listings = ref([]);
const categories = ref([]);

// Slug based category
const selectedCategorySlug = ref(route.params.paths?.[0] || route.query.category || '');
const page = ref(1);
const finished = ref(false);
const scrollTrigger = ref(null);
let observer = null;

const currentCategory = computed(() => {
  if (!selectedCategorySlug.value) return null;
  return categories.value.find(c => c.slug === selectedCategorySlug.value || c.id == selectedCategorySlug.value);
});

// Dynamic SEO
useHead({
  title: computed(() => {
    if (currentCategory.value) return `${currentCategory.value.name} Marketplace | Hire Professional Service Experts | StarJD`;
    return 'Professional Service Marketplace | Hire Creative Experts | StarJD';
  }),
  meta: [
    { 
      name: 'description', 
      content: computed(() => {
        if (currentCategory.value) return `Hire vetted professional ${currentCategory.value.name} experts on StarJD. Browse ${listings.value.length}+ verified listings for your next project.`;
        return 'Hire vetted professionals for photography, video editing, digital marketing, and more in our creative marketplace. StarJD connects you with top-tier talent.';
      })
    },
    { property: 'og:title', content: 'Professional Service Marketplace | StarJD' },
    { property: 'og:type', content: 'website' }
  ]
});

function handleCategoryClick(catSlug) {
  selectedCategorySlug.value = catSlug || '';
  if (catSlug) {
     router.push({ name: 'marketplace-flexible', params: { paths: [catSlug] } });
  } else {
     router.push({ name: 'marketplace' });
  }
}

onMounted(async () => {
  await fetchCategories();
  
  // Initialize observer
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !loading.value && !finished.value) {
      loadMore();
    }
  }, { threshold: 0.1 });
  
  if (scrollTrigger.value) {
    observer.observe(scrollTrigger.value);
  }
  
  fetchData(1);
});

onUnmounted(() => {
  if (observer) observer.disconnect();
});

async function fetchCategories() {
  try {
    const res = await axios.get('/api/services');
    categories.value = res.data;
  } catch (e) {
    console.error('Failed to load categories', e);
  }
}

async function loadMore() {
  if (loading.value || finished.value) return;
  page.value++;
  fetchData(page.value);
}

async function fetchData(p = 1) {
  loading.value = true;
  try {
    const params = { page: p };
    if (selectedCategorySlug.value) {
      params.category = selectedCategorySlug.value;
    }
    const res = await axios.get('/api/gigs', { params });
    const resData = res.data;
    const items = resData.data || [];
    
    if (p === 1) {
      listings.value = items;
    } else {
      listings.value = [...listings.value, ...items];
    }
    
    if (items.length === 0 || items.length < (resData.per_page || 12)) {
      finished.value = true;
    }
  } catch (e) {
    console.error('Failed to load gigs', e);
    finished.value = true;
  } finally {
    loading.value = false;
  }
}

watch(selectedCategorySlug, () => {
  page.value = 1;
  listings.value = [];
  finished.value = false;
  fetchData(1);
});

// Watch for route changes (e.g. back button)
watch(() => route.params.paths, (newPaths) => {
  const newSlug = newPaths?.[0] || '';
  if (newSlug !== selectedCategorySlug.value) {
    selectedCategorySlug.value = newSlug;
  }
});

function formatCurrency(amt) {
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(amt || 0);
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
