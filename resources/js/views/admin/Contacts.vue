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
            <td class="px-4 py-3 text-right"><button type="button" class="text-red-600 hover:underline" @click="remove(item)">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const items = ref([]);
const loading = ref(true);

function formatDate(s) {
  if (!s) return '—';
  try {
    const d = new Date(s);
    return d.toLocaleDateString();
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
