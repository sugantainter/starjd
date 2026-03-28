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

    <!-- Social Reach Section -->
    <div v-if="connectedSocialCount > 0" class="mt-8">
      <h2 class="text-lg font-semibold text-[#1a1a1a]">Social Audience Reach</h2>
      <p class="mt-1 text-sm text-[#64748b]">Total combined reach across your connected platforms.</p>
      <div class="mt-4 grid gap-4 xs:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4">
        <div 
          v-for="acc in data.social_accounts.filter(a => a.is_connected && a.followers_count)" 
          :key="acc.id"
          class="flex items-center gap-3 rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm transition hover:shadow"
        >
          <SocialPlatformIcon :platform="acc.platform" :size="40" class="shrink-0" />
          <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-[#64748b]">{{ acc.platform }}</div>
            <div class="mt-0.5 text-xl font-bold text-[#1a1a1a]">{{ formatFollowers(acc.followers_count) }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Performance Insights section (Graphs) -->
    <div v-if="youtubeAccount && analyticsHistory.length > 0" class="mt-8">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-lg font-semibold text-[#1a1a1a]">Performance Insights</h2>
          <p class="mt-1 text-sm text-[#64748b]">Historical growth and engagement for {{ youtubeAccount.username }}</p>
        </div>
        <div class="flex gap-2">
          <button 
            @click="activeTab = 'views'"
            class="px-4 py-2 text-sm font-medium rounded-xl transition-all"
            :class="activeTab === 'views' ? 'bg-[#1a1a1a] text-white' : 'bg-white border border-[#e2e8f0] text-[#64748b] hover:bg-slate-50'"
          >
            Views
          </button>
          <button 
            @click="activeTab = 'subscribers'"
            class="px-4 py-2 text-sm font-medium rounded-xl transition-all"
            :class="activeTab === 'subscribers' ? 'bg-[#1a1a1a] text-white' : 'bg-white border border-[#e2e8f0] text-[#64748b] hover:bg-slate-50'"
          >
            Subscribers
          </button>
        </div>
      </div>

      <div class="rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <div v-if="activeTab === 'views'">
          <h3 class="text-sm font-semibold text-[#64748b] mb-4 uppercase tracking-wider">Daily Views (Total reach)</h3>
          <GrowthChart :history="analyticsHistory" :metricIndex="3" metricName="Views" color="#3b82f6" />
        </div>
        <div v-else>
          <h3 class="text-sm font-semibold text-[#64748b] mb-4 uppercase tracking-wider">Net Subscriber Growth</h3>
          <GrowthChart :history="analyticsHistory" :metricIndex="1" metricName="Gained" color="#ef4444" />
        </div>
      </div>
    </div>

    <!-- Additional Insights Grid -->
    <div v-if="youtubeAccount && (topVideos.length > 0 || demographics.age.length > 0)" class="mt-8 grid gap-8 lg:grid-cols-2">
      <!-- Top Content -->
      <div v-if="topVideos.length > 0">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Top Content</h2>
        <p class="mt-1 text-sm text-[#64748b]">Your best performing videos in the last 60 days.</p>
        <div class="mt-4 space-y-3">
          <div v-for="video in topVideos" :key="video.id" class="flex gap-4 rounded-xl border border-[#e2e8f0] bg-white p-3 transition hover:shadow-md">
            <img :src="video.thumbnail" alt="" class="h-16 w-28 rounded-lg object-cover bg-slate-100 shadow-inner" />
            <div class="min-w-0 flex-1">
              <h4 class="truncate font-medium text-[#1a1a1a]" :title="video.title">{{ video.title }}</h4>
              <div class="mt-1 flex items-center gap-3 text-xs text-[#64748b]">
                <span class="flex items-center gap-1">
                  <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  {{ formatFollowers(video.views) }} views
                </span>
                <a :href="'https://youtube.com/watch?v=' + video.id" target="_blank" class="text-[#3b82f6] hover:underline">Watch</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Audience Demographics -->
      <div v-if="demographics.age.length > 0">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Audience Demographics</h2>
        <p class="mt-1 text-sm text-[#64748b]">Based on viewer percentages across age and gender.</p>
        <div class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-5 space-y-6">
          <!-- Gender -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-3">Gender Distribution</h4>
            <div class="flex items-center gap-4">
              <div class="flex-1">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-[#1e293b] font-medium">Male</span>
                  <span class="text-[#64748b]">{{ Math.round(demographics.gender.male || 0) }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                  <div class="h-full bg-blue-500 rounded-full" :style="{ width: (demographics.gender.male || 0) + '%' }"></div>
                </div>
              </div>
              <div class="flex-1">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-[#1e293b] font-medium">Female</span>
                  <span class="text-[#64748b]">{{ Math.round(demographics.gender.female || 0) }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                  <div class="h-full bg-pink-500 rounded-full" :style="{ width: (demographics.gender.female || 0) + '%' }"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Age Groups -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-3">Top Age Groups</h4>
            <div class="space-y-3">
              <div v-for="[age, pct] in demographics.age.slice(0, 4)" :key="age">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-[#1e293b] font-medium">{{ age.replace('age', '') }} years</span>
                  <span class="text-[#64748b]">{{ Math.round(pct) }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                  <div class="h-full bg-indigo-500 rounded-full" :style="{ width: pct + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
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
import SocialPlatformIcon from '../../components/SocialPlatformIcon.vue';
import GrowthChart from '../../components/GrowthChart.vue';

const data = ref(null);
const activeTab = ref('views'); // 'views' or 'subscribers'
const selectedPlatform = ref('youtube');

onMounted(async () => {
  const res = await axios.get('/api/creator/dashboard', { withCredentials: true });
  data.value = res.data;
});

const youtubeAccount = computed(() => {
  return data.value?.social_accounts?.find(a => a.platform === 'youtube' && a.is_connected && a.analytics_data);
});

const analyticsHistory = computed(() => {
  return youtubeAccount.value?.analytics_data?.history ?? [];
});

const topVideos = computed(() => {
  return youtubeAccount.value?.analytics_data?.top_videos ?? [];
});

const demographics = computed(() => {
  const data = youtubeAccount.value?.analytics_data?.demographics ?? [];
  // Format: [ [ageGroup, gender, percentage], ... ]
  
  const genderMap = { male: 0, female: 0 };
  const ageGroups = {};
  
  data.forEach(row => {
    genderMap[row[1]] = (genderMap[row[1]] || 0) + row[2];
    ageGroups[row[0]] = (ageGroups[row[0]] || 0) + row[2];
  });
  
  return {
    gender: genderMap,
    age: Object.entries(ageGroups).sort((a, b) => b[1] - a[1])
  };
});

const connectedSocialCount = computed(() => {
  const accounts = data.value?.social_accounts ?? [];
  return accounts.filter((a) => a.is_connected).length;
});

function formatFollowers(n) {
  if (n == null || n === '') return '0';
  const num = Number(n);
  if (num >= 1e6) return (num / 1e6).toFixed(1) + 'M';
  if (num >= 1e3) return (num / 1e3).toFixed(1) + 'K';
  return num.toLocaleString();
}

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
