<template>
  <div>
    <div class="mb-6 flex items-center gap-4">
      <router-link to="/studio/studios" class="text-sm text-[#64748b] hover:text-[#1a1a1a]">← My studios</router-link>
    </div>
    <div v-if="loadingStudio" class="text-[#64748b]">Loading…</div>
    <div v-else-if="studio" class="space-y-8">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Edit studio</h1>
      <p class="mt-1 text-[#64748b]">{{ studio.name }}</p>

      <!-- Basic info -->
      <section class="rounded-xl border border-[#e2e8f0] bg-white p-6">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Basic info</h2>
        <form @submit.prevent="saveBasic" class="mt-4 max-w-2xl space-y-4">
          <div v-if="basicError" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ basicError }}</div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Studio name <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" required maxlength="255" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
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
              <label v-for="a in amenitiesList" :key="a.id" class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition" :class="form.amenity_ids.includes(a.id) ? 'border-[#e63946] bg-red-50' : 'border-[#e2e8f0] hover:bg-[#f8fafc]'">
                <input v-model="form.amenity_ids" type="checkbox" :value="a.id" class="rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]" />
                <span>{{ a.name }}</span>
              </label>
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Description</label>
            <RichTextEditor
              v-model="form.description"
              placeholder="Describe your studio. Use the toolbar for bold, italic, lists, and headings."
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Address</label>
            <input v-model="form.address" type="text" maxlength="500" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">City</label>
              <input v-model="form.city" type="text" maxlength="100" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">State</label>
              <input v-model="form.state" type="text" maxlength="100" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Pincode</label>
            <input v-model="form.pincode" type="text" maxlength="20" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Latitude (for map)</label>
              <input v-model.number="form.latitude" type="number" step="any" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="e.g. 28.6139" />
              <p class="mt-0.5 text-xs text-[#64748b]">Optional. Used to show this studio on the Studios map.</p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Longitude (for map)</label>
              <input v-model.number="form.longitude" type="number" step="any" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="e.g. 77.2090" />
            </div>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button
              type="button"
              :disabled="locationLoading"
              class="inline-flex items-center gap-2 rounded-xl border border-[#e2e8f0] px-4 py-2 text-sm font-medium text-[#1a1a1a] transition hover:border-[#0ea5e9] hover:bg-[#f0f9ff] hover:text-[#0369a1] disabled:opacity-50"
              @click="useMyLocation"
            >
              <svg v-if="locationLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
              <svg v-else class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              {{ locationLoading ? 'Getting location…' : 'Use my location' }}
            </button>
            <p v-if="locationError" class="text-sm text-red-600">{{ locationError }}</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Price per hour (₹)</label>
              <input v-model.number="form.price_per_hour" type="number" min="0" step="0.01" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Price per day (₹)</label>
              <input v-model.number="form.price_per_day" type="number" min="0" step="0.01" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
            </div>
          </div>
          <button type="submit" :disabled="savingBasic" class="rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f] disabled:opacity-50">
            {{ savingBasic ? 'Saving…' : 'Save changes' }}
          </button>
        </form>
      </section>

      <!-- Images -->
      <section id="photos" class="rounded-xl border border-[#e2e8f0] bg-white p-6 scroll-mt-6">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Photos</h2>
        <p class="mt-1 text-sm text-[#64748b]">Upload images; first one is shown as cover. Set one as primary.</p>
        <div class="mt-4 flex flex-wrap gap-4">
          <div v-for="img in images" :key="img.id" class="relative rounded-xl border border-[#e2e8f0] overflow-hidden w-40 h-28 bg-[#f1f5f9]">
            <img :src="img.image" :alt="img.caption" class="h-full w-full object-cover" />
            <span v-if="img.is_primary" class="absolute left-2 top-2 rounded bg-[#e63946] px-2 py-0.5 text-xs text-white">Primary</span>
            <div class="absolute bottom-0 left-0 right-0 flex gap-1 p-2 bg-black/50">
              <button type="button" v-if="!img.is_primary" class="rounded px-2 py-1 text-xs text-white hover:bg-white/20" @click="setPrimary(img.id)">Set primary</button>
              <button type="button" class="rounded px-2 py-1 text-xs text-red-200 hover:bg-red-600/80" @click="removeImage(img.id)">Remove</button>
            </div>
          </div>
          <label class="flex h-28 w-40 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc] hover:border-[#e63946] hover:bg-red-50/30">
            <span class="text-sm text-[#64748b]">+ Upload</span>
            <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onImageSelect" />
          </label>
        </div>
        <p v-if="uploadError" class="mt-2 text-sm text-red-600">{{ uploadError }}</p>
      </section>

      <!-- Availability -->
      <section id="availability" class="rounded-xl border border-[#e2e8f0] bg-white p-6 scroll-mt-6">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Availability</h2>
        <p class="mt-1 text-sm text-[#64748b]">Add time slots when your studio is available for booking.</p>
        <form @submit.prevent="addSlot" class="mt-4 flex flex-wrap items-end gap-4 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-4">
          <div>
            <label class="mb-1 block text-xs font-medium text-[#64748b]">Date</label>
            <input v-model="slotForm.date" type="date" required class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-[#64748b]">Start</label>
            <input v-model="slotForm.start_time" type="time" required class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-[#64748b]">End</label>
            <input v-model="slotForm.end_time" type="time" required class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm" />
          </div>
          <button type="submit" :disabled="addingSlot" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f] disabled:opacity-50">
            {{ addingSlot ? 'Adding…' : 'Add slot' }}
          </button>
        </form>
        <p v-if="slotError" class="mt-2 text-sm text-red-600">{{ slotError }}</p>
        <ul class="mt-4 space-y-2">
          <li v-for="s in slots" :key="s.id" class="flex items-center justify-between rounded-lg border border-[#e2e8f0] px-4 py-2">
            <span class="text-sm">{{ s.date }} {{ s.start_time }}–{{ s.end_time }}</span>
            <button type="button" class="text-sm text-red-600 hover:underline" @click="removeSlot(s.id)">Remove</button>
          </li>
        </ul>
        <p v-if="!slots.length && !loadingSlots" class="mt-4 text-sm text-[#64748b]">No slots added yet. Add dates and times when your studio is available.</p>
        <p v-if="loadingSlots" class="mt-4 text-sm text-[#64748b]">Loading slots…</p>
      </section>
    </div>
    <div v-else class="text-[#64748b]">Studio not found.</div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const route = useRoute();
const studioId = () => Number(route.params.id);

function scrollToSection() {
  const id = route.hash ? route.hash.slice(1) : '';
  if (!id) return;
  nextTick(() => {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
}

const studio = ref(null);
const loadingStudio = ref(true);
const categories = ref([]);
const amenitiesList = ref([]);
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
  latitude: null,
  longitude: null,
  price_per_hour: null,
  price_per_day: null,
});
const basicError = ref('');
const savingBasic = ref(false);
const locationLoading = ref(false);
const locationError = ref('');

const images = ref([]);
const uploadError = ref('');

const slots = ref([]);
const loadingSlots = ref(false);
const slotForm = reactive({ date: '', start_time: '09:00', end_time: '18:00' });
const slotError = ref('');
const addingSlot = ref(false);

function getToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

async function loadStudio() {
  loadingStudio.value = true;
  try {
    const [studioRes, catRes, amRes] = await Promise.all([
      axios.get('/api/studio-owner/studios/' + studioId(), { withCredentials: true }),
      axios.get('/api/studios/categories', { withCredentials: true }),
      axios.get('/api/amenities', { withCredentials: true }),
    ]);
    studio.value = studioRes.data;
    categories.value = catRes.data?.data ?? catRes.data ?? [];
    amenitiesList.value = amRes.data?.data ?? amRes.data ?? [];
    images.value = studio.value.images ?? [];
    form.name = studio.value.name ?? '';
    form.category_id = studio.value.category_id ?? null;
    form.cancellation_policy = studio.value.cancellation_policy ?? 'moderate';
    form.amenity_ids = Array.isArray(studio.value.amenity_ids) ? [...studio.value.amenity_ids] : [];
    form.description = studio.value.description ?? '';
    form.address = studio.value.address ?? '';
    form.city = studio.value.city ?? '';
    form.state = studio.value.state ?? '';
    form.pincode = studio.value.pincode ?? '';
    form.latitude = studio.value.latitude ?? null;
    form.longitude = studio.value.longitude ?? null;
    form.price_per_hour = studio.value.price_per_hour ?? null;
    form.price_per_day = studio.value.price_per_day ?? null;
  } catch {
    studio.value = null;
  } finally {
    loadingStudio.value = false;
  }
}

async function loadSlots() {
  if (!studioId()) return;
  loadingSlots.value = true;
  try {
    const res = await axios.get('/api/studio-owner/studios/' + studioId() + '/availability', { withCredentials: true });
    slots.value = res.data?.slots ?? [];
  } catch {
    slots.value = [];
  } finally {
    loadingSlots.value = false;
  }
}

onMounted(() => {
  loadStudio();
});
watch(() => route.params.id, () => {
  if (route.name === 'studio-edit') {
    loadStudio();
    loadSlots();
  }
}, { immediate: false });
watch(studio, (s) => {
  if (s) {
    loadSlots();
    nextTick(() => nextTick(scrollToSection));
  }
}, { immediate: true });
watch(() => route.hash, () => nextTick(scrollToSection));

function useMyLocation() {
  if (!navigator.geolocation) {
    locationError.value = 'Geolocation is not supported by your browser.';
    return;
  }
  locationError.value = '';
  locationLoading.value = true;
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      form.latitude = Math.round(pos.coords.latitude * 1e6) / 1e6;
      form.longitude = Math.round(pos.coords.longitude * 1e6) / 1e6;
      locationLoading.value = false;
    },
    (err) => {
      locationLoading.value = false;
      if (err.code === 1) locationError.value = 'Location permission denied.';
      else if (err.code === 2) locationError.value = 'Location unavailable.';
      else locationError.value = 'Could not get your location.';
    },
    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
  );
}

