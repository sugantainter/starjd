<template>
  <div class="animate-in fade-in duration-700">
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-extrabold text-[#1a1a1a] tracking-tight">Withdrawal Requests</h1>
        <p class="mt-1 text-sm font-medium text-[#64748b]">Manage and process collaboration payout claims and refunds.</p>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-2xl border border-slate-200 shadow-sm">
           <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
           <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ pendingCount }} Pending Approvals</span>
        </div>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
       <div v-for="stat in stats" :key="stat.label" class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">{{ stat.label }}</p>
          <p class="text-3xl font-black text-slate-900">₹{{ stat.value }}</p>
       </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap items-center gap-4 bg-white p-4 rounded-[24px] border border-slate-100 shadow-sm">
       <div class="flex items-center gap-2">
          <span class="text-[10px] font-black text-slate-400 uppercase ml-2">Status:</span>
          <select v-model="filterStatus" class="bg-slate-50 border-none rounded-xl text-xs font-bold py-2 pr-10 focus:ring-2 focus:ring-indigo-100">
             <option value="all">All Requests</option>
             <option value="pending">Pending</option>
             <option value="processing">Processing</option>
             <option value="paid">Paid</option>
             <option value="rejected">Rejected</option>
          </select>
       </div>
       <div class="flex items-center gap-2 border-l border-slate-100 pl-4">
          <span class="text-[10px] font-black text-slate-400 uppercase">Type:</span>
          <select v-model="filterType" class="bg-slate-50 border-none rounded-xl text-xs font-bold py-2 pr-10 focus:ring-2 focus:ring-indigo-100">
             <option value="all">All Types</option>
             <option value="creator_payout">Creator Payouts</option>
             <option value="brand_refund">Brand Refunds</option>
          </select>
       </div>
       <div class="flex-1"></div>
       <button @click="load" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
       </button>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-[32px] border border-slate-100 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/50">
              <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">User Details</th>
              <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">Bank Information</th>
              <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">Project / Amount</th>
              <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">Status</th>
              <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="r in filteredList" :key="r.id" class="hover:bg-slate-50/30 transition-colors group">
              <td class="px-8 py-6">
                <div class="flex items-center gap-4">
                  <div class="h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center font-bold text-slate-400 overflow-hidden border border-slate-200">
                     <img v-if="r.user?.profile?.avatar || r.user?.profile?.logo" :src="r.user.profile.avatar || r.user.profile.logo" class="h-full w-full object-cover" />
                     <span v-else>{{ r.user?.name?.charAt(0) }}</span>
                  </div>
                  <div>
                    <div class="text-sm font-bold text-[#1a1a1a]">{{ r.user?.name }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ r.user?.email }}</div>
                    <!-- Performance Stats -->
                    <div class="mt-2 flex flex-wrap gap-3">
                       <!-- Approval Circle -->
                       <div class="relative h-7 w-7 flex items-center justify-center" :title="'Approval Rate: ' + getAppRate(r.user) + '%'">
                          <svg class="h-full w-full -rotate-90">
                             <circle cx="14" cy="14" r="12" fill="transparent" stroke="#f1f5f9" stroke-width="2.5" />
                             <circle cx="14" cy="14" r="12" fill="transparent" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" :stroke-dasharray="75" :stroke-dashoffset="75 - (75 * getAppRate(r.user) / 100)" />
                          </svg>
                          <span class="absolute text-[6px] font-black text-emerald-700">{{ getAppRate(r.user) }}%</span>
                       </div>
                       <!-- Rejection Circle -->
                       <div class="relative h-7 w-7 flex items-center justify-center" :title="'Rejection Rate: ' + getRejRate(r.user) + '%'">
                          <svg class="h-full w-full -rotate-90">
                             <circle cx="14" cy="14" r="12" fill="transparent" stroke="#f1f5f9" stroke-width="2.5" />
                             <circle cx="14" cy="14" r="12" fill="transparent" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" :stroke-dasharray="75" :stroke-dashoffset="75 - (75 * getRejRate(r.user) / 100)" />
                          </svg>
                          <span class="absolute text-[6px] font-black text-red-700">{{ getRejRate(r.user) }}%</span>
                       </div>
                       <!-- Revision Circle -->
                       <div class="h-7 w-7 rounded-full bg-purple-50 border border-purple-100 flex flex-col items-center justify-center leading-none" :title="'Avg Revisions: ' + getRevRate(r.user)">
                          <span class="text-[8px] font-black text-purple-700">{{ getRevRate(r.user) }}</span>
                          <span class="text-[4px] font-bold text-purple-400 uppercase">Rev</span>
                       </div>
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-6">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ r.bank_account?.account_holder_name }}</div>
                <div class="text-xs font-bold text-slate-700">{{ r.bank_account?.bank_name }}</div>
                <div class="text-xs font-mono text-slate-500 mt-0.5">{{ r.bank_account?.account_number }}</div>
                <div class="text-[9px] font-black text-indigo-500 uppercase mt-1">IFSC: {{ r.bank_account?.ifsc_code }}</div>
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center gap-2">
                   <span class="text-lg font-black text-slate-900">₹{{ r.amount }}</span>
                   <span :class="r.type === 'creator_payout' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600'" class="text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-tighter border-px">
                      {{ r.type === 'creator_payout' ? 'Payout' : 'Refund' }}
                   </span>
                </div>
                <!-- Platform Fee Breakdown for Creators -->
                <div v-if="r.type === 'creator_payout'" class="mt-1 text-[9px] text-slate-400 font-bold uppercase">
                   Net payout after 10% platform fee
                </div>
                <div class="mt-1.5 text-[10px] text-slate-400 font-bold uppercase tracking-tight">Collab #{{ r.collaboration_id }}</div>
              </td>
              <td class="px-8 py-6">
                 <span :class="getStatusClass(r.status)" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border">
                    {{ r.status }}
                 </span>
                 <div v-if="r.processed_at" class="mt-2 text-[9px] text-slate-400 font-bold uppercase">{{ formatDate(r.processed_at) }}</div>
              </td>
              <td class="px-8 py-6">
                <button v-if="r.status === 'pending' || r.status === 'processing'" @click="openProcessModal(r)" class="flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition active:scale-95">
                  Process
                </button>
                <div v-else-if="r.receipt_url" class="flex gap-2">
                   <a v-if="r.receipt_full_url" :href="r.receipt_full_url" target="_blank" class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:text-indigo-600 border border-slate-200 transition" title="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                   </a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="!filteredList.length" class="p-20 text-center">
         <div class="h-16 w-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300 border border-dashed border-slate-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
         </div>
         <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No withdrawal requests found</p>
      </div>
    </div>

    <!-- Process Modal -->
    <div v-if="showProcessModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" @click.self="showProcessModal = false">
       <div class="w-full max-w-lg overflow-hidden rounded-[40px] bg-white shadow-2xl animate-in zoom-in-95 duration-500">
          <div class="p-10 border-b border-slate-50 bg-[#f8fafc]">
             <h2 class="text-2xl font-black text-slate-900 tracking-tight">Update Request</h2>
             <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Processing claim for {{ selectedRequest?.user?.name }}</p>
             
             <!-- Amount Breakdown -->
             <div v-if="selectedRequest?.type === 'creator_payout'" class="mt-6 p-4 rounded-2xl bg-white border border-slate-200">
                <div class="flex justify-between text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">
                   <span>Project Amount (Gross)</span>
                   <span>₹{{ (selectedRequest?.amount / 0.9).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-[10px] font-black text-red-500 uppercase tracking-widest leading-relaxed mt-1">
                   <span>Platform Fee (10%)</span>
                   <span>- ₹{{ (selectedRequest?.amount * 10 / 90).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-xs font-black text-emerald-600 uppercase tracking-widest leading-relaxed mt-3 pt-3 border-t border-slate-50">
                   <span>Net Final Payout</span>
                   <span>₹{{ selectedRequest?.amount }}</span>
                </div>
             </div>
          </div>
          <form @submit.prevent="submitProcess" class="p-10 space-y-6">
             <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">New Status</label>
                <div class="grid grid-cols-2 gap-3">
                   <button v-for="st in ['paid', 'rejected', 'processing']" :key="st" type="button" 
                           @click="form.status = st"
                           :class="form.status === st ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-100'"
                           class="py-3 rounded-2xl border text-xs font-black uppercase tracking-widest transition-all">
                      {{ st }}
                   </button>
                </div>
             </div>

             <div v-if="form.status === 'paid'">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Payment Receipt (Invoice/PDF)</label>
                <input type="file" @change="handleFile" class="w-full text-xs font-bold text-slate-500 bg-slate-50 rounded-2xl border-none p-4 file:mr-4 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
             </div>

             <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Internal Notes</label>
                <textarea v-model="form.admin_notes" class="w-full h-24 rounded-[24px] border-none bg-slate-50 p-5 text-sm font-medium focus:ring-2 focus:ring-indigo-100" placeholder="Add details about payment transaction ID etc..."></textarea>
             </div>

             <div class="pt-4 flex gap-3">
                <button type="submit" :disabled="submitting" class="flex-1 h-14 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-slate-200 hover:bg-black transition active:scale-95 disabled:opacity-40">
                   {{ submitting ? 'Saving...' : 'Update Status' }}
                </button>
                <button type="button" @click="showProcessModal = false" class="px-8 h-14 border border-slate-200 rounded-2xl font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition">
                   Cancel
                </button>
             </div>
          </form>
       </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { notify } from '../../lib/notify.js';

const list = ref([]);
const filterStatus = ref('all');
const filterType = ref('all');
const showProcessModal = ref(false);
const selectedRequest = ref(null);
const submitting = ref(false);

const form = ref({
   status: 'paid',
   admin_notes: '',
   receipt_file: null
});

const filteredList = computed(() => {
   return list.value.filter(r => {
      const matchStatus = filterStatus.value === 'all' || r.status === filterStatus.value;
      const matchType = filterType.value === 'all' || r.type === filterType.value;
      return matchStatus && matchType;
   });
});

const pendingCount = computed(() => list.value.filter(r => r.status === 'pending').length);

const stats = computed(() => {
   const totalPaid = list.value.filter(r => r.status === 'paid').reduce((sum, r) => sum + Number(r.amount), 0);
   const totalPending = list.value.filter(r => r.status === 'pending').reduce((sum, r) => sum + Number(r.amount), 0);
   const totalRequested = list.value.reduce((sum, r) => sum + Number(r.amount), 0);
   return [
      { label: 'Total Paid Out', value: totalPaid.toLocaleString() },
      { label: 'Pending for Approval', value: totalPending.toLocaleString() },
      { label: 'Overall Claims', value: totalRequested.toLocaleString() },
   ];
});

async function load() {
   try {
      const res = await axios.get('/api/admin/payout-requests', { withCredentials: true });
      list.value = res.data;
   } catch (e) {
      notify.error('Failed to load withdrawal requests.');
   }
}

function getStatusClass(status) {
   switch (status) {
      case 'paid': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
      case 'pending': return 'bg-amber-50 text-amber-600 border-amber-100';
      case 'processing': return 'bg-indigo-50 text-indigo-600 border-indigo-100';
      case 'rejected': return 'bg-red-50 text-red-600 border-red-100';
      default: return 'bg-slate-50 text-slate-600 border-slate-100';
   }
}

function formatDate(d) {
   if (!d) return '';
   return new Date(d).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
}

function getAppRate(u) {
   const total = Number(u?.creator_collabs_count || 0);
   if (total === 0) return 0;
   return Math.round(((u?.creator_completed_count || 0) / total) * 100);
}

function getRejRate(u) {
   const totalC = Number(u?.creator_collabs_count || 0);
   const totalB = Number(u?.brand_collabs_count || 0);
   const total = totalC + totalB;
   if (total === 0) return 0;
   const rej = Number(u?.creator_rejected_count || 0) + Number(u?.brand_rejected_count || 0);
   return Math.round((rej / total) * 100);
}

function getRevRate(u) {
   const total = Number(u?.creator_collabs_count || 0);
   if (total === 0) return 0;
   return ((u?.creator_total_revisions || 0) / total).toFixed(1);
}

function openProcessModal(r) {
   selectedRequest.value = r;
   form.value.status = r.status === 'pending' ? 'paid' : r.status;
   form.value.admin_notes = r.admin_notes || '';
   form.value.receipt_file = null;
   showProcessModal.value = true;
}

function handleFile(e) {
   form.value.receipt_file = e.target.files[0];
}

async function submitProcess() {
   submitting.value = true;
   try {
      const fd = new FormData();
      fd.append('status', form.value.status);
      fd.append('admin_notes', form.value.admin_notes);
      if (form.value.receipt_file) {
         fd.append('receipt_file', form.value.receipt_file);
      }

      const res = await axios.post(`/api/admin/payout-requests/${selectedRequest.value.id}/process`, fd, {
         headers: { 'Content-Type': 'multipart/form-data' },
         withCredentials: true
      });

      notify.success('Request updated successfully.');
      showProcessModal.value = false;
      await load();
   } catch (e) {
      notify.error(e.response?.data?.message || 'Failed to update request.');
   } finally {
      submitting.value = false;
   }
}

onMounted(load);
</script>
