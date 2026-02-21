<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Packages</h1>
    <p class="mt-1 text-[#64748b]">Set your collaboration packages with categories and detailed pricing.</p>
    <div class="mt-6 flex justify-end">
      <button type="button" class="cursor-link rounded-xl bg-[#10b981] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#059669]" @click="openAdd">Add package</button>
    </div>
    <div v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
    <div class="mt-6 space-y-4">
      <div
        v-for="pkg in packages"
        :key="pkg.id"
        class="rounded-xl border border-[#e2e8f0] bg-white shadow-sm overflow-hidden"
      >
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 p-5">
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <span v-if="pkg.package_category" class="rounded-full bg-[#10b981]/10 px-2.5 py-0.5 text-xs font-medium text-[#059669]">
                {{ pkg.package_category.name }}
              </span>
              <span :class="pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'" class="rounded-full px-2.5 py-0.5 text-xs font-medium">
                {{ pkg.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <h3 class="mt-2 font-semibold text-[#1a1a1a]">{{ pkg.name }}</h3>
            <p class="mt-1 text-2xl font-bold text-[#10b981]">₹{{ formatPrice(pkg.price) }}</p>
            <p v-if="pkg.description" class="mt-2 text-sm text-[#64748b] line-clamp-2">{{ pkg.description }}</p>
            <div v-if="pkg.items?.length" class="mt-3 text-xs text-[#64748b]">
              <span v-for="(it, i) in pkg.items" :key="it.id || i">
                {{ it.name }}: {{ it.quantity }} × ₹{{ formatPrice(it.unit_price) }} = ₹{{ formatPrice(it.quantity * it.unit_price) }}<span v-if="i < pkg.items.length - 1"> · </span>
              </span>
            </div>
          </div>
          <div class="flex gap-2 shrink-0">
            <button type="button" class="cursor-link rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm font-medium text-[#1a1a1a] hover:bg-[#f1f5f9]" @click="openEdit(pkg)">Edit</button>
            <button type="button" class="cursor-link rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50" @click="remove(pkg)">Delete</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="!packages.length && !error" class="mt-8 rounded-xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc] p-8 text-center text-[#64748b]">
      <p class="font-medium">No packages yet</p>
      <p class="mt-1 text-sm">Add a package to show rates and deliverables on your public profile.</p>
      <button type="button" class="mt-4 cursor-link rounded-xl bg-[#10b981] px-4 py-2 text-sm font-semibold text-white hover:bg-[#059669]" @click="openAdd">Add package</button>
    </div>

    <!-- Add/Edit modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-white shadow-xl">
        <div class="sticky top-0 z-10 border-b border-[#e2e8f0] bg-white px-6 py-4">
          <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit package' : 'Add package' }}</h2>
        </div>
        <form @submit.prevent="submitModal" class="space-y-5 p-6">
          <div class="grid gap-5 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Category</label>
              <select v-model="modal.package_category_id" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]">
                <option :value="null">Select category (optional)</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Package name</label>
              <input v-model="modal.name" type="text" required placeholder="e.g. Social Media Bundle" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
            </div>
          </div>

          <div>
            <div class="mb-2 flex items-center justify-between">
              <label class="block text-sm font-medium text-[#1a1a1a]">Pricing breakdown (quantity × unit price)</label>
              <button type="button" class="text-sm font-medium text-[#10b981] hover:underline" @click="addItem">+ Add line</button>
            </div>
            <div class="space-y-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
              <div v-for="(item, idx) in modal.items" :key="idx" class="flex flex-wrap items-end gap-3">
                <div class="min-w-0 flex-1">
                  <label class="sr-only">Item name</label>
                  <input v-model="item.name" type="text" placeholder="e.g. Instagram Post" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
                </div>
                <div class="w-20">
                  <label class="sr-only">Qty</label>
                  <input v-model.number="item.quantity" type="number" min="1" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
                </div>
                <div class="w-28">
                  <label class="sr-only">Unit price (₹)</label>
                  <input v-model.number="item.unit_price" type="number" step="0.01" min="0" placeholder="₹" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
                </div>
                <div class="w-24 text-right text-sm font-medium text-[#64748b]">₹{{ formatPrice((item.quantity || 0) * (item.unit_price || 0)) }}</div>
                <button type="button" class="rounded-lg p-2 text-red-600 hover:bg-red-50" aria-label="Remove line" @click="modal.items.splice(idx, 1)">×</button>
              </div>
              <p v-if="!modal.items.length" class="text-sm text-[#64748b]">Add line items for a detailed breakdown, or leave empty and set package price below.</p>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Package price (₹) <span class="text-[#64748b]">— total charged for this package</span></label>
            <input v-model.number="modal.price" type="number" step="0.01" min="0" required class="w-full max-w-xs rounded-xl border border-[#e2e8f0] px-4 py-3 text-lg font-semibold focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
            <button v-if="computedItemsTotal > 0" type="button" class="ml-2 text-sm text-[#10b981] hover:underline" @click="modal.price = computedItemsTotal">Use sum of items (₹{{ formatPrice(computedItemsTotal) }})</button>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Description</label>
            <textarea v-model="modal.description" rows="3" placeholder="What's included in this package?" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]"></textarea>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Deliverables / timeline</label>
            <textarea v-model="modal.deliverables" rows="2" placeholder="e.g. Within 10–20 days" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]"></textarea>
          </div>
          <div class="flex items-center gap-2">
            <input v-model="modal.is_active" type="checkbox" id="modal_active" class="rounded border-[#e2e8f0] text-[#10b981] focus:ring-[#10b981]" />
            <label for="modal_active" class="text-sm font-medium text-[#1a1a1a]">Active (visible on profile)</label>
          </div>
          <div class="flex gap-3 pt-2">
            <button type="submit" class="cursor-link rounded-xl bg-[#10b981] px-5 py-2.5 font-semibold text-white hover:bg-[#059669]">Save</button>
            <button type="button" class="cursor-link rounded-xl border border-[#e2e8f0] px-5 py-2.5 font-medium hover:bg-[#f1f5f9]" @click="showModal = false">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';

