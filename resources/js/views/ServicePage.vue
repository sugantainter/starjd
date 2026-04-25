<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <template v-if="service">
      <!-- Banner / wallpaper hero -->
      <section class="relative aspect-[21/9] min-h-[240px] overflow-hidden md:min-h-[320px]">
        <img
          v-if="service.banner_image"
          :src="service.banner_image"
          :alt="service.name"
          class="absolute inset-0 h-full w-full object-cover"
          :style="{ objectPosition: bannerPositionStyle }"
          @error="onServiceBannerError"
        />
        <div
          v-else
          class="absolute inset-0 bg-gradient-to-br from-[#1a1a1a] via-[#2d2d2d] to-[#1a1a1a]"
        ></div>
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 flex items-end px-4 pb-8 md:px-8 md:pb-12">
          <div class="mx-auto w-full max-w-4xl">
            <h1 class="text-3xl font-bold tracking-tight text-white drop-shadow-lg md:text-4xl lg:text-5xl">{{ service.name }}</h1>
            <p v-if="service.short_description" class="mt-2 max-w-2xl text-lg text-[#e2e8f0] drop-shadow md:text-xl">{{ service.short_description }}</p>
          </div>
        </div>
      </section>

      <!-- Main content -->
      <article class="border-b border-[#e5e7eb] bg-white px-4 py-12 md:py-20">
        <div class="mx-auto max-w-3xl">
          <RichTextContent
            :content="service.body"
          />
        </div>
      </article>

      <!-- Back + CTA -->
      <section class="border-t border-[#e5e7eb] bg-[#f8fafc] px-4 pt-10 pb-20 md:pb-24">
        <div class="mx-auto flex max-w-3xl flex-col items-center justify-between gap-4 sm:flex-row">
          <router-link to="/services" class="inline-flex items-center text-sm font-medium text-[#64748b] transition hover:text-[#fc4402]">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            All services
          </router-link>
          <router-link to="/contact-us" class="rounded-lg bg-[#fc4402] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#e63d02]">Contact us</router-link>
        </div>
      </section>
    </template>
    <div v-else-if="loading" class="flex min-h-[50vh] items-center justify-center">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#fc4402] border-t-transparent"></div>
    </div>
    <div v-else class="px-4 pt-24 pb-20 md:pb-24 text-center text-[#64748b]">Service not found.</div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import RichTextContent from '../components/RichTextContent.vue';

const route = useRoute();
const service = ref(null);
const loading = ref(true);

const SERVICE_BANNER_FALLBACK =
  'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1920&h=600&fit=crop';

function onServiceBannerError(e) {
  const el = e?.target;
  if (!el || el.dataset.bannerFallback === '1') return;
  el.dataset.bannerFallback = '1';
  el.src = SERVICE_BANNER_FALLBACK;
}

const bannerPositionStyle = computed(() => {
  const p = service.value?.banner_position;
  if (p === 'top') return 'center top';
  if (p === 'bottom') return 'center bottom';
  return 'center center';
});

// Reactive SEO Title & Description
const seoTitle = computed(() => {
  if (!service.value) return 'Service | StarJD';
  return `${service.value.meta_title || service.value.name} | StarJD`;
});

const seoDescription = computed(() => {
  if (!service.value) return 'Professional influencer marketing services on StarJD.';
  return service.value.meta_description || service.value.short_description || 'Explore our professional creative services.';
});

const seoImage = computed(() => {
  return service.value?.banner_image || (window.location.origin + '/logo.png');
});

// useHead must be called in setup synchronously
useHead({
  title: seoTitle,
  meta: [
    { name: 'description', content: seoDescription },
    { property: 'og:title', content: seoTitle },
    { property: 'og:description', content: seoDescription },
    { property: 'og:image', content: seoImage },
    { property: 'og:type', content: 'website' }
  ],
  script: [
    {
      type: 'application/ld+json',
      children: computed(() => {
        if (!service.value) return '';
        return JSON.stringify({
          "@context": "https://schema.org",
          "@type": "Service",
          "name": service.value.name,
          "description": service.value.short_description || seoDescription.value,
          "provider": {
            "@type": "Organization",
            "name": "StarJD"
          },
          "image": seoImage.value,
          "url": typeof window !== 'undefined' ? window.location.href : ''
        });
      })
    }
  ]
});

async function loadService() {
  loading.value = true;
  service.value = null;
  try {
    const r = await axios.get('/api/services/' + encodeURIComponent(route.params.slug));
    service.value = r.data?.data ?? r.data;
  } catch {
    service.value = null;
  } finally {
    loading.value = false;
  }
}

watch(() => route.params.slug, loadService);

onMounted(() => {
  loadService();
});
</script>
