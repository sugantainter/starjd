<template>
  <div>
    <AdminPageHeader
      title="States"
      description="Manage states for location-based pages. Add states first, then cities under each state."
      :breadcrumbs="[{ label: 'Locations', path: '/admin' }, { label: 'States', path: '/admin/states' }]"
    >
      <template #actions>
        <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add State</button>
      </template>
    </AdminPageHeader>

    <div v-if="loading" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white">
      <AdminTableSkeleton :columns="4" :rows="6" />
    </div>
    <div v-else-if="items.length" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Slug</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Code</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.name }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.slug }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.code || '—' }}</td>
            <td class="px-4 py-3 text-right">
              <button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button>
              <button type="button" class="ml-3 text-red-600 hover:underline" @click="confirmRemove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <AdminEmptyState
      v-else
      title="No states yet"
      description="Add a state to enable state and city-based dynamic pages."
    >
      <button type="button" class="mt-4 rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add first state</button>
    </AdminEmptyState>

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} State</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label>
            <input v-model="form.name" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. Maharashtra" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug</label>
            <input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="auto from name" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Code</label>
            <input v-model="form.code" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. MH" maxlength="20" />
          </div>
          <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]">Save</button>
            <button type="button" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="showModal = false">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <AdminConfirmModal
      :open="showDeleteModal"
      title="Delete state"
      message="Deleting this state will also affect its cities and any state-level pages. This cannot be undone."
      confirm-label="Delete"
      @close="showDeleteModal = false; itemToRemove = null"
      @confirm="doRemove"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import AdminPageHeader from '../../components/admin/AdminPageHeader.vue';
import AdminEmptyState from '../../components/admin/AdminEmptyState.vue';
import AdminTableSkeleton from '../../components/admin/AdminTableSkeleton.vue';
import AdminConfirmModal from '../../components/admin/AdminConfirmModal.vue';

const items = ref([]);
const loading = ref(true);
const showModal = ref(false);
const showDeleteModal = ref(false);
const itemToRemove = ref(null);
const editing = ref(null);
const form = reactive({ name: '', slug: '', code: '' });

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/states');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.name = item.name;
    form.slug = item.slug || '';
    form.code = item.code || '';
  } else {
    form.name = form.slug = form.code = '';
  }
  showModal.value = true;
}
async function save() {
  try {
    if (editing.value) {
      await axios.put('/api/admin/states/' + editing.value.id, form);
    } else {
      await axios.post('/api/admin/states', form);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || e.response?.data?.errors?.name?.[0] || 'Error saving');
  }
}
function confirmRemove(item) {
  itemToRemove.value = item;
  showDeleteModal.value = true;
}
async function doRemove() {
  if (!itemToRemove.value) return;
  try {
    await axios.delete('/api/admin/states/' + itemToRemove.value.id);
    showDeleteModal.value = false;
    itemToRemove.value = null;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(load);
</script>
