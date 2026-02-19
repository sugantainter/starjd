<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <article v-if="service" class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-3xl">
        <h1 class="text-3xl font-bold text-[#1a1a1a] md:text-4xl">{{ service.name }}</h1>
        <p v-if="service.short_description" class="mt-4 text-lg text-[#6b7280]">{{ service.short_description }}</p>
        <div class="prose prose-lg mt-8 max-w-none text-[#1a1a1a]" v-html="renderedBody"></div>
      </div>
    </article>
    <div v-else-if="loading" class="px-4 py-24 text-center text-[#6b7280]">Loading…</div>
    <div v-else class="px-4 py-24 text-center text-[#6b7280]">Service not found.</div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const service = ref(null);
const loading = ref(true);

const renderedBody = computed(() => {
  if (!service.value?.body) return '';
  const b = service.value.body;
  if (b.startsWith('<')) return b;
  return b
    .split(/\n\n+/)
    .map((p) => '<p>' + p.replace(/\n/g, '<br/>') + '</p>')
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
