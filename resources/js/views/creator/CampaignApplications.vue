<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Campaign applications</h1>
    <p class="mt-1 text-[#64748b]">Campaigns you applied to and your application status.</p>

    <div v-if="loading" class="mt-6 flex justify-center py-12">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#10b981] border-t-transparent" />
    </div>
    <div v-else-if="!applications.length" class="mt-6 rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f1f5f9]">
        <svg class="h-7 w-7 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
      </div>
      <p class="mt-4 text-[#64748b]">No campaign applications yet.</p>
      <p class="mt-1 text-sm text-[#94a3b8]">Browse campaigns and apply to see your applications here.</p>
      <router-link to="/campaigns" class="mt-4 inline-block rounded-xl bg-[#10b981] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#059669]">Browse campaigns</router-link>
    </div>
    <div v-else class="mt-6 overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[560px] text-left text-sm">
          <thead>
            <tr class="border-b border-[#e2e8f0] bg-[#f8fafc]">
              <th class="px-5 py-4 font-semibold text-[#475569]">Campaign</th>
              <th class="px-5 py-4 font-semibold text-[#475569]">Type</th>
              <th class="px-5 py-4 font-semibold text-[#475569]">Your status</th>
              <th class="px-5 py-4 font-semibold text-[#475569]">Quoted</th>
              <th class="px-5 py-4 font-semibold text-[#475569]">Applied</th>
              <th class="px-5 py-4 font-semibold text-[#475569] text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="app in applications"
              :key="app.id"
              class="border-b border-[#f1f5f9] transition hover:bg-[#fafafa]"
            >
              <td class="px-5 py-4">
                <p class="font-medium text-[#1a1a1a]">{{ app.campaign?.title || 'Campaign' }}</p>
              </td>
              <td class="px-5 py-4">
                <span class="rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-xs font-medium text-[#475569]">{{ typeLabel(app.campaign?.campaign_type) }}</span>
              </td>
              <td class="px-5 py-4">
                <span
                  class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                  :class="applicationStatusClass(app.status)"
                >
                  {{ applicationStatusLabel(app.status) }}
                </span>
              </td>
              <td class="px-5 py-4">
                <span v-if="app.quoted_amount != null && app.quoted_amount > 0" class="font-medium text-[#1a1a1a]">₹{{ formatNumber(app.quoted_amount) }}</span>
                <span v-else class="text-[#94a3b8]">—</span>
              </td>
              <td class="px-5 py-4 text-[#64748b]">{{ formatDate(app.created_at) }}</td>
              <td class="px-5 py-4 text-right">
                <router-link
                  v-if="app.campaign?.slug"
                  :to="'/campaigns/' + app.campaign.slug"
                  class="inline-flex items-center text-sm font-medium text-[#10b981] hover:underline"
                >
                  View campaign
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="pagination.last_page > 1" class="flex items-center justify-between border-t border-[#e2e8f0] px-5 py-3">
        <p class="text-sm text-[#64748b]">Page {{ pagination.current_page }} of {{ pagination.last_page }}</p>
        <div class="flex gap-2">
          <button
            type="button"
            :disabled="pagination.current_page <= 1"
            class="rounded-lg border border-[#e2e8f0] px-3 py-1.5 text-sm text-[#475569] hover:bg-[#f1f5f9] disabled:opacity-50"
            @click="loadPage(pagination.current_page - 1)"
          >
            Previous
          </button>
          <button
            type="button"
            :disabled="pagination.current_page >= pagination.last_page"
            class="rounded-lg border border-[#e2e8f0] px-3 py-1.5 text-sm text-[#475569] hover:bg-[#f1f5f9] disabled:opacity-50"
            @click="loadPage(pagination.current_page + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const applications = ref([]);
const loading = ref(true);
const pagination = reactive({ current_page: 1, last_page: 1 });

function typeLabel(type) {
  const map = { instagram: 'Instagram', tiktok: 'TikTok', ugc: 'UGC', youtube: 'YouTube' };
  return type ? (map[type] || type) : '—';
}

function applicationStatusLabel(status) {
  const map = { pending: 'Pending', approved: 'Approved', rejected: 'Rejected' };
  return status ? (map[status] || status) : '—';
}

function applicationStatusClass(status) {
  const map = {
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-red-100 text-red-700',
  };
  return map[status] || 'bg-slate-100 text-slate-600';
}

function formatNumber(n) {
  if (n == null) return '—';
  const num = Number(n);
  if (num >= 100000) return (num / 100000).toFixed(1) + 'L';
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
  return num.toLocaleString();
}

function formatDate(iso) {
  if (!iso) return '—';
  const d = new Date(iso);
  return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
}

async function loadPage(page = 1) {
  loading.value = true;
  try {
    const res = await axios.get('/api/creator/campaign-applications', {
      params: { page, per_page: 15 },
      withCredentials: true,
    });
    applications.value = res.data?.data ?? [];
    pagination.current_page = res.data?.current_page ?? 1;
    pagination.last_page = res.data?.last_page ?? 1;
  } catch (_) {
    applications.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => loadPage(1));
</script>
