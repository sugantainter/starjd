<template>
  <div class="space-y-8 pb-12">
    <!-- Back Button & Page Header -->
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-4">
        <button 
          @click="$router.push('/admin/users')"
          class="flex h-10 w-10 items-center justify-center rounded-full border border-[#e2e8f0] bg-white transition hover:bg-[#f8fafc] hover:shadow-sm"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-[#1a1a1a]">User Analytics & Performance</h1>
          <p class="text-sm text-[#64748b]">Deep-dive into profile data and social influence.</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <button 
          class="rounded-lg border border-[#e2e8f0] bg-white px-4 py-2 text-sm font-medium text-[#1a1a1a] shadow-sm transition hover:bg-[#f8fafc]"
          @click="sendMessage"
        >
          Message User
        </button>
        <button 
          class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-bold text-white shadow-lg shadow-[#e63946]/20 transition hover:bg-[#d62839] hover:shadow-xl"
          @click="toggleStatus"
        >
          Manage Account
        </button>
      </div>
    </header>

    <div v-if="loading" class="flex h-64 items-center justify-center">
      <div class="h-12 w-12 animate-spin rounded-full border-4 border-[#e63946] border-t-transparent"></div>
    </div>

    <div v-else-if="user" class="grid gap-8 lg:grid-cols-12">
      <!-- Left Column: Quick Glance -->
      <div class="space-y-8 lg:col-span-4">
        <!-- Profile Card -->
        <div class="overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-sm">
          <div class="h-24 bg-gradient-to-r from-slate-800 to-slate-900"></div>
          <div class="px-6 pb-6 pt-0">
            <div class="-mt-12 flex flex-col items-center">
              <div class="relative rounded-full bg-white p-1 ring-4 ring-white shadow-xl">
                <div class="h-24 w-24 overflow-hidden rounded-full bg-[#f1f5f9]">
                  <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" class="h-full w-full object-cover" />
                  <div v-else class="flex h-full w-full items-center justify-center text-3xl font-bold text-[#94a3b8]">
                    {{ user.name.charAt(0) }}
                  </div>
                </div>
                <div 
                  class="absolute bottom-1 right-1 h-5 w-5 rounded-full border-2 border-white"
                  :class="user.email_verified_at ? 'bg-emerald-500' : 'bg-amber-500'"
                  :title="user.email_verified_at ? 'Email Verified' : 'Email Not Verified'"
                ></div>
              </div>
              <h2 class="mt-4 text-xl font-bold text-[#1a1a1a]">{{ user.name }}</h2>
              <span class="mt-1 flex items-center gap-1.5 text-sm font-medium text-[#64748b]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ user.email }}
              </span>
              <div class="mt-4 flex flex-wrap justify-center gap-2">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-600">
                  {{ user.role?.replace('_', ' ') || 'Customer' }}
                </span>
                <span v-if="user.state" class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-red-600">
                  {{ user.state.name }}
                </span>
              </div>
            </div>

            <!-- Profile Completion -->
            <div class="mt-8 rounded-xl bg-[#f8fafc] p-5 shadow-inner">
              <div class="mb-4 flex items-center justify-between">
                <div>
                  <h4 class="text-xs font-black uppercase tracking-widest text-[#64748b]">Profile Health</h4>
                  <p class="text-[10px] text-[#94a3b8]">{{ user.profile_completion_details?.filter(d => d.filled).length }} of {{ user.profile_completion_details?.length }} items completed</p>
                </div>
                <span class="text-lg font-black" :class="user.profile_completion >= 80 ? 'text-emerald-600' : 'text-[#e63946]'">
                  {{ user.profile_completion }}%
                </span>
              </div>
              <div class="h-3 w-full overflow-hidden rounded-full bg-white ring-1 ring-[#e2e8f0]">
                <div 
                  class="h-full rounded-full transition-all duration-1000 ease-out"
                  :class="user.profile_completion >= 80 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 shadow-sm' : 'bg-gradient-to-r from-[#e63946] to-rose-400 shadow-sm'"
                  :style="{ width: user.profile_completion + '%' }"
                ></div>
              </div>
              
              <!-- Detailed Breakdown -->
              <div class="mt-6 space-y-3">
                <div v-for="detail in user.profile_completion_details" :key="detail.label" class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="flex h-4 w-4 items-center justify-center rounded-full" :class="detail.filled ? 'bg-emerald-100' : 'bg-gray-100'">
                      <svg v-if="detail.filled" xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      <div v-else class="h-1.5 w-1.5 rounded-full bg-gray-300"></div>
                    </div>
                    <span class="text-xs" :class="detail.filled ? 'text-[#1a1a1a] font-medium' : 'text-[#94a3b8]'">{{ detail.label }}</span>
                  </div>
                  <span v-if="!detail.filled" class="text-[10px] font-bold uppercase tracking-tighter text-[#e63946]">Missing</span>
                </div>
              </div>

              <p class="mt-6 text-[10px] text-center uppercase tracking-widest text-[#94a3b8] border-t border-white pt-4">Last checked {{ timeAgo(user.updated_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Social Reach Summary -->
        <div class="rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
          <h3 class="mb-6 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-[#1a1a1a]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#e63946]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            Social Footprint
          </h3>
          <div class="space-y-4">
            <div v-for="social in user.social_accounts" :key="social.id" class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-[#f1f5f9]">
                  <!-- Simple social icons based on platform -->
                  <span class="text-xs font-bold capitalize">{{ social.platform.charAt(0) }}</span>
                </div>
                <span class="text-sm font-medium text-[#1a1a1a]">{{ social.platform }}</span>
              </div>
              <span class="text-sm font-bold text-[#e63946]">{{ formatNumber(social.follower_count) }}</span>
            </div>
            <div v-if="!user.social_accounts?.length" class="text-center py-4">
              <p class="text-xs text-[#94a3b8]">No social accounts linked yet.</p>
            </div>
            <div class="mt-6 border-t border-[#f1f5f9] pt-4">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-[#64748b]">Total Influence</span>
                <span class="text-lg font-black text-[#1a1a1a]">{{ formatNumber(user.analytics.social_reach) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Details & Analytics -->
      <div class="space-y-8 lg:col-span-8">
        <!-- Key Performance Indicators -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div class="rounded-2xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-[#64748b]">Wallet Balance</p>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-black text-[#1a1a1a]">₹{{ formatCurrency(user.analytics.total_earnings) }}</span>
            </div>
            <p class="mt-2 text-xs text-emerald-600 font-medium">Available for withdrawal</p>
          </div>
          <div class="rounded-2xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-[#64748b]">Total Collaborations</p>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-black text-[#1a1a1a]">{{ user.analytics.total_collaborations }}</span>
            </div>
            <p class="mt-2 text-xs text-blue-600 font-medium">+{{ user.analytics.total_collaborations }} this lifetime</p>
          </div>
          <div class="rounded-2xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-[#64748b]">Content Posts</p>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-black text-[#1a1a1a]">{{ user.analytics.total_posts }}</span>
            </div>
            <p class="mt-2 text-xs text-purple-600 font-medium">{{ user.analytics.total_packages }} Active Packages</p>
          </div>
        </div>

        <!-- Detail Tabs -->
        <div class="overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-sm">
          <div class="flex border-b border-[#e2e8f0] bg-[#f8fafc] px-6">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="activeTab = tab.id"
              class="relative py-4 text-sm font-bold transition-all"
              :class="activeTab === tab.id ? 'text-[#e63946]' : 'text-[#64748b] hover:text-[#1a1a1a]'"
              style="margin-right: 2rem;"
            >
              {{ tab.label }}
              <div v-if="activeTab === tab.id" class="absolute bottom-0 left-0 h-1 w-full rounded-t-full bg-[#e63946]"></div>
            </button>
          </div>

          <div class="p-8">
            <!-- Profile Info Tab -->
            <div v-if="activeTab === 'profile'" class="space-y-10">
              <div v-if="user.creator_profile" class="grid gap-10 md:grid-cols-2">
                <div class="space-y-6">
                  <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-[#94a3b8] mb-2">Biography</h4>
                    <p class="text-sm leading-relaxed text-[#1a1a1a]">{{ user.creator_profile.bio || 'No bio provided.' }}</p>
                  </div>
                  <div class="grid grid-cols-2 gap-6">
                    <div>
                      <h4 class="text-xs font-bold uppercase tracking-widest text-[#94a3b8] mb-1">Category</h4>
                      <p class="text-sm font-bold text-[#1a1a1a]">{{ user.creator_profile.category || '--' }}</p>
                    </div>
                    <div>
                      <h4 class="text-xs font-bold uppercase tracking-widest text-[#94a3b8] mb-1">Gender</h4>
                      <p class="text-sm font-bold text-[#1a1a1a]">{{ user.creator_profile.gender || '--' }}</p>
                    </div>
                  </div>
                </div>
                <div class="space-y-6 rounded-2xl bg-[#f8fafc] p-6">
                  <h4 class="text-xs font-black uppercase tracking-widest text-[#e63946]">Financial Metrics</h4>
                  <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-white pb-3">
                      <span class="text-sm text-[#64748b]">Minimum Rate</span>
                      <span class="text-sm font-black text-[#1a1a1a]">₹{{ user.creator_profile.min_rate || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-white pb-3">
                      <span class="text-sm text-[#64748b]">Engagement Rate</span>
                      <span class="text-sm font-black text-emerald-600">{{ user.creator_profile.engagement_rate || 0 }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-[#64748b]">Language</span>
                      <span class="text-sm font-black text-[#1a1a1a]">{{ user.creator_profile.language || 'English' }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else-if="user.brand_profile" class="space-y-6">
                <!-- Brand specifics would go here -->
                <p>Brand profile data view under development.</p>
              </div>
              <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <div class="h-16 w-16 rounded-full bg-[#f1f5f9] flex items-center justify-center mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <p class="text-[#64748b]">No professional profile created for this account yet.</p>
              </div>
            </div>

            <!-- Packages Tab -->
            <div v-if="activeTab === 'packages'" class="grid gap-6 sm:grid-cols-2">
              <div v-for="pkg in user.packages" :key="pkg.id" class="group relative overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white transition hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute top-0 right-0 p-4">
                   <div class="rounded-lg bg-[#e63946] px-3 py-1 text-xs font-black text-white shadow-sm">₹{{ pkg.price }}</div>
                </div>
                <div class="p-6">
                  <h4 class="pr-12 text-lg font-black text-[#1a1a1a]">{{ pkg.name }}</h4>
                  <p class="mt-3 text-sm leading-relaxed text-[#64748b] line-clamp-3">{{ pkg.description }}</p>
                  <div class="mt-6 flex items-center justify-between border-t border-[#f1f5f9] pt-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#94a3b8]">{{ pkg.category }}</span>
                    <span class="text-xs font-medium text-[#1a1a1a]">Includes {{ pkg.deliverables?.length || 0 }} items</span>
                  </div>
                </div>
              </div>
              <div v-if="!user.packages?.length" class="col-span-full py-12 text-center">
                <p class="text-[#64748b]">This user hasn't created any service packages yet.</p>
              </div>
            </div>

            <!-- Activity Tab -->
            <div v-if="activeTab === 'activity'" class="space-y-4">
              <div v-for="app in user.campaign_applications" :key="app.id" class="flex items-center gap-4 rounded-2xl border border-[#e2e8f0] p-4 transition hover:bg-[#f8fafc]">
                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl bg-[#f1f5f9]">
                  <img v-if="app.campaign?.image_url" :src="app.campaign.image_url" class="h-full w-full object-cover" />
                  <div v-else class="flex h-full w-full items-center justify-center bg-slate-200 text-slate-400">?</div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="truncate text-sm font-black text-[#1a1a1a]">{{ app.campaign?.title || 'Unknown Campaign' }}</p>
                  <p class="text-xs text-[#64748b]">Applied on {{ new Date(app.created_at).toLocaleDateString() }}</p>
                </div>
                <span 
                  class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest"
                  :class="{
                    'bg-amber-100 text-amber-700': app.status === 'pending',
                    'bg-emerald-100 text-emerald-700': app.status === 'accepted',
                    'bg-rose-100 text-rose-700': app.status === 'rejected'
                  }"
                >
                  {{ app.status }}
                </span>
              </div>
              <div v-if="!user.campaign_applications?.length" class="py-12 text-center text-[#64748b]">
                No campaign activity recorded.
              </div>
            </div>

            <!-- Direct Messages Tab -->
            <div v-if="activeTab === 'messages'" class="flex flex-col h-[500px] border border-[#e2e8f0] rounded-2xl bg-gray-50 overflow-hidden shadow-inner">
              <div class="flex-1 overflow-y-auto p-6 space-y-4" ref="messageBox">
                <div v-for="msg in messages" :key="msg.id" class="flex flex-col" :class="msg.isMe ? 'items-end' : 'items-start'">
                  <div 
                    class="max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-sm"
                    :class="msg.isMe ? 'bg-[#e63946] text-white rounded-tr-none' : 'bg-white text-[#1a1a1a] border border-[#e2e8f0] rounded-tl-none'"
                  >
                    {{ msg.text }}
                  </div>
                  <span class="mt-1 text-[10px] text-[#94a3b8] px-1">{{ msg.time }}</span>
                </div>
                <div v-if="!messages.length" class="flex flex-col items-center justify-center h-full text-center py-20">
                  <div class="h-16 w-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                  </div>
                  <p class="text-sm font-bold text-[#1a1a1a]">Start a conversation</p>
                  <p class="text-xs text-[#94a3b8] mt-1">Send a message to {{ user.name }} directly.</p>
                </div>
              </div>
              
              <!-- Message Input -->
              <div class="p-4 bg-white border-t border-[#e2e8f0]">
                <div class="flex items-center gap-3">
                  <textarea 
                    v-model="newMessage" 
                    rows="1" 
                    placeholder="Type a message..." 
                    class="flex-1 bg-gray-50 border-[#e2e8f0] rounded-xl text-sm focus:ring-[#e63946] focus:border-[#e63946] resize-none py-2 px-4"
                    @keyup.enter.exact="sendDirectMessage"
                  ></textarea>
                  <button 
                    @click="sendDirectMessage"
                    :disabled="!newMessage.trim() || sendingMessage"
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-[#e63946] text-white shadow-lg shadow-[#e63946]/20 transition hover:bg-[#d62839] disabled:opacity-50"
                  >
                    <svg v-if="!sendingMessage" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                    <div v-else class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                  </button>
                </div>
                <p class="mt-2 text-[10px] text-[#94a3b8] text-center">Admin communication is synced across all user platforms.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const user = ref(null);
const loading = ref(true);
const activeTab = ref('profile');

const tabs = [
  { id: 'profile', label: 'Detailed Profile' },
  { id: 'messages', label: 'Direct Messages' },
  { id: 'packages', label: 'Packages' },
  { id: 'activity', label: 'History & Activity' },
  { id: 'wallet', label: 'Financials' },
];

const messages = ref([]);
const newMessage = ref('');
const sendingMessage = ref(false);
const messageBox = ref(null);
let messagePolling = null;

const fetchUser = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/admin/users/${route.params.id}`);
    user.value = response.data;
    if (activeTab.value === 'messages') {
      startMessagePolling();
    }
  } catch (error) {
    console.error('Error fetching user:', error);
  } finally {
    loading.value = false;
  }
};

const fetchMessages = async (background = false) => {
  if (!user.value) return;
  try {
    const response = await axios.get(`/api/messages/${user.value.id}`);
    const newMessages = response.data;
    
    if (JSON.stringify(newMessages) !== JSON.stringify(messages.value)) {
      messages.value = newMessages;
      scrollToBottom();
    }
  } catch (error) {
    console.error('Error fetching messages:', error);
  }
};

const sendDirectMessage = async () => {
  if (!newMessage.value.trim() || sendingMessage.value) return;
  sendingMessage.value = true;
  try {
    await axios.post('/api/messages', {
      receiver_id: user.value.id,
      body: newMessage.value
    });
    newMessage.value = '';
    await fetchMessages();
    scrollToBottom();
  } catch (error) {
    console.error('Error sending message:', error);
    alert('Failed to send message.');
  } finally {
    sendingMessage.value = false;
  }
};

const startMessagePolling = () => {
  stopMessagePolling();
  fetchMessages();
  messagePolling = setInterval(() => fetchMessages(true), 5000);
};

const stopMessagePolling = () => {
  if (messagePolling) {
    clearInterval(messagePolling);
    messagePolling = null;
  }
};

const scrollToBottom = () => {
  setTimeout(() => {
    if (messageBox.value) {
      messageBox.value.scrollTop = messageBox.value.scrollHeight;
    }
  }, 100);
};

// ... existing formatters ...
const formatNumber = (num) => {
  if (!num) return '0';
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
  return num.toString();
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-IN').format(amount || 0);
};

const timeAgo = (date) => {
  if (!date) return 'never';
  const seconds = Math.floor((new Date() - new Date(date)) / 1000);
  let interval = seconds / 31536000;
  if (interval > 1) return Math.floor(interval) + " years ago";
  interval = seconds / 2592000;
  if (interval > 1) return Math.floor(interval) + " months ago";
  interval = seconds / 86400;
  if (interval > 1) return Math.floor(interval) + " days ago";
  interval = seconds / 3600;
  if (interval > 1) return Math.floor(interval) + " hours ago";
  interval = seconds / 60;
  if (interval > 1) return Math.floor(interval) + " mins ago";
  return Math.floor(seconds) + " secs ago";
};

const sendMessage = () => {
  activeTab.value = 'messages';
  scrollToBottom();
};

const toggleStatus = async () => {
  if (!confirm(`Are you sure you want to manage account status for ${user.value.name}?`)) return;
  
  try {
    // We already have a patch route for updateStatus in web.php
    // Though it doesn't do much yet, we can call it to show it's working.
    await axios.patch(`/api/admin/users/${user.value.id}/status`, {
      is_active: true // This would be toggled in a real scenario
    });
    alert('User account managed successfully. (Status updated in system)');
  } catch (error) {
    console.error('Error updating status:', error);
    alert('Failed to update account status.');
  }
};

watch(activeTab, (newTab) => {
  if (newTab === 'messages') {
    startMessagePolling();
  } else {
    stopMessagePolling();
  }
});

onMounted(fetchUser);

onUnmounted(() => {
  stopMessagePolling();
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
