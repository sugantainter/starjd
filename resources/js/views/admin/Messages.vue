<template>
  <div class="flex flex-col h-[calc(100vh-120px)] bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden shadow-sm">
    <div class="flex flex-1 overflow-hidden">
      <!-- Sidebar: Conversation List -->
      <aside class="w-80 border-r border-[#e2e8f0] flex flex-col bg-[#f8fafc]">
        <div class="p-4 border-b border-[#e2e8f0] bg-white">
          <h2 class="text-xl font-bold text-[#1a1a1a] mb-4">Direct Messages</h2>
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search conversations..." 
              class="w-full pl-9 pr-4 py-2 bg-[#f1f5f9] border-none rounded-xl text-sm focus:ring-2 focus:ring-[#e63946]/20 transition-all"
            />
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
          <div v-if="loadingList" class="flex flex-col items-center justify-center p-12 space-y-4">
            <div class="h-8 w-8 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent"></div>
            <p class="text-xs font-medium text-[#64748b] animate-pulse">Loading inbox...</p>
          </div>

          <div v-else-if="filteredConversations.length === 0" class="flex flex-col items-center justify-center p-12 text-center">
            <div class="h-12 w-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-[#e2e8f0]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-[#1a1a1a]">No messages yet</p>
            <p class="text-xs text-[#64748b] mt-1 pr-6 pl-6">Conversations with users will appear here.</p>
          </div>

          <div v-else>
            <button
              v-for="conv in filteredConversations"
              :key="conv.id"
              @click="selectUser(conv)"
              class="w-full flex items-center gap-3 p-4 border-b border-[#e2e8f0] transition-all hover:bg-white text-left group"
              :class="selectedUser?.id === conv.id ? 'bg-white !border-l-4 !border-l-[#e63946]' : 'bg-transparent border-l-4 border-l-transparent'"
            >
              <div class="relative flex-shrink-0">
                <div class="h-12 w-12 rounded-full overflow-hidden bg-white border border-[#e2e8f0] shadow-sm group-hover:shadow-md transition-shadow">
                  <img v-if="conv.avatar" :src="conv.avatar" :alt="conv.name" class="h-full w-full object-cover" />
                  <div v-else class="h-full w-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500 font-bold">
                    {{ conv.name?.[0] }}
                  </div>
                </div>
                <div v-if="conv.unreadCount > 0" class="absolute -top-1 -right-1 h-5 w-5 bg-[#e63946] text-white text-[10px] font-black rounded-full flex items-center justify-center ring-2 ring-white">
                  {{ conv.unreadCount }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-0.5">
                  <h3 class="text-sm font-bold text-[#1a1a1a] truncate" :class="conv.unreadCount > 0 ? 'font-black' : ''">
                    {{ conv.name }}
                  </h3>
                  <span class="text-[10px] text-[#94a3b8] whitespace-nowrap">{{ conv.time }}</span>
                </div>
                <p class="text-xs text-[#64748b] truncate leading-tight" :class="conv.unreadCount > 0 ? 'text-[#1a1a1a] font-medium' : ''">
                  {{ conv.lastMessage }}
                </p>
                <div v-if="conv.isBrand" class="mt-1">
                  <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-600 uppercase tracking-wider">Brand</span>
                </div>
              </div>
            </button>
          </div>
        </div>
      </aside>

      <!-- Main: Chat Interface -->
      <main class="flex-1 flex flex-col bg-white relative">
        <template v-if="selectedUser">
          <!-- Chat Header -->
          <header class="h-16 flex items-center justify-between px-6 border-b border-[#e2e8f0] z-10 bg-white/80 backdrop-blur-md sticky top-0">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-full overflow-hidden border border-[#e2e8f0]">
                <img v-if="selectedUser.avatar" :src="selectedUser.avatar" :alt="selectedUser.name" class="h-full w-full object-cover" />
                <div v-else class="h-full w-full flex items-center justify-center bg-slate-100 text-slate-500 font-bold text-xs">
                  {{ selectedUser.name?.[0] }}
                </div>
              </div>
              <div>
                <h3 class="text-sm font-bold text-[#1a1a1a]">{{ selectedUser.name }}</h3>
                <p class="text-[10px] text-emerald-500 font-bold flex items-center gap-1 uppercase tracking-tighter">
                  <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></span> Active Conversation
                </p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button 
                @click="$router.push(`/admin/users/${selectedUser.id}`)"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#64748b] hover:bg-[#f1f5f9] transition-colors"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                View Profile
              </button>
            </div>
          </header>

          <!-- Messages Area -->
          <div 
            ref="messageBox"
            class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#fafafa] custom-scrollbar"
          >
            <div v-if="loadingThread" class="flex justify-center items-center h-full">
               <div class="h-8 w-8 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent"></div>
            </div>
            
            <template v-else>
              <div v-for="(msg, index) in messages" :key="msg.id" class="flex flex-col" :class="msg.isMe ? 'items-end' : 'items-start'">
                <!-- Date separator placeholder (can be improved) -->
                <div v-if="shouldShowDate(index)" class="w-full flex justify-center my-6">
                  <span class="px-3 py-1 bg-white border border-[#e2e8f0] rounded-full text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest shadow-sm">
                    {{ formatDateHeader(msg.time) }}
                  </span>
                </div>

                <div 
                  class="max-w-[70%] px-4 py-2.5 rounded-2xl text-sm shadow-sm transition-all hover:shadow-md"
                  :class="msg.isMe ? 'bg-[#e63946] text-white rounded-tr-none' : 'bg-white text-[#1a1a1a] border border-[#e2e8f0] rounded-tl-none'"
                >
                  <p class="leading-relaxed whitespace-pre-wrap">{{ msg.text }}</p>
                </div>
                <span class="mt-1.5 text-[10px] text-[#94a3b8] px-1 font-medium">{{ msg.time }}</span>
              </div>

              <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center p-12 opacity-40">
                <div class="h-20 w-20 bg-white rounded-full flex items-center justify-center mb-4 shadow-inner border border-dashed border-[#cbd5e1]">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                  </svg>
                </div>
                <p class="text-sm font-bold">No messages here yet</p>
              </div>
            </template>
          </div>

          <!-- Message Input -->
          <footer class="p-4 bg-white border-t border-[#e2e8f0]">
            <form @submit.prevent="sendMessage" class="flex items-end gap-3 max-w-5xl mx-auto relative">
              <div class="flex-1 relative group">
                <textarea 
                  v-model="newMessage" 
                  rows="1" 
                  @input="adjustTextareaRows"
                  @keydown.enter.exact.prevent="sendMessage"
                  ref="textarea"
                  placeholder="Draft a professional response..." 
                  class="w-full bg-[#f8fafc] border-[#e2e8f0] rounded-2xl text-sm focus:ring-2 focus:ring-[#e63946]/10 focus:border-[#e63946] focus:bg-white transition-all py-3 px-4 pr-12 min-h-[48px] max-h-32 resize-none overflow-y-auto custom-scrollbar"
                ></textarea>
                <div class="absolute right-3 bottom-3 flex items-center gap-2">
                   <button type="button" class="text-[#94a3b8] hover:text-[#e63946] transition-colors p-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                   </button>
                </div>
              </div>
              <button 
                type="submit"
                :disabled="!newMessage.trim() || sending"
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#e63946] text-white shadow-lg shadow-[#e63946]/20 transition-all hover:bg-[#d62839] hover:shadow-xl hover:-translate-y-0.5 disabled:opacity-50 disabled:translate-y-0 disabled:shadow-none"
              >
                <svg v-if="!sending" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-90" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
                <div v-else class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
              </button>
            </form>
            <div class="mt-2 flex justify-center gap-4 text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">
              <span>Press <strong class="text-[#e63946]">Enter</strong> to send</span>
              <span class="h-1 w-1 bg-[#e2e8f0] rounded-full self-center"></span>
              <span>Professional Sync Active</span>
            </div>
          </footer>
        </template>

        <!-- Empty State (No selection) -->
        <div v-else class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-[#fafafa]">
          <div class="h-24 w-24 bg-white rounded-full flex items-center justify-center mb-6 shadow-xl border border-[#f1f5f9]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#e63946]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994-1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
            </svg>
          </div>
          <h2 class="text-2xl font-black text-[#1a1a1a]">Select a conversation</h2>
          <p class="text-[#64748b] text-sm mt-2 max-w-sm">Choose a user from the list on the left to start handling messages professionally.</p>
          <div class="mt-8 flex gap-3">
             <div class="bg-white px-4 py-2 rounded-xl border border-[#e2e8f0] shadow-sm flex items-center gap-2">
                <span class="h-2 w-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold text-[#1a1a1a]">Real-time Updates</span>
             </div>
             <div class="bg-white px-4 py-2 rounded-xl border border-[#e2e8f0] shadow-sm flex items-center gap-2">
                <span class="h-2 w-2 bg-[#e63946] rounded-full"></span>
                <span class="text-xs font-bold text-[#1a1a1a]">Priority Inbox</span>
             </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';

const route = useRoute();
const conversations = ref([]);
const selectedUser = ref(null);
const messages = ref([]);
const loadingList = ref(true);
const loadingThread = ref(false);
const newMessage = ref('');
const sending = ref(false);
const searchQuery = ref('');
const messageBox = ref(null);
const textarea = ref(null);

let pollInterval = null;
let threadPollInterval = null;

// Filtered conversations based on search
const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value;
  const query = searchQuery.value.toLowerCase();
  return conversations.value.filter(c => 
    c.name.toLowerCase().includes(query) || 
    c.lastMessage.toLowerCase().includes(query)
  );
});

// Load all own conversations
const loadConversations = async (background = false) => {
  if (!background) loadingList.value = true;
  try {
    const res = await axios.get('/api/conversations');
    conversations.value = res.data;
  } catch (err) {
    console.error('Error loading conversations:', err);
  } finally {
    if (!background) loadingList.value = false;
  }
};

// Select a user and load thread
const selectUser = async (user) => {
  selectedUser.value = user;
  loadingThread.value = true;
  await loadThread();
  loadingThread.value = false;
  scrollToBottom();
  
  // Mark as read locally if there were unread count
  const index = conversations.value.findIndex(c => c.id === user.id);
  if (index !== -1) conversations.value[index].unreadCount = 0;

  // Restart thread polling for this user
  startThreadPolling();
};

// Load messages for the selected user
const loadThread = async (background = false) => {
  if (!selectedUser.value) return;
  try {
    const res = await axios.get(`/api/messages/${selectedUser.value.id}`);
    const newMessages = res.data;
    
    // Only scroll if message count changed
    const countChanged = newMessages.length !== messages.value.length;
    messages.value = newMessages;
    
    if (countChanged) {
      scrollToBottom();
    }
  } catch (err) {
    console.error('Error loading thread:', err);
  }
};

// Send a message
const sendMessage = async () => {
  const body = newMessage.value.trim();
  if (!body || !selectedUser.value || sending.value) return;

  sending.value = true;
  try {
    const res = await axios.post('/api/messages', {
      receiver_id: selectedUser.value.id,
      body: body
    });
    
    messages.value.push(res.data);
    newMessage.value = '';
    if (textarea.value) textarea.value.style.height = 'auto';
    
    scrollToBottom();
    loadConversations(true); // Update last message in sidebar
  } catch (err) {
    alert('Failed to send message.');
  } finally {
    sending.value = false;
  }
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messageBox.value) {
      messageBox.value.scrollTop = messageBox.value.scrollHeight;
    }
  });
};

