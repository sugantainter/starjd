<template>
  <div>
    <AdminPageHeader
      title="Success Stories"
      description="Manage success stories categorized by roles. Showcase how StarJD helps Creators, Brands, and Professionals."
      :breadcrumbs="[{ label: 'Content & CMS', path: '/admin' }, { label: 'Success Stories', path: '/admin/success-stories' }]"
    >
      <template #actions>
        <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Story</button>
      </template>
    </AdminPageHeader>

    <div v-if="loading" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white">
      <AdminTableSkeleton :columns="5" :rows="6" />
    </div>
    <div v-else-if="items.length" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Story Title</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Role</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Featured</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">
              <div class="flex items-center gap-3">
                <img v-if="item.image" :src="item.image" class="h-10 w-10 rounded object-cover border border-[#e2e8f0]" />
                <div v-else class="flex h-10 w-10 items-center justify-center rounded border border-[#e2e8f0] bg-[#f8fafc] text-[10px] text-[#94a3b8]">No IMG</div>
                <span>{{ item.title }}</span>
              </div>
            </td>
            <td class="px-4 py-3 text-sm text-[#64748b]">
              <span v-if="item.role" class="rounded-full bg-[#f1f5f9] px-2.5 py-0.5 text-xs font-medium text-[#64748b]">{{ item.role.name }}</span>
              <span v-else>—</span>
            </td>
            <td class="px-4 py-3">
              <span v-if="item.is_featured" class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">★ Featured</span>
              <span v-else class="text-[#94a3b8]">—</span>
            </td>
            <td class="px-4 py-3">
              <span :class="item.status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium">{{ item.status }}</span>
            </td>
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
      title="No stories yet"
      description="Share success cases to build trust. Categorize them by role and feature the best ones on the home page."
    >
      <button type="button" class="mt-4 rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add first story</button>
    </AdminEmptyState>

    <AdminConfirmModal
      :open="showDeleteModal"
      title="Delete story"
      message="This success story will be permanently deleted. This cannot be undone."
      confirm-label="Delete"
      @close="showDeleteModal = false; itemToRemove = null"
      @confirm="doRemove"
    />

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4" @click.self="showModal = false">
      <div class="my-8 w-full max-w-3xl max-h-[95vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Success Story</h2>
        <form @submit.prevent="save" class="space-y-4">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Title</label>
              <input v-model="form.title" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Story title" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug (URL Path)</label>
              <input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Leave empty for auto-slug" />
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-3">
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Category/Role</label>
              <select v-model="form.role_id" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]">
                <option value="">Select Role</option>
                <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Author Name</label>
              <input v-model="form.author_name" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. John Doe" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Author Designation</label>
              <input v-model="form.author_designation" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. Creator / Brand Manager" />
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Featured Image</label>
            <div class="flex flex-wrap items-end gap-3">
              <div class="flex-1 min-w-0">
                <input v-model="form.image" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a]" placeholder="Image URL or upload" />
              </div>
              <div class="flex items-center gap-2">
                <input ref="imageInput" type="file" accept="image/*" class="hidden" @change="onImageSelect" />
                <button type="button" class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="imageInput?.click()">Upload</button>
              </div>
            </div>
            <img v-if="form.image" :src="form.image" class="mt-2 max-h-32 rounded border border-[#e2e8f0] object-cover" />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Content</label>
            <RichTextEditor
              v-model="form.content"
              placeholder="Tell the story..."
              upload-image-url="/api/admin/success-stories/upload"
            />
          </div>

          <div class="flex flex-wrap gap-6 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] p-4">
             <label class="flex items-center gap-2 font-medium text-[#1a1a1a]">
                <input v-model="form.is_featured" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#e63946] focus:ring-[#e63946]" />
                <span class="text-sm">Feature on Home Page</span>
              </label>

              <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-[#1a1a1a]">Status</label>
                <select v-model="form.status" class="rounded-lg border border-[#e2e8f0] px-3 py-1.5 text-sm text-[#1a1a1a]">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                </select>
              </div>
          </div>

          <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] p-3">
            <h3 class="mb-2 text-sm font-semibold text-[#1a1a1a]">SEO</h3>
            <div class="space-y-2">
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta title</label>
                <input v-model="form.meta_title" type="text" class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]" maxlength="70" />
              </div>
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta description</label>
                <textarea v-model="form.meta_description" rows="2" class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]" maxlength="160"></textarea>
              </div>
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
import AdminPageHeader from '../../components/admin/AdminPageHeader.vue';
import AdminEmptyState from '../../components/admin/AdminEmptyState.vue';
import AdminTableSkeleton from '../../components/admin/AdminTableSkeleton.vue';
import AdminConfirmModal from '../../components/admin/AdminConfirmModal.vue';
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const items = ref([]);
const roles = ref([]);
const loading = ref(true);
const showModal = ref(false);
const showDeleteModal = ref(false);
const itemToRemove = ref(null);
const editing = ref(null);
const imageInput = ref(null);

const form = reactive({
  title: '',
  slug: '',
  content: '',
  image: '',
  role_id: '',
  author_name: '',
  author_designation: '',
  is_featured: false,
  status: 'draft',
  meta_title: '',
  meta_description: '',
});

async function loadRoles() {
  const r = await axios.get('/api/admin/roles'); // I need to verify if this exists
  roles.value = r.data;
}

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/success-stories');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}

function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.title = item.title || '';
    form.slug = item.slug || '';
    form.content = item.content || '';
    form.image = item.image || '';
    form.role_id = item.role_id || '';
    form.author_name = item.author_name || '';
    form.author_designation = item.author_designation || '';
    form.is_featured = !!item.is_featured;
    form.status = item.status || 'draft';
    form.meta_title = item.meta_title || '';
    form.meta_description = item.meta_description || '';
  } else {
    Object.assign(form, {
      title: '', slug: '', content: '', image: '', role_id: '',
      author_name: '', author_designation: '', is_featured: false, status: 'draft',
      meta_title: '', meta_description: '',
    });
  }
  showModal.value = true;
}

async function onImageSelect(ev) {
  const file = ev.target.files?.[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('image', file);
  try {
    const { data } = await axios.post('/api/admin/success-stories/upload', fd);
    form.image = data.url;
  } catch (e) {
    alert('Upload failed: ' + (e.response?.data?.message || 'Error'));
  }
  ev.target.value = '';
}

async function save() {
  try {
    const payload = { ...form };
    if (editing.value) {
      await axios.put('/api/admin/success-stories/' + editing.value.id, payload);
    } else {
      await axios.post('/api/admin/success-stories', payload);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving story');
  }
}

function confirmRemove(item) {
  itemToRemove.value = item;
  showDeleteModal.value = true;
}

async function doRemove() {
  if (!itemToRemove.value) return;
  try {
    await axios.delete('/api/admin/success-stories/' + itemToRemove.value.id);
    showDeleteModal.value = false;
    itemToRemove.value = null;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}

onMounted(() => {
  loadRoles();
  load();
});
</script>
