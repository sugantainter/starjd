<template>
  <div class="space-y-8 pb-12">
    <header class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-[#1a1a1a]">AI Usage Monitor</h1>
        <p class="mt-1 text-sm text-[#64748b]">Track and analyze AI token usage across the platform</p>
      </div>
      <button 
        @click="fetchData" 
        :disabled="loading"
        class="flex items-center gap-2 px-4 py-2 bg-white border border-[#e2e8f0] rounded-lg text-sm font-bold text-[#1a1a1a] hover:bg-[#f8fafc] transition-all disabled:opacity-50"
      >
        <svg :class="{'animate-spin': loading}" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Refresh Data
      </button>
    </header>

    <!-- Global Stats -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div class="bg-white p-6 rounded-2xl border border-[#e2e8f0] shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <span class="text-xs font-bold text-[#64748b] bg-[#f1f5f9] px-2 py-1 rounded">GLOBAL TOTAL</span>
          <div class="w-8 h-8 bg-[#f59e0b]/10 rounded-lg flex items-center justify-center text-[#f59e0b]">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L14.5 9H22L16 14L18.5 21L12 17L5.5 21L8 14L2 9H9.5L12 2Z"/></svg>
          </div>
        </div>
        <div class="text-3xl font-black text-[#1a1a1a]">{{ formatNumber(stats.total_tokens) }}</div>
        <div class="mt-1 text-xs text-[#94a3b8] font-medium tracking-wide uppercase">Cumulative Tokens Used</div>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-[#e2e8f0] shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <span class="text-xs font-bold text-[#64748b] bg-[#f1f5f9] px-2 py-1 rounded">PROMPT VS RESPONSE</span>
          <div class="flex -space-x-2">
            <div class="w-8 h-8 bg-blue-500/10 rounded-full flex items-center justify-center text-blue-600 border-2 border-white">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="w-8 h-8 bg-green-500/10 rounded-full flex items-center justify-center text-green-600 border-2 border-white">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" /></svg>
            </div>
          </div>
        </div>
        <div class="flex items-end gap-3">
           <div>
             <div class="text-xl font-bold text-[#1a1a1a]">{{ formatNumber(stats.total_prompt_tokens) }}</div>
             <div class="text-[10px] text-[#94a3b8] font-bold uppercase">Prompt</div>
           </div>
           <div class="h-8 w-px bg-[#e2e8f0] mb-1"></div>
           <div>
             <div class="text-xl font-bold text-[#1a1a1a]">{{ formatNumber(stats.total_completion_tokens) }}</div>
             <div class="text-[10px] text-[#94a3b8] font-bold uppercase">Completion</div>
           </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-[#e2e8f0] shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <span class="text-xs font-bold text-[#64748b] bg-[#f1f5f9] px-2 py-1 rounded">PROVIDERS</span>
        </div>
        <div class="space-y-3">
          <div v-for="p in stats.usage_by_provider" :key="p.provider" class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span :class="p.provider === 'openai' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700'" class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded">
                {{ p.provider }}
              </span>
            </div>
            <div class="text-sm font-bold text-[#475569]">{{ formatNumber(p.tokens) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <!-- User Usage -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-[#f1f5f9] flex items-center justify-between bg-white">
          <h3 class="font-bold text-[#1a1a1a] text-sm uppercase tracking-wider">Usage by User</h3>
          <span class="text-xs text-[#94a3b8]">{{ userStats.length }} Professionals</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-[#f8fafc] text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                <th class="px-6 py-4">Professional</th>
                <th class="px-6 py-4">Requests</th>
                <th class="px-6 py-4 text-right">Tokens</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#f1f5f9]">
              <tr v-for="u in userStats" :key="u.user_id" class="hover:bg-[#fcfcfc] transition-colors">
                <td class="px-6 py-4">
                  <div class="font-bold text-[#1a1a1a] text-sm">{{ u.user?.name || 'System' }}</div>
                  <div class="text-[10px] text-[#94a3b8]">{{ u.user?.email || '-' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-[#475569]">{{ formatNumber(u.requests) }}</td>
                <td class="px-6 py-4 text-sm font-bold text-[#1a1a1a] text-right">{{ formatNumber(u.tokens) }}</td>
              </tr>
              <tr v-if="userStats.length === 0">
                <td colspan="3" class="px-6 py-12 text-center text-[#94a3b8] text-sm font-medium italic">No usage data logged yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Usage by Type -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden shadow-sm flex flex-col">
        <div class="px-6 py-4 border-b border-[#f1f5f9] bg-white">
          <h3 class="font-bold text-[#1a1a1a] text-sm uppercase tracking-wider">Categorized Usage</h3>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-center gap-6">
          <div v-for="t in stats.usage_by_type" :key="t.type" class="space-y-1.5">
            <div class="flex justify-between text-xs font-bold">
              <span class="text-[#1a1a1a] uppercase tracking-wide">{{ t.type }}</span>
              <span class="text-[#f59e0b]">{{ formatNumber(t.tokens) }} tokens</span>
            </div>
            <div class="h-2 bg-[#f1f5f9] rounded-full overflow-hidden">
              <div 
                class="h-full bg-[#f59e0b] transition-all duration-1000 ease-out"
                :style="{ width: getPercentage(t.tokens) + '%' }"
              ></div>
            </div>
          </div>
          <div v-if="!stats.usage_by_type?.length" class="text-center py-12 text-[#94a3b8] italic text-sm">
            Categorization data pending...
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Logs -->
    <div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden shadow-sm">
      <div class="px-6 py-4 border-b border-[#f1f5f9] bg-white">
        <h3 class="font-bold text-[#1a1a1a] text-sm uppercase tracking-wider">Recent AI Activity Logs</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-[#f8fafc] text-[10px] font-black text-[#64748b] uppercase tracking-widest">
              <th class="px-6 py-4">Time</th>
              <th class="px-6 py-4">User</th>
              <th class="px-6 py-4">Model</th>
              <th class="px-6 py-4">Action</th>
              <th class="px-6 py-4 text-right">Tokens</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9]">
            <tr v-for="log in recentLogs" :key="log.id" class="hover:bg-[#fcfcfc] transition-colors">
              <td class="px-6 py-4 text-xs font-medium text-[#64748b] whitespace-nowrap">{{ formatDate(log.created_at) }}</td>
              <td class="px-6 py-4 text-sm font-bold text-[#1a1a1a]">{{ log.user?.name || 'System' }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-0.5 rounded-full bg-[#f1f5f9] text-[10px] font-black text-[#475569] uppercase border border-[#e2e8f0]">
                  {{ log.model }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="text-[10px] font-bold text-white bg-[#1a1a1a] px-2 py-0.5 rounded uppercase tracking-wide">
                  {{ log.type }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm font-bold text-[#1a1a1a] text-right">{{ log.total_tokens }}</td>
            </tr>
            <tr v-if="recentLogs.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-[#94a3b8] text-sm font-medium italic">No logs found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({});
const userStats = ref([]);
const recentLogs = ref([]);
const loading = ref(true);

async function fetchData() {
  loading.value = true;
  try {
    const [s, u, l] = await Promise.all([
      axios.get('/api/admin/ai/usage'),
      axios.get('/api/admin/ai/usage/users'),
      axios.get('/api/admin/ai/usage/logs')
    ]);
    stats.value = s.data;
    userStats.value = u.data;
    recentLogs.value = l.data;
  } catch (e) {
    console.error('Failed to fetch AI usage data', e);
  } finally {
    loading.value = false;
  }
}

function formatNumber(val) {
  if (!val) return '0';
  return new Intl.NumberFormat('en-US').format(val);
}

function formatDate(date) {
  return new Date(date).toLocaleString('en-IN', {
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function getPercentage(tokens) {
  if (!stats.value.total_tokens) return 0;
  return Math.round((tokens / stats.value.total_tokens) * 100);
}

onMounted(() => {
  fetchData();
});
</script>
