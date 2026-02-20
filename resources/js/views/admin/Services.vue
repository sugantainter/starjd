<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Services</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Service</button>
    </div>
    <p class="mb-4 text-sm text-[#64748b]">Create service categories; each has its own page. Services appear in the main nav under "Services" dropdown.</p>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Slug</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.name }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.slug }}</td>
            <td class="px-4 py-3 text-sm"><span :class="item.is_active ? 'text-emerald-600' : 'text-amber-600'">{{ item.is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="px-4 py-3 text-right">
              <a :href="'/services/' + item.slug" target="_blank" class="text-[#64748b] hover:underline">View</a>
              <button type="button" class="ml-3 text-[#e63946] hover:underline" @click="openForm(item)">Edit</button>
              <button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Service Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4" @click.self="showModal = false">
      <div class="my-8 w-full max-w-3xl max-h-[95vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Service</h2>
        <form @submit.prevent="save" class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label>
            <input v-model="form.name" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. Web Development" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug</label>
            <input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="auto from name" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Short description</label>
            <input v-model="form.short_description" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Brief summary for listings" maxlength="500" />
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Card image</label>
              <p class="mb-2 text-xs text-[#64748b]">Shown on the services listing page.</p>
              <div class="flex gap-2">
                <input ref="cardImageInput" type="file" accept="image/*" class="hidden" @change="e => onImageSelect(e, 'image')" />
                <button type="button" class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="cardImageInput?.click()">Upload</button>
                <button v-if="form.image" type="button" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600 hover:bg-red-100" @click="form.image = ''">Remove</button>
              </div>
              <div v-if="form.image" class="mt-2 flex items-center gap-2">
                <img :src="form.image" alt="Card" class="max-h-32 w-full max-w-[200px] rounded-lg border border-[#e2e8f0] object-cover" />
                <div>
                  <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Display</label>
                  <select v-model="form.image_fit" class="rounded border border-[#e2e8f0] px-2 py-1 text-sm text-[#1a1a1a]">
                    <option value="cover">Cover (fill)</option>
                    <option value="contain">Contain (fit)</option>
                  </select>
                </div>
              </div>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Banner / wallpaper image</label>
              <p class="mb-2 text-xs text-[#64748b]">Hero image on the service detail page.</p>
              <div class="flex gap-2">
                <input ref="bannerImageInput" type="file" accept="image/*" class="hidden" @change="e => onImageSelect(e, 'banner_image')" />
                <button type="button" class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="bannerImageInput?.click()">Upload</button>
                <button v-if="form.banner_image" type="button" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600 hover:bg-red-100" @click="form.banner_image = ''">Remove</button>
              </div>
              <div v-if="form.banner_image" class="mt-2 flex items-center gap-2">
                <img :src="form.banner_image" alt="Banner" class="max-h-32 w-full max-w-[200px] rounded-lg border border-[#e2e8f0] object-cover" />
                <div>
                  <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Position</label>
                  <select v-model="form.banner_position" class="rounded border border-[#e2e8f0] px-2 py-1 text-sm text-[#1a1a1a]">
                    <option value="center">Center</option>
                    <option value="top">Top</option>
                    <option value="bottom">Bottom</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] p-3">
            <h3 class="mb-2 text-sm font-semibold text-[#1a1a1a]">SEO</h3>
            <div class="space-y-2">
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta title</label>
                <input v-model="form.meta_title" type="text" class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]" placeholder="50–60 chars" maxlength="70" />
              </div>
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta description</label>
                <textarea v-model="form.meta_description" rows="2" class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]" placeholder="150–160 chars" maxlength="160"></textarea>
              </div>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Page content</label>
            <RichTextEditor
              v-model="form.body"
              placeholder="Full page content for this service. Use toolbar for formatting, images, YouTube."
              upload-image-url="/api/admin/posts/upload"
            />
          </div>

          <div class="flex gap-4">
            <div class="flex-1">
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Sort order</label>
              <input v-model.number="form.sort_order" type="number" min="0" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
            </div>
            <div class="flex items-end gap-2">
              <input v-model="form.is_active" type="checkbox" id="service-active" class="h-4 w-4 rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]" />
              <label for="service-active" class="text-sm font-medium text-[#1a1a1a]">Active (show in nav)</label>
            </div>
          </div>

          <div class="flex gap-2 border-t border-[#e2e8f0] pt-4">
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
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const items = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editing = ref(null);
const cardImageInput = ref(null);
const bannerImageInput = ref(null);

const form = reactive({
  name: '',
  slug: '',
  short_description: '',
  image: '',
  banner_image: '',
  image_fit: 'cover',
  banner_position: 'center',
  body: '',
  meta_title: '',
  meta_description: '',
  sort_order: 0,
  is_active: true,
});

async function onImageSelect(ev, field) {
  const file = ev.target.files?.[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('image', file);
  try {
    const { data } = await axios.post('/api/admin/posts/upload', fd, {
      headers: { Accept: 'application/json' },
      withCredentials: true,
    });
    form[field] = data.url || '';
  } catch (e) {
    const status = e.response?.status;
    let msg = 'Upload failed.';
    if (status === 413) msg = 'File is too large. Maximum size is 2 MB. Please choose a smaller image.';
    else if (status === 401) msg = 'Please log in again.';
    else if (e.response?.data?.errors?.image?.[0]) msg = e.response.data.errors.image[0];
    else if (e.response?.data?.message) msg = e.response.data.message;
    alert(msg);
  }
  ev.target.value = '';
}

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/services');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}

function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.name = item.name || '';
    form.slug = item.slug || '';
    form.short_description = item.short_description || '';
    form.image = item.image || '';
    form.banner_image = item.banner_image || '';
    form.image_fit = item.image_fit || 'cover';
    form.banner_position = item.banner_position || 'center';
    form.body = item.body || '';
    form.meta_title = item.meta_title || '';
    form.meta_description = item.meta_description || '';
    form.sort_order = item.sort_order ?? 0;
    form.is_active = item.is_active !== false;
  } else {
    form.name = '';
    form.slug = '';
    form.short_description = '';
    form.image = '';
    form.banner_image = '';
    form.image_fit = 'cover';
    form.banner_position = 'center';
    form.body = '';
    form.meta_title = '';
    form.meta_description = '';
    form.sort_order = items.value.length ? Math.max(...items.value.map((s) => s.sort_order ?? 0), 0) + 1 : 0;
    form.is_active = true;
  }
  showModal.value = true;
}

async function save() {
  try {
    const payload = {
      name: form.name,
      slug: form.slug || undefined,
      short_description: form.short_description || undefined,
      image: form.image || undefined,
      banner_image: form.banner_image || undefined,
      image_fit: form.image_fit || undefined,
      banner_position: form.banner_position || undefined,
      body: form.body || undefined,
      meta_title: form.meta_title || undefined,
      meta_description: form.meta_description || undefined,
      sort_order: form.sort_order,
      is_active: form.is_active,
    };
    if (editing.value) {
      await axios.put('/api/admin/services/' + editing.value.id, payload);
    } else {
      await axios.post('/api/admin/services', payload);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving');
  }
}

async function remove(item) {
  if (!confirm('Delete this service?')) return;
  try {
    await axios.delete('/api/admin/services/' + item.id);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}

onMounted(load);
</script>
