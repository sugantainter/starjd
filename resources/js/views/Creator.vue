<template>
  <div class="min-h-screen bg-[#fafaf9] text-[#1a1a1a] overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative pt-20 pb-32 md:pt-32 md:pb-48 overflow-hidden">
      <!-- Background Elements -->
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/3 w-96 h-96 bg-pink-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
      </div>

      <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 text-sm font-semibold mb-8 animate-fade-in">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
          </span>
          Join 10,000+ top creators already using StarJD
        </div>
        
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black tracking-tight mb-8 animate-fade-in-up">
          Turn Your Passion Into <br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">A Sustainable Career</span>
        </h1>
        
        <p class="max-w-2xl mx-auto text-lg md:text-xl text-[#64748b] mb-12 animate-fade-in-up animation-delay-300">
          StarJD connects you directly with leading brands. No middlemen, no hidden fees, just pure collaboration and growth.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up animation-delay-500">
          <router-link to="/register?type=creator" class="w-full sm:w-auto px-8 py-4 bg-emerald-600 text-white rounded-2xl font-bold text-lg hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-600/20 transition-all transform hover:-translate-y-1">
            Start Earning Now
          </router-link>
          <a href="#how-it-works" class="w-full sm:w-auto px-8 py-4 bg-white text-[#1a1a1a] border border-[#e2e8f0] rounded-2xl font-bold text-lg hover:bg-gray-50 transition-all">
            See How it Works
          </a>
        </div>

        <!-- Floating Avatars Social Proof -->
        <div class="mt-20 relative h-32 md:h-48 overflow-visible">
            <div 
                v-for="(c, i) in featuredCreators.slice(0, 6)" 
                :key="i"
                class="absolute hidden md:block transition-all duration-700 hover:scale-110"
                :style="getFloatingStyle(i)"
            >
                <div class="relative p-1 bg-white rounded-2xl shadow-xl border border-[#e2e8f0]">
                    <img :src="c.avatar_url || 'https://i.pravatar.cc/150?u=' + i" class="w-12 h-12 md:w-16 md:h-16 rounded-xl object-cover" />
                    <div class="absolute -bottom-1 -right-1 bg-emerald-500 p-1 rounded-lg">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745a3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745a3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745a3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </section>

    <!-- Brands Carousel Section -->
    <section class="py-16 bg-white border-y border-[#e2e8f0]">
      <div class="max-w-7xl mx-auto px-4">
        <p class="text-center text-sm font-bold text-[#94a3b8] uppercase tracking-widest mb-10">Work with leading brands nationwide</p>
        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
          <router-link v-for="b in brands" :key="b.id" :to="'/brands/' + b.slug" class="h-8 md:h-12 flex items-center justify-center">
            <img :src="b.logo_url" :alt="b.company_name" class="h-full w-auto object-contain" />
          </router-link>
          <template v-if="!brands.length">
             <div v-for="i in 5" :key="i" class="h-10 w-24 bg-gray-100 rounded animate-pulse"></div>
          </template>
        </div>
      </div>
    </section>

    <!-- Why Creators Section -->
    <section class="py-24 md:py-32">
      <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-20">
          <h2 class="text-3xl md:text-5xl font-black mb-6">Designed for Your Success</h2>
          <p class="text-[#64748b] max-w-2xl mx-auto">We provide the tools, the network, and the security you need to turn your creativity into a business.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="group p-8 rounded-3xl bg-white border border-[#e2e8f0] hover:border-emerald-500/30 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold mb-4">You Control the Pricing</h3>
            <p class="text-[#64748b] leading-relaxed">Set your own rates and keep 100% of it. No platform commissions taken from your hard-earned talent.</p>
          </div>

          <div class="group p-8 rounded-3xl bg-white border border-[#e2e8f0] hover:border-blue-500/30 hover:shadow-2xl hover:shadow-blue-500/10 transition-all">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="text-xl font-bold mb-4">Escrow Protection</h3>
            <p class="text-[#64748b] leading-relaxed">Payment is secured before you start work and released instantly upon brand approval. Say goodbye to chasing payments.</p>
          </div>

          <div class="group p-8 rounded-3xl bg-white border border-[#e2e8f0] hover:border-pink-500/30 hover:shadow-2xl hover:shadow-pink-500/10 transition-all">
            <div class="w-14 h-14 bg-pink-50 rounded-2xl flex items-center justify-center text-pink-600 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-xl font-bold mb-4">One-Click Media Kits</h3>
            <p class="text-[#64748b] leading-relaxed">Connect your socials and we generate a live, real-time media kit for you to share with potential brand partners.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Cross-Listing: Brands Section -->
    <section class="py-24 bg-[#0f172a] text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-5">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.83L19.07 19H4.93L12 5.83z"/></svg>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <h2 class="text-3xl md:text-5xl font-black mb-4">Brands Looking for You</h2>
                    <p class="text-[#94a3b8] max-w-xl">These companies are actively searching for creators like you on StarJD right now. Apply to their campaigns today.</p>
                </div>
                <router-link to="/register?type=creator" class="inline-flex items-center gap-2 text-emerald-400 font-bold hover:text-emerald-300 transition-colors">
                    Browse all brand opportunities
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </router-link>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <router-link 
                    v-for="b in brands.slice(0, 12)" 
                    :key="b.id" 
                    :to="'/brands/' + b.slug"
                    class="group bg-slate-800/50 backdrop-blur-sm border border-slate-700/50 p-6 rounded-2xl hover:border-emerald-500/50 transition-all"
                >
                    <div class="h-16 aspect-square bg-white rounded-xl mb-4 p-3 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <img :src="b.logo_url" :alt="b.company_name" class="max-h-full max-w-full object-contain" />
                    </div>
                    <h4 class="font-bold text-sm truncate">{{ b.company_name || b.user?.name }}</h4>
                    <p class="text-xs text-[#94a3b8] mt-1">Active Campaigns: {{ b.active_campaigns_count || 0 }}</p>
                </router-link>
            </div>
        </div>
    </section>

    <!-- Cross-Listing: Featured Creators -->
    <section class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black mb-6">Join a Thriving Community</h2>
                <p class="text-[#64748b] max-w-2xl mx-auto">See how other creators are building their personal brands and landing huge collaborations.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <router-link 
                    v-for="c in featuredCreators.slice(0, 4)" 
                    :key="c.id"
                    :to="'/creators/' + c.slug"
                    class="group bg-white border border-[#e2e8f0] rounded-3xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl transition-all duration-500"
                >
                    <div class="h-64 relative overflow-hidden">
                        <img :src="c.avatar_url || 'https://i.pravatar.cc/400?u=' + c.id" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                        <div class="absolute bottom-4 left-4">
                            <span class="bg-black/60 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 rounded-full border border-white/20 px-2 py-1 uppercase tracking-wider">{{ c.category }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h4 class="text-lg font-bold group-hover:text-emerald-600 transition-colors">{{ c.user?.name }}</h4>
                        <p class="text-sm text-[#64748b] line-clamp-1 mt-1">{{ c.tagline }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-[#f1f5f9] pt-4">
                            <span class="text-xs font-bold text-[#94a3b8] uppercase tracking-wider">Starts at</span>
                            <span class="text-lg font-black text-[#1a1a1a]">₹{{ c.min_rate || '0' }}</span>
                        </div>
                    </div>
                </router-link>
            </div>
            
            <div class="mt-16 text-center">
                <router-link to="/creators" class="inline-flex items-center gap-3 px-8 py-4 bg-[#1a1a1a] text-white rounded-2xl font-bold hover:bg-emerald-600 transition-all hover:shadow-xl group">
                    Discover More Creators
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </router-link>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-24 px-4">
      <div class="max-w-5xl mx-auto bg-gradient-to-br from-emerald-600 to-teal-500 rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 pointer-events-none opacity-10">
          <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
              <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
            </pattern>
            <rect width="100%" height="100%" fill="url(#grid)" />
          </svg>
        </div>
        
        <div class="relative z-10">
          <h2 class="text-4xl md:text-5xl font-black mb-8 leading-tight">Ready to build your <br/>dream creator career?</h2>
          <p class="text-emerald-50 text-xl mb-12 max-w-2xl mx-auto">Create your professional profile in under 2 minutes and start connecting with brands today.</p>
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <router-link to="/register?type=creator" class="w-full sm:w-auto px-10 py-5 bg-white text-emerald-600 rounded-2xl font-black text-xl hover:bg-emerald-50 hover:shadow-2xl transition-all transform hover:-translate-y-1">
              Join StarJD Now
            </router-link>
            <router-link to="/contact" class="w-full sm:w-auto px-10 py-5 bg-emerald-700/30 text-white border border-white/20 rounded-2xl font-black text-xl hover:bg-emerald-700/40 transition-all">
              Talk to Us
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const brands = ref([]);
const featuredCreators = ref([]);

onMounted(async () => {
    // Fetch Brands
    axios.get('/api/brands').then(res => {
        brands.value = (res.data.data || res.data || []).slice(0, 12);
    });

    // Fetch Featured Creators
    axios.get('/api/creators', { params: { is_featured: 1, per_page: 8 } }).then(res => {
        featuredCreators.value = res.data.data || res.data || [];
    });
});

function getFloatingStyle(index) {
    const positions = [
        { top: '0%', left: '10%', delay: '0s' },
        { top: '20%', right: '15%', delay: '1s' },
        { top: '60%', left: '5%', delay: '2s' },
        { bottom: '0%', right: '10%', delay: '3s' },
        { top: '10%', left: '40%', delay: '1.5s' },
        { bottom: '20%', left: '20%', delay: '2.5s' },
    ];
    const pos = positions[index % positions.length];
    return {
        top: pos.top,
        left: pos.left,
        right: pos.right,
        bottom: pos.bottom,
        animation: `float 6s ease-in-out infinite`,
        animationDelay: pos.delay
    };
}
</script>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}
.animate-fade-in {
  animation: fadeIn 1s ease-out forwards;
}
.animate-fade-in-up {
  opacity: 0;
  animation: fadeInUp 1s ease-out forwards;
}
.animation-delay-300 { animation-delay: 0.3s; }
.animation-delay-500 { animation-delay: 0.5s; }

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
