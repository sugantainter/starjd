<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Dashboard</h1>
    <p class="mt-1 text-[#64748b]">Welcome back, {{ data?.user?.name }}.</p>

    <div v-if="data?.profile" class="mt-6 rounded-xl border-2 border-[#f59e0b]/30 bg-[#fffbeb] p-4 sm:p-5">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-lg font-semibold text-[#1a1a1a]">Featured creator</h2>
          <p v-if="data.profile.is_featured && data.profile.featured_until" class="mt-1 text-sm text-[#64748b]">Your profile is featured until <strong>{{ formatDate(data.profile.featured_until) }}</strong>. You appear first in Discover.</p>
          <p v-else class="mt-1 text-sm text-[#64748b]">Get more visibility: featured creators appear at the top of Discover and on the homepage.</p>
        </div>
        <router-link v-if="!data.profile.is_featured" to="/creator/featured" class="shrink-0 rounded-xl bg-[#f59e0b] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#d97706]">Get Featured</router-link>
        <router-link v-else to="/creator/featured" class="shrink-0 rounded-xl border border-[#f59e0b] bg-white px-5 py-2.5 text-sm font-semibold text-[#d97706] transition hover:bg-[#fffbeb]">Extend featured</router-link>
      </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Packages</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ data?.packages?.length ?? 0 }}</div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Collaborations</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ data?.collaborations?.length ?? 0 }}</div>
      </div>
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="text-sm text-[#64748b]">Social connected</div>
        <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">{{ connectedSocialCount }}</div>
      </div>
    </div>
    <div class="mt-8">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Campaigns you applied to</h2>
      <p class="mt-1 text-sm text-[#64748b]">Campaigns you showed interest in and your application status.</p>
      <div v-if="!data?.campaign_applications?.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-6 text-center text-[#64748b]">No campaign applications yet. <router-link to="/campaigns" class="text-[#10b981] hover:underline">Browse campaigns</router-link> to apply.</div>
      <div v-else class="mt-4 overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[500px] text-left text-sm">
            <thead>
              <tr class="border-b border-[#e2e8f0] bg-[#f8fafc]">
                <th class="px-5 py-4 font-semibold text-[#475569]">Campaign</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Type</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Your status</th>
                <th class="px-5 py-4 font-semibold text-[#475569]">Applied</th>
                <th class="px-5 py-4 font-semibold text-[#475569] text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="app in data.campaign_applications"
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
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Recent collaborations</h2>
      <div v-if="!data?.collaborations?.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-6 text-center text-[#64748b]">No collaborations yet.</div>
      <ul v-else class="mt-4 space-y-2">
        <li v-for="c in data.collaborations" :key="c.id" class="rounded-xl border border-[#e2e8f0] bg-white p-4">
          <span class="font-medium text-[#1a1a1a]">{{ c.brand?.name }}</span>
          <span class="text-[#64748b]"> – ₹{{ c.amount }} ({{ c.status }})</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const data = ref(null);

onMounted(async () => {
  const res = await axios.get('/api/creator/dashboard', { withCredentials: true });
  data.value = res.data;
});

const connectedSocialCount = computed(() => {
  const accounts = data.value?.social_accounts ?? [];
  return accounts.filter((a) => a.is_connected).length;
});

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

function formatDate(iso) {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>
