<template>
  <div>
    <div v-if="loading" class="rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
      <p class="text-[#64748b]">Loading campaign…</p>
    </div>
    <template v-else-if="campaign">
      <nav class="mb-6 text-sm text-[#64748b]">
        <router-link to="/brand/post-campaign" class="hover:text-[#e63946]">Post Campaign</router-link>
        <span class="mx-2">/</span>
        <span class="text-[#1a1a1a]">{{ campaign.title }}</span>
      </nav>

      <div class="rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-2">
          <span class="rounded-full bg-[#e63946]/10 px-3 py-1 text-sm font-semibold text-[#e63946]">{{ typeLabel(campaign.campaign_type) }}</span>
          <span
            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
            :class="statusClass(campaign.status)"
          >
            {{ statusLabel(campaign.status) }}
          </span>
        </div>
        <h1 class="mt-4 text-2xl font-bold text-[#1a1a1a]">{{ campaign.title }}</h1>
        <div class="mt-3 flex flex-wrap gap-4 text-sm text-[#64748b]">
          <span v-if="campaign.budget != null && campaign.budget > 0">Budget: ₹{{ formatNumber(campaign.budget) }}</span>
          <span v-if="campaign.max_applications">Max applications: {{ campaign.max_applications }}</span>
          <span>Created {{ formatDate(campaign.created_at) }}</span>
        </div>
        <p v-if="campaign.description" class="mt-4 whitespace-pre-wrap text-[#475569]">{{ campaign.description }}</p>
        <p v-if="campaign.deliverables" class="mt-3 text-sm text-[#64748b]"><span class="font-medium text-[#1a1a1a]">Deliverables:</span> {{ campaign.deliverables }}</p>
      </div>

      <!-- Applications -->
      <div class="mt-8">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Applications</h2>
        <p class="mt-1 text-sm text-[#64748b]">Review creator applications and reach out to collaborate.</p>
        <div v-if="!campaign.applications?.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
          <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f1f5f9]">
            <svg class="h-7 w-7 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
          </div>
          <p class="mt-4 text-[#64748b]">No applications yet.</p>
          <p class="mt-1 text-sm text-[#94a3b8]">When creators apply, they will appear here.</p>
          <router-link to="/campaigns" class="mt-4 inline-block text-sm font-medium text-[#e63946] hover:underline">View public campaign page</router-link>
        </div>
        <div v-else class="mt-4 overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
              <thead>
                <tr class="border-b border-[#e2e8f0] bg-[#f8fafc]">
                  <th class="px-5 py-4 font-semibold text-[#475569]">Creator</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Message</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Quoted amount</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Status</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Applied</th>
                  <th class="px-5 py-4 font-semibold text-[#475569] text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="app in campaign.applications"
                  :key="app.id"
                  class="border-b border-[#f1f5f9] transition hover:bg-[#fafafa]"
                >
                  <td class="px-5 py-4">
                    <router-link
                      v-if="app.creator?.profile_slug"
                      :to="'/creators/' + app.creator.profile_slug"
                      class="font-medium text-[#e63946] hover:underline"
                    >
                      {{ app.creator?.name || 'Creator' }}
                    </router-link>
                    <span v-else class="font-medium text-[#1a1a1a]">{{ app.creator?.name || 'Creator' }}</span>
                  </td>
                  <td class="max-w-xs px-5 py-4">
                    <p class="line-clamp-2 text-[#475569]">{{ app.cover_message || '—' }}</p>
                  </td>
                  <td class="px-5 py-4">
                    <span v-if="app.quoted_amount != null && app.quoted_amount > 0" class="font-medium text-[#1a1a1a]">₹{{ formatNumber(app.quoted_amount) }}</span>
                    <span v-else class="text-[#94a3b8]">—</span>
                  </td>
                  <td class="px-5 py-4">
                    <span
                      class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                      :class="applicationStatusClass(app.status)"
                    >
                      {{ applicationStatusLabel(app.status) }}
                    </span>
                  </td>
                  <td class="px-5 py-4 text-[#64748b]">{{ formatDate(app.created_at) }}</td>
                  <td class="px-5 py-4 text-right">
                    <router-link
                      v-if="app.creator?.profile_slug"
                      :to="'/creators/' + app.creator.profile_slug"
                      class="mr-2 inline-flex items-center text-sm text-[#e63946] hover:underline"
                    >
                      View profile
                    </router-link>
                    <router-link
                      :to="{ name: 'brand-messages' }"
                      class="inline-flex items-center text-sm text-[#3b82f6] hover:underline"
                    >
                      Message
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
    <div v-else class="rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
      <p class="text-[#64748b]">Campaign not found.</p>
      <router-link to="/brand/post-campaign" class="mt-2 inline-block text-[#e63946] hover:underline">Back to Post Campaign</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const campaign = ref(null);
const loading = ref(true);

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

async function loadCampaign() {
  const id = route.params.id;
  if (!id) return;
  loading.value = true;
  try {
    const res = await axios.get('/api/brand/campaigns/' + id, { withCredentials: true });
    campaign.value = res.data;
  } catch {
    campaign.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(loadCampaign);
watch(() => route.params.id, loadCampaign);
</script>
