<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Packages</h1>
    <p class="mt-1 text-[#64748b]">Set your collaboration packages and rates.</p>
    <div class="mt-6 flex justify-end">
      <button type="button" class="cursor-link rounded-xl bg-[#10b981] px-4 py-2 text-sm font-semibold text-white hover:bg-[#059669]" @click="openAdd">Add package</button>
    </div>
    <div v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
    <ul class="mt-4 space-y-4">
      <li v-for="pkg in packages" :key="pkg.id" class="rounded-xl border border-[#e2e8f0] bg-white p-4 flex justify-between items-start">
        <div>
          <div class="font-semibold text-[#1a1a1a]">{{ pkg.name }}</div>
          <div class="text-[#10b981] font-medium">₹{{ pkg.price }}</div>
          <p v-if="pkg.description" class="mt-2 text-sm text-[#64748b]">{{ pkg.description }}</p>
          <span class="mt-2 inline-block rounded-full px-2 py-0.5 text-xs" :class="pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">{{ pkg.is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="flex gap-2">
          <button type="button" class="cursor-link rounded-lg border px-3 py-1 text-sm hover:bg-[#f1f5f9]" @click="openEdit(pkg)">Edit</button>
          <button type="button" class="cursor-link rounded-lg border border-red-200 px-3 py-1 text-sm text-red-600 hover:bg-red-50" @click="remove(pkg)">Delete</button>
        </div>
      </li>
    </ul>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6">
        <h2 class="text-lg font-semibold">{{ editing ? 'Edit package' : 'Add package' }}</h2>
        <form @submit.prevent="submitModal" class="mt-4 space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium">Name</label>
            <input v-model="modal.name" type="text" required class="w-full rounded-xl border px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Price (₹)</label>
            <input v-model.number="modal.price" type="number" step="0.01" min="0" required class="w-full rounded-xl border px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Description</label>
            <textarea v-model="modal.description" rows="3" class="w-full rounded-xl border px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]"></textarea>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Deliverables</label>
            <textarea v-model="modal.deliverables" rows="2" class="w-full rounded-xl border px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]"></textarea>
          </div>
          <div class="flex items-center gap-2">
            <input v-model="modal.is_active" type="checkbox" id="modal_active" class="rounded text-[#10b981]" />
            <label for="modal_active" class="text-sm">Active</label>
          </div>
          <div class="flex gap-2 pt-2">
            <button type="submit" class="cursor-link rounded-xl bg-[#10b981] px-4 py-2 text-white hover:bg-[#059669]">Save</button>
            <button type="button" class="cursor-link rounded-xl border px-4 py-2 hover:bg-[#f1f5f9]" @click="showModal = false">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const packages = ref([]);
const showModal = ref(false);
const editing = ref(null);
const modal = reactive({ name: '', price: 0, description: '', deliverables: '', is_active: true });
const error = ref('');

onMounted(load);

async function load() {
  const res = await axios.get('/api/creator/packages', { withCredentials: true });
  packages.value = res.data;
}

function openAdd() {
  editing.value = null;
  modal.name = '';
  modal.price = 0;
  modal.description = '';
  modal.deliverables = '';
  modal.is_active = true;
  showModal.value = true;
}

function openEdit(pkg) {
  editing.value = pkg;
  modal.name = pkg.name;
  modal.price = pkg.price;
  modal.description = pkg.description ?? '';
  modal.deliverables = pkg.deliverables ?? '';
  modal.is_active = pkg.is_active;
  showModal.value = true;
}

async function submitModal() {
  error.value = '';
  try {
    if (editing.value) {
      await axios.put('/api/creator/packages/' + editing.value.id, modal, { withCredentials: true });
    } else {
      await axios.post('/api/creator/packages', modal, { withCredentials: true });
    }
    showModal.value = false;
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to save.';
  }
}

async function remove(pkg) {
  if (!confirm('Delete this package?')) return;
  try {
    await axios.delete('/api/creator/packages/' + pkg.id, { withCredentials: true });
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to delete.';
  }
}
</script>
