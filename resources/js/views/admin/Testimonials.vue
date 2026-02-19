<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Testimonials</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]"><tr><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Name</th><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Role</th><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Quote</th><th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th></tr></thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm text-[#1a1a1a]">{{ item.name }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.role }}</td>
            <td class="max-w-xs truncate px-4 py-3 text-sm text-[#64748b]">{{ item.quote }}</td>
            <td class="px-4 py-3 text-right"><button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button><button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Testimonial</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label><input v-model="form.name" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Role</label><input v-model="form.role" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Quote</label><textarea v-model="form.quote" rows="3" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]"></textarea></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Avatar URL</label><input v-model="form.avatar" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
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
const form = reactive({ name: '', role: '', quote: '', avatar: '' });

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/testimonials');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.name = item.name;
    form.role = item.role || '';
    form.quote = item.quote || '';
    form.avatar = item.avatar || '';
  } else {
    form.name = form.role = form.quote = form.avatar = '';
  }
  showModal.value = true;
}
async function save() {
  try {
    if (editing.value) {
      await axios.put('/api/admin/testimonials/' + editing.value.id, form);
    } else {
      await axios.post('/api/admin/testimonials', form);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving');
  }
}
async function remove(item) {
  if (!confirm('Delete this testimonial?')) return;
  try {
    await axios.delete('/api/admin/testimonials/' + item.id);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(load);
</script>
