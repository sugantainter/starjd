<template>
  <div v-if="brand" class="mx-auto max-w-6xl px-4 pt-8 pb-20 md:pb-24">
    <div class="rounded-2xl border border-[#e2e8f0] bg-white p-8 shadow-sm">
      <div class="flex flex-col gap-8 sm:flex-row sm:items-start">
        <div class="h-24 w-24 shrink-0 rounded-2xl overflow-hidden border border-[#e2e8f0] bg-[#f8fafc] flex items-center justify-center p-2">
          <img v-if="brand.logo_url" :src="brand.logo_url" :alt="brand.company_name" class="h-full w-full object-contain" />
          <span v-else class="text-3xl font-semibold text-[#94a3b8]">{{ (brand.company_name || brand.user?.name || '?').charAt(0) }}</span>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
              <h1 class="text-2xl font-bold text-[#1a1a1a]">{{ brand.company_name || brand.user?.name }}</h1>
              <a v-if="brand.website" :href="brand.website" target="_blank" rel="noopener noreferrer" class="mt-1 text-sm text-[#fc4402] hover:underline flex items-center gap-1">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                {{ brand.website.replace(/^https?:\/\//, '') }}
              </a>
            </div>
            <div class="flex items-center gap-2">
              <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 border border-emerald-100 flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Verified Brand
              </span>
              <span v-if="brand.industry" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 border border-blue-100 uppercase tracking-wider">
                {{ brand.industry }}
              </span>
              <span v-if="brand.hq_location" class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-100 flex items-center gap-1">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                {{ brand.hq_location }}
              </span>
            </div>
          </div>
          <div v-if="brand.bio" class="mt-6 text-[#4b5563] leading-relaxed max-w-3xl whitespace-pre-line">
            {{ brand.bio }}
          </div>
        </div>
      </div>
    </div>

    <!-- Active Campaigns Section -->
    <div class="mt-12">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-xl font-bold text-[#1a1a1a]">Open Campaigns</h2>
          <p class="text-sm text-[#64748b] mt-1">Explore opportunities to collaborate with {{ brand.company_name || brand.user?.name }}.</p>
        </div>
        <div class="h-px flex-1 bg-[#e2e8f0] mx-8 hidden md:block"></div>
        <div class="text-sm font-semibold text-[#fc4402]">{{ campaigns.length }} Active</div>
      </div>

      <div v-if="campaigns.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <router-link
          v-for="campaign in campaigns"
          :key="campaign.id"
          :to="'/campaigns/' + campaign.slug"
          class="group flex flex-col rounded-2xl border border-[#e2e8f0] bg-white p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-[#fc4402]/30 hover:shadow-md"
        >
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-3">
              <span class="rounded-lg bg-[#fc4402]/10 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-[#fc4402]">
                {{ campaign.campaign_type || 'Social Media' }}
              </span>
              <span v-if="campaign.budget" class="ml-auto text-sm font-bold text-[#1a1a1a]">₹{{ formatPrice(campaign.budget) }}</span>
            </div>
            <h3 class="font-bold text-[#1a1a1a] group-hover:text-[#fc4402] transition-colors line-clamp-2">{{ campaign.title }}</h3>
            <p class="mt-2 text-sm text-[#64748b] line-clamp-3">{{ campaign.description }}</p>
          </div>
          
          <div class="mt-6 flex items-center justify-between pt-4 border-t border-[#f1f5f9]">
            <div class="flex items-center gap-1.5 text-xs text-[#94a3b8]">
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              Ends {{ formatDate(campaign.ends_at) }}
            </div>
            <span class="text-xs font-bold text-[#fc4402] flex items-center gap-1">
              View Brief
              <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </span>
          </div>
        </router-link>
      </div>
      <div v-else class="rounded-2xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc] p-12 text-center">
        <div class="mx-auto w-16 h-16 rounded-full bg-white flex items-center justify-center shadow-sm mb-4">
          <svg class="h-8 w-8 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
        </div>
        <h3 class="text-lg font-bold text-[#1a1a1a]">No active campaigns right now</h3>
        <p class="text-sm text-[#64748b] mt-1 max-w-xs mx-auto">This brand hasn't posted any open campaigns recently. Check back later!</p>
      </div>
    </div>
  </div>
  <div v-else-if="!loading" class="mx-auto max-w-4xl px-4 pt-12 pb-20 md:pb-24 text-center text-[#64748b]">Brand not found.</div>
  <div v-else class="mx-auto max-w-4xl px-4 pt-12 pb-20 md:pb-24 text-center text-[#64748b]">Loading…</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const brand = ref(null);
const campaigns = ref([]);
const loading = ref(true);

function formatPrice(n) {
  const x = Number(n);
  return Number.isFinite(x) ? x.toLocaleString() : '0';
}

function formatDate(d) {
  if (!d) return 'N/A';
  return new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
}

onMounted(async () => {
  try {
    const slug = route.params.slug;
    const res = await axios.get('/api/brands/' + slug);
    brand.value = res.data.brand;
    campaigns.value = res.data.campaigns || [];
  } catch (e) {
    brand.value = null;
  } finally {
    loading.value = false;
  }
});
</script>
