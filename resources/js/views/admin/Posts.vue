<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Blog Posts</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Post</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Title</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Published</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.title }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]"><span :class="item.status === 'published' ? 'text-emerald-600' : 'text-amber-600'">{{ item.status }}</span></td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.published_at ? formatDate(item.published_at) : '—' }}</td>
            <td class="px-4 py-3 text-right"><a :href="'/blog/' + item.slug" target="_blank" class="text-[#64748b] hover:underline">View</a><button type="button" class="ml-3 text-[#e63946] hover:underline" @click="openForm(item)">Edit</button><button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4" @click.self="showModal = false">
      <div class="my-8 w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Post</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Title</label><input v-model="form.title" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug</label><input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="auto from title" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Excerpt</label><input v-model="form.excerpt" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Body (markdown or HTML)</label><textarea v-model="form.body" rows="8" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a] font-mono text-sm"></textarea></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Image URL</label><input v-model="form.image" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Category label</label><input v-model="form.category_label" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. For Creators" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Status</label><select v-model="form.status" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]"><option value="draft">Draft</option><option value="published">Published</option></select></div>
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
const form = reactive({ title: '', slug: '', excerpt: '', body: '', image: '', category_label: '', status: 'draft' });

function formatDate(s) {
  if (!s) return '—';
  try {
    return new Date(s).toLocaleDateString();
  } catch {
    return s;
  }
}
async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/posts');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.title = item.title || '';
    form.slug = item.slug || '';
    form.excerpt = item.excerpt || '';
    form.body = item.body || '';
    form.image = item.image || '';
    form.category_label = item.category_label || '';
    form.status = item.status || 'draft';
  } else {
    form.title = form.slug = form.excerpt = form.body = form.image = form.category_label = '';
    form.status = 'draft';
  }
  showModal.value = true;
}
async function save() {
  try {
    if (editing.value) {
      await axios.put('/api/admin/posts/' + editing.value.id, form);
    } else {
      await axios.post('/api/admin/posts', form);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving');
  }
}
async function remove(item) {
  if (!confirm('Delete this post?')) return;
  try {
    await axios.delete('/api/admin/posts/' + item.id);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(load);
</script>
