<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-[#1a1a1a]">Sub Categories</h1>
        <p class="text-sm text-[#64748b]">Manage sub-categories for each principal category.</p>
      </div>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Sub Category</button>
    </div>

    <div class="mb-4 flex gap-4">
        <select v-model="filterCategoryId" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm" @change="load">
            <option :value="null">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
    </div>

    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Category</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Sub Category Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Slug</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Sort</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm text-[#1a1a1a] font-medium">{{ item.category_name }}</td>
            <td class="px-4 py-3 text-sm text-[#1a1a1a]">{{ item.name }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.slug }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.sort_order }}</td>
            <td class="px-4 py-3 text-right">
                <button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button>
                <button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button>
            </td>
          </tr>
          <tr v-if="!items.length">
              <td colspan="5" class="px-4 py-8 text-center text-[#64748b]">No sub-categories found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Sub Category</h2>
        <form @submit.prevent="save" class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Parent Category</label>
            <select v-model="form.category_id" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]">
                <option :value="null">Select category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label>
            <input v-model="form.name" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug (optional)</label>
            <input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Sort Order</label>
            <input v-model.number="form.sort_order" type="number" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
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
import { notify } from '../../lib/notify.js';

const items = ref([]);
const categories = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editing = ref(null);
const filterCategoryId = ref(null);

const form = reactive({ category_id: null, name: '', slug: '', sort_order: 0 });

async function load() {
  loading.value = true;
  try {
    const params = {};
    if (filterCategoryId.value) params.category_id = filterCategoryId.value;
    const [scRes, catRes] = await Promise.all([
        axios.get('/api/admin/sub-categories', { params }),
        axios.get('/api/admin/categories')
    ]);
    items.value = scRes.data;
    categories.value = catRes.data;
  } finally {
    loading.value = false;
  }
}

function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.category_id = item.category_id;
    form.name = item.name;
    form.slug = item.slug;
    form.sort_order = item.sort_order || 0;
  } else {
    form.category_id = filterCategoryId.value || null;
    form.name = form.slug = '';
    form.sort_order = 0;
  }
  showModal.value = true;
}

async function save() {
  try {
    if (editing.value) {
      await axios.put(`/api/admin/sub-categories/${editing.value.id}`, form);
    } else {
      await axios.post('/api/admin/sub-categories', form);
    }
    showModal.value = false;
    load();
    notify.success('Saved successfully');
  } catch (e) {
    notify.error(e);
  }
}

async function remove(item) {
  if (!confirm('Delete this sub-category?')) return;
  try {
    await axios.delete(`/api/admin/sub-categories/${item.id}`);
    load();
    notify.success('Deleted successfully');
  } catch (e) {
    notify.error(e);
  }
}

onMounted(load);
</script>