const adjustTextareaRows = () => {
  if (textarea.value) {
    textarea.value.style.height = 'auto';
    textarea.value.style.height = (textarea.value.scrollHeight) + 'px';
  }
};

const formatDateHeader = (timeStr) => {
  // Simple check for today/yesterday or date
  // This depends on the format returned by the API (h:i A)
  // Since the API only returns time, we'd need more data for real date headers
  // For now, let's just return a placeholder or hide it
  return "Today"; 
};

const shouldShowDate = (index) => {
  if (index === 0) return true;
  // Logic to show date only when day changes could be added here
  return false;
};

const startGeneralPolling = () => {
  pollInterval = setInterval(() => loadConversations(true), 10000);
};

const startThreadPolling = () => {
  if (threadPollInterval) clearInterval(threadPollInterval);
  threadPollInterval = setInterval(() => loadThread(true), 5000);
};

onMounted(() => {
  loadConversations();
  startGeneralPolling();

  // If coming from User Detail with a user ID
  if (route.query.user) {
    const id = Number(route.query.user);
    // Wait for list to load then try to select
    watch(loadingList, (val) => {
      if (!val) {
        const found = conversations.value.find(c => c.id === id);
        if (found) selectUser(found);
        else {
           // If not in recent list, we might need a fallback to fetch basic user info
           // but for simplicity, we'll assume they are there or we'd need a separate fetch
        }
      }
    }, { immediate: true });
  }
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
  if (threadPollInterval) clearInterval(threadPollInterval);
});

// Watch route query for changes (e.g. if navigating from another detail page)
watch(() => route.query.user, (newId) => {
  if (newId) {
    const found = conversations.value.find(c => c.id === Number(newId));
    if (found) selectUser(found);
  }
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}

[contenteditable]:empty:before {
  content: attr(placeholder);
  color: #94a3b8;
}
</style>
