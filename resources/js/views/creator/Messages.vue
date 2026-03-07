<template>
  <div class="flex h-[calc(100vh-8rem)] min-h-[420px] flex-col rounded-2xl border border-[#e2e8f0] bg-white shadow-sm md:flex-row">
    <!-- Conversation list -->
    <aside class="w-full border-b border-[#e2e8f0] md:w-80 md:border-b-0 md:border-r">
      <div class="flex items-center justify-between border-b border-[#e2e8f0] p-4">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Messages</h2>
      </div>
      <div v-if="loadingList" class="flex justify-center p-6">
        <div class="h-8 w-8 animate-spin rounded-full border-2 border-[#10b981] border-t-transparent" />
      </div>
      <ul v-else class="max-h-[280px] overflow-y-auto md:max-h-none">
        <li
          v-for="conv in conversations"
          :key="conv.id"
          class="flex cursor-pointer items-center gap-3 border-b border-[#f1f5f9] px-4 py-3 transition hover:bg-[#f8fafc]"
          :class="{ 'bg-[#10b981]/10': selectedUserId === conv.id }"
          @click="selectUser(conv.id)"
        >
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#10b981]/20 text-sm font-semibold text-[#10b981]">
            {{ (conv.name || '?').charAt(0).toUpperCase() }}
          </div>
          <div class="min-w-0 flex-1">
            <p class="truncate font-medium text-[#1a1a1a]">{{ conv.name }}</p>
            <p class="truncate text-xs text-[#64748b]">{{ conv.lastMessage }}</p>
          </div>
          <span v-if="conv.unreadCount > 0" class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-[#10b981] px-1.5 text-xs font-medium text-white">{{ conv.unreadCount }}</span>
        </li>
        <li v-if="!conversations.length" class="p-6 text-center text-sm text-[#64748b]">No conversations yet. Apply to a campaign to chat with brands.</li>
      </ul>
    </aside>

    <!-- Thread -->
    <div class="flex flex-1 flex-col">
      <div v-if="!selectedUserId" class="flex flex-1 items-center justify-center p-6 text-[#64748b]">
        <p>Select a conversation or open chat from a campaign.</p>
      </div>
      <template v-else>
        <div class="flex items-center gap-3 border-b border-[#e2e8f0] px-4 py-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#10b981]/20 text-sm font-semibold text-[#10b981]">
            {{ (selectedUserName || '?').charAt(0).toUpperCase() }}
          </div>
          <p class="font-medium text-[#1a1a1a]">{{ selectedUserName || 'User' }}</p>
        </div>
        <div ref="threadEl" class="flex-1 overflow-y-auto p-4 space-y-3">
          <div v-if="loadingThread" class="flex justify-center py-8">
            <div class="h-8 w-8 animate-spin rounded-full border-2 border-[#10b981] border-t-transparent" />
          </div>
          <template v-else>
            <div
              v-for="msg in messages"
              :key="msg.id"
              class="flex"
              :class="msg.isMe ? 'justify-end' : 'justify-start'"
            >
              <div
                class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm"
                :class="msg.isMe ? 'bg-[#10b981] text-white' : 'bg-[#f1f5f9] text-[#1a1a1a]"
              >
                <p class="whitespace-pre-wrap">{{ msg.text }}</p>
                <p class="mt-1 text-xs opacity-80">{{ msg.time }}</p>
              </div>
            </div>
          </template>
        </div>
        <form class="flex gap-2 border-t border-[#e2e8f0] p-4" @submit.prevent="sendMessage">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Type a message..."
            class="flex-1 rounded-xl border border-[#e2e8f0] px-4 py-3 text-[#1a1a1a] placeholder-[#94a3b8] focus:border-[#10b981] focus:outline-none focus:ring-2 focus:ring-[#10b981]/20"
            maxlength="5000"
          />
          <button
            type="submit"
            :disabled="!newMessage.trim() || sending"
            class="rounded-xl bg-[#10b981] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#059669] disabled:opacity-50"
          >
            Send
          </button>
        </form>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const conversations = ref([]);
const loadingList = ref(true);
const selectedUserId = ref(null);
const selectedUserName = ref('');
const messages = ref([]);
const loadingThread = ref(false);
const newMessage = ref('');
const sending = ref(false);
const threadEl = ref(null);

async function loadConversations() {
  loadingList.value = true;
  try {
    const res = await axios.get('/api/messages', { withCredentials: true });
    conversations.value = res.data ?? [];
  } catch (_) {
    conversations.value = [];
  } finally {
    loadingList.value = false;
  }
}

function selectUser(userId) {
  selectedUserId.value = userId;
  const conv = conversations.value.find((c) => c.id === userId);
  selectedUserName.value = conv?.name ?? '';
  loadThread();
}

async function loadThread() {
  if (!selectedUserId.value) return;
  loadingThread.value = true;
  messages.value = [];
  try {
    const res = await axios.get('/api/messages/' + selectedUserId.value, { withCredentials: true });
    messages.value = res.data ?? [];
    nextTickScroll();
  } catch (_) {
    messages.value = [];
  } finally {
    loadingThread.value = false;
  }
}

function nextTickScroll() {
  setTimeout(() => {
    if (threadEl.value) threadEl.value.scrollTop = threadEl.value.scrollHeight;
  }, 50);
}

async function sendMessage() {
  const body = newMessage.value.trim();
  if (!body || !selectedUserId.value) return;
  sending.value = true;
  try {
    const res = await axios.post(
      '/api/messages',
      { receiver_id: selectedUserId.value, body },
      { withCredentials: true }
    );
    messages.value.push(res.data);
    newMessage.value = '';
    nextTickScroll();
    await loadConversations();
  } catch (_) {}
  finally {
    sending.value = false;
  }
}

onMounted(async () => {
  await loadConversations();
  const withUser = route.query.user || route.query.with;
  if (withUser) {
    const id = Number(withUser);
    if (id && !conversations.value.some((c) => c.id === id)) {
      selectedUserId.value = id;
      selectedUserName.value = route.query.name || 'Brand';
      loadThread();
    } else if (id) {
      selectUser(id);
    }
  }
});

watch(() => route.query.user, (v) => {
  if (v) selectUser(Number(v));
});
watch(() => route.query.with, (v) => {
  if (v) selectUser(Number(v));
});
</script>