const packages = ref([]);
const categories = ref([]);
const showModal = ref(false);
const editing = ref(null);
const modal = reactive({
  package_category_id: null,
  name: '',
  price: 0,
  description: '',
  deliverables: '',
  is_active: true,
  items: [],
});
const error = ref('');

const computedItemsTotal = computed(() => {
  return modal.items.reduce((sum, it) => sum + (Number(it.quantity) || 0) * (Number(it.unit_price) || 0), 0);
});

function formatPrice(n) {
  const x = Number(n);
  return Number.isFinite(x) ? x.toFixed(2) : '0.00';
}

onMounted(() => {
  load();
  loadCategories();
});

async function load() {
  const res = await axios.get('/api/creator/packages', { withCredentials: true });
  packages.value = res.data;
}

async function loadCategories() {
  const res = await axios.get('/api/creator/package-categories', { withCredentials: true });
  categories.value = res.data;
}

function addItem() {
  modal.items.push({ name: '', quantity: 1, unit_price: 0 });
}

function openAdd() {
  editing.value = null;
  modal.package_category_id = null;
  modal.name = '';
  modal.price = 0;
  modal.description = '';
  modal.deliverables = '';
  modal.is_active = true;
  modal.items = [];
  showModal.value = true;
}

function openEdit(pkg) {
  editing.value = pkg;
  modal.package_category_id = pkg.package_category_id ?? null;
  modal.name = pkg.name;
  modal.price = pkg.price;
  modal.description = pkg.description ?? '';
  modal.deliverables = pkg.deliverables ?? '';
  modal.is_active = pkg.is_active;
  modal.items = (pkg.items || []).map((it) => ({
    id: it.id,
    name: it.name,
    quantity: it.quantity,
    unit_price: Number(it.unit_price),
  }));
  if (!modal.items.length) modal.items = [];
  showModal.value = true;
}

function buildPayload() {
  const payload = {
    package_category_id: modal.package_category_id || null,
    name: modal.name,
    price: Number(modal.price),
    description: modal.description || null,
    deliverables: modal.deliverables || null,
    is_active: modal.is_active,
  };
  const validItems = modal.items.filter((it) => it.name && (Number(it.quantity) > 0 || Number(it.unit_price) >= 0));
  if (validItems.length) {
    payload.items = validItems.map((it) => ({
      id: it.id,
      name: it.name,
      quantity: Math.max(1, Number(it.quantity) || 1),
      unit_price: Math.max(0, Number(it.unit_price) || 0),
    }));
  }
  return payload;
}

async function submitModal() {
  error.value = '';
  try {
    const payload = buildPayload();
    if (editing.value) {
      await axios.put('/api/creator/packages/' + editing.value.id, payload, { withCredentials: true });
    } else {
      await axios.post('/api/creator/packages', payload, { withCredentials: true });
    }
    showModal.value = false;
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || e.response?.data?.errors?.message?.[0] || 'Failed to save.';
  }
}

async function remove(pkg) {
  if (!confirm('Delete this package? This cannot be undone.')) return;
  try {
    await axios.delete('/api/creator/packages/' + pkg.id, { withCredentials: true });
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to delete.';
  }
}
</script>
