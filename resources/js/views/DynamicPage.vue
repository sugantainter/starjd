<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <template v-if="loading">
      <div class="flex min-h-[40vh] items-center justify-center">
        <p class="text-[#64748b]">Loading…</p>
      </div>
    </template>
    <template v-else-if="page">
      <header class="border-b border-[#e5e7eb] bg-white">
        <div class="mx-auto max-w-4xl px-4 py-12 md:py-16">
          <h1 class="text-3xl font-bold leading-tight text-[#1a1a1a] md:text-4xl">{{ page.title }}</h1>
        </div>
      </header>
      <div class="mx-auto max-w-4xl px-4 py-10">
        <RichTextContent
          :content="page.content"
        />
      </div>
    </template>
    <template v-else>
      <div class="flex min-h-[40vh] flex-col items-center justify-center px-4">
        <h2 class="text-xl font-semibold text-[#1a1a1a]">Page not found</h2>
        <p class="mt-2 text-[#64748b]">The page you're looking for doesn't exist or is not published.</p>
        <router-link to="/" class="mt-4 text-[#e63946] hover:underline">Back to home</router-link>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import RichTextContent from '../components/RichTextContent.vue';

const route = useRoute();
const page = ref(null);
const loading = ref(true);



async function loadPage() {
  let slug = route.meta?.pageSlug || route.params.slug;
  if (!slug) {
    loading.value = false;
    return;
  }

  // Handle SEO-friendly slug format: slug-in-location
  let locationSlug = null;
  if (slug.includes('-in-')) {
    const parts = slug.split('-in-');
    // The last part after the last '-in-' is the location (city or state)
    locationSlug = parts.pop();
    slug = parts.join('-in-');
  }

  loading.value = true;
  page.value = null;
  try {
    const params = {};
    // Priority 1: Query parameters (backward compatibility)
    if (route.query.state_slug) params.state_slug = route.query.state_slug;
    if (route.query.city_slug) params.city_slug = route.query.city_slug;
    
    // Priority 2: Path-based location (slug-in-location)
    // If we have a locationSlug, let the backend try it as both city and state
    if (locationSlug && !params.state_slug && !params.city_slug) {
      params.state_slug = locationSlug;
      params.city_slug = locationSlug;
    }

    const r = await axios.get(`/api/pages/${encodeURIComponent(slug)}`, { params });
    page.value = r.data;
    
    // Update Meta Tags
    if (page.value) {
      document.title = page.value.meta_title || page.value.title || 'StarJD';
      const metaDescription = document.querySelector('meta[name="description"]');
      if (metaDescription) {
        metaDescription.setAttribute('content', page.value.meta_description || '');
      }
    }
  } catch (e) {
    if (e.response?.status !== 404) {
      page.value = null;
    }
  } finally {
    loading.value = false;
  }
}

onMounted(loadPage);
watch(() => [route.meta?.pageSlug, route.params.slug, route.query.state_slug, route.query.city_slug], loadPage);
</script>
