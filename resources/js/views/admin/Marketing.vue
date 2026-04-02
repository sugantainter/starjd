<template>
  <div class="p-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 drop-shadow-sm">Marketing Management</h1>
        <p class="text-gray-500 mt-1">Send multi-channel campaigns with professional precision.</p>
      </div>
      <button 
        @click="showCreateModal = true"
        class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-indigo-200 focus:ring-4 focus:ring-indigo-100 group"
      >
        <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        New Campaign
      </button>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <div v-for="(stat, index) in quickStats" :key="index" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center space-x-4">
          <div :class="`p-3 rounded-xl ${stat.bgColor}`">
            <svg v-if="stat.icon === 'document'" class="w-6 h-6" :class="stat.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <svg v-else-if="stat.icon === 'check'" class="w-6 h-6" :class="stat.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else class="w-6 h-6" :class="stat.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ stat.label }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ stat.value }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Campaign History Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-10">
      <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Campaign History</h2>
        <div class="relative max-w-xs">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search campaigns..." 
            class="pl-10 pr-4 py-2 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 transition-all w-full"
          >
          <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50/50 text-left text-xs font-semibold text-gray-500 uppercase tracking-widest">
              <th class="px-6 py-4">Title & Content</th>
              <th class="px-6 py-4">Type</th>
              <th class="px-6 py-4">Targeting</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading">
              <td colspan="5" class="px-6 py-12 text-center text-gray-400">Loading campaigns...</td>
            </tr>
            <tr v-else-if="filteredCampaigns.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-gray-400">No campaigns found. Start by creating one!</td>
            </tr>
            <tr v-else v-for="campaign in filteredCampaigns" :key="campaign.id" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900">{{ campaign.title }}</div>
                <div class="text-xs text-gray-400 truncate max-w-xs">{{ campaign.content }}</div>
              </td>
              <td class="px-6 py-4">
                <span :class="getTypeBadge(campaign.type)">{{ campaign.type }}</span>
              </td>
              <td class="px-6 py-4">
                <div class="text-xs font-medium text-gray-600">{{ capitalize(campaign.target_type) }}</div>
                <div class="text-[10px] text-gray-400">{{ getTargetDetail(campaign) }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <span :class="`w-2 h-2 rounded-full mr-2 ${getStatusColor(campaign.status)}`"></span>
                  <span class="text-sm text-gray-600 capitalize">{{ campaign.status }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-right">
                <button 
                  v-if="campaign.status === 'draft'"
                  @click="sendCampaign(campaign)"
                  class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm transition-colors"
                  :disabled="sendingId === campaign.id"
                >
                  {{ sendingId === campaign.id ? 'Sending...' : 'Send Now' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Campaign Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div @click="showCreateModal = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity"></div>
      
      <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 transform transition-all z-10">
        <div class="px-8 pt-8 pb-6">
          <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-bold text-gray-900">Launch New Campaign</h3>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="createCampaign" class="space-y-6">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Campaign Title</label>
              <input v-model="form.title" type="text" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Message Content</label>
              <textarea v-model="form.content" rows="4" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none resize-none"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Channels</label>
                <select v-model="form.type" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none">
                  <option value="both">Both (Email & Push)</option>
                  <option value="email">Email Only</option>
                  <option value="push">Push Only</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Target Audience</label>
                <select v-model="form.target_type" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none">
                  <option value="all">All Users</option>
                  <option value="role">By Role</option>
                  <option value="category">By Category</option>
                  <option value="individual">Individual</option>
                </select>
              </div>
            </div>

            <!-- Conditional Filters -->
            <div v-if="form.target_type !== 'all'" class="p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100 animate-fadeIn">
              <template v-if="form.target_type === 'role'">
                <label class="block text-sm font-semibold text-indigo-900 mb-2">Select User Role</label>
                <select v-model="form.target_id" class="w-full px-4 py-3 bg-white border border-indigo-200 rounded-xl outline-none">
                  <option :value="null" disabled>Select User Role</option>
                  <option v-for="role in filterData.roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                </select>
              </template>

              <template v-if="form.target_type === 'category'">
                <label class="block text-sm font-semibold text-indigo-900 mb-2">Select Creator Category</label>
                <select v-model="form.target_id" class="w-full px-4 py-3 bg-white border border-indigo-200 rounded-xl outline-none">
                  <option :value="null" disabled>Select Creator Category</option>
                  <option v-for="cat in filterData.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </template>

              <template v-if="form.target_type === 'individual'">
                <label class="block text-sm font-semibold text-indigo-900 mb-2">User ID</label>
                <input v-model="form.target_id" type="number" placeholder="Enter User ID" class="w-full px-4 py-3 bg-white border border-indigo-200 rounded-xl outline-none">
              </template>
            </div>

            <div class="pt-4 flex items-center gap-4">
              <button type="submit" :disabled="creating" class="flex-1 px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-indigo-200 disabled:opacity-50">
                {{ creating ? 'Creating...' : 'Create Draft' }}
              </button>
              <button type="button" @click="showCreateModal = false" class="px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { notify } from '../../lib/notify.js';

const loading = ref(true);
const creating = ref(false);
const sendingId = ref(null);
const showCreateModal = ref(false);
const campaigns = ref([]);
const searchQuery = ref('');
const stats = ref({
  total_campaigns: 0,
  total_sent: 0,
  total_failed: 0
});

const filterData = ref({
  roles: [],
  categories: []
});

const form = ref({
  title: '',
  content: '',
  type: 'both',
  target_type: 'all',
  target_id: null
});

const quickStats = computed(() => [
  { 
    label: 'Total Campaigns', 
    value: stats.value.total_campaigns, 
    icon: 'document',
    bgColor: 'bg-indigo-50', 
    iconColor: 'text-indigo-600' 
  },
  { 
    label: 'Successful Sends', 
    value: stats.value.total_sent, 
    icon: 'check',
    bgColor: 'bg-green-50', 
    iconColor: 'text-green-600' 
  },
  { 
    label: 'Delivery Failures', 
    value: stats.value.total_failed, 
    icon: 'exclamation',
    bgColor: 'bg-red-50', 
    iconColor: 'text-red-600' 
  }
]);

const filteredCampaigns = computed(() => {
  if (!searchQuery.value) return campaigns.value;
  return campaigns.value.filter(c => 
    c.title.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
    c.content.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const loadData = async () => {
  loading.value = true;
  try {
    const [cRes, sRes, fRes] = await Promise.all([
      axios.get('/api/admin/marketing'),
      axios.get('/api/admin/marketing/stats'),
      axios.get('/api/admin/marketing/filters')
    ]);
    campaigns.value = cRes.data;
    stats.value = sRes.data;
    filterData.value = fRes.data;
  } catch (e) {
    console.error('Failed to load marketing data', e);
    notify.error('Failed to load marketing data. Please check your credentials.');
  } finally {
    loading.value = false;
  }
};

const createCampaign = async () => {
  console.log('Creating campaign with form data:', form.value);
  creating.value = true;
  try {
    const res = await axios.post('/api/admin/marketing', form.value);
    console.log('Campaign created successfully:', res.data);
    campaigns.value.unshift(res.data.campaign);
    showCreateModal.value = false;
    form.value = { title: '', content: '', type: 'both', target_type: 'all', target_id: null };
  } catch (e) {
    notify.error('Error creating campaign. Please try again.');
    console.error('Error creating campaign:', e);
  } finally {
    creating.value = false;
  }
};

const sendCampaign = async (campaign) => {
  if (!confirm(`Are you sure you want to launch "${campaign.title}"? This cannot be undone.`)) return;
  
  sendingId.value = campaign.id;
  try {
    const res = await axios.post(`/api/admin/marketing/${campaign.id}/send`);
    campaign.status = res.data.campaign.status;
  } catch (e) {
    notify.error('Failed to send campaign.');
  } finally {
    sendingId.value = null;
    loadData(); // Refresh to get updated stats
  }
};

const getTypeBadge = (type) => {
  const base = 'px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider ';
  if (type === 'both') return base + 'bg-purple-100 text-purple-700';
  if (type === 'email') return base + 'bg-blue-100 text-blue-700';
  return base + 'bg-orange-100 text-orange-700';
};

const getStatusColor = (status) => {
  if (status === 'completed') return 'bg-green-500';
  if (status === 'sending' || status === 'queued') return 'bg-yellow-500 animate-pulse';
  if (status === 'failed') return 'bg-red-500';
  return 'bg-gray-300';
};

const getTargetDetail = (c) => {
  if (c.target_type === 'all') return 'Everyone';
  if (c.target_type === 'role') {
    const role = filterData.value.roles.find(r => r.id == c.target_id);
    return role ? `Role: ${role.name}` : `Role ID: ${c.target_id}`;
  }
  if (c.target_type === 'category') {
    const cat = filterData.value.categories.find(r => r.id == c.target_id);
    return cat ? `Category: ${cat.name}` : `Category ID: ${c.target_id}`;
  }
  return `User ID: ${c.target_id}`;
};

const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);

onMounted(() => {
  loadData();
});
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
