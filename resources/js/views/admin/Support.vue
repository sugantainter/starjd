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
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';

const tickets = ref([]);
const selectedTicket = ref(null);
const messages = ref([]);
const loading = ref(true);
const sending = ref(false);
const replyText = ref('');
const messageBox = ref(null);

function formatDate(s) {
  if (!s) return '—';
  return new Date(s).toLocaleDateString();
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

async function loadTickets() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/support/tickets');
    tickets.value = r.data;
  } finally {
    loading.value = false;
  }
}

async function selectTicket(ticket) {
  selectedTicket.value = ticket;
  try {
    const r = await axios.get(`/api/admin/support/tickets/${ticket.id}`);
    messages.value = r.data.messages;
    scrollToBottom();
  } catch (e) {
    alert('Error loading messages');
  }
}

async function updateStatus(ticket) {
  try {
    await axios.patch(`/api/admin/support/tickets/${ticket.id}/status`, { status: ticket.status });
  } catch (e) {
    alert('Error updating status');
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
  } catch (e) {
    alert('Error sending reply');
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

onMounted(loadTickets);
</script>
