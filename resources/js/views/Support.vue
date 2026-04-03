<template>
  <div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#1a1a1a]">Help Desk & Disputes</h1>
            <p class="text-sm text-[#64748b]">Get help with your projects and manage active disputes.</p>
        </div>
        <button v-if="!showNewTicketForm" @click="showNewTicketForm = true" class="rounded-xl bg-[#1a1a1a] px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-black active:scale-95">
            + New Ticket
        </button>
    </div>

    <!-- New Ticket Form -->
    <div v-if="showNewTicketForm" class="mb-8 rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-[#1a1a1a]">Create New Support Ticket</h3>
            <button @click="showNewTicketForm = false" class="text-xs font-semibold text-[#64748b] hover:text-red-500">Cancel</button>
        </div>
        <form @submit.prevent="createTicket" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#64748b] mb-1.5">Subject</label>
                <input v-model="newTicket.subject" type="text" required placeholder="Briefly describe the issue" class="w-full rounded-xl border border-[#e2e8f0] p-3 text-sm focus:border-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#64748b] mb-1.5">Message</label>
                <textarea v-model="newTicket.message" rows="4" required placeholder="Explain your concern in detail..." class="w-full rounded-xl border border-[#e2e8f0] p-3 text-sm focus:border-blue-500 focus:outline-none"></textarea>
            </div>
            <button type="submit" :disabled="creating" class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 disabled:opacity-50">
                {{ creating ? 'Creating...' : 'Submit Support Ticket' }}
            </button>
        </form>
    </div>
    
    <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
    </div>
    
    <div v-else-if="!tickets.length && !showNewTicketForm" class="rounded-2xl border border-dashed border-[#e2e8f0] bg-white p-20 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-300 mb-4">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <p class="font-bold text-[#1a1a1a]">No active support tickets</p>
        <p class="mt-1 text-sm text-[#64748b]">If you have any issues with projects, our support team is here to help.</p>
        <button @click="showNewTicketForm = true" class="mt-6 rounded-xl border border-blue-600 px-6 py-2.5 text-sm font-bold text-blue-600 transition hover:bg-blue-50">Contact Support</button>
    </div>
    
    <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Ticket List -->
      <div class="lg:col-span-1">
        <div class="overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-sm sticky top-6">
          <div class="bg-[#f8fafc] px-5 py-4 border-b border-[#e2e8f0]">
            <span class="text-sm font-bold text-[#1a1a1a]">Your Tickets</span>
          </div>
          <div class="max-h-[600px] overflow-y-auto divide-y divide-[#f1f5f9]">
            <div 
              v-for="ticket in tickets" 
              :key="ticket.id" 
              class="cursor-pointer p-5 transition hover:bg-[#fafafa]"
              :class="{'bg-blue-50/50 border-l-4 border-blue-600': selectedTicket?.id === ticket.id}"
              @click="selectTicket(ticket)"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black tracking-widest text-[#64748b] uppercase">{{ ticket.ticket_id }}</span>
                <span :class="statusClass(ticket.status)" class="rounded-lg px-2 py-0.5 text-[10px] font-black uppercase tracking-wider">
                  {{ ticket.status.replace('_', ' ') }}
                </span>
              </div>
              <div class="text-sm font-bold text-[#1a1a1a] line-clamp-1 mb-1">{{ ticket.subject }}</div>
              <div class="flex items-center justify-between text-[11px] text-[#94a3b8]">
                <span class="flex items-center gap-1">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2-2h-5l-5 5v-5z" /></svg>
                    {{ ticket.messages_count }} replies
                </span>
                <span>{{ formatDate(ticket.updated_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Chat Interface -->
      <div class="lg:col-span-2">
        <div v-if="selectedTicket" class="flex flex-col h-[700px] rounded-2xl border border-[#e2e8f0] bg-white shadow-sm overflow-hidden">
          <div class="bg-white px-6 py-5 border-b border-[#e2e8f0] flex items-center justify-between shadow-sm z-10">
            <div>
              <div class="flex items-center gap-2 mb-0.5">
                  <h3 class="text-base font-black text-[#1a1a1a]">{{ selectedTicket.subject }}</h3>
                  <span v-if="selectedTicket.ticket_id.startsWith('DISP')" class="bg-red-100 text-red-600 text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest">Formal Dispute</span>
              </div>
              <p class="text-xs text-[#64748b]">Ticket ID: {{ selectedTicket.ticket_id }} • Last updated {{ formatDate(selectedTicket.updated_at) }}</p>
            </div>
            <div :class="statusClass(selectedTicket.status)" class="rounded-xl px-3 py-1.5 text-xs font-black uppercase tracking-widest">
                {{ selectedTicket.status.replace('_', ' ') }}
            </div>
          </div>

          <!-- Linked Collaboration Card -->
          <div v-if="selectedTicket.collaboration" class="px-6 py-4 bg-[#f1f5f9] border-b border-[#e2e8f0] flex items-center justify-between">
              <div class="flex items-center gap-4">
                  <div class="h-10 w-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm">
                      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                  </div>
                  <div>
                      <p class="text-[10px] font-black text-[#64748b] uppercase tracking-tighter">Disputed Collaboration</p>
                      <h4 class="text-xs font-bold text-slate-800">#{{ selectedTicket.collaboration.id }} — {{ selectedTicket.collaboration.package?.title || 'Custom Project' }}</h4>
                  </div>
              </div>
              <div class="flex items-center gap-3">
                  <div class="text-right mr-2">
                      <p class="text-[10px] font-bold text-[#64748b]">Project Price</p>
                      <p class="text-xs font-black text-slate-900">₹{{ selectedTicket.collaboration.amount }}</p>
                  </div>
                  <button 
                    v-if="selectedTicket.collaboration.deliverable_content" 
                    type="button"
                    @click="openCollaborationDeliverablePreview"
                    class="rounded-xl bg-white border border-[#cbd5e1] px-4 py-2 text-xs font-bold text-[#1e293b] shadow-sm hover:bg-[#1a1a1a] hover:text-white transition-all flex items-center gap-2"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    Secure Preview
                  </button>
              </div>
          </div>
          
          <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-[#f8fafc]" ref="messageBox">
            <div v-for="msg in messages" :key="msg.id" :class="[Number(msg.user_id) === Number(currentUserId) ? 'flex flex-col items-end' : 'flex flex-col items-start']">
              <div :class="[
                  Number(msg.user_id) === Number(currentUserId) ? 'bg-[#1a1a1a] text-white rounded-2xl rounded-tr-none' : 
                  msg.is_admin ? 'bg-amber-50 text-amber-900 border border-amber-200 rounded-2xl' :
                  'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-[#e2e8f0]'
                ]" 
                class="inline-block px-5 py-3 shadow-sm max-w-[85%] text-sm leading-relaxed">
                <p class="whitespace-pre-wrap">{{ msg.message }}</p>
              </div>
              <div class="flex items-center gap-2 mt-1.5 px-1">
                <span class="text-[10px] font-bold" :class="msg.is_admin ? 'text-amber-600' : 'text-[#64748b]'">
                  <span v-if="msg.is_admin">🛡️ StarJD Admin</span>
                  <span v-else-if="Number(msg.user_id) === Number(currentUserId)">You</span>
                  <span v-else>{{ msg.user?.name || 'Counterparty' }}</span>
                </span>
                <span class="text-[8px] text-slate-300">•</span>
                <span class="text-[10px] text-[#94a3b8]">{{ formatTime(msg.created_at) }}</span>
              </div>
            </div>
          </div>
          
          <div class="p-6 border-t border-[#e2e8f0] bg-white">
            <div v-if="selectedTicket.status === 'closed' || selectedTicket.status === 'resolved'" class="rounded-2xl bg-slate-50 p-6 text-center border-2 border-dashed border-slate-200">
                <div class="h-10 w-10 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <p class="text-[11px] font-black uppercase text-slate-500 tracking-widest mb-1">Notice: Ticket Archived</p>
                <p class="text-[10px] font-bold text-slate-400">This ticket has been marked as <b>{{ selectedTicket.status }}</b>. Only administrators can reopen closed cases.</p>
            </div>
            <div v-else class="flex flex-col gap-3">
              <div class="relative group">
                <textarea 
                  v-model="replyText" 
                  rows="3" 
                  placeholder="Write your message here..." 
                  class="w-full border-2 border-[#e2e8f0] rounded-2xl p-5 text-sm font-medium transition-all focus:ring-4 focus:ring-blue-100 focus:border-blue-500 placeholder:text-slate-300 resize-none outline-none group-hover:border-slate-300"
                  @keyup.enter.ctrl="sendReply"
                  :disabled="sending"
                ></textarea>
                <div v-if="sending" class="absolute inset-0 bg-white/60 backdrop-blur-[1px] rounded-2xl flex items-center justify-center">
                   <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest animate-pulse">Sending Message...</span>
                </div>
              </div>
              <div class="flex items-center justify-between">
                 <div class="flex items-center gap-1.5 text-[9px] font-bold text-slate-300">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Ctrl + Enter to send quickly
                 </div>
                 <button 
                  @click="sendReply" 
                  :disabled="!replyText.trim() || sending"
                  class="bg-[#3b82f6] text-white px-10 h-12 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50 transition-all shadow-xl shadow-blue-500/20 active:scale-95"
                >
                  Send
                </button>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="h-full flex flex-col items-center justify-center rounded-2xl border border-dashed border-[#e2e8f0] bg-white p-12 text-center">
          <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-300">
              <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
          </div>
          <h3 class="text-lg font-bold text-[#1a1a1a]">Select a Conversion</h3>
          <p class="text-sm text-[#64748b] mt-2 max-w-xs">Select a support ticket from the list or create a new one to chat with our mediation team.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { notify } from '../lib/notify.js';

const tickets = ref([]);
const selectedTicket = ref(null);
const messages = ref([]);
const loading = ref(true);
const sending = ref(false);
const creating = ref(false);
const replyText = ref('');
const messageBox = ref(null);
const showNewTicketForm = ref(false);
const currentUserId = ref(null);
const newTicket = ref({ subject: '', message: '' });

const route = useRoute();

let pollingInterval = null;

function formatDate(s) {
  if (!s) return '—';
  const d = new Date(s);
  return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
}

function formatTime(s) {
  if (!s) return '—';
  return new Date(s).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function statusClass(s) {
  if (s === 'open') return 'bg-blue-100 text-blue-700';
  if (s === 'in_progress') return 'bg-amber-100 text-amber-700';
  if (s === 'resolved') return 'bg-emerald-100 text-emerald-700 border border-emerald-200';
  return 'bg-slate-100 text-slate-600';
}

async function openCollaborationDeliverablePreview() {
  const collab = selectedTicket.value?.collaboration;
  if (!collab?.id) return;
  try {
    const res = await axios.get(`/api/collaborations/${collab.id}/file`, { withCredentials: true });
    if (res.data.ready === false) {
      notify.info(res.data.message || 'Preview is not ready yet.');
      return;
    }
    const token = res.data.preview_token;
    if (!token) {
      notify.error('Unable to open deliverable preview.');
      return;
    }
    const url = `${res.data.url}?preview_token=${encodeURIComponent(token)}`;
    window.open(url, '_blank', 'noopener,noreferrer');
  } catch (e) {
    notify.error(e.response?.data?.message || 'Unable to open deliverable preview.');
  }
}

async function loadTickets(background = false) {
  if (!background) loading.value = true;
  try {
    const r = await axios.get('/api/support/tickets', { withCredentials: true });
    tickets.value = r.data;
  } finally {
    if (!background) loading.value = false;
  }
}

async function selectTicket(ticket) {
  loading.value = true;
  try {
    const r = await axios.get(`/api/support/tickets/${ticket.id}`, { withCredentials: true });
    selectedTicket.value = r.data;
    messages.value = r.data.messages || [];
    showNewTicketForm.value = false;
    scrollToBottom();
  } catch(e) {
    notify.error('Failed to load ticket details');
  } finally {
    loading.value = false;
  }
}

async function loadMessages(background = false) {
  if (!selectedTicket.value) return;
  try {
    const r = await axios.get(`/api/support/tickets/${selectedTicket.value.id}`, { withCredentials: true });
    const newMessages = r.data.messages;
    
    if (newMessages.length !== messages.value.length) {
      messages.value = newMessages;
      scrollToBottom();
    }
    
    // Update local ticket status in list if changed
    const index = tickets.value.findIndex(t => t.id === selectedTicket.value.id);
    if (index !== -1) {
      tickets.value[index].status = r.data.status;
      tickets.value[index].messages_count = r.data.messages?.length || 0;
    }
    selectedTicket.value.status = r.data.status;
  } catch (e) {
    console.error('Error loading messages', e);
  }
}

async function createTicket() {
  if (creating.value) return;
  creating.value = true;
  try {
    await axios.post('/api/support/tickets', {
        subject: newTicket.value.subject,
        message: newTicket.value.message
    }, { withCredentials: true });
    notify.success('Support ticket created successfully.');
    newTicket.value = { subject: '', message: '' };
    showNewTicketForm.value = false;
    await loadTickets();
  } catch (e) {
    notify.error(e.response?.data?.message || 'Failed to create ticket.');
  } finally {
    creating.value = false;
  }
}

async function sendReply() {
  if (!replyText.value.trim() || sending.value) return;
  sending.value = true;
  try {
    const r = await axios.post(`/api/support/tickets/${selectedTicket.value.id}/messages`, {
      message: replyText.value
    }, { withCredentials: true });
    messages.value.push(r.data);
    replyText.value = '';
    scrollToBottom();
  } catch (e) {
    notify.error('Error sending reply');
  } finally {
    sending.value = false;
  }
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
  }, 10000); // 10 seconds for general users
}

onMounted(async () => {
  // Get current user ID for chat alignment
  try {
      const userRes = await axios.get('/api/me', { withCredentials: true });
      currentUserId.value = userRes.data.id;
  } catch(e) {
      console.error('Failed to fetch user for chat alignment', e);
  }
  
  await loadTickets();
  startPolling();

  // Handle direct link from collaborations (dispute chat)
  const collabId = route.query.collab_id;
  if (collabId && tickets.value.length) {
      const ticket = tickets.value.find(t => t.collaboration_id == collabId);
      if (ticket) {
          selectTicket(ticket);
      } else {
          notify.error('Dispute ticket not found or you are not authorized to view it.');
      }
  }
});

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
});
</script>
