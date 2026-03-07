<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-[#1a1a1a]">Dashboard</h1>
        <p class="mt-1 text-[#64748b]">Welcome back, {{ data?.user?.name }}.</p>
      </div>
      <PostCampaignFlow @created="onCampaignCreated" />
    </div>

    <!-- Stats cards -->
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e63946]/10">
            <svg class="h-5 w-5 text-[#e63946]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-[#64748b]">Collaborations</p>
            <p class="text-2xl font-bold text-[#1a1a1a]">{{ data?.collaborations?.length ?? 0 }}</p>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#3b82f6]/10">
            <svg class="h-5 w-5 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-[#64748b]">Total campaigns</p>
            <p class="text-2xl font-bold text-[#1a1a1a]">{{ data?.campaign_stats?.total ?? 0 }}</p>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#10b981]/10">
            <svg class="h-5 w-5 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-[#64748b]">Open for applications</p>
            <p class="text-2xl font-bold text-[#1a1a1a]">{{ data?.campaign_stats?.open ?? 0 }}</p>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#8b5cf6]/10">
            <svg class="h-5 w-5 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-[#64748b]">Total applications</p>
            <p class="text-2xl font-bold text-[#1a1a1a]">{{ data?.campaign_stats?.total_applications ?? 0 }}</p>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#f59e0b]/10">
            <svg class="h-5 w-5 text-[#f59e0b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-[#64748b]">Pending review</p>
            <p class="text-2xl font-bold text-[#1a1a1a]">{{ data?.campaign_stats?.pending_applications ?? 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Your campaigns -->
    <div class="mt-10">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Your campaigns</h2>
        <router-link to="/brand/post-campaign" class="text-sm font-medium text-[#e63946] hover:underline">+ New campaign</router-link>
      </div>
      <div v-if="!data?.campaigns?.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f1f5f9]">
          <svg class="h-7 w-7 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
        </div>
        <p class="mt-4 text-[#64748b]">No campaigns yet.</p>
        <p class="mt-1 text-sm text-[#94a3b8]">Create a campaign to start receiving creator applications.</p>
        <PostCampaignFlow class="mt-6 inline-block" @created="onCampaignCreated" />
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
                v-for="c in data.campaigns"
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
                    :to="'/campaigns/' + (c.slug || c.id)"
                    class="mr-2 inline-flex items-center text-[#e63946] hover:underline"
                  >
                    View
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

    <!-- Recent collaborations -->
    <div class="mt-10">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Recent collaborations</h2>
      <div v-if="!data?.collaborations?.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-6 text-center text-[#64748b] shadow-sm">No collaborations yet.</div>
      <ul v-else class="mt-4 space-y-2">
        <li v-for="c in data.collaborations" :key="c.id" class="rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <span class="font-medium text-[#1a1a1a]">{{ c.creator?.name }}</span>
              <span class="text-[#64748b]"> – ₹{{ c.amount }} ({{ c.status }})</span>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PostCampaignFlow from '../../components/brand/PostCampaignFlow.vue';

const data = ref(null);

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
    await loadDashboard();
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to publish campaign.');
  }
}

async function loadDashboard() {
  const res = await axios.get('/api/brand/dashboard', { withCredentials: true });
  data.value = res.data;
}

onMounted(loadDashboard);

function onCampaignCreated() {
  loadDashboard();
}
</script>
