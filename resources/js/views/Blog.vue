<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero Header -->
    <section class="bg-white border-b border-[#e5e7eb] px-4 py-20 md:py-32">
      <div class="mx-auto max-w-5xl text-center">
        <span class="inline-block rounded-full bg-[#e63946]/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-[#e63946] mb-6">StarJD Insights</span>
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter text-[#1a1a1a] mb-8">The Creative Blog</h1>
        <p class="text-xl text-[#64748b] max-w-2xl mx-auto leading-relaxed">Master the art of influencer marketing with our curated collection of insights, trends, and success strategies.</p>
        <p v-if="route.query.category" class="mt-8 text-sm font-black text-[#e63946] uppercase tracking-widest">Filtering by: {{ categoryLabel }}</p>
      </div>
    </section>

    <!-- Posts List -->
    <section class="px-4 py-12 md:py-24 -mt-12">
      <div class="mx-auto max-w-5xl">
        <div v-if="loading" class="flex justify-center py-20 bg-white rounded-3xl border border-[#e5e7eb] shadow-xl">
           <div class="w-12 h-12 border-4 border-[#e63946] border-t-transparent rounded-full animate-spin"></div>
        </div>
        
        <div v-else-if="!filteredPosts.length" class="rounded-3xl border border-[#e5e7eb] bg-white p-20 text-center shadow-xl text-[#64748b] font-bold text-lg">
           No articles found in this category.
        </div>

        <div v-else class="flex flex-col gap-10">
          <article
            v-for="post in filteredPosts"
            :key="post.id"
            class="group flex flex-col md:flex-row overflow-hidden rounded-3xl border border-[#e5e7eb] bg-white shadow-xl transition-all duration-500 hover:border-[#e63946]/30 hover:shadow-2xl hover:-translate-y-1"
          >
            <!-- Feature Image -->
            <div class="w-full md:w-96 h-64 md:h-80 shrink-0 overflow-hidden bg-[#f3f4f6] relative">
               <img v-if="post.image" :src="post.image" :alt="post.title" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" />
               <div v-else class="h-full w-full flex items-center justify-center bg-gradient-to-br from-[#e63946] to-[#1a1a1a]">
                 <span class="text-6xl font-black text-white/20">{{ post.title.charAt(0) }}</span>
               </div>
               <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent md:hidden"></div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 p-8 md:p-12 flex flex-col">
              <div class="mb-6">
                 <div class="flex items-center gap-3 mb-4">
                    <span v-if="post.category" class="text-[10px] font-black uppercase tracking-[0.2em] text-[#e63946]">{{ post.category }}</span>
                    <span class="w-1 h-1 bg-[#cbd5e1] rounded-full"></span>
                    <span class="text-xs font-bold text-[#94a3b8]">{{ post.date }}</span>
                 </div>
                 <h2 class="text-3xl font-black text-[#1a1a1a] mb-4 group-hover:text-[#e63946] transition-colors leading-tight">{{ post.title }}</h2>
                 <p v-if="post.excerpt" class="text-[#64748b] text-lg leading-relaxed line-clamp-3">{{ post.excerpt }}</p>
              </div>

              <div class="mt-auto pt-8 border-t border-[#f1f5f9] flex items-center justify-between">
                 <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#f1f5f9] border border-[#e2e8f0] flex items-center justify-center">
                       <span class="text-[10px] font-bold text-[#64748b]">JD</span>
                    </div>
                    <span class="text-xs font-black text-[#1a1a1a] uppercase tracking-widest">StarJD Team</span>
                 </div>

                 <router-link :to="'/blog/' + post.slug" class="inline-flex items-center gap-2 text-sm font-black text-[#1a1a1a] group-hover:text-[#e63946] transition-colors uppercase tracking-widest">
                    Read Story
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                 </router-link>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Professional Newsletter CTA -->
    <section class="max-w-5xl mx-auto px-4 py-24">
       <div class="bg-black rounded-3xl p-12 md:p-20 text-center relative overflow-hidden shadow-2xl">
          <div class="absolute inset-0 opacity-20">
             <div class="absolute top-0 right-0 w-96 h-96 bg-[#e63946] rounded-full blur-[120px]"></div>
          </div>
          <div class="relative z-10">
             <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Stay ahead of the curve</h2>
             <p class="text-[#94a3b8] text-xl max-w-xl mx-auto mb-10">Get the best of StarJD Insights delivered to your inbox once a week. No spam, just strategies.</p>
             <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" placeholder="Enter your email" class="flex-1 px-6 py-4 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:border-white transition-all" />
                <button class="px-8 py-4 bg-[#e63946] text-white font-black rounded-2xl shadow-xl shadow-[#e63946]/20 transition-all hover:bg-[#c1121f] transform hover:scale-105">Subscribe</button>
             </div>
          </div>
       </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const posts = ref([]);
const loading = ref(true);

function slugify(text) {
  if (!text) return '';
  return String(text)
    .trim()
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w-]+/g, '');
}

const filteredPosts = computed(() => {
  const list = posts.value;
  const cat = route.query.category;
  if (!cat) return list;
  return list.filter((p) => slugify(p.category) === cat);
});

const categoryLabel = computed(() => {
  const cat = route.query.category;
  if (!cat) return '';
  if (filteredPosts.value.length) return filteredPosts.value[0]?.category || cat;
  return cat.replace(/-/g, ' ');
});

onMounted(async () => {
  try {
    const r = await axios.get('/api/posts');
    posts.value = r.data.posts || [];
  } finally {
    loading.value = false;
  }
});
</script>
