<template>
  <div v-if="profile" class="mx-auto max-w-6xl px-4 pt-8 pb-20 md:pb-24">
    <div class="rounded-2xl border border-[#e2e8f0] bg-white p-8 shadow-sm">
      <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
        <div class="h-24 w-24 shrink-0 rounded-full overflow-hidden border-2 border-[#e2e8f0] bg-[#f1f5f9] flex items-center justify-center">
          <img v-if="profile.avatar_url" :src="profile.avatar_url" :alt="profile.user?.name" class="h-full w-full object-cover" />
          <span v-else class="text-3xl font-semibold text-[#94a3b8]">{{ (profile.user?.name || '?').charAt(0) }}</span>
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-[#1a1a1a]">{{ profile.user?.name }}</h1>
          <p v-if="profile.tagline" class="mt-1 text-[#64748b]">{{ profile.tagline }}</p>
          <p v-if="profile.city_name || profile.state_name" class="mt-1 text-sm text-[#64748b]">
            {{ profile.city_name }}{{ profile.city_name && profile.state_name ? ', ' : '' }}{{ profile.state_name }}
          </p>
          <p v-else-if="profile.location" class="mt-1 text-sm text-[#64748b]">{{ profile.location }}</p>
          <p v-if="profile.category" class="mt-1 text-sm text-[#64748b]">{{ profile.category }}</p>
          <RichTextContent v-if="profile.bio" class="mt-4 text-[#1a1a1a]" :content="profile.bio" />
          <div v-if="connectedSocialAccounts.length" class="mt-5">
            <p class="mb-3 text-sm font-medium text-[#64748b]">Connect & reach</p>
            <div class="flex flex-wrap gap-3">
              <a
                v-for="s in connectedSocialAccounts"
                :key="s.platform"
                :href="s.profile_url || '#'"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-3 rounded-xl border border-[#e2e8f0] bg-white px-4 py-3 text-left shadow-sm transition hover:border-[#fc4402]/40 hover:shadow-md"
              >
                <SocialPlatformIcon :platform="s.platform" :size="36" />
                <div>
                  <span class="block font-medium text-[#1a1a1a]">{{ platformName(s.platform) }}</span>
                  <span v-if="s.username" class="block text-sm text-[#64748b]">@{{ s.username }}</span>
                  <span v-if="s.followers_count" class="block text-xs font-medium text-[#fc4402]">{{ formatFollowers(s.followers_count) }} followers</span>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="portfolio.length" class="mt-8">
      <h2 class="text-xl font-semibold text-[#1a1a1a]">Portfolio</h2>
      <div
        class="mt-4 grid gap-4"
        :class="portfolio.length <= 2 ? 'grid-cols-1 sm:grid-cols-2 max-w-3xl' : 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4'"
      >
        <div
          v-for="post in portfolio"
          :key="post.id"
          class="group relative overflow-hidden rounded-xl border border-[#e2e8f0] bg-[#f1f5f9] shadow-sm transition hover:shadow-md"
        >
          <div class="aspect-[4/5] overflow-hidden">
            <img
              :src="post.image"
              :alt="post.caption || 'Portfolio'"
              class="h-full w-full object-cover object-center transition duration-300 group-hover:scale-[1.02]"
            />
          </div>
          <p v-if="post.caption" class="p-3 text-xs text-[#64748b] line-clamp-2">{{ post.caption }}</p>
        </div>
      </div>
    </div>
    <div class="mt-8">
      <h2 class="text-xl font-semibold text-[#1a1a1a]">Packages & rates</h2>
      <p class="mt-1 text-sm text-[#64748b]">Collaboration packages with transparent pricing.</p>
      <div class="mt-6 flex flex-nowrap gap-6 overflow-x-auto pb-4 -mx-1 px-1 snap-x">
        <div 
          v-for="pkg in packages" 
          :key="pkg.id" 
          class="w-[320px] shrink-0 rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm transition hover:shadow-md flex flex-col snap-start"
        >
          <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
              <span v-if="pkg.package_category || pkg.category" class="rounded-full bg-[#fc4402]/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#fc4402]">
                {{ pkg.package_category?.name || pkg.category }}
              </span>
              <span v-if="pkg.is_negotiable" class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-600 border border-blue-100">
                Negotiable
              </span>
            </div>
            
            <h3 class="text-xl font-bold text-[#1a1a1a]">{{ pkg.name }}</h3>
            <div class="mt-2 text-3xl font-extrabold text-[#fc4402]">₹{{ formatPrice(pkg.price) }}</div>
            
            <div v-if="pkg.items?.length" class="mt-6 space-y-2">
              <div v-for="(it, i) in pkg.items" :key="i" class="flex items-center gap-2 text-sm text-[#475569]">
                <svg class="h-4 w-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="flex-1 truncate">{{ it.name }}</span>
                <span v-if="it.quantity > 1" class="text-xs font-semibold text-[#94a3b8]">x{{ it.quantity }}</span>
              </div>
            </div>

            <RichTextContent 
              v-if="pkg.description" 
              class="mt-6 text-sm text-[#64748b] leading-relaxed" 
              :class="{ 'line-clamp-4': !expandedPackages.has(pkg.id) }"
              :content="pkg.description" 
            />
            <button 
              v-if="pkg.description && pkg.description.length > 200"
              type="button"
              @click="toggleExpand(pkg.id)"
              class="mt-2 text-xs font-bold text-[#fc4402] hover:underline focus:outline-none"
            >
              {{ expandedPackages.has(pkg.id) ? 'Show less' : 'Read more' }}
            </button>
            
            <div v-if="pkg.deliverables || pkg.revisions != null" class="mt-4 flex flex-wrap items-center gap-4 text-[11px] font-semibold text-[#64748b]">
                <div v-if="pkg.deliverables" class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-50 border border-slate-100">
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ pkg.deliverables }}
                </div>
                <div v-if="pkg.revisions > 0" class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-purple-50 border border-purple-100 text-purple-700">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    {{ pkg.revisions === 20 ? 'Unlimited' : pkg.revisions }} Revisions
                </div>
                <div v-else class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-50 border border-slate-100 text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    0 Revisions
                </div>
            </div>
          </div>

          <button 
            v-if="isBrand && !pkg.is_requested" 
            type="button" 
            class="mt-8 w-full cursor-link rounded-xl bg-[#fc4402] py-3 text-sm font-bold text-white shadow-lg shadow-[#fc4402]/20 transition-all hover:bg-[#e63d02] hover:scale-[1.02] active:scale-95" 
            @click="openCollab(pkg)"
          >
            Collaborate Now
          </button>
          <button 
            v-else-if="isBrand && pkg.is_requested" 
            disabled 
            type="button" 
            class="mt-8 w-full rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-400 border border-slate-200 cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Request Sent
          </button>
        </div>
      </div>
      <p v-if="isBrand && !packages.length" class="mt-4 text-[#64748b]">No packages listed. Contact the creator directly.</p>
      <p v-if="!isBrand" class="mt-4 text-[#64748b]">Log in as a brand to request a collaboration.</p>
    </div>

    <!-- Advanced Channel Insights (Public Mirror of Dashboard) -->
    <div v-if="connectedSocialAccountsWithAnalytics.length" class="mt-8">
      <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex-1 min-w-[200px]">
           <h2 class="text-xl font-semibold text-[#1a1a1a]">Channel Insights</h2>
           <p class="text-sm text-[#64748b]">Real-time audience performance and demographics.</p>
        </div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
          <!-- Metric Selectors (Sub/Likes/Views) -->
          <div v-if="activeAccount" class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl">
             <button
               v-for="tab in (platformTabs[selectedPlatform] || [])"
               :key="tab.id"
               @click="activeTab = tab.id"
               class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider rounded-lg transition-all"
               :class="activeTab === tab.id ? 'bg-[#1a1a1a] text-white shadow-sm' : 'text-[#64748b] hover:text-[#1a1a1a]'"
             >
               {{ tab.name }}
             </button>
          </div>
          <!-- Platform Tabs -->
          <div class="flex items-center gap-1 overflow-x-auto rounded-xl bg-slate-100 p-1">
            <button
              v-for="s in connectedSocialAccountsWithAnalytics"
              :key="s.platform"
              @click="selectedPlatform = s.platform; activeTab = platformTabs[s.platform]?.[0]?.id || 'views'"
              class="flex items-center gap-2 whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-all"
              :class="selectedPlatform === s.platform ? 'bg-white text-[#1a1a1a] shadow-sm' : 'text-[#64748b] hover:text-[#1a1a1a]'"
            >
              <SocialPlatformIcon :platform="s.platform" :size="18" />
              {{ platformName(s.platform) }}
            </button>
          </div>
        </div>
      </div>

      <div class="relative overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <!-- Dashboard-like Stats Grid -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5" :class="{ 'opacity-10 blur-xl pointer-events-none': !isLoggedIn }">
           <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[#94a3b8]">Reach</span>
              <div class="mt-1 text-xl font-bold text-[#1a1a1a]">{{ formatFollowers(activeAccount?.followers_count) || '0' }}</div>
           </div>
           <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[#94a3b8]">Platform</span>
              <div class="mt-1 text-sm font-bold text-[#1a1a1a] capitalize">{{ activeAccount?.platform }}</div>
           </div>
        </div>

        <!-- Lock Overlay for guests -->
        <div v-if="!isLoggedIn" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-white/40 backdrop-blur-lg">
           <div class="w-full max-w-sm rounded-3xl bg-[#0f172a]/90 p-8 text-center text-white shadow-2xl border border-white/10">
              <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#10b981]/10 border border-[#10b981]/20">
                 <svg class="h-8 w-8 text-[#10b981]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                 </svg>
              </div>
              <h3 class="text-xl font-bold tracking-tight">Analytics Locked</h3>
              <p class="mt-3 text-sm text-slate-400 leading-relaxed">Create a free account to unlock daily insights, historical charts, and audience demographics for <b>{{ profile.user?.name }}</b>.</p>
              <div class="mt-8 flex flex-col gap-3">
                 <router-link to="/register" class="rounded-2xl bg-[#10b981] px-6 py-3.5 font-bold text-white transition-all hover:bg-[#059669] hover:scale-[1.02] active:scale-95 shadow-lg shadow-[#10b981]/20">Create Free Account</router-link>
                 <router-link to="/login" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Already have an account? Log in</router-link>
              </div>
           </div>
        </div>

        <!-- Scrollable Analytics Content (only fully visible to logged in users) -->
        <div :class="{ 'opacity-10 blur-2xl pointer-events-none select-none max-h-48 overflow-hidden': !isLoggedIn }" class="transition-all duration-700">
          <!-- Graphs (views) -->
          <div class="mt-10">
             <h3 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-4">{{ activeTabLabel }} History (Last 30 days)</h3>
             <GrowthChart 
                v-if="activeHistory.length" 
                :history="activeHistory" 
                :metricIndex="activeMetricIndex" 
                :metricName="activeTabLabel"
                :color="selectedPlatform === 'youtube' ? '#ef4444' : selectedPlatform === 'linkedin' ? '#0a66c2' : '#3b82f6'" 
             />
             <div v-else class="h-[200px] flex items-center justify-center text-sm text-slate-400 italic">No historical data available yet.</div>
          </div>

          <div class="mt-12 grid gap-12 lg:grid-cols-2">
             <!-- Top Videos -->
             <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-4">Top Content</h3>
                <div class="space-y-4">
                   <div v-for="video in activeTopVideos" :key="video.id" class="flex gap-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-3">
                      <img :src="video.thumbnail" class="h-16 w-28 rounded-lg object-cover bg-slate-200" />
                      <div class="min-w-0 flex-1">
                         <h4 class="truncate font-medium text-[#1a1a1a] shadow-sm" :title="video.title">{{ video.title }}</h4>
                         <span class="text-xs text-[#64748b]">{{ formatFollowers(video.views) }} views</span>
                      </div>
                   </div>
                </div>
             </div>
             <!-- Demographics -->
             <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-4">Audience Demographics</h3>
                <div class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-6">
                   <div class="grid gap-8 sm:grid-cols-2">
                       <div>
                          <p class="text-[10px] font-bold uppercase text-[#94a3b8] mb-2 text-center">Gender</p>
                          <div class="h-[200px]">
                             <DoughnutChart :counts="activeDemographics.gender" />
                          </div>
                       </div>
                       <div>
                          <p class="text-[10px] font-bold uppercase text-[#94a3b8] mb-2 text-center">Age Groups</p>
                          <div class="h-[200px]">
                             <BarChart :dataRows="activeDemographics.age" color="#6366f1" />
                          </div>
                       </div>
                   </div>
                </div>
             </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Request collaboration</h2>
        <form @submit.prevent="submitCollab" class="mt-4 space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium">Package</label>
            <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-4 py-2 text-sm">{{ selectedPackage?.name }} – ₹{{ selectedPackage?.price }}</div>
          </div>
          <div>
            <div class="mb-1 flex items-center justify-between">
              <label class="text-sm font-semibold text-[#1a1a1a]">Collaboration Amount (₹)</label>
              <span v-if="selectedPackage?.is_negotiable" class="rounded-lg bg-blue-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-600 border border-blue-100">Negotiable</span>
            </div>
            <div class="relative group">
              <input 
                v-model.number="collabForm.amount" 
                type="number" 
                step="0.01" 
                min="0" 
                required 
                :disabled="!selectedPackage?.is_negotiable"
                class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-xl font-bold text-[#1a1a1a] shadow-sm transition-all focus:border-[#fc4402] focus:outline-none focus:ring-4 focus:ring-[#fc4402]/10 disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed" 
                :placeholder="formatPrice(selectedPackage?.price)"
              />
              <div v-if="!selectedPackage?.is_negotiable" class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1.5 text-[#94a3b8]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                <span class="text-[10px] font-bold uppercase tracking-wider">Fixed</span>
              </div>
            </div>
            <p v-if="selectedPackage?.is_negotiable" class="mt-2 text-xs text-blue-600 font-medium">You can propose a different amount for this negotiable package.</p>
            <p v-else class="mt-2 text-xs text-[#64748b]">This package has a fixed marketplace rate.</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Notes (optional)</label>
            <textarea v-model="collabForm.brand_notes" rows="3" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#fc4402] focus:outline-none focus:ring-1 focus:ring-[#fc4402]"></textarea>
          </div>
          <div v-if="platformFee" class="rounded-lg bg-[#f8fafc] px-4 py-2 text-sm text-[#64748b]">Platform fee (10%): ₹{{ platformFee }} · Total: ₹{{ collabForm.amount }}</div>
          <div v-if="error" class="rounded-lg bg-red-50 px-4 py-2 text-sm text-red-800">{{ error }}</div>
          <div class="flex gap-2 pt-2">
            <button type="submit" :disabled="loadingCollab" class="cursor-link rounded-xl bg-[#fc4402] px-4 py-2 text-white hover:bg-[#e63d02] disabled:opacity-50">Send request</button>
            <button type="button" class="cursor-link rounded-xl border px-4 py-2 hover:bg-[#f1f5f9]" @click="showModal = false">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div v-else-if="!loading" class="mx-auto max-w-4xl px-4 pt-12 pb-20 md:pb-24 text-center text-[#64748b]">Creator not found.</div>
  <div v-else class="mx-auto max-w-4xl px-4 pt-12 pb-20 md:pb-24 text-center text-[#64748b]">Loading…</div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import SocialPlatformIcon from '../components/SocialPlatformIcon.vue';
import GrowthChart from '../components/GrowthChart.vue';
import DoughnutChart from '../components/DoughnutChart.vue';
import BarChart from '../components/BarChart.vue';
import RichTextContent from '../components/RichTextContent.vue';
import { platformDisplayName } from '../lib/socialPlatforms.js';
import { notify } from '../lib/notify.js';
const expandedPackages = ref(new Set());

function toggleExpand(id) {
  if (expandedPackages.value.has(id)) {
    expandedPackages.value.delete(id);
  } else {
    expandedPackages.value.add(id);
  }
}

const route = useRoute();
const profile = ref(null);
const loading = ref(true);
const showModal = ref(false);
const selectedPackage = ref(null);
const collabForm = reactive({ amount: 0, brand_notes: '' });
const error = ref('');
const loadingCollab = ref(false);
const isBrand = ref(false);
const isLoggedIn = ref(false);

const selectedPlatform = ref('');
const activeTab = ref('views');

const platformTabs = {
    youtube: [
        { id: "views", name: "Views", label: "Daily Views", index: 3 },
        { id: "subscribers", name: "Subscribers", label: "Subscriber growth", index: 1 },
        { id: "likes", name: "Likes", label: "Daily Likes", index: 4 },
    ],
    facebook: [
        { id: "reach", name: "Reach", label: "Daily Reach", index: 3 },
        { id: "engagement", name: "Engagement", label: "Daily Engagement", index: 1 },
    ],
    linkedin: [
        { id: "engagement", name: "Engagement", label: "Total Engagement", index: 3 },
        { id: "likes", name: "Likes", label: "Post Likes", index: 1 },
        { id: "comments", name: "Comments", label: "Post Comments", index: 2 },
    ],
    instagram: [
        { id: "reach", name: "Reach", label: "Account Reach", index: 3 },
        { id: "impressions", name: "Impressions", label: "Total Impressions", index: 2 },
    ],
    pinterest: [
        { id: "impressions", name: "Impressions", label: "Daily Impressions", index: 1 },
        { id: "saves", name: "Saves", label: "Daily Saves", index: 2 },
        { id: "clicks", name: "Clicks", label: "Outbound Clicks", index: 3 },
    ],
};

const connectedSocialAccountsWithAnalytics = computed(() => {
  const list = profile.value?.user?.social_accounts || [];
  return list.filter((s) => s.has_analytics || s.is_connected);
});

const activeAccount = computed(() => {
  if (!selectedPlatform.value && connectedSocialAccountsWithAnalytics.value.length) {
    selectedPlatform.value = connectedSocialAccountsWithAnalytics.value[0].platform;
    activeTab.value = platformTabs[selectedPlatform.value]?.[0]?.id || 'views';
  }
  return connectedSocialAccountsWithAnalytics.value.find(a => a.platform === selectedPlatform.value);
});

const activeMetricIndex = computed(() => {
    const tabs = platformTabs[selectedPlatform.value] || [];
    const tab = tabs.find((t) => t.id === activeTab.value);
    return tab ? tab.index : 3;
});

const activeTabLabel = computed(() => {
    const tabs = platformTabs[selectedPlatform.value] || [];
    const tab = tabs.find((t) => t.id === activeTab.value);
    return tab ? tab.name : "Metric";
});

const activeHistory = computed(() => activeAccount.value?.analytics_data?.history ?? []);
const activeTopVideos = computed(() => activeAccount.value?.analytics_data?.top_videos ?? []);
const activeDemographics = computed(() => {
  const data = activeAccount.value?.analytics_data?.demographics ?? [];
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

const platformFee = computed(() => {
  const amt = Number(collabForm.amount);
  if (!amt || amt <= 0) return '0.00';
  return (amt * 0.1).toFixed(2);
});

function formatPrice(n) {
  const x = Number(n);
  return Number.isFinite(x) ? x.toFixed(2) : '0.00';
}

function platformName(platform) {
  return platformDisplayName(platform);
}

function formatFollowers(n) {
  if (n == null || n === '') return '';
  const num = Number(n);
  if (num >= 1e6) return (num / 1e6).toFixed(1) + 'M';
  if (num >= 1e3) return (num / 1e3).toFixed(1) + 'K';
  return num.toLocaleString();
}

const connectedSocialAccounts = computed(() => {
  const list = profile.value?.user?.social_accounts || [];
  return list.filter((s) => s.profile_url || s.username);
});

const packages = computed(() => {
  const p = profile.value;
  if (!p) return [];
  if (Array.isArray(p.user?.packages)) {
    return p.user.packages;
  }
  if (Array.isArray(p.packages)) {
    return p.packages;
  }
  return [];
});

const portfolio = computed(() => {
  const p = profile.value;
  if (!p) return [];
  if (Array.isArray(p.user?.creator_image_posts)) {
    return p.user.creator_image_posts.map((post) => ({
      id: post.id,
      image: post.image_url || post.image || null,
      caption: post.caption,
    }));
  }
  if (Array.isArray(p.portfolio)) {
    return p.portfolio.map((post) => ({
      id: post.id,
      image: post.image_url || post.image || null,
      caption: post.caption,
    }));
  }
  return [];
});

onMounted(async () => {
  try {
    const slug = route.params.slug;
    const res = await axios.get('/api/creators/' + slug);
    profile.value = res.data;
    if (selectedPackage.value) collabForm.amount = Number(selectedPackage.value.price);

    // Dynamic SEO
    if (profile.value) {
      const name = profile.value.user?.name || 'Creator';
      const category = profile.value.category || 'Influencer';
      const location = [profile.value.city_name, profile.value.state_name].filter(Boolean).join(', ');
      const title = `${name} | ${category} Creator in ${location || 'India'} | StarJD`;
      const description = profile.value.tagline || `Collaborate with ${name}, a ${category} creator. Browse packages, check analytics, and book high-quality content services on StarJD.`;
      const avatar = profile.value.avatar_url || (window.location.origin + '/logo.png');

      useHead({
        title,
        meta: [
          { name: 'description', content: description },
          { property: 'og:title', content: title },
          { property: 'og:description', content: description },
          { property: 'og:image', content: avatar },
          { property: 'og:type', content: 'profile' },
          { property: 'profile:username', content: slug },
          { name: 'twitter:card', content: 'summary' }
        ],
        script: [
          {
            type: 'application/ld+json',
            children: JSON.stringify({
              "@context": "https://schema.org",
              "@type": "Person",
              "name": name,
              "description": description,
              "image": avatar,
              "jobTitle": category,
              "address": {
                "@type": "PostalAddress",
                "addressLocality": profile.value.city_name,
                "addressRegion": profile.value.state_name
              },
              "url": window.location.href
            })
          }
        ]
      });
    }
  } catch (e) {
    profile.value = null;
  } finally {
    loading.value = false;
  }
  try {
    const userRes = await axios.get('/api/me', { withCredentials: true });
    isLoggedIn.value = !!userRes.data;
    isBrand.value = userRes.data?.role === 'brand';
  } catch {
    isLoggedIn.value = false;
    isBrand.value = false;
  }
});

watch(selectedPackage, (p) => {
  if (p) collabForm.amount = Number(p.price);
}, { immediate: true });

function openCollab(pkg) {
  selectedPackage.value = pkg;
  collabForm.amount = Number(pkg.price);
  collabForm.brand_notes = '';
  error.value = '';
  showModal.value = true;
}

async function submitCollab() {
  error.value = '';
  loadingCollab.value = true;
  try {
    await axios.post('/api/collaborations', {
      creator_id: profile.value.user.id,
      package_id: selectedPackage.value?.id ?? null,
      amount: collabForm.amount,
      brand_notes: collabForm.brand_notes || null,
    }, { withCredentials: true });

    if (selectedPackage.value) {
      selectedPackage.value.is_requested = true;
    }

    showModal.value = false;
    notify.success('Collaboration request sent. Check your Brand dashboard for updates.');

  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) {
      window.location.href = '/login?redirect=' + encodeURIComponent(route.fullPath);
      return;
    }
    if (e.response?.status === 402) {
      window.location.href = '/brand/choose-plan';
      return;
    }
    error.value = e.response?.data?.message || 'Failed to send request.';
  } finally {
    loadingCollab.value = false;
  }
}
</script>
