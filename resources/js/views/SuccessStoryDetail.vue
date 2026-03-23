<template>
  <div v-if="loading" class="min-h-screen flex items-center justify-center bg-white">
    <div class="w-12 h-12 border-4 border-[#e63946] border-t-transparent rounded-full animate-spin"></div>
  </div>
  <div v-else-if="!story" class="min-h-screen flex flex-col items-center justify-center bg-white px-4 text-center">
    <h1 class="text-4xl font-black text-[#1a1a1a] mb-4">Story not found</h1>
    <p class="text-[#64748b] mb-8">The success story you're looking for doesn't exist or has been moved.</p>
    <router-link to="/success-stories" class="bg-[#1a1a1a] text-white px-8 py-3 rounded-full font-bold hover:bg-[#374151] transition">Back to Stories</router-link>
  </div>
  <div v-else class="min-h-screen bg-white">
    <!-- Story Hero -->
    <header class="relative h-[60vh] min-h-[400px] w-full overflow-hidden bg-[#1a1a1a]">
      <img
        v-if="story.image"
        :src="story.image"
        :alt="story.title"
        class="h-full w-full object-cover opacity-60"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
      
      <div class="absolute inset-0 flex items-end pb-16">
        <div class="mx-auto max-w-4xl px-4 w-full">
           <router-link to="/success-stories" class="inline-flex items-center gap-2 text-white/70 hover:text-white mb-8 font-bold uppercase tracking-widest text-xs transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
              Success Stories
           </router-link>
           
           <div v-if="story.role" class="mb-4 inline-block rounded-full bg-[#e63946] px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-white">
              {{ story.role.name }}
           </div>
           
           <h1 class="text-4xl md:text-6xl font-black text-white leading-tight mb-8">{{ story.title }}</h1>
           
           <div v-if="story.author_name" class="flex items-center gap-4 text-white">
              <div class="h-12 w-12 rounded-full border-2 border-white/20 bg-white/10 flex items-center justify-center font-bold text-xl uppercase">
                 {{ story.author_name.charAt(0) }}
              </div>
              <div>
                 <p class="font-black text-lg leading-none">{{ story.author_name }}</p>
                 <p v-if="story.author_designation" class="text-white/60 text-sm mt-1 uppercase tracking-widest font-bold">{{ story.author_designation }}</p>
              </div>
              <span class="mx-2 h-4 w-[1px] bg-white/20"></span>
              <p class="text-white/60 text-sm uppercase tracking-widest font-bold">{{ formatDate(story.created_at) }}</p>
           </div>
        </div>
      </div>
    </header>

    <!-- Content Area -->
    <article class="mx-auto max-w-4xl px-4 py-16 md:py-24">
      <div class="prose prose-lg prose-red max-w-none text-[#1a1a1a]" v-html="story.content"></div>
      
      <!-- Footer Actions -->
      <div class="mt-20 border-t border-[#e5e7eb] pt-12 flex flex-col items-center text-center">
         <h3 class="text-2xl font-black text-[#1a1a1a] mb-4">Inspired by this story?</h3>
         <p class="text-[#64748b] text-lg mb-8 max-w-xl">Join StarJD today and start your own success journey with thousands of creators and brands.</p>
         <div class="flex flex-wrap justify-center gap-4">
            <router-link to="/register?role=creator" class="bg-[#10b981] text-white px-8 py-4 rounded-2xl font-black hover:bg-[#059669] transition shadow-xl shadow-emerald-500/20">Join as Creator</router-link>
            <router-link to="/register?role=brand" class="bg-[#e63946] text-white px-8 py-4 rounded-2xl font-black hover:bg-[#c1121f] transition shadow-xl shadow-red-500/20">Join as Brand</router-link>
         </div>
      </div>
    </article>
    
    <!-- Related Stories (Optional) -->
    <section class="bg-[#fafaf9] py-24 border-t border-[#e5e7eb]">
       <div class="mx-auto max-w-6xl px-4">
          <div class="flex items-end justify-between mb-12">
             <h2 class="text-3xl font-black text-[#1a1a1a]">More stories</h2>
             <router-link to="/success-stories" class="font-black text-[#e63946] hover:underline uppercase tracking-widest text-sm">View all</router-link>
          </div>
          
          <div v-if="relatedStories.length" class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
             <SuccessStoryCard
               v-for="s in relatedStories"
               :key="s.id"
               :story="s"
             />
          </div>
          <div v-else class="text-[#94a3b8] text-center italic">Loading more stories...</div>
       </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import SuccessStoryCard from '../components/SuccessStoryCard.vue';

const route = useRoute();
const story = ref(null);
const loading = ref(true);
const relatedStories = ref([]);

async function loadStory() {
  loading.value = true;
  try {
    const { data } = await axios.get(`/api/success-stories/${route.params.slug}`);
    story.value = data;
    loadRelated(data.role?.slug);
  } catch (e) {
    console.error('Failed to load story', e);
  } finally {
    loading.value = false;
  }
}

async function loadRelated(roleSlug) {
  try {
    const { data } = await axios.get('/api/success-stories', {
      params: { 
        limit: 3,
        role_slug: roleSlug !== 'all' ? roleSlug : undefined
      }
    });
    relatedStories.value = (data.data || []).filter(s => s.id !== story.value?.id).slice(0, 3);
  } catch (e) {
    console.error('Failed to load related stories', e);
  }
}

function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
}

onMounted(loadStory);

watch(() => route.params.slug, loadStory);
</script>

<style scoped>
:deep(.prose) {
  color: #1a1a1a;
  line-height: 1.625;
}
:deep(.prose h2) {
  font-size: 1.875rem;
  font-weight: 900;
  margin-top: 3rem;
  margin-bottom: 1.5rem;
  color: #111827;
}
:deep(.prose p) {
  margin-bottom: 1.5rem;
  font-size: 1.125rem;
}
:deep(.prose img) {
  border-radius: 1.5rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  margin-top: 3rem;
  margin-bottom: 3rem;
  margin-left: auto;
  margin-right: auto;
}
:deep(.prose blockquote) {
  border-left-width: 8px;
  border-color: #e63946;
  padding-left: 2rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  font-style: italic;
  font-size: 1.5rem;
  font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
  color: #374151;
  background-color: #f9fafb;
  border-top-right-radius: 1.5rem;
  border-bottom-right-radius: 1.5rem;
  margin-top: 3rem;
  margin-bottom: 3rem;
}
</style>