async function saveBasic() {
  savingBasic.value = true;
  basicError.value = '';
  try {
    await axios.put('/api/studio-owner/studios/' + studioId(), {
      name: form.name,
      category_id: form.category_id,
      cancellation_policy: form.cancellation_policy,
      amenity_ids: form.amenity_ids.length ? form.amenity_ids : [],
      description: form.description || null,
      address: form.address || null,
      city: form.city || null,
      state: form.state || null,
      pincode: form.pincode || null,
      latitude: form.latitude != null && form.latitude !== '' ? Number(form.latitude) : null,
      longitude: form.longitude != null && form.longitude !== '' ? Number(form.longitude) : null,
      price_per_hour: form.price_per_hour ?? null,
      price_per_day: form.price_per_day ?? null,
    }, { headers: { 'X-CSRF-TOKEN': getToken() }, withCredentials: true });
    studio.value = { ...studio.value, name: form.name };
  } catch (e) {
    basicError.value = e.response?.data?.message || (e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(' ') : 'Failed to save.');
  } finally {
    savingBasic.value = false;
  }
}

function onImageSelect(ev) {
  const file = ev.target?.files?.[0];
  if (!file || !studioId()) return;
  uploadError.value = '';
  const fd = new FormData();
  fd.append('image', file);
  fd.append('_token', getToken());
  axios.post('/api/studio-owner/studios/' + studioId() + '/images', fd, {
    headers: { 'X-CSRF-TOKEN': getToken(), 'Accept': 'application/json' },
    withCredentials: true,
  }).then((res) => {
    images.value.push(res.data.image);
  }).catch((e) => {
    uploadError.value = e.response?.data?.message || 'Upload failed.';
  }).finally(() => {
    ev.target.value = '';
  });
}

