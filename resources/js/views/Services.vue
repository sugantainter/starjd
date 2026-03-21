<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-black px-4 py-24 md:py-32">
       <!-- Abstract Background -->
       <div class="absolute inset-0 opacity-20">
          <div class="absolute top-0 -left-1/4 w-full h-full bg-gradient-to-br from-[#e63946] to-transparent rounded-full blur-[120px]"></div>
          <div class="absolute bottom-0 -right-1/4 w-full h-full bg-gradient-to-tl from-[#e63946] to-transparent rounded-full blur-[120px]"></div>
       </div>
       
       <div class="relative mx-auto max-w-5xl text-center">
        <span class="inline-flex items-center gap-2 rounded-full bg-[#e63946]/10 border border-[#e63946]/20 px-4 py-2 text-xs font-black uppercase tracking-widest text-[#e63946] mb-6">
           <span class="w-2 h-2 rounded-full bg-[#e63946] animate-ping"></span>
           Premium Agency Solutions
        </span>
        <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white mb-8">Elevate Your Presence</h1>
        <p class="text-xl text-[#94a3b8] max-w-2xl mx-auto leading-relaxed">We provide end-to-end professional services for creators and brands who want to dominate their niche.</p>
      </div>
    </section>

    <!-- Services Section -->
    <section class="px-4 py-12 -mt-20">
      <div class="mx-auto max-w-5xl">
        <div v-if="loading" class="flex justify-center py-20 bg-white rounded-3xl border border-[#e5e7eb] shadow-xl">
           <div class="w-12 h-12 border-4 border-[#e63946] border-t-transparent rounded-full animate-spin"></div>
        </div>
        
        <div v-else-if="!services.length" class="rounded-3xl border border-[#e5e7eb] bg-white p-20 text-center shadow-2xl">
           <svg class="w-16 h-16 text-[#cbd5e1] mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
           <h3 class="text-2xl font-black text-[#1a1a1a]">Services coming soon</h3>
           <p class="text-[#64748b] mt-2">Check back soon for our professional solutions.</p>
        </div>

        <div v-else class="flex flex-col gap-8">
          <router-link
            v-for="s in services"
            :key="s.id"
            :to="'/services/' + s.slug"
            class="group flex flex-col md:flex-row overflow-hidden rounded-3xl border border-[#e5e7eb] bg-white shadow-xl transition-all duration-500 hover:border-[#e63946]/30 hover:shadow-2xl hover:-translate-y-1"
          >
            <!-- Image Area -->
            <div class="w-full md:w-80 h-72 md:h-auto shrink-0 overflow-hidden bg-[#f1f5f9] relative">
              <img v-if="s.image" :src="s.image" :alt="s.name" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" />
              <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#e63946] to-[#1a1a1a]">
                <span class="text-6xl font-black text-white/20">{{ s.name.charAt(0) }}</span>
              </div>
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent md:hidden"></div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 p-8 md:p-12 flex flex-col">
              <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-6">
                 <div>
                    <h2 class="text-3xl font-black text-[#1a1a1a] group-hover:text-[#e63946] transition-colors leading-tight mb-4">{{ s.name }}</h2>
                    <p v-if="s.short_description" class="text-[#64748b] text-lg leading-relaxed max-w-xl">{{ s.short_description }}</p>
                 </div>
                 <div class="shrink-0 flex items-center md:flex-col md:items-end font-black">
                    <span class="text-[10px] text-[#94a3b8] uppercase tracking-widest mb-1 md:block hidden">Starting from</span>
                    <span class="text-2xl text-[#1a1a1a]">₹{{ s.pricing_tiers?.[0]?.price || 'N/A' }}</span>
                 </div>
              </div>

              <div class="mt-auto pt-8 border-t border-[#f1f5f9] flex flex-wrap items-center justify-between gap-6">
                 <div class="flex items-center gap-4">
                    <div class="flex -space-x-2">
                       <img v-for="i in 3" :key="i" :src="'https://i.pravatar.cc/100?u=' + (s.id + i)" class="w-8 h-8 rounded-full border-2 border-white" />
                    </div>
                    <span class="text-xs font-bold text-[#64748b]">Trusted by 50+ clients</span>
                 </div>
                 
                 <div class="flex items-center gap-2 text-[#e63946] font-black uppercase tracking-widest text-sm">
                    View Details
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                 </div>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Professional CTA -->
    <section class="px-4 py-24">
      <div class="mx-auto max-w-5xl">
        <div class="relative rounded-3xl bg-black px-8 py-16 text-center shadow-2xl overflow-hidden md:px-16 md:py-24">
           <!-- Abstract BG -->
           <div class="absolute inset-0 opacity-30">
              <div class="absolute top-0 -left-1/4 w-full h-full bg-[#e63946] rounded-full blur-[100px]"></div>
           </div>
           
           <div class="relative z-10">
              <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Ready to scale?</h2>
              <p class="text-[#94a3b8] text-xl max-w-xl mx-auto mb-10">We create tailored strategies that actually convert. Let's build something legendary together.</p>
              <router-link to="/contact" class="inline-block rounded-2xl bg-[#e63946] px-12 py-5 text-lg font-black text-white shadow-xl shadow-[#e63946]/20 transition-all hover:bg-[#c1121f] transform hover:scale-105">Get Started Now</router-link>
           </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const r = await axios.get('/api/services');
    services.value = r.data;
  } finally {
    loading.value = false;
  }
});
</script>
