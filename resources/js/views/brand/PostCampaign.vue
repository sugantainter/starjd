<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Post Campaign</h1>
      <p class="mt-1 text-[#64748b]">Create a new campaign and reach creators for your brand.</p>
    </div>
    <PostCampaignFlow @created="onCampaignCreated" />

    <!-- Your campaigns -->
    <div class="mt-10">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Your campaigns</h2>
      <div v-if="loading" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-8 text-center shadow-sm">
        <p class="text-[#64748b]">Loading campaigns…</p>
      </div>
      <div v-else-if="!campaigns.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f1f5f9]">
          <svg class="h-7 w-7 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
        </div>
        <p class="mt-4 text-[#64748b]">No campaigns yet.</p>
        <p class="mt-1 text-sm text-[#94a3b8]">Create one above to start receiving creator applications.</p>
      </div>
      <div v-else class="mt-4 overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[640px] text-left text-sm">
            <thead>
              <tr class="border-b border-[#e2e8f0] bg-[#f8fafc]">
                <th class="px-5 py-4 font-semibold text-[#475569]">Campaign</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Type</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Status</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Applications</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Budget</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Created</th>
                <th class="px-5 py-4 font-semibold text-[#475569] text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="c in campaigns"
                :key="c.id"
                class="border-b border-[#f1f5f9] transition hover:bg-[#fafafa]"
              >
                <td class="px-5 py-4">
                  <p class="font-medium text-[#1a1a1a]">{{ c.title }}</p>
                  <p v-if="c.slug" class="mt-0.5 text-xs text-[#94a3b8]">{{ c.slug }}</p>
                </td>
                <td class="px-5 py-4">
                  <span class="rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-xs font-medium text-[#475569]">{{ typeLabel(c.campaign_type) }}</span>
                </td>
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                    :class="statusClass(c.status)"
                  >
                    {{ statusLabel(c.status) }}
                  </span>
                </td>
                <td class="px-5 py-4">
                  <span class="font-medium text-[#1a1a1a]">{{ c.applications_count }}</span>
                  <span class="text-[#64748b]"> / {{ c.max_applications || '—' }}</span>
                  <span v-if="c.applications_pending_count > 0" class="ml-1.5 rounded bg-[#fef3c7] px-1.5 py-0.5 text-xs font-medium text-[#b45309]">{{ c.applications_pending_count }} pending</span>
                </td>
                <td class="px-5 py-4">
                  <span v-if="c.budget != null && c.budget > 0" class="text-[#1a1a1a]">₹{{ formatNumber(c.budget) }}</span>
                  <span v-else class="text-[#94a3b8]">—</span>
                </td>
                <td class="px-5 py-4 text-[#64748b]">{{ formatDate(c.created_at) }}</td>
                <td class="px-5 py-4 text-right">
                  <router-link
                    :to="{ name: 'brand-campaign-detail', params: { id: c.id } }"
                    class="mr-2 inline-flex items-center text-[#e63946] hover:underline"
                  >
                    View applications
                  </router-link>
                  <button
                    v-if="c.status === 'draft'"
                    type="button"
                    class="inline-flex items-center text-[#3b82f6] hover:underline"
                    @click="openCampaign(c)"
                  >
                    Publish
                  </button>
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
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PostCampaignFlow from '../../components/brand/PostCampaignFlow.vue';

const loading = ref(true);
const campaigns = ref([]);

function typeLabel(type) {
  const map = { instagram: 'Instagram', tiktok: 'TikTok', ugc: 'UGC', youtube: 'YouTube' };
  return type ? (map[type] || type) : '—';
}

function statusLabel(status) {
  const map = { draft: 'Draft', open: 'Open', in_progress: 'In progress', completed: 'Completed', cancelled: 'Cancelled' };
  return status ? (map[status] || status) : '—';
}

function statusClass(status) {
  const map = {
    draft: 'bg-slate-100 text-slate-700',
    open: 'bg-emerald-100 text-emerald-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-slate-100 text-slate-600',
    cancelled: 'bg-red-100 text-red-700',
  };
  return map[status] || 'bg-slate-100 text-slate-600';
}

function formatNumber(n) {
  if (n == null) return '—';
  if (n >= 100000) return (n / 100000).toFixed(1) + 'L';
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
  return Number(n).toLocaleString();
}

function formatDate(iso) {
  if (!iso) return '—';
  const d = new Date(iso);
  return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
}

async function openCampaign(c) {
  if (!confirm('Publish this campaign so creators can apply?')) return;
  try {
    await axios.patch('/api/brand/campaigns/' + c.id, { status: 'open' }, { withCredentials: true });
    await loadCampaigns();
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to publish campaign.');
  }
}

async function loadCampaigns() {
  loading.value = true;
  try {
    const res = await axios.get('/api/brand/dashboard', { withCredentials: true });
    campaigns.value = res.data?.campaigns ?? [];
  } catch (_) {
    campaigns.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(loadCampaigns);

function onCampaignCreated() {
  loadCampaigns();
}
</script>