function setPrimary(imgId) {
  const order = images.value.map((i) => i.id);
  axios.put('/api/studio-owner/studios/' + studioId() + '/images/reorder', { order, primary_id: imgId }, {
    headers: { 'X-CSRF-TOKEN': getToken(), 'Content-Type': 'application/json' },
    withCredentials: true,
  }).then((res) => {
    images.value = res.data.images ?? images.value;
  });
}

function removeImage(imgId) {
  if (!confirm('Remove this image?')) return;
  axios.delete('/api/studio-owner/studio-images/' + imgId, { headers: { 'X-CSRF-TOKEN': getToken() }, withCredentials: true }).then(() => {
    images.value = images.value.filter((i) => i.id !== imgId);
  });
}

async function addSlot() {
  slotError.value = '';
  addingSlot.value = true;
  try {
    const res = await axios.post('/api/studio-owner/studios/' + studioId() + '/availability', {
      date: slotForm.date,
      start_time: slotForm.start_time,
      end_time: slotForm.end_time,
    }, { headers: { 'X-CSRF-TOKEN': getToken(), 'Content-Type': 'application/json' }, withCredentials: true });
    const added = res.data?.slots ?? [];
    slots.value = [...slots.value, ...added];
    slotForm.date = '';
  } catch (e) {
    slotError.value = e.response?.data?.message || (e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(' ') : 'Failed to add slot.');
  } finally {
    addingSlot.value = false;
  }
}

function removeSlot(slotId) {
  axios.delete('/api/studio-owner/availability-slots/' + slotId, { headers: { 'X-CSRF-TOKEN': getToken() }, withCredentials: true }).then(() => {
    slots.value = slots.value.filter((s) => s.id !== slotId);
  });
}
</script>
