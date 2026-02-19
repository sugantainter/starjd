<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Partner Companies</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Partner</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Logo</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Link</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3">
              <img v-if="item.logo_url" :src="item.logo_url" :alt="item.name" class="h-10 w-auto max-w-[120px] object-contain" />
              <span v-else class="text-sm text-[#94a3b8]">—</span>
            </td>
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.name || '—' }}</td>
            <td class="max-w-xs truncate px-4 py-3 text-sm text-[#64748b]">{{ item.link || '—' }}</td>
            <td class="px-4 py-3 text-right">
              <button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button>
              <button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="!items.length && !loading" class="rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]">No partners yet. Add one above.</div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Partner</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Logo</label>
            <div class="flex items-center gap-3">
              <div v-if="form.logoPreview || (editing && editing.logo_url)" class="h-16 w-24 shrink-0 overflow-hidden rounded-lg border border-[#e2e8f0] bg-[#f8fafc]">
                <img :src="form.logoPreview || editing.logo_url" alt="Logo" class="h-full w-full object-contain" />
              </div>
              <div class="flex-1">
                <input ref="logoInput" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="hidden" @change="onLogoSelect" />
                <button type="button" class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm font-medium text-[#1a1a1a] hover:bg-[#f1f5f9]" @click="logoInput?.click()">
                  {{ form.logoFile ? 'Change' : 'Upload' }} logo
                </button>
              </div>
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name (optional)</label>
            <input v-model="form.name" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Company name" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Link (optional)</label>
            <input v-model="form.link" type="url" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="https://..." />
          </div>
          <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]">Save</button>
            <button type="button" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="showModal = false">Cancel</button>
          </div>
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
const logoInput = ref(null);
const form = reactive({ name: '', link: '', logoFile: null, logoPreview: null });

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/partners');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}

function openForm(item = null) {
  editing.value = item;
  form.name = item?.name ?? '';
  form.link = item?.link ?? '';
  form.logoFile = null;
  form.logoPreview = item?.logo_url ?? null;
  showModal.value = true;
}

function onLogoSelect(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  form.logoFile = file;
  form.logoPreview = URL.createObjectURL(file);
}

async function save() {
  try {
    const payload = new FormData();
    payload.append('name', form.name);
    payload.append('link', form.link || '');
    if (form.logoFile) payload.append('logo', form.logoFile);
    if (editing.value) {
      await axios.put('/api/admin/partners/' + editing.value.id, payload);
    } else {
      await axios.post('/api/admin/partners', payload);
    }
    showModal.value = false;
    load();
  } catch (err) {
    alert(err.response?.data?.message || 'Error saving');
  }
}

async function remove(item) {
  if (!confirm('Remove this partner?')) return;
  try {
    await axios.delete('/api/admin/partners/' + item.id);
    load();
  } catch (err) {
    alert(err.response?.data?.message || 'Error deleting');
  }
}

onMounted(load);
</script>
