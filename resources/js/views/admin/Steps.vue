<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Steps (How It Works)</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]"><tr><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Title</th><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Description</th><th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th></tr></thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.title }}</td>
            <td class="max-w-md truncate px-4 py-3 text-sm text-[#64748b]">{{ item.desc }}</td>
            <td class="px-4 py-3 text-right"><button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button><button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Step</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Title</label><input v-model="form.title" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Description</label><textarea v-model="form.desc" rows="3" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]"></textarea></div>
          <div class="flex gap-2 pt-2"><button type="submit" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]">Save</button><button type="button" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="showModal = false">Cancel</button></div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const items = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editing = ref(null);
const form = reactive({ title: '', desc: '' });

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/steps');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.title = item.title || '';
    form.desc = item.desc || '';
  } else {
    form.title = form.desc = '';
  }
  showModal.value = true;
}
async function save() {
  try {
    if (editing.value) {
      await axios.put('/api/admin/steps/' + editing.value.id, form);
    } else {
      await axios.post('/api/admin/steps', form);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving');
  }
}
async function remove(item) {
  if (!confirm('Delete this step?')) return;
  try {
    await axios.delete('/api/admin/steps/' + item.id);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(load);
</script>
