<template>
  <div>
    <div class="mb-6 flex items-center gap-4">
      <router-link to="/studio/studios" class="text-sm text-[#64748b] hover:text-[#1a1a1a]">← My studios</router-link>
    </div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Add studio</h1>
    <p class="mt-1 text-[#64748b]">List a new studio to start receiving bookings.</p>

    <form @submit.prevent="submit" class="mt-6 max-w-2xl space-y-4 rounded-xl border border-[#e2e8f0] bg-white p-6">
      <div v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Studio name <span class="text-red-500">*</span></label>
        <input v-model="form.name" type="text" required maxlength="255" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="e.g. Downtown Photo Studio" />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Category <span class="text-red-500">*</span></label>
        <select v-model.number="form.category_id" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
          <option :value="null">Select category</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Cancellation policy</label>
        <select v-model="form.cancellation_policy" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
          <option value="flexible">Flexible (full refund 24h before)</option>
          <option value="moderate">Moderate (full refund 72h before)</option>
          <option value="strict">Strict (full refund 7 days before)</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Amenities</label>
        <div class="mt-2 flex flex-wrap gap-2 rounded-xl border border-[#e5e7eb] p-3">
          <label v-for="a in amenities" :key="a.id" class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition" :class="form.amenity_ids.includes(a.id) ? 'border-[#e63946] bg-red-50 text-[#e63946]' : 'border-[#e2e8f0] hover:bg-[#f8fafc]'">
            <input v-model="form.amenity_ids" type="checkbox" :value="a.id" class="rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]" />
            <span>{{ a.name }}</span>
          </label>
        </div>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Description</label>
        <RichTextEditor
          v-model="form.description"
          placeholder="Describe your studio, equipment, and what it's best for. Use the toolbar for bold, italic, lists, and headings."
        />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Address</label>
        <input v-model="form.address" type="text" maxlength="500" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="Street address" />
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">City</label>
          <input v-model="form.city" type="text" maxlength="100" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="City" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">State</label>
          <input v-model="form.state" type="text" maxlength="100" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="State" />
        </div>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Pincode</label>
        <input v-model="form.pincode" type="text" maxlength="20" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="Pincode / ZIP" />
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Price per hour (₹)</label>
          <input v-model.number="form.price_per_hour" type="number" min="0" step="0.01" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="0" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Price per day (₹)</label>
          <input v-model.number="form.price_per_day" type="number" min="0" step="0.01" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="0" />
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" :disabled="loading" class="rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f] disabled:opacity-50">
          {{ loading ? 'Saving…' : 'Add studio' }}
        </button>
        <router-link to="/studio/studios" class="rounded-xl border border-[#e5e7eb] px-6 py-2.5 text-sm font-medium text-[#1a1a1a] hover:bg-[#f1f5f9]">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const router = useRouter();
const categories = ref([]);
const amenities = ref([]);
const loading = ref(false);
const error = ref('');

const form = reactive({
  name: '',
  category_id: null,
  cancellation_policy: 'moderate',
  amenity_ids: [],
  description: '',
  address: '',
  city: '',
  state: '',
  pincode: '',
  price_per_hour: null,
  price_per_day: null,
});

onMounted(async () => {
  try {
    const [catRes, amRes] = await Promise.all([
      axios.get('/api/studios/categories', { withCredentials: true }),
      axios.get('/api/amenities', { withCredentials: true }),
    ]);
    categories.value = catRes.data?.data ?? catRes.data ?? [];
    amenities.value = amRes.data?.data ?? amRes.data ?? [];
  } catch (_) {
    categories.value = [];
    amenities.value = [];
  }
});

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const payload = {
      name: form.name,
      category_id: form.category_id,
      cancellation_policy: form.cancellation_policy,
      amenity_ids: form.amenity_ids.length ? form.amenity_ids : null,
      description: form.description || null,
      address: form.address || null,
      city: form.city || null,
      state: form.state || null,
      pincode: form.pincode || null,
      price_per_hour: form.price_per_hour ?? null,
      price_per_day: form.price_per_day ?? null,
    };
    const res = await axios.post('/api/studio-owner/studios', payload, {
      headers: token ? { 'X-CSRF-TOKEN': token } : {},
      withCredentials: true,
    });
    const newId = res.data?.id;
    if (newId) {
      router.push('/studio/studios/' + newId + '/edit');
    } else {
      router.push('/studio/studios');
    }
  } catch (e) {
    error.value = e.response?.data?.message || (e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(' ') : 'Failed to add studio.');
  } finally {
    loading.value = false;
  }
}
</script>
