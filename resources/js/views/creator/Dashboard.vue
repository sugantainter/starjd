<template>
  <div class="relative">
    <!-- Notifications -->
    <div v-if="notification" class="fixed top-6 right-6 z-[100] animate-in fade-in slide-in-from-top-4 duration-300">
      <div :class="['rounded-2xl px-6 py-4 shadow-2xl flex items-center gap-3 border transition-all', 
        notification.type === 'success' ? 'bg-green-50 border-green-200 text-green-900' : 'bg-red-50 border-red-200 text-red-900']">
        <div v-if="notification.type === 'success'" class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div v-else class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center text-white shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
        <div>
          <p class="font-bold">{{ notification.title }}</p>
          <p class="text-sm opacity-90">{{ notification.message }}</p>
        </div>
      </div>
    </div>
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
    <div v-if="selectedAccount && analyticsHistory.length > 0" class="mt-8">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
        <div>
          <h2 class="text-lg font-semibold text-[#1a1a1a]">Performance Insights</h2>
          <p class="mt-1 text-sm text-[#64748b]">Select a platform to view your analytics history.</p>
        </div>
        
        <!-- Platform Switcher Tabs -->
        <div class="flex items-center gap-1 p-1 bg-slate-100/50 rounded-2xl border border-[#e2e8f0]">
          <button 
            v-for="acc in data.social_accounts.filter(a => a.is_connected && a.analytics_data)"
            :key="acc.platform"
            @click="selectedPlatform = acc.platform; activeTab = platformTabs[acc.platform][0].id"
            class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition-all"
            :class="selectedPlatform === acc.platform ? 'bg-white shadow text-[#1a1a1a]' : 'text-[#64748b] hover:text-[#1a1a1a] hover:bg-white/50'"
          >
            <SocialPlatformIcon :platform="acc.platform" :size="20" />
            <span class="capitalize">{{ acc.platform }}</span>
          </button>
        </div>
      </div>

      <!-- Metric Selector and Graph Title -->
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8]">
          {{ platformTabs[selectedPlatform]?.find(t => t.id === activeTab)?.label ?? 'Metrics' }} History
        </h3>
        <div class="flex gap-2">
          <button 
            v-for="tab in platformTabs[selectedPlatform]"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="px-4 py-2 text-sm font-medium rounded-xl transition-all"
            :class="activeTab === tab.id ? 'bg-[#1a1a1a] text-white' : 'bg-white border border-[#e2e8f0] text-[#64748b] hover:bg-slate-50'"
          >
            {{ tab.name }}
          </button>
        </div>
      </div>

      <div class="rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <GrowthChart 
          :history="analyticsHistory" 
          :metricIndex="activeMetricIndex" 
          :metricName="activeTabLabel" 
          :color="selectedPlatform === 'linkedin' ? '#0077b5' : (selectedPlatform === 'youtube' ? '#ef4444' : '#3b82f6')" 
        />
      </div>
    </div>

    <!-- Additional Insights Grid -->
    <div v-if="selectedAccount && (topContent.length > 0 || demographics.age.length > 0)" class="mt-8 grid gap-8 lg:grid-cols-2">
      <!-- Top Content -->
      <div v-if="topContent.length > 0">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Top Content</h2>
        <p class="mt-1 text-sm text-[#64748b]">Your best performing {{ selectedPlatform === 'youtube' ? 'videos' : 'posts' }} recently.</p>
        <div class="mt-4 space-y-3">
          <div v-for="item in topContent" :key="item.id" class="flex gap-4 rounded-xl border border-[#e2e8f0] bg-white p-3 transition hover:shadow-md">
            <div v-if="item.thumbnail" class="shrink-0">
               <img :src="item.thumbnail" alt="" class="h-16 w-28 rounded-lg object-cover bg-slate-100 shadow-inner" />
            </div>
            <div v-else class="h-16 w-28 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
               <SocialPlatformIcon :platform="selectedPlatform" :size="32" class="opacity-30" />
            </div>
            <div class="min-w-0 flex-1">
              <h4 class="line-clamp-2 font-medium text-[#1a1a1a]" :title="item.title">{{ item.title }}</h4>
              <div class="mt-1 flex items-center gap-3 text-xs text-[#64748b]">
                <span class="flex items-center gap-1">
                  <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  {{ formatFollowers(item.views || item.engagement) }} {{ item.views ? 'views' : 'engagement' }}
                </span>
                <a v-if="item.url" :href="item.url" target="_blank" class="text-[#3b82f6] hover:underline">View</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Audience Demographics -->
      <div v-if="demographics.age && demographics.age.length > 0">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Audience Demographics</h2>
        <p class="mt-1 text-sm text-[#64748b]">Deep insights into who is watching your content.</p>
        <div class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-6 grid gap-8 sm:grid-cols-2">
          <!-- Gender Donut -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-4">Gender Distribution</h4>
            <div class="h-[250px]">
               <DoughnutChart :counts="demographics.gender" />
            </div>
          </div>

          <!-- Age Bars -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-4">Top Age Groups</h4>
            <div class="h-[250px]">
               <BarChart :dataRows="demographics.age" color="#6366f1" />
            </div>
          </div>
        </div>
      </div>
    </div>
