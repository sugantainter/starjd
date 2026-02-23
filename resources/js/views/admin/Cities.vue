<template>
  <div>
    <AdminPageHeader
      title="Cities"
      description="Manage cities by state. Cities are used for city-level dynamic pages."
      :breadcrumbs="[{ label: 'Locations', path: '/admin' }, { label: 'Cities', path: '/admin/cities' }]"
    >
      <template #actions>
        <select v-model="filterStateId" class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a]">
          <option value="">All states</option>
          <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
        <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add City</button>
      </template>
    </AdminPageHeader>

    <div v-if="loading" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white">
      <AdminTableSkeleton :columns="4" :rows="6" />
    </div>
    <div v-else-if="items.length" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">City</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Slug</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">State</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.name }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.slug }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.state?.name || '—' }}</td>
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
      title="No cities yet"
      :description="states.length ? 'Add a city under a state to enable city-level pages.' : 'Add states first, then add cities under each state.'"
    >
      <router-link v-if="!states.length" to="/admin/states" class="mt-4 inline-block rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]">Go to States</router-link>
      <button v-else type="button" class="mt-4 rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add first city</button>
    </AdminEmptyState>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} City</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">State</label>
            <select v-model="form.state_id" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]">
              <option value="">Select state</option>
              <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label>
            <input v-model="form.name" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. Mumbai" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug</label>
            <input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="auto from name" />
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
      title="Delete city"
      message="This will remove the city. City-level pages linked to it will be affected. This cannot be undone."
      confirm-label="Delete"
      @close="showDeleteModal = false; itemToRemove = null"
      @confirm="doRemove"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';
import AdminPageHeader from '../../components/admin/AdminPageHeader.vue';
import AdminEmptyState from '../../components/admin/AdminEmptyState.vue';
import AdminTableSkeleton from '../../components/admin/AdminTableSkeleton.vue';
import AdminConfirmModal from '../../components/admin/AdminConfirmModal.vue';

const items = ref([]);
const states = ref([]);
const loading = ref(true);
const showModal = ref(false);
const showDeleteModal = ref(false);
const itemToRemove = ref(null);
const editing = ref(null);
const filterStateId = ref('');
const form = reactive({ state_id: '', name: '', slug: '' });

async function loadStates() {
  const r = await axios.get('/api/admin/states');
  states.value = r.data;
}
async function load() {
  loading.value = true;
  try {
    const params = filterStateId.value ? { state_id: filterStateId.value } : {};
    const r = await axios.get('/api/admin/cities', { params });
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
watch(filterStateId, load);
function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.state_id = item.state_id;
    form.name = item.name;
    form.slug = item.slug || '';
  } else {
    form.state_id = filterStateId.value || '';
    form.name = form.slug = '';
  }
  showModal.value = true;
}
async function save() {
  try {
    if (editing.value) {
      await axios.put('/api/admin/cities/' + editing.value.id, form);
    } else {
      await axios.post('/api/admin/cities', form);
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
    await axios.delete('/api/admin/cities/' + itemToRemove.value.id);
    showDeleteModal.value = false;
    itemToRemove.value = null;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(() => {
  loadStates().then(load);
});
</script>
