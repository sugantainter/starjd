<template>
  <div>
    <AdminPageHeader
      title="Pages"
      description="Dynamic CMS pages. Create global pages or location-specific (state/city) pages."
      :breadcrumbs="[{ label: 'Content & CMS', path: '/admin' }, { label: 'Pages', path: '/admin/pages' }]"
    >
      <template #actions>
        <button type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add Page</button>
      </template>
    </AdminPageHeader>

    <div class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-[#e2e8f0] bg-white px-4 py-3 shadow-sm">
      <span class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8]">Filter by scope</span>
      <button
        type="button"
        class="rounded-full px-3 py-1.5 text-sm font-medium transition"
        :class="scopeFilter === '' ? 'bg-[#e63946] text-white' : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'"
        @click="scopeFilter = ''; scopeFilterStateId = ''; scopeFilterCityId = ''"
      >
        All
      </button>
      <button
        type="button"
        class="rounded-full px-3 py-1.5 text-sm font-medium transition"
        :class="scopeFilter === 'global' ? 'bg-[#e63946] text-white' : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'"
        @click="scopeFilter = 'global'; scopeFilterStateId = ''; scopeFilterCityId = ''"
      >
        Global
      </button>
      <select
        v-model="scopeFilterStateId"
        class="rounded-full border border-[#e2e8f0] bg-white px-3 py-1.5 text-sm text-[#1a1a1a]"
        @change="scopeFilter = scopeFilterStateId ? 'state' : ''; scopeFilterCityId = ''"
      >
        <option value="">By state…</option>
        <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>
      <select
        v-model="scopeFilterCityId"
        class="rounded-full border border-[#e2e8f0] bg-white px-3 py-1.5 text-sm text-[#1a1a1a]"
        @change="scopeFilter = scopeFilterCityId ? 'city' : scopeFilter"
      >
        <option value="">By city…</option>
        <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}, {{ c.state?.name }}</option>
      </select>
    </div>

    <div v-if="loading" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white">
      <AdminTableSkeleton :columns="5" :rows="6" />
    </div>
    <div v-else-if="items.length" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Title</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Slug</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Location</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ item.title }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b] font-mono">{{ item.slug }}</td>
            <td class="px-4 py-3 text-sm text-[#64748b]">
              <span v-if="!item.state_id && !item.city_id" class="inline-flex rounded-full bg-[#f1f5f9] px-2.5 py-0.5 text-xs font-medium text-[#64748b]">Global</span>
              <span v-else-if="item.city" class="text-[#1a1a1a]">{{ item.city.name }}, {{ item.state?.name }}</span>
              <span v-else-if="item.state" class="text-[#1a1a1a]">{{ item.state.name }} <span class="text-[#94a3b8]">(state)</span></span>
              <span v-else>—</span>
            </td>
            <td class="px-4 py-3">
              <span :class="item.status === 'published' ? 'text-emerald-600' : 'text-amber-600'" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium" :style="item.status === 'published' ? { backgroundColor: 'rgb(209 250 229)' } : { backgroundColor: 'rgb(254 243 199)' }">{{ item.status }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <a v-if="item.status === 'published'" :href="pageUrl(item)" target="_blank" class="text-[#64748b] hover:underline">View</a>
              <button type="button" class="ml-3 text-[#e63946] hover:underline" @click="openForm(item)">Edit</button>
              <button type="button" class="ml-3 text-red-600 hover:underline" @click="confirmRemove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <AdminEmptyState
      v-else
      title="No pages yet"
      description="Create a global page (same for all) or a location-specific page for a state or city."
    >
      <button type="button" class="mt-4 rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openForm()">Add first page</button>
    </AdminEmptyState>

    <AdminConfirmModal
      :open="showDeleteModal"
      title="Delete page"
      message="This page will be permanently deleted. This cannot be undone."
      confirm-label="Delete"
      @close="showDeleteModal = false; itemToRemove = null"
      @confirm="doRemove"
    />

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4" @click.self="showModal = false">
      <div class="my-8 w-full max-w-3xl max-h-[95vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-[#1a1a1a]">{{ editing ? 'Edit' : 'Add' }} Page</h2>
        <form @submit.prevent="save" class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Title</label>
            <input v-model="form.title" type="text" required class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Page title" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Slug</label>
            <input v-model="form.slug" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="URL path (e.g. about-us)" />
          </div>
          <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] p-3">
            <h3 class="mb-2 text-sm font-semibold text-[#1a1a1a]">Scope (location)</h3>
            <div class="space-y-2">
              <label class="flex items-center gap-2">
                <input v-model="scopeType" type="radio" value="global" />
                <span class="text-sm">Global — same page for all locations</span>
              </label>
              <label class="flex items-center gap-2">
                <input v-model="scopeType" type="radio" value="state" />
                <span class="text-sm">State — select state</span>
              </label>
              <select v-if="scopeType === 'state'" v-model="form.state_id" class="ml-6 rounded border border-[#e2e8f0] px-2 py-1.5 text-sm">
                <option value="">Select state</option>
                <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
              <label class="flex items-center gap-2">
                <input v-model="scopeType" type="radio" value="city" />
                <span class="text-sm">City — select city</span>
              </label>
              <template v-if="scopeType === 'city'">
                <select v-model="form.state_id" class="ml-6 mr-2 rounded border border-[#e2e8f0] px-2 py-1.5 text-sm" @change="form.city_id = ''">
                  <option value="">State first</option>
                  <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
                <select v-model="form.city_id" class="rounded border border-[#e2e8f0] px-2 py-1.5 text-sm">
                  <option value="">Select city</option>
                  <option v-for="c in citiesForState" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </template>
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Content</label>
            <RichTextEditor
              v-model="form.content"
              placeholder="Page content. Use the toolbar for formatting, links, and images."
              upload-image-url="/api/admin/posts/upload"
            />
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
import { ref, reactive, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import AdminPageHeader from '../../components/admin/AdminPageHeader.vue';
import AdminEmptyState from '../../components/admin/AdminEmptyState.vue';
import AdminTableSkeleton from '../../components/admin/AdminTableSkeleton.vue';
import AdminConfirmModal from '../../components/admin/AdminConfirmModal.vue';
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const items = ref([]);
const states = ref([]);
const cities = ref([]);
const loading = ref(true);
const showModal = ref(false);
const showDeleteModal = ref(false);
const itemToRemove = ref(null);
const editing = ref(null);
const scopeFilter = ref('');
const scopeFilterStateId = ref('');
const scopeFilterCityId = ref('');
const scopeType = ref('global');
const form = reactive({
  title: '',
  slug: '',
  content: '',
  meta_title: '',
  meta_description: '',
  status: 'draft',
  state_id: '',
  city_id: '',
});

const citiesForState = computed(() => {
  if (!form.state_id) return [];
  return cities.value.filter((c) => String(c.state_id) === String(form.state_id));
});

function pageUrl(page) {
  const base = window.location.origin;
  if (page.city_id && page.city?.slug && page.state?.slug) {
    return `${base}/page/${page.slug}?state_slug=${page.state.slug}&city_slug=${page.city.slug}`;
  }
  if (page.state_id && page.state?.slug) {
    return `${base}/page/${page.slug}?state_slug=${page.state.slug}`;
  }
  return `${base}/page/${page.slug}`;
}

async function loadStates() {
  const r = await axios.get('/api/admin/states');
  states.value = r.data;
}
async function loadCities() {
  const r = await axios.get('/api/admin/cities');
  cities.value = r.data;
}
async function load() {
  loading.value = true;
  try {
    const params = {};
    if (scopeFilter.value === 'global') params.scope = 'global';
    if (scopeFilter.value === 'state' && scopeFilterStateId.value) {
      params.scope = 'state';
      params.state_id = scopeFilterStateId.value;
    }
    if (scopeFilter.value === 'city' && scopeFilterCityId.value) {
      params.scope = 'city';
      params.city_id = scopeFilterCityId.value;
    }
    const r = await axios.get('/api/admin/pages', { params });
    items.value = r.data;
  } finally {
    loading.value = false;
  }
}
watch([scopeFilter, scopeFilterStateId, scopeFilterCityId], load);
function openForm(item = null) {
  editing.value = item;
  if (item) {
    form.title = item.title || '';
    form.slug = item.slug || '';
    form.content = item.content || '';
    form.meta_title = item.meta_title || '';
    form.meta_description = item.meta_description || '';
    form.status = item.status || 'draft';
    form.state_id = item.state_id ? String(item.state_id) : '';
    form.city_id = item.city_id ? String(item.city_id) : '';
    scopeType.value = item.city_id ? 'city' : item.state_id ? 'state' : 'global';
  } else {
    form.title = form.slug = form.content = form.meta_title = form.meta_description = '';
    form.status = 'draft';
    form.state_id = form.city_id = '';
    scopeType.value = 'global';
  }
  showModal.value = true;
}
watch(scopeType, (t) => {
  if (t === 'global') form.state_id = form.city_id = '';
  if (t === 'state') form.city_id = '';
});
async function save() {
  try {
    const payload = {
      title: form.title,
      slug: form.slug || undefined,
      content: form.content || undefined,
      meta_title: form.meta_title || undefined,
      meta_description: form.meta_description || undefined,
      status: form.status,
      state_id: scopeType.value === 'global' ? null : (form.state_id || null),
      city_id: scopeType.value === 'city' ? (form.city_id || null) : null,
    };
    if (editing.value) {
      await axios.put('/api/admin/pages/' + editing.value.id, payload);
    } else {
      await axios.post('/api/admin/pages', payload);
    }
    showModal.value = false;
    load();
  } catch (e) {
    alert(e.response?.data?.message || e.response?.data?.errors?.title?.[0] || 'Error saving');
  }
}
function confirmRemove(item) {
  itemToRemove.value = item;
  showDeleteModal.value = true;
}
async function doRemove() {
  if (!itemToRemove.value) return;
  try {
    await axios.delete('/api/admin/pages/' + itemToRemove.value.id);
    showDeleteModal.value = false;
    itemToRemove.value = null;
    load();
  } catch (e) {
    alert(e.response?.data?.message || 'Error deleting');
  }
}
onMounted(() => {
  loadStates();
  loadCities();
  load();
});
</script>