... (Campaigns and Collaborations sections) ...

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
import DoughnutChart from '../../components/DoughnutChart.vue';
import BarChart from '../../components/BarChart.vue';

const data = ref(null);
const selectedPlatform = ref('youtube');
const activeTab = ref('views');

const platformTabs = {
  youtube: [
    { id: 'views', name: 'Views', label: 'Daily Views', index: 3 },
    { id: 'subscribers', name: 'Subscribers', label: 'Net Subscriber growth', index: 1 },
    { id: 'likes', name: 'Likes', label: 'Daily Likes', index: 4 },
  ],
  facebook: [
    { id: 'reach', name: 'Reach', label: 'Daily Reach', index: 3 },
    { id: 'engagement', name: 'Engagement', label: 'Daily Engagement', index: 1 },
  ],
  linkedin: [
    { id: 'engagement', name: 'Engagement', label: 'Total Engagement', index: 3 },
    { id: 'likes', name: 'Likes', label: 'Post Likes', index: 1 },
    { id: 'comments', name: 'Comments', label: 'Post Comments', index: 2 },
  ],
  instagram: [
    { id: 'reach', name: 'Reach', label: 'Account Reach', index: 3 },
    { id: 'impressions', name: 'Impressions', label: 'Total Impressions', index: 2 },
  ],
  pinterest: [
    { id: 'impressions', name: 'Impressions', label: 'Daily Impressions', index: 1 },
    { id: 'saves', name: 'Saves', label: 'Daily Saves', index: 2 },
    { id: 'clicks', name: 'Clicks', label: 'Outbound Clicks', index: 3 },
  ]
};

const notification = ref(null);

onMounted(async () => {
  // Professional Feedback: Check for success/error handshake from URL
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('success') || window.location.hash.includes('_=_')) {
    notification.value = {
      type: 'success',
      title: 'Success!',
      message: 'Social account connected successfully.'
    };
    // Professional Cleanup: URL Wash
    window.history.replaceState({}, document.title, window.location.pathname);
    setTimeout(() => notification.value = null, 5000);
  }
  
  if (urlParams.has('error')) {
    notification.value = {
      type: 'error',
      title: 'Connection Failed',
      message: urlParams.get('msg') ? decodeURIComponent(urlParams.get('msg')) : 'Could not link your social account.'
    };
    window.history.replaceState({}, document.title, window.location.pathname);
    setTimeout(() => notification.value = null, 5000);
  }

  try {
    const res = await axios.get('/api/creator/dashboard', { withCredentials: true });
    data.value = res.data;
    
    // Set default platform based on what's connected
    if (data.value?.social_accounts) {
      const connected = data.value.social_accounts.find(a => a.is_connected && a.analytics_data);
      if (connected) {
        selectedPlatform.value = connected.platform;
        activeTab.value = platformTabs[connected.platform]?.[0]?.id || 'views';
      }
    }
  } catch (err) {
    console.error('Failed to load dashboard:', err);
  }
});

const selectedAccount = computed(() => {
  return data.value?.social_accounts?.find(a => a.platform === selectedPlatform.value && a.is_connected && a.analytics_data);
});

const analyticsHistory = computed(() => {
  return selectedAccount.value?.analytics_data?.history ?? [];
});

const topContent = computed(() => {
  const ad = selectedAccount.value?.analytics_data;
  return ad?.top_videos ?? [];
});

const activeMetricIndex = computed(() => {
  const tabs = platformTabs[selectedPlatform.value] || [];
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.index : 3;
});

const activeTabLabel = computed(() => {
  const tabs = platformTabs[selectedPlatform.value] || [];
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab ? tab.name : 'Metric';
});

const demographics = computed(() => {
  const ad = selectedAccount.value?.analytics_data;
  const demoData = ad?.demographics ?? [];
  if (!demoData.length) return { gender: {}, age: [] };

  const genderMap = { male: 0, female: 0 };
  const ageGroups = {};
  
  demoData.forEach(row => {
    // Standardizing demographic data parsing (YouTube: [age, gender, %])
    if (row.length >= 3) {
      const age = row[0];
      const gender = row[1].toLowerCase();
      const value = parseFloat(row[2]);
      genderMap[gender] = (genderMap[gender] || 0) + value;
      ageGroups[age] = (ageGroups[age] || 0) + value;
    }
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
