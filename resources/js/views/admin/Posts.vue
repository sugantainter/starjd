<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Blog Posts</h1>
      <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Post</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Title</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">Published</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.title }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]"><span :class="item.status === 'published' ? 'text-emerald-600' : 'text-amber-600'">{{ item.status }}</span></td>
            <td class="px-4 py-3 text-sm text-[#64748b]">{{ item.published_at ? formatDate(item.published_at) : '—' }}</td>
            <td class="px-4 py-3 text-right"><a :href="'/blog/' + item.slug" target="_blank" class="text-[#64748b] hover:underline">View</a><button type="button" class="ml-3 text-[#e63946] hover:underline" @click="openForm(item)">Edit</button><button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Post Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4" @click.self="showModal = false">
      <div class="my-8 w-full max-w-3xl max-h-[95vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Post</h2>
        <form @submit.prevent="save" class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Title</label>
            <input v-model="form.title" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Post title" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug</label>
            <input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="auto from title" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Excerpt</label>
            <input v-model="form.excerpt" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Short summary for listings" maxlength="500" />
          </div>

          <!-- SEO -->
          <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] p-3">
            <h3 class="mb-2 text-sm font-semibold text-[#1a1a1a]">SEO</h3>
            <div class="space-y-2">
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta title</label>
                <input v-model="form.meta_title" type="text" class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]" placeholder="Recommended 50–60 characters" maxlength="70" />
              </div>
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta description</label>
                <textarea v-model="form.meta_description" rows="2" class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]" placeholder="Recommended 150–160 characters" maxlength="160"></textarea>
              </div>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Body</label>
            <RichTextEditor
              v-model="form.body"
              placeholder="Write your content. Use the toolbar for bold, italic, links, images, and YouTube embeds."
              upload-image-url="/api/admin/posts/upload"
            />
          </div>

          <!-- Featured image: upload or URL -->
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Featured image</label>
            <div class="flex flex-wrap items-end gap-3">
              <div class="flex-1 min-w-0">
                <input v-model="form.image" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a]" placeholder="Image URL or upload below" />
              </div>
              <div class="flex items-center gap-2">
                <input ref="featuredImageInput" type="file" accept="image/*" class="hidden" @change="onFeaturedImageSelect" />
                <button type="button" class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="featuredImageInput?.click()">Upload image</button>
              </div>
            </div>
            <p v-if="form.image" class="mt-1.5 text-xs text-[#64748b]">Preview:</p>
            <img v-if="form.image" :src="form.image" alt="Featured" class="mt-1 max-h-32 rounded-lg border border-[#e2e8f0] object-cover" />
          </div>

          <!-- Category label (primary) -->
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Primary category</label>
            <input v-model="form.category_label" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="e.g. For Creators" />
          </div>

          <!-- Category tags -->
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Category tags</label>
            <div class="flex flex-wrap gap-2 rounded-lg border border-[#e2e8f0] bg-white px-2 py-2">
              <span
                v-for="(tag, idx) in form.category_tags"
                :key="idx"
                class="inline-flex items-center gap-1 rounded-full bg-[#e63946]/10 px-2.5 py-0.5 text-sm text-[#e63946]"
              >
                {{ tag }}
                <button type="button" class="ml-0.5 rounded-full hover:bg-[#e63946]/20" @click="removeTag(idx)" aria-label="Remove tag">&times;</button>
              </span>
              <input
                v-model="tagInput"
                type="text"
                class="min-w-[120px] flex-1 rounded border-0 bg-transparent px-1 py-0.5 text-sm text-[#1a1a1a] focus:ring-0"
                placeholder="Add tag and press Enter"
                maxlength="50"
                @keydown.enter.prevent="addTag"
              />
            </div>
            <p class="mt-1 text-xs text-[#64748b]">Press Enter to add a tag.</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Status</label>
            <select v-model="form.status" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]">
              <option value="draft">Draft</option>
              <option value="published">Published</option>
            </select>
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
const tagInput = ref('');
const featuredImageInput = ref(null);

const form = reactive({
  title: '',
  slug: '',
  excerpt: '',
  meta_title: '',
  meta_description: '',
  body: '',
  image: '',
  category_label: '',
  category_tags: [],
  status: 'draft',
});

function formatDate(s) {
  if (!s) return '—';
  try {
    return new Date(s).toLocaleDateString();
  } catch {
    return s;
  }
}

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/posts');
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}

function openForm(item = null) {
  editing.value = item;
  tagInput.value = '';
  if (item) {
    form.title = item.title || '';
    form.slug = item.slug || '';
    form.excerpt = item.excerpt || '';
    form.meta_title = item.meta_title || '';
    form.meta_description = item.meta_description || '';
    form.body = item.body || '';
    form.image = item.image || '';
    form.category_label = item.category_label || '';
    form.category_tags = Array.isArray(item.category_tags) ? [...item.category_tags] : [];
    form.status = item.status || 'draft';
  } else {
    form.title = '';
    form.slug = '';
    form.excerpt = '';
    form.meta_title = '';
    form.meta_description = '';
    form.body = '';
    form.image = '';
    form.category_label = '';
    form.category_tags = [];
    form.status = 'draft';
  }
  showModal.value = true;
}

function addTag() {
  const t = tagInput.value?.trim();
  if (!t || form.category_tags.includes(t)) return;
  form.category_tags.push(t);
  tagInput.value = '';
}

function removeTag(idx) {
  form.category_tags.splice(idx, 1);
}

async function onFeaturedImageSelect(ev) {
  const file = ev.target.files?.[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('image', file);
  try {
    const { data } = await axios.post('/api/admin/posts/upload', fd, {
      headers: { 'Content-Type': 'multipart/form-data', 'X-Requested-With': 'XMLHttpRequest' },
    });
    form.image = data.url || '';
  } catch (e) {
    alert(e.response?.data?.message || 'Image upload failed');
  }
  ev.target.value = '';
}

async function save() {
  try {
    const payload = {
      title: form.title,
      slug: form.slug || undefined,
      excerpt: form.excerpt || undefined,
      meta_title: form.meta_title || undefined,
      meta_description: form.meta_description || undefined,
      body: form.body,
      image: form.image || undefined,
      category_label: form.category_label || undefined,
      category_tags: form.category_tags.length ? form.category_tags : undefined,
      status: form.status,
    };
    if (editing.value) {
      await axios.put('/api/admin/posts/' + editing.value.id, payload);
    } else {
      await axios.post('/api/admin/posts', payload);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving');
  }
}

async function remove(item) {
  if (!confirm('Delete this post?')) return;
  try {
    await axios.delete('/api/admin/posts/' + item.id);
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}

onMounted(load);
</script>
