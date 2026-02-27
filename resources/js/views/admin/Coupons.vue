<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Coupon codes</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add coupon</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Code</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Discount</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Min / Uses</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Valid</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Applicable</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3">
              <span class="font-mono font-semibold text-[#1a1a1a]">{{ item.code }}</span>
              <span v-if="!item.is_active" class="ml-2 rounded bg-[#94a3b8] px-1.5 py-0.5 text-xs text-white">Inactive</span>
            </td>
            <td class="px-4 py-3 text-sm text-[#1a1a1a]">
              {{ item.discount_type === 'percent' ? item.discount_value + '%' : '₹' + item.discount_value }}
            </td>
            <td class="px-4 py-3 text-sm text-[#64748b]">
              {{ item.min_amount != null ? '₹' + item.min_amount : '—' }} / {{ item.max_uses != null ? item.used_count + '/' + item.max_uses : item.used_count + ' used' }}
            </td>
            <td class="px-4 py-3 text-sm text-[#64748b]">
              {{ formatDate(item.valid_from) }} – {{ formatDate(item.valid_until) }}
            </td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.applicable_to || 'All' }}</td>
            <td class="px-4 py-3 text-right">
              <button type="button" class="text-[#e63946] hover:underline" @click="openForm(item)">Edit</button>
              <button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} coupon</h2>
        <form @submit.prevent="save" class="space-y-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Code</label>
            <input v-model="form.code" type="text" required placeholder="SAVE20" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a] uppercase" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Description (optional)</label>
            <input v-model="form.description" type="text" placeholder="e.g. 20% off first order" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Type</label>
              <select v-model="form.discount_type" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]">
                <option value="percent">Percent</option>
                <option value="fixed">Fixed (₹)</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Value</label>
              <input v-model.number="form.discount_value" type="number" step="0.01" min="0" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Min order (₹, optional)</label>
            <input v-model.number="form.min_amount" type="number" step="0.01" min="0" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Max uses (optional)</label>
            <input v-model.number="form.max_uses" type="number" min="1" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Valid from</label>
              <input v-model="form.valid_from" type="date" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Valid until</label>
              <input v-model="form.valid_until" type="date" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Applicable to</label>
            <select v-model="form.applicable_to" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]">
              <option value="">All</option>
              <option value="access">Access plans</option>
              <option value="collaboration">Collaborations</option>
              <option value="booking">Studio bookings</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <input id="coupon-active" v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]" />
            <label for="coupon-active" class="text-sm text-[#64748b]">Active</label>
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
const form = reactive({
  code: '',
  description: '',
  discount_type: 'percent',
  discount_value: 10,
  min_amount: null,
  max_uses: null,
  valid_from: '',
  valid_until: '',
  is_active: true,
  applicable_to: '',
});

function formatDate(val) {
  if (!val) return '—';
  const d = new Date(val);
  return isNaN(d.getTime()) ? '—' : d.toLocaleDateString();
}

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/coupons', { withCredentials: true });
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}

function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.code = item.code || '';
    form.description = item.description || '';
    form.discount_type = item.discount_type || 'percent';
    form.discount_value = item.discount_value ?? 10;
    form.min_amount = item.min_amount ?? null;
    form.max_uses = item.max_uses ?? null;
    form.valid_from = item.valid_from ? item.valid_from.slice(0, 10) : '';
    form.valid_until = item.valid_until ? item.valid_until.slice(0, 10) : '';
    form.is_active = item.is_active !== false;
    form.applicable_to = item.applicable_to || '';
  } else {
    form.code = '';
    form.description = '';
    form.discount_type = 'percent';
    form.discount_value = 10;
    form.min_amount = null;
    form.max_uses = null;
    form.valid_from = '';
    form.valid_until = '';
    form.is_active = true;
    form.applicable_to = '';
  }
  showModal.value = true;
}

async function save() {
  try {
    const payload = {
      code: form.code.trim(),
      description: form.description || null,
      discount_type: form.discount_type,
      discount_value: form.discount_value,
      min_amount: form.min_amount || null,
      max_uses: form.max_uses || null,
      valid_from: form.valid_from || null,
      valid_until: form.valid_until || null,
      is_active: form.is_active,
      applicable_to: form.applicable_to || null,
    };
    if (editing.value) {
      await axios.put('/api/admin/coupons/' + editing.value.id, payload, { withCredentials: true });
    } else {
      await axios.post('/api/admin/coupons', payload, { withCredentials: true });
    }
    showModal.value = false;
    load();
  } catch (e) {
    const msg = e.response?.data?.message || e.response?.data?.errors?.code?.[0] || 'Error saving';
    alert(msg);
  }
}

async function remove(item) {
  if (!confirm('Delete coupon "' + item.code + '"?')) return;
  try {
    await axios.delete('/api/admin/coupons/' + item.id, { withCredentials: true });
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}

onMounted(load);
</script>
