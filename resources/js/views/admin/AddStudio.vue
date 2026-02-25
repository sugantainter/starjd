<template>
  <div>
    <div class="mb-6 flex items-center gap-4">
      <router-link to="/admin/studios" class="text-sm text-[#64748b] hover:text-[#1a1a1a]">← Studios</router-link>
    </div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Add studio</h1>
    <p class="mt-1 text-[#64748b]">Create a studio and assign it to a studio owner. They can add photos and availability from their dashboard.</p>

    <form @submit.prevent="submit" class="mt-6 max-w-2xl space-y-4 rounded-xl border border-[#e2e8f0] bg-white p-6">
      <div v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>

      <div class="relative">
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Studio owner <span class="text-red-500">*</span></label>
        <div class="relative">
          <input
            :value="selectedOwner ? selectedOwner.name + ' (' + selectedOwner.email + ')' : ownerSearch"
            type="text"
            autocomplete="off"
            class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 pr-20 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
            :placeholder="!selectedOwner ? 'Search by name or email…' : ''"
            :readonly="!!selectedOwner"
            @focus="ownerDropdownOpen = true"
            @blur="setTimeout(() => { ownerDropdownOpen = false }, 200)"
            @input="ownerSearch = $event.target.value; if (selectedOwner) { selectedOwner = null; form.user_id = null }"
          />
          <button
            v-if="selectedOwner"
            type="button"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs font-medium text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]"
            @click="clearOwner"
          >
            Clear
          </button>
        </div>
        <div
          v-show="ownerDropdownOpen && !selectedOwner"
          class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-xl border border-[#e2e8f0] bg-white shadow-lg"
        >
          <button
            v-for="u in filteredOwners"
            :key="u.id"
            type="button"
            class="w-full px-4 py-3 text-left text-sm hover:bg-[#f8fafc] focus:bg-[#f1f5f9] focus:outline-none"
            @click="selectOwner(u)"
          >
            <span class="font-medium text-[#1a1a1a]">{{ u.name }}</span>
            <span class="ml-2 text-[#64748b]">{{ u.email }}</span>
          </button>
          <p v-if="filteredOwners.length === 0 && !loadingOwners" class="px-4 py-3 text-sm text-[#64748b]">
            {{ ownerSearch.trim() ? 'No matching owner.' : 'Type to search.' }}
          </p>
        </div>
        <p v-if="!owners.length && !loadingOwners" class="mt-1 text-xs text-amber-600">No studio owners yet. Users must register as studio owners first.</p>
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Status</label>
        <select v-model="form.status" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
          <option value="draft">Draft (owner can edit; not visible on marketplace)</option>
          <option value="active">Active (visible on marketplace immediately)</option>
        </select>
      </div>

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
          <option value="flexible">Flexible</option>
          <option value="moderate">Moderate</option>
          <option value="strict">Strict</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Amenities</label>
        <div class="mt-2 flex flex-wrap gap-2 rounded-xl border border-[#e5e7eb] p-3">
          <label v-for="a in amenities" :key="a.id" class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition" :class="form.amenity_ids.includes(a.id) ? 'border-[#e63946] bg-red-50' : 'border-[#e2e8f0] hover:bg-[#f8fafc]'">
            <input v-model="form.amenity_ids" type="checkbox" :value="a.id" class="rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]" />
            <span>{{ a.name }}</span>
          </label>
        </div>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Description</label>
        <RichTextEditor
          v-model="form.description"
          placeholder="Describe the studio. Use the toolbar for bold, italic, lists, and headings."
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
        <button type="submit" :disabled="loading || !owners.length || !form.user_id" class="rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f] disabled:opacity-50">
          {{ loading ? 'Saving…' : 'Add studio' }}
        </button>
        <router-link to="/admin/studios" class="rounded-xl border border-[#e5e7eb] px-6 py-2.5 text-sm font-medium text-[#1a1a1a] hover:bg-[#f1f5f9]">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const router = useRouter();
const owners = ref([]);
const categories = ref([]);
const amenities = ref([]);
const loadingOwners = ref(true);
const loading = ref(false);
const error = ref('');
const ownerSearch = ref('');
const ownerDropdownOpen = ref(false);
const selectedOwner = ref(null);

const filteredOwners = computed(() => {
  const q = ownerSearch.value.trim().toLowerCase();
  if (!q) return owners.value;
  return owners.value.filter(
    (u) =>
      (u.name || '').toLowerCase().includes(q) ||
      (u.email || '').toLowerCase().includes(q)
  );
});

function selectOwner(u) {
  selectedOwner.value = u;
  form.user_id = u.id;
  ownerSearch.value = '';
  ownerDropdownOpen.value = false;
}

function clearOwner() {
  selectedOwner.value = null;
  form.user_id = null;
  ownerSearch.value = '';
  ownerDropdownOpen.value = true;
}

const form = reactive({
  user_id: null,
  status: 'draft',
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
  loadingOwners.value = true;
  try {
    const [ownersRes, catRes, amRes] = await Promise.all([
      axios.get('/api/admin/studio-owners', { withCredentials: true }),
      axios.get('/api/studios/categories', { withCredentials: true }),
      axios.get('/api/amenities', { withCredentials: true }),
    ]);
    owners.value = ownersRes.data ?? [];
    categories.value = catRes.data?.data ?? catRes.data ?? [];
    amenities.value = amRes.data?.data ?? amRes.data ?? [];
  } catch (_) {
    owners.value = [];
    categories.value = [];
    amenities.value = [];
  } finally {
    loadingOwners.value = false;
  }
});

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const payload = {
      user_id: form.user_id,
      status: form.status,
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
    await axios.post('/api/admin/studios', payload, {
      headers: token ? { 'X-CSRF-TOKEN': token } : {},
      withCredentials: true,
    });
    router.push('/admin/studios');
  } catch (e) {
    error.value = e.response?.data?.message || (e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(' ') : 'Failed to add studio.');
  } finally {
    loading.value = false;
  }
}
</script>
