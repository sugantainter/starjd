<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero Header -->
    <section class="bg-white border-b border-[#e5e7eb] px-4 py-20 md:py-32">
      <div class="mx-auto max-w-5xl text-center">
        <span class="inline-block rounded-full bg-[#e63946]/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-[#e63946] mb-6">Learning Center</span>
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter text-[#1a1a1a] mb-8">Video Guides</h1>
        <p class="text-xl text-[#64748b] max-w-2xl mx-auto leading-relaxed">Master our platform and learn industry secrets from top-tier professional creators and marketing experts.</p>
      </div>
    </section>

    <!-- Videos List -->
    <section class="max-w-5xl mx-auto px-4 py-12 md:py-24 -mt-12">
      <div v-if="loading" class="flex justify-center py-20 bg-white rounded-3xl border border-[#e5e7eb] shadow-xl">
         <div class="w-12 h-12 border-4 border-[#e63946] border-t-transparent rounded-full animate-spin"></div>
      </div>
      
      <div v-else-if="!videos.length" class="rounded-3xl border border-[#e5e7eb] bg-white p-20 text-center shadow-xl text-[#64748b] font-bold text-lg">
         No videos published yet.
      </div>

      <div v-else class="flex flex-col gap-10">
        <div 
          v-for="video in videos" 
          :key="video.id" 
          class="group flex flex-col md:flex-row overflow-hidden rounded-3xl border border-[#e5e7eb] bg-white shadow-xl transition-all duration-500 hover:border-[#e63946]/30 hover:shadow-2xl hover:-translate-y-1"
        >
          <!-- Video Player Area -->
          <div class="w-full md:w-[480px] shrink-0 bg-black relative md:h-72 overflow-hidden aspect-video md:aspect-auto">
             <iframe 
               :src="video.embedUrl" 
               :title="video.title" 
               class="absolute inset-0 h-full w-full" 
               allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
               allowfullscreen
             ></iframe>
          </div>

          <!-- Video Details Area -->
          <div class="flex-1 p-8 md:p-12 flex flex-col">
            <div class="mb-6">
               <div class="flex items-center gap-3 mb-4">
                  <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#e63946]">Success Story</span>
                  <span class="w-1 h-1 bg-[#cbd5e1] rounded-full"></span>
                  <span class="text-xs font-bold text-[#94a3b8]">12:45 Mins</span>
               </div>
               <h2 class="text-3xl font-black text-[#1a1a1a] mb-4 group-hover:text-[#e63946] transition-colors leading-tight">{{ video.title }}</h2>
               <p v-if="video.desc" class="text-[#64748b] text-lg leading-relaxed line-clamp-3">{{ video.desc }}</p>
            </div>

            <div class="mt-auto pt-8 border-t border-[#f1f5f9] flex items-center justify-between">
               <a 
                 :href="video.watchUrl" 
                 target="_blank" 
                 rel="noopener noreferrer" 
                 class="inline-flex items-center gap-3 px-8 py-4 bg-black text-white rounded-2xl font-black text-sm hover:bg-[#e63946] transition-all group-hover:scale-105 shadow-xl shadow-black/10"
               >
                  Watch on YouTube
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 4-8 4z"/></svg>
               </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Help Section -->
    <section class="max-w-5xl mx-auto px-4 py-24">
       <div class="bg-gradient-to-br from-[#1a1a1a] to-black rounded-3xl p-12 md:p-20 text-center relative overflow-hidden shadow-2xl border border-white/5">
          <div class="relative z-10">
             <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Need more help?</h2>
             <p class="text-[#94a3b8] text-xl max-w-xl mx-auto mb-10">Our support team is available 24/7 to help you with any questions or technical issues.</p>
             <router-link to="/contact-us" class="inline-block rounded-2xl bg-white px-12 py-5 text-lg font-black text-black shadow-xl transition-all hover:bg-[#e63946] hover:text-white transform hover:scale-105">Contact Support</router-link>
          </div>
       </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const videos = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const r = await axios.get('/api/videos');
    videos.value = r.data.videos || [];
  } finally {
    loading.value = false;
  }
});
</script>
