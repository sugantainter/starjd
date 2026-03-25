<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-[#1a1a1a]">Banners</h1>
        <p class="text-sm text-[#64748b]">Manage dynamic banners for the mobile app home screen</p>
      </div>
      <button 
        @click="openModal()" 
        class="inline-flex items-center gap-2 rounded-lg bg-[#e63946] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#d62d3a]"
      >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Add Banner
      </button>
    </div>

    <div class="rounded-xl border border-[#e2e8f0] bg-white shadow-sm overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-[#f8fafc] border-b border-[#e2e8f0]">
          <tr>
            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#64748b]">Image</th>
            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#64748b]">Title</th>
            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#64748b]">Link</th>
            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#64748b]">Order</th>
            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#64748b]">Status</th>
            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-[#64748b] text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="banner in banners" :key="banner.id" class="hover:bg-[#f8fafc] transition-colors">
            <td class="px-6 py-4">
              <img :src="banner.image" class="h-12 w-24 object-cover rounded-lg border border-[#e2e8f0]" />
            </td>
            <td class="px-6 py-4 text-sm font-medium text-[#1a1a1a]">{{ banner.title || 'No Title' }}</td>
            <td class="px-6 py-4 text-sm text-[#64748b]">{{ banner.link || '-' }}</td>
            <td class="px-6 py-4 text-sm text-[#64748b]">{{ banner.sort_order }}</td>
            <td class="px-6 py-4">
              <span 
                :class="banner.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
              >
                {{ banner.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-3">
              <button @click="openModal(banner)" class="text-[#64748b] hover:text-[#e63946] transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              </button>
              <button @click="deleteBanner(banner.id)" class="text-[#64748b] hover:text-[#e63946] transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </td>
          </tr>
          <tr v-if="banners.length === 0">
            <td colspan="6" class="px-6 py-12 text-center text-[#64748b]">No banners found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <h2 class="text-xl font-bold text-[#1a1a1a] mb-6">{{ modal.isEdit ? 'Edit Banner' : 'Add New Banner' }}</h2>
        <form @submit.prevent="saveBanner" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-[#64748b] mb-1">Title</label>
            <input v-model="form.title" type="text" class="w-full rounded-lg border-[#e2e8f0] px-4 py-2 text-sm focus:border-[#e63946] focus:ring-[#e63946]" />
          </div>
          <div>
            <label class="block text-sm font-medium text-[#64748b] mb-1">Link (Deep link or URL)</label>
            <input v-model="form.link" type="text" class="w-full rounded-lg border-[#e2e8f0] px-4 py-2 text-sm focus:border-[#e63946] focus:ring-[#e63946]" placeholder="/studios or https://..." />
          </div>
          <div>
            <label class="block text-sm font-medium text-[#64748b] mb-1">Banner Image</label>
            <div class="mt-1 flex items-center gap-4">
              <img v-if="form.image" :src="form.image" class="h-20 w-40 object-cover rounded-lg border" />
              <div v-else class="h-20 w-40 flex items-center justify-center border-2 border-dashed rounded-lg text-[#64748b] text-xs">No image</div>
              <input type="file" @change="handleFileUpload" class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#e63946]/10 file:text-[#e63946] hover:file:bg-[#e63946]/20" accept="image/*" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[#64748b] mb-1">Sort Order</label>
              <input v-model="form.sort_order" type="number" class="w-full rounded-lg border-[#e2e8f0] px-4 py-2 text-sm focus:border-[#e63946] focus:ring-[#e63946]" />
            </div>
            <div class="flex items-center gap-2 mt-6">
              <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded text-[#e63946] focus:ring-[#e63946]" />
              <label for="is_active" class="text-sm font-medium text-[#64748b]">Active</label>
            </div>
          </div>
          <div class="flex justify-end gap-3 mt-8">
            <button type="button" @click="modal.show = false" class="rounded-lg px-4 py-2 text-sm font-semibold text-[#64748b] hover:bg-[#f1f5f9]">Cancel</button>
            <button type="submit" :disabled="loading" class="rounded-lg bg-[#e63946] px-6 py-2 text-sm font-semibold text-white transition hover:bg-[#d62d3a] disabled:opacity-50">
              {{ loading ? 'Saving...' : 'Save Banner' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

const banners = ref([]);
const loading = ref(false);
const modal = reactive({ show: false, isEdit: false, id: null });
const form = reactive({ title: '', link: '', image: '', sort_order: 0, is_active: true });

onMounted(fetchBanners);

async function fetchBanners() {
  const res = await axios.get('/api/admin/banners');
  banners.value = res.data;
}

function openModal(banner = null) {
  if (banner) {
    modal.isEdit = true;
    modal.id = banner.id;
    form.title = banner.title;
    form.link = banner.link;
    form.image = banner.image;
    form.sort_order = banner.sort_order;
    form.is_active = banner.is_active;
  } else {
    modal.isEdit = false;
    modal.id = null;
    form.title = '';
    form.link = '';
    form.image = '';
    form.sort_order = 0;
    form.is_active = true;
  }
  modal.show = true;
}

async function handleFileUpload(e) {
  const file = e.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);
  
  loading.value = true;
  try {
    const res = await axios.post('/api/admin/banners/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    form.image = res.data.url;
  } catch (err) {
    alert('Upload failed');
  } finally {
    loading.value = false;
  }
}

async function saveBanner() {
  if (!form.image) return alert('Image is required');
  
  loading.value = true;
  try {
    if (modal.isEdit) {
      await axios.put(`/api/admin/banners/${modal.id}`, form);
    } else {
      await axios.post('/api/admin/banners', form);
    }
    modal.show = false;
    fetchBanners();
  } catch (err) {
    alert('Save failed');
  } finally {
    loading.value = false;
  }
}

async function deleteBanner(id) {
  if (!confirm('Are you sure?')) return;
  await axios.delete(`/api/admin/banners/${id}`);
  fetchBanners();
}
</script>
