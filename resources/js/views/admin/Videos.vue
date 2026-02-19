<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Video Guides</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Video</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Title</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Video ID</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.title }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.video_id }}</td>
            <td class="px-4 py-3 text-right"><button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button><button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Video</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">YouTube Video ID</label><input v-model="form.video_id" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2" placeholder="e.g. dQw4w9WgXcQ" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Title</label><input v-model="form.title" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Description</label><textarea v-model="form.desc" rows="2" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2"></textarea></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Embed URL</label><input v-model="form.embed_url" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2" placeholder="https://www.youtube.com/embed/VIDEO_ID" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Watch URL</label><input v-model="form.watch_url" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2" placeholder="https://www.youtube.com/watch?v=VIDEO_ID" /></div>
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
const form = reactive({ video_id: '', title: '', desc: '', embed_url: '', watch_url: '' });

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/videos');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.video_id = item.video_id || '';
    form.title = item.title || '';
    form.desc = item.desc || '';
    form.embed_url = item.embed_url || '';
    form.watch_url = item.watch_url || '';
  } else {
    form.video_id = form.title = form.desc = form.embed_url = form.watch_url = '';
  }
  showModal.value = true;
}
async function save() {
  try {
    if (editing.value) {
      await axios.put('/api/admin/videos/' + editing.value.id, form);
    } else {
      await axios.post('/api/admin/videos', form);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving');
  }
}
async function remove(item) {
  if (!confirm('Delete this video?')) return;
  try {
    await axios.delete('/api/admin/videos/' + item.id);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(load);
</script>
