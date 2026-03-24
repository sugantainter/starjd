<template>
  <div class="p-6 max-w-7xl mx-auto">
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
            <component :is="stat.icon" class="w-6 h-6" :class="stat.iconColor" />
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
            <tr v-if="loading" v-for="i in 3" :key="i" class="animate-pulse">
              <td colspan="5" class="px-6 py-4"><div class="h-4 bg-gray-100 rounded w-full"></div></td>
            </tr>
            <tr v-else-if="filteredCampaigns.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-gray-400">No campaigns found. Start by creating one!</td>
            </tr>
            <tr v-for="campaign in filteredCampaigns" :key="campaign.id" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900">{{ campaign.title }}</div>
                <div class="text-xs text-gray-400 line-clamp-1 truncate max-w-xs">{{ campaign.content }}</div>
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
                  class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm transition-colors disabled:opacity-50"
                  :disabled="sendingId === campaign.id"
                >
                  {{ sendingId === campaign.id ? 'Sending...' : 'Send Now' }}
                </button>
                <button 
                  @click="viewDetails(campaign)"
                  class="ml-4 text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Campaign Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div @click="showCreateModal = false" class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200">
          <div class="bg-white px-8 pt-8 pb-6">
            <div class="flex items-center justify-between mb-8">
              <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Launch New Campaign</h3>
              <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18" />
                </svg>
              </button>
            </div>

            <form @submit.prevent="createCampaign" class="space-y-6">
              <!-- Title -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Campaign Title</label>
                <input 
                  v-model="form.title" 
                  type="text" 
                  required
                  placeholder="e.g. Weekly Premium Creators Spotlight"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                >
              </div>

              <!-- Content -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Message Content</label>
                <textarea 
                  v-model="form.content" 
                  rows="4" 
                  required
                  placeholder="What would you like to say?"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none resize-none"
                ></textarea>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Delivery Type -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Channels</label>
                  <select 
                    v-model="form.type"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none appearance-none"
                  >
                    <option value="both">Both (Email & Push)</option>
                    <option value="email">Email Only</option>
                    <option value="push">Push Only</option>
                  </select>
                </div>

                <!-- Target Type -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Target Audience</label>
                  <select 
                    v-model="form.target_type"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none appearance-none"
                  >
                    <option value="all">All Registered Users</option>
                    <option value="role">By User Role</option>
                    <option value="category">By Creator Category</option>
                    <option value="individual">Specific User</option>
                  </select>
                </div>
              </div>

              <!-- Conditional Filters -->
              <div v-if="form.target_type !== 'all'" class="p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100 animate-fadeIn">
                <template v-if="form.target_type === 'role'">
                  <label class="block text-sm font-semibold text-indigo-900 mb-2">Select User Role</label>
                  <select v-model="form.target_id" class="w-full px-4 py-3 bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option v-for="role in filterData.roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                  </select>
                </template>

                <template v-if="form.target_type === 'category'">
                  <label class="block text-sm font-semibold text-indigo-900 mb-2">Select Creator Category</label>
                  <select v-model="form.target_id" class="w-full px-4 py-3 bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option v-for="cat in filterData.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </template>

                <template v-if="form.target_type === 'individual'">
                  <label class="block text-sm font-semibold text-indigo-900 mb-2">User ID</label>
                  <input v-model="form.target_id" type="number" placeholder="Enter User ID" class="w-full px-4 py-3 bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </template>
              </div>

              <div class="pt-4 flex items-center gap-4">
                <button 
                  type="submit" 
                  class="flex-1 px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-indigo-200 disabled:opacity-50"
                  :disabled="creating"
                >
                  {{ creating ? 'Creating...' : 'Create Draft' }}
                </button>
                <button 
                  type="button"
                  @click="showCreateModal = false"
                  class="px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

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
    icon: 'DocumentTextIcon',
    bgColor: 'bg-indigo-50', 
    iconColor: 'text-indigo-600' 
  },
  { 
    label: 'Successful Sends', 
    value: stats.value.total_sent, 
    icon: 'CheckCircleIcon',
    bgColor: 'bg-green-50', 
    iconColor: 'text-green-600' 
  },
  { 
    label: 'Delivery Failures', 
    value: stats.value.total_failed, 
    icon: 'ExclamationCircleIcon',
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
    alert('Failed to load marketing data. Please check your credentials.');
  } finally {
    loading.value = false;
  }
};

const createCampaign = async () => {
  creating.value = true;
  try {
    const res = await axios.post('/api/admin/marketing', form.value);
    campaigns.value.unshift(res.data.campaign);
    showCreateModal.value = false;
    form.value = { title: '', content: '', type: 'both', target_type: 'all', target_id: null };
  } catch (e) {
    alert('Error creating campaign. Please try again.');
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
    alert('Failed to send campaign.');
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
