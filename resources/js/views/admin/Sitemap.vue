<template>
  <div class="sitemap-management">
    <header class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-[#1a1a1a]">Sitemap Management</h1>
        <p class="mt-1 text-sm text-[#64748b]">Generate and manage XML sitemaps for search engines</p>
      </div>
      <button 
        @click="generate" 
        :disabled="generating"
        class="inline-flex items-center justify-center rounded-lg bg-[#e63946] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#d62839] focus:outline-none focus:ring-2 focus:ring-[#e63946] focus:ring-offset-2 disabled:opacity-50"
      >
        <span v-if="generating" class="mr-2">
          <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </span>
        {{ generating ? 'Generating...' : 'Generate Sitemap Now' }}
      </button>
    </header>

    <div class="grid gap-6">
      <!-- Status Cards -->
      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
          <p class="text-sm font-medium text-[#64748b]">Last Generated</p>
          <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ status.last_generated || 'Never' }}</p>
        </div>
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
          <p class="text-sm font-medium text-[#64748b]">URLs in Sitemap</p>
          <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ status.total_url_count ?? 0 }}</p>
          <p class="mt-0.5 text-xs text-[#94a3b8]">Indexed page entries</p>
        </div>
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
          <p class="text-sm font-medium text-[#64748b]">Total Sitemap Files</p>
          <p class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ status.sitemaps?.length || 0 }}</p>
        </div>
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
          <p class="text-sm font-medium text-[#64748b]">Main Index URL</p>
          <div class="mt-1 flex items-center gap-2">
            <code class="rounded bg-[#f1f5f9] px-2 py-1 text-xs text-[#e63946] break-all">{{ mainIndexUrl }}</code>
          </div>
        </div>
      </div>

      <!-- Info Box -->
      <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-4 text-blue-800">
        <div class="flex">
          <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          <div class="ml-3">
            <h3 class="text-sm font-semibold">Pro Tip: Google Search Console</h3>
            <p class="mt-1 text-sm opacity-90">
              Submit the <code>sitemap.xml</code> URL to Google Search Console to speed up indexing of your <strong>blogs, campaigns, creators, and studios</strong>. We automatically split large sitemaps (30,000+ URLs) to maintain efficiency.
            </p>
          </div>
        </div>
      </div>

      <!-- Sitemaps List -->
      <div class="rounded-xl border border-[#e2e8f0] bg-white shadow-sm overflow-hidden">
        <div class="border-b border-[#e2e8f0] bg-[#f8fafc] px-5 py-4">
          <h2 class="text-sm font-semibold text-[#1a1a1a]">Generated XML Files</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-[#f8fafc] text-[#64748b] uppercase text-[11px] font-bold tracking-wider">
              <tr>
                <th class="px-5 py-3">File Name</th>
                <th class="px-5 py-3 text-right">URLs</th>
                <th class="px-5 py-3">Size</th>
                <th class="px-5 py-3">Last Modified</th>
                <th class="px-5 py-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e2e8f0]">
              <tr v-if="loading_status" v-for="i in 3" :key="'loader-'+i">
                <td colspan="5" class="px-5 py-4"><div class="h-4 w-full animate-pulse rounded bg-[#f1f5f9]"></div></td>
              </tr>
              <tr v-else-if="status.sitemaps?.length === 0">
                <td colspan="5" class="px-5 py-10 text-center text-[#94a3b8]">No sitemaps generated yet.</td>
              </tr>
              <tr v-for="file in status.sitemaps" :key="file.name" class="hover:bg-[#f8fafc] transition">
                <td class="px-5 py-4">
                  <span class="font-medium text-[#1a1a1a]">{{ file.name }}</span>
                </td>
                <td class="px-5 py-4 text-right tabular-nums font-semibold text-[#1a1a1a]">{{ file.url_count ?? 0 }}</td>
                <td class="px-5 py-4 text-[#64748b]">{{ file.size }}</td>
                <td class="px-5 py-4 text-[#64748b]">{{ file.last_modified }}</td>
                <td class="px-5 py-4 text-right">
                  <a :href="file.url" target="_blank" class="text-[#e63946] hover:underline font-medium">View XML</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { notify } from '../../lib/notify.js';

const status = ref({});
const loading_status = ref(true);
const generating = ref(false);

const mainIndexUrl = computed(() => {
  return window.location.origin + '/sitemap.xml';
});

const fetchStatus = async () => {
  try {
    const r = await axios.get('/api/admin/sitemap');
    status.value = r.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading_status.value = false;
  }
};

const generate = async () => {
  generating.value = true;
  try {
    const r = await axios.post('/api/admin/sitemap/generate');
    if (r.data.success) {
      notify.success(r.data.message || 'Sitemap generated successfully!');
      fetchStatus();
    }
  } catch (err) {
    notify.error(err.response?.data?.message || 'Failed to generate sitemap');
  } finally {
    generating.value = false;
  }
};

onMounted(fetchStatus);
</script>

<style scoped>
.sitemap-management {
  max-width: 1200px;
}
</style>
