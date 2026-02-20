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
          <div class="prose prose-lg max-w-none text-[#1a1a1a] prose-headings:font-bold prose-headings:text-[#1a1a1a] prose-a:text-[#e63946] prose-a:no-underline hover:prose-a:underline services-body" v-html="renderedBody"></div>
        </div>
      </article>

      <!-- Back + CTA -->
      <section class="border-t border-[#e5e7eb] bg-[#f8fafc] px-4 py-10">
        <div class="mx-auto flex max-w-3xl flex-col items-center justify-between gap-4 sm:flex-row">
          <router-link to="/services" class="inline-flex items-center text-sm font-medium text-[#64748b] transition hover:text-[#e63946]">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            All services
          </router-link>
          <router-link to="/contact" class="rounded-lg bg-[#e63946] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#c1121f]">Contact us</router-link>
        </div>
      </section>
    </template>
    <div v-else-if="loading" class="flex min-h-[50vh] items-center justify-center">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent"></div>
    </div>
    <div v-else class="px-4 py-24 text-center text-[#64748b]">Service not found.</div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const service = ref(null);
const loading = ref(true);

const bannerPositionStyle = computed(() => {
  const p = service.value?.banner_position;
  if (p === 'top') return 'center top';
  if (p === 'bottom') return 'center bottom';
  return 'center center';
});

const renderedBody = computed(() => {
  if (!service.value?.body) return '';
  const b = String(service.value.body).trim();
  if (!b) return '';
  if (b.startsWith('<')) return b;
  return b
    .split(/\r?\n\r?\n+/)
    .map((p) => '<p>' + p.replace(/\r?\n/g, '<br/>') + '</p>')
    .join('');
});

function updateDocumentMeta() {
  if (!service.value) return;
  const title = service.value.meta_title || service.value.name;
  const description = service.value.meta_description || service.value.short_description || '';
  document.title = title ? `${title} | Starjd` : document.title;
  let meta = document.querySelector('meta[name="description"]');
  if (!meta) {
    meta = document.createElement('meta');
    meta.name = 'description';
    document.head.appendChild(meta);
  }
  meta.setAttribute('content', description);
}

watch(service, updateDocumentMeta, { immediate: true });

onMounted(async () => {
  try {
    const r = await axios.get('/api/services/' + encodeURIComponent(route.params.slug));
    service.value = r.data;
  } catch {
    service.value = null;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.services-body :deep(p) {
  margin-bottom: 1em;
}
.services-body :deep(p:last-child) {
  margin-bottom: 0;
}
.services-body :deep(br) {
  display: block;
  content: '';
  margin-top: 0.25em;
}
.prose :deep(.youtube-embed) {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  margin: 1.5rem 0;
}
.prose :deep(.youtube-embed iframe) {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>
