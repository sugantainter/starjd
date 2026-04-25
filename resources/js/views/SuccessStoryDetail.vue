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
    <header class="relative overflow-hidden bg-[#1a1a1a] py-20 px-4 md:py-32">
      <!-- Background Image with Overlay -->
      <div v-if="story.image" class="absolute inset-0 z-0">
        <img
          :src="story.image"
          :alt="story.title"
          class="h-full w-full object-cover opacity-40 grayscale-[50%]"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-[#1a1a1a] via-[#1a1a1a]/40 to-transparent"></div>
      </div>
      
      <div class="relative z-10 mx-auto max-w-4xl">
         <!-- Breadcrumbs -->
         <nav class="mb-12 flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.3em] text-white/50">
            <router-link to="/" class="hover:text-white transition">Home</router-link>
            <span>/</span>
            <router-link to="/success-stories" class="hover:text-white transition">Success Stories</router-link>
            <template v-if="story.role">
               <span>/</span>
               <span class="text-[#e63946]">{{ story.role.name }}</span>
            </template>
         </nav>
         
         <div v-if="story.role" class="mb-8 inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-2 pr-6 text-[10px] font-black uppercase tracking-widest text-white backdrop-blur-md">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#e63946] text-white shadow-lg shadow-[#e63946]/40">★</span>
            {{ story.role.name }} Success Story
         </div>
         
         <h1 class="text-4xl md:text-7xl font-black text-white leading-[1.05] tracking-tight mb-12 animate-fade-in-up">
            {{ story.title }}
         </h1>
         
         <div v-if="story.author_name" class="flex items-center gap-6 text-white animate-fade-in-up animation-delay-200">
            <div class="h-16 w-16 rounded-[1.25rem] border-2 border-white/20 bg-white/10 flex items-center justify-center font-black text-2xl shadow-2xl backdrop-blur-xl">
               {{ story.author_name.charAt(0) }}
            </div>
            <div>
               <p class="text-xl font-black leading-tight tracking-tight">{{ story.author_name }}</p>
               <div class="mt-2 flex items-center gap-3">
                  <p v-if="story.author_designation" class="text-white/50 text-[10px] uppercase tracking-widest font-black">{{ story.author_designation }}</p>
                  <span class="h-1 w-1 rounded-full bg-[#e63946]"></span>
                  <p class="text-white/50 text-[10px] uppercase tracking-widest font-black">{{ formatDate(story.created_at) }}</p>
               </div>
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
    
    <!-- Related Stories -->
    <section class="bg-[#fafaf9] py-24 border-t border-[#e5e7eb]">
       <div class="mx-auto max-w-6xl px-4">
          <div class="flex items-end justify-between mb-12">
             <h2 class="text-3xl font-black text-[#1a1a1a]">More stories</h2>
             <router-link to="/success-stories" class="font-black text-[#e63946] hover:underline uppercase tracking-widest text-sm">View all</router-link>
          </div>
          
          <div v-if="loadingRelated" class="flex justify-center py-12">
             <div class="h-8 w-8 animate-spin rounded-full border-4 border-[#e63946] border-t-transparent"></div>
          </div>
          <div v-else-if="relatedStories.length" class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
             <SuccessStoryCard
               v-for="s in relatedStories"
               :key="s.id"
               :story="s"
             />
          </div>
          <div v-else class="text-[#94a3b8] text-center border border-dashed border-[#e2e8f0] rounded-3xl py-12">
            <p class="italic">No more stories found in this category.</p>
            <router-link to="/success-stories" class="mt-4 inline-block text-[#e63946] font-bold hover:underline">Browse all stories</router-link>
          </div>
       </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import SuccessStoryCard from '../components/SuccessStoryCard.vue';

const route = useRoute();
const story = ref(null);
const loading = ref(true);
const loadingRelated = ref(false);
const relatedStories = ref([]);

// Reactive SEO Title & Description
const seoTitle = computed(() => {
  if (!story.value) return 'Success Story | StarJD';
  return `${story.value.title} | ${story.value.role?.name || 'Success Story'} | StarJD`;
});

const seoDescription = computed(() => {
  if (!story.value) return 'Read success stories on StarJD.';
  return story.value.content?.replace(/<[^>]*>/g, '').substring(0, 160) || `Read how ${story.value.author_name} achieved massive growth and success on StarJD.`;
});

const seoImage = computed(() => {
  return story.value?.image || (window.location.origin + '/logo.png');
});

// useHead must be called in setup synchronously
useHead({
  title: seoTitle,
  meta: [
    { name: 'description', content: seoDescription },
    { property: 'og:title', content: seoTitle },
    { property: 'og:description', content: seoDescription },
    { property: 'og:image', content: seoImage },
    { property: 'og:type', content: 'article' },
    { name: 'twitter:card', content: 'summary_large_image' }
  ],
  script: [
    {
      type: 'application/ld+json',
      children: computed(() => {
        if (!story.value) return '';
        return JSON.stringify({
          "@context": "https://schema.org",
          "@type": "Article",
          "headline": story.value.title,
          "description": seoDescription.value,
          "image": seoImage.value,
          "author": {
            "@type": "Person",
            "name": story.value.author_name || "StarJD User"
          },
          "publisher": {
            "@type": "Organization",
            "name": "StarJD",
            "logo": {
              "@type": "ImageObject",
              "url": window.location.origin + "/logo.png"
            }
          },
          "datePublished": story.value.created_at,
          "url": typeof window !== 'undefined' ? window.location.href : ''
        });
      })
    }
  ]
});

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
  loadingRelated.value = true;
  try {
    const { data } = await axios.get('/api/success-stories', {
      params: { 
        limit: 4,
        role_slug: roleSlug !== 'all' ? roleSlug : undefined
      }
    });
    // Filter out current story and take top 3
    relatedStories.value = (data.data || []).filter(s => s.id !== story.value?.id).slice(0, 3);
  } catch (e) {
    console.error('Failed to load related stories', e);
  } finally {
    loadingRelated.value = false;
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
