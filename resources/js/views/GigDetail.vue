<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const loading = ref(true);
const gig = ref(null);
const activeTab = ref('Basic');

onMounted(async () => {
  try {
    const res = await axios.get(`/api/gigs/${route.params.slug}`);
    gig.value = res.data;
    if (gig.value.pricing_tiers?.length > 0) {
      activeTab.value = gig.value.pricing_tiers[0].name;
    }
  } catch (e) {
    console.error('Failed to load gig', e);
  } finally {
    loading.value = false;
  }
});

const activePackage = computed(() => {
  return gig.value?.pricing_tiers?.find(t => t.name === activeTab.value);
});

function formatCurrency(amt) {
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amt || 0);
}
</script>

<template>
  <div v-if="!loading && gig" class="min-h-screen bg-white pb-20">
    <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
      <!-- Left Column: Image, Description, FAQ -->
      <div class="lg:col-span-2 space-y-10">
        <h1 class="text-3xl font-bold text-[#1a1a1a] leading-tight">{{ gig.title }}</h1>
        
        <!-- Seller Preview -->
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 rounded-full bg-[#f1f5f9] overflow-hidden border border-[#e2e8f0]">
             <img v-if="gig.user?.avatar_url" :src="gig.user.avatar_url" class="w-full h-full object-cover" />
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="font-bold text-[#1a1a1a]">{{ gig.user?.name }}</span>
              <span class="px-2 py-0.5 rounded bg-[#fef3c7] text-[#f59e0b] text-[10px] font-bold uppercase tracking-wider">Top Rated</span>
            </div>
            <div class="flex items-center gap-1 text-sm text-[#475569]">
              <svg class="w-4 h-4 text-[#f59e0b]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <span class="font-bold">{{ gig.user?.professional_profile?.avg_rating || '5.0' }}</span>
              <span class="text-[#94a3b8]">({{ gig.user?.professional_profile?.total_reviews || '0' }})</span>
            </div>
          </div>
        </div>

        <!-- Gallery -->
        <div class="aspect-video rounded-2xl bg-[#f8fafc] border border-[#e2e8f0] overflow-hidden shadow-sm">
           <img v-if="gig.gallery?.[0]" :src="gig.gallery[0]" class="w-full h-full object-contain" />
           <div v-else class="w-full h-full flex items-center justify-center text-[#94a3b8]">
             <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
           </div>
        </div>

        <!-- Tabs for mobile pricing? No, stay simple for now -->

        <!-- About Gig -->
        <div class="space-y-4">
          <h2 class="text-2xl font-bold text-[#1a1a1a]">About this gig</h2>
          <div class="prose max-w-none text-[#475569] leading-relaxed whitespace-pre-wrap">{{ gig.description }}</div>
        </div>

        <!-- FAQ -->
        <div v-if="gig.faqs?.length" class="space-y-6 pt-10 border-t border-[#f1f5f9]">
          <h2 class="text-2xl font-bold text-[#1a1a1a]">FAQ</h2>
          <div class="space-y-4">
            <div v-for="(faq, i) in gig.faqs" :key="i" class="p-5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]">
              <div class="font-bold text-[#1a1a1a] mb-2">{{ faq.question }}</div>
              <div class="text-sm text-[#475569] leading-relaxed">{{ faq.answer }}</div>
            </div>
          </div>
        </div>

        <!-- About Seller -->
        <div class="p-8 rounded-2xl border border-[#e2e8f0] bg-white space-y-6">
          <h2 class="text-xl font-bold text-[#1a1a1a]">About the seller</h2>
          <div class="flex gap-6">
            <div class="h-24 w-24 rounded-full bg-[#f1f5f9] overflow-hidden shrink-0 border border-[#e2e8f0]">
               <img v-if="gig.user?.avatar_url" :src="gig.user.avatar_url" class="w-full h-full object-cover" />
            </div>
            <div>
              <div class="text-lg font-bold text-[#1a1a1a]">{{ gig.user?.name }}</div>
              <div class="text-[#64748b] text-sm mb-3">{{ gig.user?.professional_profile?.tagline }}</div>
              <div class="flex items-center gap-1 text-sm text-[#f59e0b] font-bold mb-4">
                 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                 {{ gig.user?.professional_profile?.avg_rating || '5.0' }} ({{ gig.user?.professional_profile?.total_reviews || '0' }} reviews)
              </div>
              <button class="px-6 py-2 rounded-lg border border-[#1a1a1a] font-bold text-[#1a1a1a] hover:bg-[#1a1a1a] hover:text-white transition-all">Contact Me</button>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-6 pt-6 border-t border-[#f1f5f9]">
            <div v-if="gig.user?.professional_profile?.languages?.length">
              <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider mb-1">Languages</div>
              <div class="text-sm font-medium text-[#475569]">{{ gig.user.professional_profile.languages.map(l => l.name).join(', ') }}</div>
            </div>
            <div>
              <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider mb-1">Avg. Response Time</div>
              <div class="text-sm font-medium text-[#475569]">{{ gig.user?.professional_profile?.response_time || '1 hour' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Pricing Table & Booking -->
      <div class="lg:col-span-1">
        <div class="sticky top-24 rounded-2xl border border-[#e2e8f0] bg-white shadow-xl overflow-hidden">
          <div class="flex border-b border-[#e2e8f0]">
            <button v-for="tier in gig.pricing_tiers" :key="tier.name"
              @click="activeTab = tier.name"
              :class="['flex-1 py-4 text-sm font-bold transition-all border-b-2', activeTab === tier.name ? 'text-[#f59e0b] border-[#f59e0b] bg-[#fffbeb]/50' : 'text-[#64748b] border-transparent hover:text-[#1a1a1a]']">
              {{ tier.name }}
            </button>
          </div>
          
          <div class="p-8 space-y-6">
            <div class="flex items-center justify-between">
              <h3 class="font-bold text-[#1a1a1a]">{{ activeTab }} Package</h3>
              <div class="text-2xl font-bold text-[#1a1a1a]">{{ formatCurrency(activePackage?.price) }}</div>
            </div>
            
            <p class="text-sm text-[#475569] leading-relaxed">{{ activePackage?.description }}</p>
            
            <div class="flex items-center gap-6 text-sm font-bold text-[#1a1a1a]">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ activePackage?.delivery }} Days Delivery
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ activePackage?.revisions === 20 ? 'Unlimited' : activePackage?.revisions }} Revisions
              </div>
            </div>

            <ul class="space-y-2 pt-4">
              <li v-for="(val, key) in activePackage?.features" :key="key" class="flex items-center gap-3 text-sm text-[#475569]">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ key }}
              </li>
            </ul>

            <button class="w-full py-4 bg-[#1a1a1a] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-black transition-all transform active:scale-[0.98]">
              Continue ({{ formatCurrency(activePackage?.price) }})
            </button>
            <p class="text-center text-xs text-[#94a3b8]">You won't be charged yet</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="flex min-h-[60vh] items-center justify-center">
    <div class="h-10 w-10 animate-spin rounded-full border-4 border-[#f59e0b] border-t-transparent"></div>
  </div>
</template>
