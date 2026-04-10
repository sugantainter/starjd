<template>
  <div>
    <h1 class="mb-6 text-2xl font-bold text-[#1a1a1a]">Contact Messages</h1>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else-if="!items.length" class="rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]">No messages yet.</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Email</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Subject</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Message</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Date</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm text-[#1a1a1a]">{{ item.name }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.email }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.subject || '—' }}</td>
            <td class="max-w-xs truncate px-4 py-3 text-sm text-[#64748b]">{{ item.body }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ formatDate(item.created_at) }}</td>
            <td class="px-4 py-3 text-right space-x-3">
              <button type="button" class="text-indigo-600 font-medium hover:underline" @click="selectedItem = item">View</button>
              <button type="button" class="text-red-600 hover:underline" @click="remove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- View Message Modal -->
    <div v-if="selectedItem" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @click.self="selectedItem = null">
      <div class="relative w-full max-w-2xl transform rounded-2xl bg-white shadow-2xl transition-all">
        <div class="border-b border-[#e2e8f0] p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-[#1a1a1a]">Message Details</h3>
            <button class="rounded-lg p-2 text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" @click="selectedItem = null">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
        </div>
        <div class="max-h-[70vh] overflow-y-auto p-8">
          <div class="grid gap-6 sm:grid-cols-2">
            <div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-[#94a3b8]">From</p>
              <p class="mt-1 font-bold text-[#1a1a1a]">{{ selectedItem.name }}</p>
              <p class="text-sm text-[#64748b]">{{ selectedItem.email }}</p>
            </div>
            <div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-[#94a3b8]">Date</p>
              <p class="mt-1 text-sm text-[#1a1a1a]">{{ formatDate(selectedItem.created_at) }}</p>
            </div>
          </div>
          <div class="mt-8">
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#94a3b8]">Subject</p>
            <p class="mt-1 font-bold text-[#1a1a1a]">{{ selectedItem.subject || 'No Subject' }}</p>
          </div>
          <div class="mt-8">
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#94a3b8]">Message</p>
            <div class="mt-4 rounded-xl bg-[#f8fafc] p-6 text-[#475569] leading-relaxed whitespace-pre-wrap ring-1 ring-[#e2e8f0]">
              {{ selectedItem.body }}
            </div>
          </div>
        </div>
        <div class="border-t border-[#e2e8f0] p-6 text-right">
          <button class="rounded-xl bg-[#1a1a1a] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-black" @click="selectedItem = null">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const items = ref([]);
const loading = ref(true);
const selectedItem = ref(null);

function formatDate(s) {
  if (!s) return '—';
  try {
    const d = new Date(s);
    return d.toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' });
  } catch {
    return s;
  }
}
async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/contacts');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
async function remove(item) {
  if (!confirm('Delete this message?')) return;
  try {
    await axios.delete(`/api/admin/contacts/${item.id}`);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(load);
</script>
