<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Categories</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]"><tr><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Name</th><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Slug</th><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Count</th><th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Image</th><th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th></tr></thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm text-[#1a1a1a]">{{ item.name }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.slug }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.count_display }}</td>
            <td class="px-4 py-3">
              <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="h-10 w-10 rounded-lg object-cover border border-[#e2e8f0]" />
              <span v-else class="text-xs text-[#94a3b8]">—</span>
            </td>
            <td class="px-4 py-3 text-right"><button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button><button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Category</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label><input v-model="form.name" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug</label><input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" /></div>
          <div><label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Count display</label><input v-model="form.count_display" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. 1.2k" /></div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Image</label>
            <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" @change="onImageSelect" />
            <div class="flex flex-wrap items-center gap-3">
              <button type="button" class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="imageInput?.click()">{{ imagePreview ? 'Change image' : 'Upload image' }}</button>
              <button v-if="imagePreview" type="button" class="text-sm text-red-600 hover:underline" @click="clearImage">Remove</button>
            </div>
            <p class="mt-1 text-xs text-[#64748b]">JPEG, PNG or WebP. Max 2 MB.</p>
            <p v-if="imageError" class="mt-1 text-xs text-red-600">{{ imageError }}</p>
            <div v-if="imagePreview" class="mt-2">
              <img :src="imagePreview" :alt="form.name" class="max-h-40 rounded-lg border border-[#e2e8f0] object-cover" />
            </div>
          </div>
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
const imageInput = ref(null);
const imageFile = ref(null);
const form = reactive({ name: '', slug: '', count_display: '', image: '' });

const imagePreview = ref('');
const imageError = ref('');
const MAX_IMAGE_BYTES = 2 * 1024 * 1024; // 2 MB

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/categories');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
function openForm(item = null) {
  editing.value = item;
  imageFile.value = null;
  imageError.value = '';
  if (item) {
    form.name = item.name;
    form.slug = item.slug;
    form.count_display = item.count_display || '';
    form.image = item.image || '';
    imagePreview.value = item.image_url || '';
  } else {
    form.name = form.slug = form.count_display = form.image = '';
    imagePreview.value = '';
  }
  showModal.value = true;
}
function onImageSelect(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  imageError.value = '';
  if (file.size > MAX_IMAGE_BYTES) {
    imageError.value = `File is ${(file.size / 1024 / 1024).toFixed(1)} MB. Please choose an image under 2 MB, or the server may reject it (413).`;
    imageFile.value = null;
    imagePreview.value = '';
    if (imageInput.value) imageInput.value.value = '';
    return;
  }
  imageFile.value = file;
  imagePreview.value = URL.createObjectURL(file);
}
function clearImage() {
  imageFile.value = null;
  imageError.value = '';
  imagePreview.value = editing.value?.image_url || '';
  if (imageInput.value) imageInput.value.value = '';
}
async function save() {
  try {
    const payload = new FormData();
    payload.append('name', form.name);
    payload.append('slug', form.slug);
    payload.append('count_display', form.count_display);
    if (imageFile.value) payload.append('image', imageFile.value);

    if (editing.value) {
      // POST with _method=PUT so PHP populates $_FILES (PUT + multipart is not parsed by PHP)
      payload.append('_method', 'PUT');
      await axios.post(`/api/admin/categories/${editing.value.id}`, payload);
    } else {
      await axios.post('/api/admin/categories', payload);
    }
    showModal.value = false;
    load();
  } catch (e) {
    const status = e.response?.status;
    const is413 = status === 413 || (e.message && e.message.includes('413'));
    const msg = is413
      ? 'Image or request too large. Use an image under 2 MB.'
      : (e.response?.data?.message || e.response?.data?.errors?.image?.[0] || 'Error saving');
    alert(msg);
  }
}
async function remove(item) {
  if (!confirm('Delete this category?')) return;
  try {
    await axios.delete(`/api/admin/categories/${item.id}`);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(load);
</script>
