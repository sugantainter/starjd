<template>
  <div class="min-h-screen bg-[#fafafa]">
    <template v-if="loading">
      <div class="flex min-h-[60vh] flex-col items-center justify-center">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-[#e63946]/10 border-t-[#e63946]"></div>
        <p class="mt-4 text-sm font-medium text-[#64748b]">Loading perfection...</p>
      </div>
    </template>

    <template v-else-if="page">
      <!-- Breadcrumbs & Hero -->
      <header class="bg-white border-b border-[#e2e8f0] pt-8 pb-12">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
          <nav class="mb-6 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-[#94a3b8]">
            <router-link to="/" class="hover:text-[#e63946]">Home</router-link>
            <span>/</span>
            <span class="text-[#e63946]">{{ page.title }}</span>
          </nav>

          <div class="grid gap-12 lg:grid-cols-[1fr_400px]">
            <div>
              <h1 class="text-4xl font-black tracking-tight text-[#1a1a1a] md:text-5xl lg:text-6xl">
                {{ page.title }}
              </h1>
              <div 
                class="mt-6 text-lg leading-relaxed text-[#64748b] md:text-xl prose-sm max-w-none" 
                v-html="page.intro_text || page.content || 'Connecting you with top-rated professionals and verified services in your location.'"
              >
              </div>
              
              <div class="mt-8 flex flex-wrap gap-4">
                <button class="rounded-xl bg-[#e63946] px-8 py-4 text-lg font-bold text-white shadow-lg hover:bg-[#c1121f] transition">
                  Get Best Quotes
                </button>
                <div class="flex items-center gap-2 rounded-xl border border-[#e2e8f0] bg-white px-6 py-4">
                  <div class="flex -space-x-2">
                    <img v-for="i in 3" :key="i" :src="`https://i.pravatar.cc/100?img=${i+10}`" class="h-8 w-8 rounded-full border-2 border-white shadow-sm" />
                  </div>
                  <span class="text-sm font-bold text-[#1a1a1a]">4.8/5 (2k+ Users)</span>
                </div>
              </div>
            </div>
            
            <div class="hidden lg:block relative">
              <div class="rounded-3xl bg-gradient-to-br from-[#e63946] to-[#c1121f] p-8 text-white shadow-2xl">
                <h3 class="text-xl font-bold">Trusted by Thousands</h3>
                <p class="mt-2 text-sm text-white/80">Get connected with 100% verified professionals in seconds.</p>
                <div class="mt-6 space-y-4">
                  <div v-for="f in ['Verified Profiles', 'Secure Payments', 'Best Prices']" :key="f" class="flex items-center gap-3">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-[10px]">✓</div>
                    <span class="text-sm font-medium">{{ f }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Dynamic Sections Layout -->
      <main class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1fr_320px]">
          <div class="space-y-16">
            <!-- Relevant Professionals Grid -->
            <section v-if="relevantInfluencers.length">
              <div class="mb-8 flex items-end justify-between border-b border-[#e2e8f0] pb-4">
                <h2 class="text-2xl font-black text-[#1a1a1a]">Featured Profiles</h2>
                <router-link to="/creators" class="text-sm font-bold text-[#e63946] hover:underline">View All</router-link>
              </div>
              <div class="grid gap-6 sm:grid-cols-2">
                <router-link 
                  v-for="p in relevantInfluencers" 
                  :key="p.id" 
                  :to="'/creator-profile/' + p.slug"
                  class="group flex items-center gap-4 rounded-2xl border border-[#e2e8f0] bg-white p-4 transition hover:border-[#e63946]/30 hover:shadow-xl"
                >
                  <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-[#f1f5f9]">
                    <img :src="p.avatar_url || 'https://ui-avatars.com/api?name=' + encodeURIComponent(p.user?.name || 'User')" class="h-full w-full object-cover group-hover:scale-110 transition duration-500" />
                  </div>
                  <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-[#1a1a1a] group-hover:text-[#e63946]">{{ p.user?.name || 'Professional' }}</h3>
                    <p class="mt-0.5 line-clamp-1 text-xs text-[#64748b]">{{ p.tagline || 'Verified StarJD Professional' }}</p>
                    <div class="mt-2 flex items-center gap-2">
                      <span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700">VERIFIED</span>
                      <span class="text-[10px] font-medium text-[#94a3b8]">4.9 ★</span>
                    </div>
                  </div>
                </router-link>
              </div>
            </section>

            <!-- Professional Content Hub -->
            <section v-if="page.guide_content && page.guide_content.length" class="space-y-10">
              <div v-for="(section, idx) in page.guide_content" :key="idx" class="prose max-w-none prose-headings:text-[#1a1a1a] prose-p:text-[#475569] prose-p:leading-relaxed">
                <h2 class="text-2xl font-black tracking-tight text-[#1a1a1a]">{{ section.title }}</h2>
                <div v-html="section.content"></div>
              </div>
            </section>

            <!-- FAQs Accordion -->
            <section v-if="page.faqs && page.faqs.length" class="rounded-3xl border border-[#e2e8f0] bg-white p-8">
              <h2 class="mb-8 text-2xl font-black text-[#1a1a1a]">Frequently Asked Questions</h2>
              <div class="space-y-4">
                <div v-for="(faq, idx) in page.faqs" :key="idx" class="border-b border-[#f1f5f9] last:border-0 pb-4 last:pb-0">
                  <button @click="openFaq === idx ? openFaq = null : openFaq = idx" class="flex w-full items-center justify-between py-4 text-left">
                    <span class="text-lg font-bold text-[#1a1a1a]">{{ faq.q }}</span>
                    <svg class="h-5 w-5 text-[#64748b] transition-transform" :class="{ 'rotate-180': openFaq === idx }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                  </button>
                  <div v-if="openFaq === idx" class="pb-4 text-sm leading-relaxed text-[#64748b] animate-in slide-in-from-top-2">
                    {{ faq.a }}
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- Sidebar -->
          <aside class="space-y-8 lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-3xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
              <h3 class="text-sm font-black uppercase tracking-widest text-[#1a1a1a]">Quick Connect</h3>
              <p class="mt-2 text-xs text-[#64748b]">Get personalized recommendations based on your needs.</p>
              <div class="mt-6 space-y-3">
                <input type="text" placeholder="Your Name" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm focus:border-[#e63946] focus:outline-none" />
                <input type="tel" placeholder="Phone Number" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm focus:border-[#e63946] focus:outline-none" />
                <button class="w-full rounded-xl bg-[#e63946] py-3 text-sm font-bold text-white shadow-md hover:bg-[#c1121f] transition">Submit Request</button>
              </div>
            </div>

            <div class="rounded-3xl bg-[#1e293b] p-8 text-white">
              <h3 class="text-xl font-bold">Are you a professional?</h3>
              <p class="mt-3 text-xs text-gray-400">Join StarJD and list your services to reach thousands of potential clients.</p>
              <router-link to="/register" class="mt-6 inline-block rounded-xl bg-white px-6 py-2.5 text-xs font-bold text-[#1e293b] transition hover:bg-[#e63946] hover:text-white">Register Now</router-link>
            </div>
          </aside>
        </div>
      </main>

      <!-- Sticky Bottom CTA (Mobile Only) -->
      <div class="fixed bottom-0 left-0 right-0 z-50 border-t border-[#e2e8f0] bg-white p-4 shadow-[0_-10px_20px_rgba(0,0,0,0.05)] lg:hidden">
        <button class="w-full rounded-xl bg-[#e63946] py-4 text-lg font-bold text-white shadow-lg active:scale-95 transition">
          Get Free Quotes
        </button>
      </div>
    </template>

    <template v-else>
      <div class="flex min-h-[60vh] flex-col items-center justify-center p-8 text-center">
        <div class="text-6xl">😕</div>
        <h2 class="mt-4 text-2xl font-bold text-[#1a1a1a]">Page Not Found</h2>
        <p class="mt-2 text-[#64748b]">The content you are looking for is missing or moved.</p>
        <router-link to="/" class="mt-8 rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-bold text-white">Back to Home</router-link>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useHead } from '@unhead/vue';
import axios from 'axios';

const route = useRoute();
const page = ref(null);
const loading = ref(true);
const relevantInfluencers = ref([]);
const openFaq = ref(0);

// Use computed properties for SEO to avoid useHead context issues in async functions
const seoTitle = computed(() => page.value?.meta_title || page.value?.title || 'StarJD — Connect. Create. Collaborate.');
const seoDesc = computed(() => page.value?.meta_description || page.value?.intro_text || 'Premium local services and professional talent on StarJD.');

useHead({
  title: seoTitle,
  meta: [
    { name: 'description', content: seoDesc },
    { property: 'og:title', content: seoTitle },
    { property: 'og:description', content: seoDesc },
    { property: 'og:url', content: () => window.location.origin + route.fullPath },
  ]
});

async function load() {
  const slug = route.params.slug;
  if (!slug) return;
  
  loading.value = true;
  try {
    const r = await axios.get(`/api/seo-content/${slug}`);
    page.value = r.data.page;
    relevantInfluencers.value = r.data.relevant_influencers || [];
  } catch (e) {
    console.error('Content Load Error:', e);
    page.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(load);
watch(() => route.params.slug, load);
</script>

<style scoped>
.prose :deep(h2) {
  margin-top: 2.5rem;
  margin-bottom: 1.25rem;
  font-weight: 900;
  letter-spacing: -0.025em;
}
.prose :deep(p) {
  margin-bottom: 1.5rem;
  line-height: 1.75;
}
.animate-in {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
