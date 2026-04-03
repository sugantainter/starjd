<template>
  <div>
    <h1 class="mb-6 text-2xl font-bold text-[#1a1a1a]">Help Desk Support</h1>
    
    <div v-if="loading" class="text-[#64748b]">Loading tickets…</div>
    
    <div v-else-if="!tickets.length" class="rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]">
      No support tickets found.
    </div>
    
    <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Ticket List -->
      <div class="lg:col-span-1">
        <div class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
          <div class="bg-[#f8fafc] px-4 py-3 border-bottom border-[#e2e8f0]">
            <span class="text-sm font-semibold text-[#64748b]">Tickets</span>
          </div>
          <div class="max-h-[600px] overflow-y-auto divide-y divide-[#e2e8f0]">
            <div 
              v-for="ticket in tickets" 
              :key="ticket.id" 
              class="cursor-pointer p-4 hover:bg-[#f8fafc]"
              :class="{'bg-[#f1f5f9]': selectedTicket?.id === ticket.id}"
              @click="selectTicket(ticket)"
            >
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-[#64748b]">{{ ticket.ticket_id }}</span>
                <span :class="statusClass(ticket.status)" class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase transition-colors">
                  {{ ticket.status }}
                </span>
              </div>
              <div class="text-sm font-semibold truncate text-[#1a1a1a]">{{ ticket.subject }}</div>
              <div class="mt-1 flex items-center justify-between text-[11px] text-[#64748b]">
                <span>{{ ticket.user?.name }}</span>
                <span>{{ formatDate(ticket.updated_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Chat Interface -->
      <div class="lg:col-span-2">
        <div v-if="selectedTicket" class="flex flex-col h-[600px] rounded-xl border border-[#e2e8f0] bg-white shadow-sm overflow-hidden">
          <div class="bg-[#f8fafc] px-4 py-3 border-bottom border-[#e2e8f0] flex items-center justify-between">
            <div>
              <h3 class="text-sm font-bold text-[#1a1a1a]">{{ selectedTicket.subject }}</h3>
              <p class="text-xs text-[#64748b]">User: {{ selectedTicket.user?.name }} ({{ selectedTicket.user?.email }})</p>
            </div>
            <select v-model="selectedTicket.status" @change="updateStatus(selectedTicket)" class="text-xs border-[#e2e8f0] rounded">
              <option value="open">Open</option>
              <option value="in_progress">In Progress</option>
              <option value="resolved">Resolved</option>
              <option value="closed">Closed</option>
            </select>
          </div>

          <!-- Collaboration Details & Preview -->
          <div v-if="selectedTicket.collaboration" class="px-5 py-4 bg-[#f1f5f9] border-b border-[#e2e8f0]">
              <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center gap-3">
                      <div class="h-10 w-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm border border-[#e2e8f0]">
                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                      </div>
                      <div>
                          <p class="text-[10px] font-black text-[#64748b] uppercase tracking-tighter">Disputed Project Details</p>
                          <h4 class="text-xs font-bold text-slate-800">#{{ selectedTicket.collaboration.id }} — {{ selectedTicket.collaboration.package?.title || 'Custom Campaign' }}</h4>
                      </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-[#64748b] uppercase">Total Price</p>
                        <p class="text-xs font-black text-blue-600">₹{{ selectedTicket.collaboration.amount }}</p>
                    </div>
                    <button v-if="selectedTicket.collaboration.delivery_file" @click="openSecurePreview(selectedTicket.collaboration)" class="rounded-lg bg-white border border-[#cbd5e1] px-4 py-2 text-xs font-bold text-[#1e293b] shadow-sm hover:bg-black hover:text-white transition-all flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        Secure Preview
                    </button>
                  </div>
              </div>
              
              <!-- Participant Info -->
              <div class="grid grid-cols-2 gap-4">
                  <div class="rounded-lg bg-white p-3 border border-[#e2e8f0]">
                      <div class="flex items-center justify-between mb-1">
                          <p class="text-[10px] font-bold text-[#94a3b8] uppercase">Creator</p>
                          <div class="flex gap-3 scale-90 origin-right">
                              <!-- Approval Circle -->
                              <div class="relative h-8 w-8 flex items-center justify-center" :title="'Approval Rate: ' + getAppRate(selectedTicket.collaboration.creator) + '%'">
                                 <svg class="h-full w-full -rotate-90">
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#f1f5f9" stroke-width="3" />
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#10b981" stroke-width="3" stroke-linecap="round" :stroke-dasharray="88" :stroke-dashoffset="88 - (88 * getAppRate(selectedTicket.collaboration.creator) / 100)" />
                                 </svg>
                                 <span class="absolute text-[7px] font-black text-emerald-700">{{ getAppRate(selectedTicket.collaboration.creator) }}%</span>
                              </div>
                              <!-- Rejection Circle -->
                              <div class="relative h-8 w-8 flex items-center justify-center" :title="'Rejection Rate: ' + getRejRate(selectedTicket.collaboration.creator) + '%'">
                                 <svg class="h-full w-full -rotate-90">
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#f1f5f9" stroke-width="3" />
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#ef4444" stroke-width="3" stroke-linecap="round" :stroke-dasharray="88" :stroke-dashoffset="88 - (88 * getRejRate(selectedTicket.collaboration.creator) / 100)" />
                                 </svg>
                                 <span class="absolute text-[7px] font-black text-red-700">{{ getRejRate(selectedTicket.collaboration.creator) }}%</span>
                              </div>
                              <!-- Revision Indicator (Circle with value) -->
                              <div class="h-8 w-8 rounded-full bg-purple-50 border border-purple-100 flex flex-col items-center justify-center leading-none" :title="'Avg Revisions: ' + getRevRate(selectedTicket.collaboration.creator)">
                                 <span class="text-[9px] font-black text-purple-700">{{ getRevRate(selectedTicket.collaboration.creator) }}</span>
                                 <span class="text-[5px] font-bold text-purple-400 uppercase">Rev</span>
                              </div>
                          </div>
                      </div>
                      <p class="text-xs font-bold text-slate-700">{{ selectedTicket.collaboration.creator?.name }}</p>
                  </div>
                  <div class="rounded-lg bg-white p-3 border border-[#e2e8f0]">
                      <div class="flex items-center justify-between mb-1">
                          <p class="text-[10px] font-bold text-[#94a3b8] uppercase">Brand</p>
                          <div class="flex gap-3 scale-90 origin-right">
                              <!-- Rejection Circle -->
                              <div class="relative h-8 w-8 flex items-center justify-center" :title="'Rejection Rate: ' + getRejRate(selectedTicket.collaboration.brand) + '%'">
                                 <svg class="h-full w-full -rotate-90">
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#f1f5f9" stroke-width="3" />
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#ef4444" stroke-width="3" stroke-linecap="round" :stroke-dasharray="88" :stroke-dashoffset="88 - (88 * getRejRate(selectedTicket.collaboration.brand) / 100)" />
                                 </svg>
                                 <span class="absolute text-[7px] font-black text-red-700">{{ getRejRate(selectedTicket.collaboration.brand) }}%</span>
                              </div>
                          </div>
                      </div>
                      <p class="text-xs font-bold text-slate-700">{{ selectedTicket.collaboration.brand?.name }}</p>
                  </div>
              </div>
          </div>
          
          <!-- Dispute Settlement Form (Only if disputed) -->
          <div v-if="selectedTicket.collaboration && selectedTicket.status !== 'resolved'" class="px-5 py-4 bg-amber-50 border-b border-amber-200">
              <h5 class="text-[10px] font-black text-amber-800 uppercase tracking-widest mb-3 flex items-center gap-1">
                  <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                  Mediation Settlement (Split Funds)
              </h5>
              <div class="flex items-center gap-4">
                  <div class="flex-1">
                      <label class="block text-[9px] font-black uppercase text-amber-700 mb-1">Refund to Brand (₹)</label>
                      <input v-model="settlement.brand" type="number" step="0.01" :max="selectedTicket.collaboration.amount" class="w-full rounded-lg border-amber-200 bg-white p-2 text-xs font-bold focus:ring-amber-500" placeholder="0.00" />
                  </div>
                  <div class="flex-1">
                      <label class="block text-[9px] font-black uppercase text-amber-700 mb-1">Payment to Creator (₹)</label>
                      <input v-model="settlement.creator" type="number" step="0.01" :max="selectedTicket.collaboration.amount" class="w-full rounded-lg border-amber-200 bg-white p-2 text-xs font-bold focus:ring-amber-500" placeholder="0.00" />
                  </div>
                  <button @click="settleDispute" :disabled="settling" class="h-[34px] self-end px-6 rounded-lg bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-amber-700 transition-all shadow-md shadow-amber-600/20 disabled:opacity-50">
                      {{ settling ? 'Settling...' : 'Settle Dispute' }}
                  </button>
              </div>
              <p class="mt-2 text-[9px] font-bold text-amber-600 flex items-center gap-1">
                  <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                  Project Total: ₹{{ selectedTicket.collaboration.amount }}. Platform fee is already deducted from the total.
              </p>
          </div>
          
          <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" ref="messageBox">
            <div v-for="msg in messages" :key="msg.id" :class="[msg.is_admin ? 'text-right' : 'text-left']">
              <div :class="[msg.is_admin ? 'bg-blue-600 text-white ml-auto' : 'bg-white text-gray-800 mr-auto']" 
                class="inline-block px-4 py-2 rounded-lg shadow-sm max-w-[80%] text-sm">
                {{ msg.message }}
              </div>
              <div class="text-[10px] text-gray-500 mt-1">
                {{ msg.is_admin ? 'Admin' : selectedTicket.user.name }} • {{ formatTime(msg.created_at) }}
              </div>
            </div>
          </div>
          
          <div class="p-4 border-t border-[#e2e8f0] bg-white">
            <div class="flex space-x-2">
              <textarea 
                v-model="replyText" 
                rows="1" 
                placeholder="Type your reply..." 
                class="flex-1 border-[#e2e8f0] rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                @keyup.enter.ctrl="sendReply"
              ></textarea>
              <button 
                @click="sendReply" 
                :disabled="!replyText.trim() || sending"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 disabled:opacity-50"
              >
                {{ sending ? 'Sending…' : 'Reply' }}
              </button>
            </div>
          </div>
        </div>
        <div v-else class="h-full flex items-center justify-center rounded-xl border border-dashed border-[#e2e8f0] bg-white text-[#64748b]">
          Select a ticket to view conversation
        </div>
      </div>
    </div>

    <!-- Secure Preview Modal -->
    <div v-if="showPreviewModal" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-slate-900/95 backdrop-blur-xl p-4 sm:p-10" @contextmenu.prevent>
      <!-- Modal Header -->
      <div class="w-full max-w-6xl flex items-center justify-between mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
        <div class="flex items-center gap-4">
          <div class="h-12 w-12 rounded-2xl bg-[#fc4402] flex items-center justify-center text-white shadow-lg shadow-[#fc4402]/30">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
          </div>
          <div>
            <h2 class="text-xl font-black text-white tracking-tight leading-none">Admin Secure Preview</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Reviewing Disputed Deliverable</p>
          </div>
        </div>
        <button @click="showPreviewModal = false" class="h-12 w-12 rounded-2xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all active:scale-95 group">
           <svg class="h-6 w-6 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>

      <!-- Viewer Container -->
      <div class="relative w-full max-w-6xl flex-1 bg-black rounded-[40px] overflow-hidden shadow-2xl border border-white/5 animate-in zoom-in-95 duration-500 select-none">
        <!-- Watermark Overlay -->
        <div class="absolute inset-0 z-50 pointer-events-none opacity-20 flex flex-wrap content-around justify-around gap-20 overflow-hidden">
           <div v-for="i in 12" :key="i" class="text-white text-3xl font-black rotate-[-30deg] whitespace-nowrap uppercase tracking-[0.5em] select-none">
             ADMIN PREVIEW • CONFIDENTIAL
           </div>
        </div>

        <div class="absolute inset-0 flex items-center justify-center p-4 text-white">
           <img v-if="previewType === 'image'" :src="previewUrl" class="max-w-full max-h-full object-contain" @contextmenu.prevent />
           <SecureVideoPlayer v-else-if="previewType === 'video'" :src="previewUrl" video-class="max-h-full max-w-full" wrapper-class="max-w-full max-h-full" />
           <iframe v-else-if="previewType === 'pdf'" :src="previewUrl + '#toolbar=0'" class="w-full h-full border-none" @contextmenu.prevent></iframe>
           <div v-else class="text-center p-12">
               <p class="text-lg font-bold">This file type must be downloaded to review.</p>
               <a :href="previewUrl" target="_blank" class="mt-4 inline-block bg-white text-black px-6 py-2 rounded-lg font-bold">Download File</a>
           </div>
        </div>
      </div>
    </div>

    <!-- Settlement Confirmation Modal -->
    <AdminConfirmModal 
      :open="showSettleConfirm"
      title="Mediation Settlement Confirmation"
      :message="`Are you sure you want to finalize this case? The funds will be distributed as follow: ₹${settlement.brand} as Refund to Brand and ₹${settlement.creator} as Payment to Creator. This action cannot be undone.`"
      confirmLabel="Settle & Close Case"
      @close="showSettleConfirm = false"
      @confirm="executeSettlement"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import { notify } from '../../lib/notify.js';
import AdminConfirmModal from '../../components/admin/AdminConfirmModal.vue';
import SecureVideoPlayer from '../../components/common/SecureVideoPlayer.vue';

const tickets = ref([]);
const selectedTicket = ref(null);
const messages = ref([]);
const loading = ref(true);
const sending = ref(false);
const replyText = ref('');
const settlement = ref({ brand: 0, creator: 0 });
const settling = ref(false);
const showSettleConfirm = ref(false);
const showPreviewModal = ref(false);
const previewUrl = ref('');
const previewType = ref('');
let pollingInterval = null;

function openSecurePreview(c) {
    const fileUrl = `/api/collaborations/${c.id}/file/stream`;
    const fileName = (c.deliverable_content || '').toLowerCase();
    
    if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png') || fileName.endsWith('.webp')) {
      previewType.value = 'image';
    } else if (fileName.endsWith('.mp4') || fileName.endsWith('.webm') || fileName.endsWith('.mov')) {
      previewType.value = 'video';
    } else if (fileName.endsWith('.pdf')) {
      previewType.value = 'pdf';
    } else {
      previewType.value = 'other';
    }
    
    previewUrl.value = fileUrl;
    showPreviewModal.value = true;
}

function formatDate(s) {
  if (!s) return '—';
  return new Date(s).toLocaleDateString();
}

function getAppRate(u) {
   if (!u) return 0;
   const total = Number(u.collabs_count || 0);
   if (total === 0) return 0;
   return Math.round(((u.completed_count || 0) / total) * 100);
}

function getRejRate(u, type) {
   if (!u) return 0;
   const total = Number(u.collabs_count || 0);
   if (total === 0) return 0;
   return Math.round(((u.rejected_count || 0) / total) * 100);
}

function getRevRate(u) {
   if (!u) return 0;
   const total = Number(u.collabs_count || 0);
   if (total === 0) return 0;
   return ((u.total_revisions || 0) / total).toFixed(1);
}

function formatTime(s) {
  if (!s) return '—';
  return new Date(s).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function statusClass(s) {
  if (s === 'open') return 'bg-blue-100 text-blue-700';
  if (s === 'in_progress') return 'bg-orange-100 text-orange-700';
  if (s === 'resolved') return 'bg-green-100 text-green-700';
  return 'bg-gray-100 text-gray-700';
}

async function loadTickets(background = false) {
  if (!background) loading.value = true;
  try {
    const r = await axios.get('/api/admin/support/tickets');
    tickets.value = r.data;
  } finally {
    if (!background) loading.value = false;
  }
}

async function selectTicket(ticket) {
  loading.value = true;
  try {
    const r = await axios.get(`/api/admin/support/tickets/${ticket.id}`);
    selectedTicket.value = r.data;
    messages.value = r.data.messages || [];
    settlement.value = { 
        brand: r.data.collaboration?.resolved_refund_amount || 0, 
        creator: r.data.collaboration?.resolved_creator_amount || r.data.collaboration?.amount || 0 
    };
    scrollToBottom();
  } finally {
    loading.value = false;
  }
}

async function loadMessages(background = false) {
  if (!selectedTicket.value) return;
  try {
    const r = await axios.get(`/api/admin/support/tickets/${selectedTicket.value.id}`);
    const newMessages = r.data.messages;
    
    if (newMessages.length !== messages.value.length) {
      messages.value = newMessages;
      scrollToBottom();
    }
    
    // Update local ticket status in list if changed
    const index = tickets.value.findIndex(t => t.id === selectedTicket.value.id);
    if (index !== -1) {
      tickets.value[index].status = r.data.status;
    }
  } catch (e) {
    console.error('Error loading messages', e);
  }
}

async function updateStatus(ticket) {
  try {
    await axios.patch(`/api/admin/support/tickets/${ticket.id}/status`, { status: ticket.status });
  } catch (e) {
    notify.error('Error updating status');
  }
}

async function sendReply() {
  if (!replyText.value.trim() || sending.value) return;
  sending.value = true;
  try {
    const r = await axios.post(`/api/admin/support/tickets/${selectedTicket.value.id}/reply`, {
      message: replyText.value,
      status: selectedTicket.value.status
    });
    messages.value.push(r.data);
    replyText.value = '';
    scrollToBottom();
    loadTickets(true);
  } catch (e) {
    notify.error('Error sending reply');
  } finally {
    sending.value = false;
  }
}

async function settleDispute() {
    const total = Number(selectedTicket.value.collaboration.amount);
    const splitTotal = roundToTwo(Number(settlement.value.brand) + Number(settlement.value.creator));
    
    if (splitTotal > total) {
        notify.error(`Total distribution (₹${splitTotal}) cannot exceed project price (₹${total})`);
        return;
    }

    showSettleConfirm.value = true;
}

async function executeSettlement() {
    showSettleConfirm.value = false;
    settling.value = true;
    try {
        await axios.post(`/api/admin/support/tickets/${selectedTicket.value.id}/settle`, {
            refund_brand: settlement.value.brand,
            payout_creator: settlement.value.creator
        });
        notify.success('Dispute settled successfully.');
        selectTicket(selectedTicket.value);
        loadTickets(true);
    } catch (e) {
        notify.error(e.response?.data?.message || 'Error settling dispute');
    } finally {
        settling.value = false;
    }
}

function roundToTwo(num) {
    return +(Math.round(num + "e+2")  + "e-2");
}

function scrollToBottom() {
  nextTick(() => {
    if (messageBox.value) {
      messageBox.value.scrollTop = messageBox.value.scrollHeight;
    }
  });
}

function startPolling() {
  pollingInterval = setInterval(() => {
    loadTickets(true);
    if (selectedTicket.value) {
      loadMessages(true);
    }
  }, 5000);
}

onMounted(() => {
  loadTickets();
  startPolling();
});

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
});
</script>
