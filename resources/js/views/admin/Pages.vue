<template>
  <div>
    <AdminPageHeader
      title="Pages"
      description="Dynamic CMS pages. Create global pages or location-specific (state/city) pages, including legal pages for website and mobile."
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
      >
        <option value="">By state…</option>
        <option v-for="s in states" :key="s.id" :value="String(s.id)">{{ s.name }}</option>
      </select>
      <div class="relative">
        <input
          type="text"
          v-model="citySearch"
          class="rounded-full border border-[#e2e8f0] bg-white px-4 py-1.5 text-sm text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946] min-w-[150px]"
          placeholder="Search city…"
          @focus="cityDropdownOpen = true"
          @input="cityDropdownOpen = true"
          @blur="setTimeout(() => cityDropdownOpen = false, 200)"
        />
        <div
          v-if="cityDropdownOpen"
          class="absolute left-0 z-50 mt-1 max-h-60 w-64 overflow-auto rounded-xl border border-[#e2e8f0] bg-white py-1 shadow-lg"
        >
          <button
            type="button"
            class="w-full px-4 py-2 text-left text-sm hover:bg-[#f8fafc]"
            @click="selectCityFilter('')"
          >
            All Cities
          </button>
          <button
            v-for="c in filteredCitiesForFilter"
            :key="c.id"
            type="button"
            class="w-full px-4 py-2 text-left text-sm hover:bg-[#f8fafc]"
            :class="{ 'bg-red-50 text-[#e63946]': String(scopeFilterCityId) === String(c.id) }"
            @click="selectCityFilter(c)"
          >
            <div class="font-medium">{{ c.name }}</div>
            <div class="text-xs text-[#64748b]">{{ c.state?.name }}</div>
          </button>
          <div v-if="filteredCitiesForFilter.length === 0" class="px-4 py-2 text-sm text-[#64748b]">No cities found</div>
        </div>
      </div>
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
      <div v-if="totalPages > 1" class="flex flex-col items-center justify-between gap-4 border-t border-[#e2e8f0] bg-[#f8fafc] px-4 py-4 sm:flex-row">
        <p class="text-xs text-[#64748b]">
          Showing <span class="font-medium text-[#1a1a1a]">{{ from }}</span> to <span class="font-medium text-[#1a1a1a]">{{ to }}</span> of <span class="font-medium text-[#1a1a1a]">{{ total }}</span> pages
        </p>
        <div class="flex items-center gap-1">
          <button
            type="button"
            @click="listPage--"
            :disabled="listPage <= 1"
            class="rounded-lg border border-[#e2e8f0] bg-white p-1.5 text-[#64748b] transition hover:bg-[#f1f5f9] disabled:opacity-50"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button
            v-for="p in visiblePages"
            :key="p"
            type="button"
            @click="listPage = p"
            class="min-w-[32px] rounded-lg border px-2 py-1.5 text-xs font-medium transition"
            :class="listPage === p ? 'bg-[#e63946] border-[#e63946] text-white shadow-sm' : 'bg-white border-[#e2e8f0] text-[#64748b] hover:bg-[#f1f5f9]'"
          >
            {{ p }}
          </button>
          <button
            type="button"
            @click="listPage++"
            :disabled="listPage >= totalPages"
            class="rounded-lg border border-[#e2e8f0] bg-white p-1.5 text-[#64748b] transition hover:bg-[#f1f5f9] disabled:opacity-50"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
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
            <input
              v-model="form.slug"
              type="text"
              class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 font-mono text-sm text-[#1a1a1a]"
              placeholder="auto from title if left blank"
            />
            <p v-if="formPublicPathPreview" class="mt-1.5 text-xs text-[#64748b]">
              Public URL: <code class="rounded bg-[#f1f5f9] px-1.5 py-0.5 text-[#1a1a1a]">{{ formPublicPathPreview }}</code>
            </p>
            <p class="mt-1 text-xs text-[#94a3b8]">
              For state or city pages, use the topic only (e.g. <code class="text-[#64748b]">top-influencers</code>) — the site adds
              <code class="text-[#64748b]">-in-{state-or-city}</code> automatically.
            </p>
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
                <option v-for="s in states" :key="s.id" :value="String(s.id)">{{ s.name }}</option>
              </select>
              <label class="flex items-center gap-2">
                <input v-model="scopeType" type="radio" value="city" />
                <span class="text-sm">City — select city</span>
              </label>
              <template v-if="scopeType === 'city'">
                <select v-model="form.state_id" class="ml-6 mr-2 rounded border border-[#e2e8f0] px-2 py-1.5 text-sm" @change="form.city_id = ''; modalCitySearch = ''">
                  <option value="">State first</option>
                  <option v-for="s in states" :key="s.id" :value="String(s.id)">{{ s.name }}</option>
                </select>
                <div class="relative ml-6 mt-2 inline-block">
                  <input
                    type="text"
                    v-model="modalCitySearch"
                    placeholder="Search city..."
                    class="rounded border border-[#e2e8f0] px-3 py-1.5 text-sm focus:border-[#e63946] focus:outline-none"
                    @focus="modalCityDropdownOpen = true"
                    @blur="setTimeout(() => modalCityDropdownOpen = false, 200)"
                  />
                  <div v-if="modalCityDropdownOpen && form.state_id" class="absolute z-50 mt-1 max-h-48 w-full overflow-y-auto rounded-lg border border-[#e2e8f0] bg-white shadow-lg">
                    <button
                      v-for="c in filteredCitiesForModal"
                      :key="c.id"
                      type="button"
                      class="w-full px-3 py-2 text-left text-sm hover:bg-[#f8fafc]"
                      @click="selectCityForModal(c)"
                    >
                      {{ c.name }}
                    </button>
                    <div v-if="filteredCitiesForModal.length === 0" class="px-3 py-2 text-sm text-[#64748b]">No cities</div>
                  </div>
                </div>
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
const listPage = ref(1);
const total = ref(0);
const from = ref(0);
const to = ref(0);
const totalPages = ref(1);
const perPage = 20;
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
const citySearch = ref('');
const cityDropdownOpen = ref(false);
const scopeType = ref('global');
const modalCitySearch = ref('');
const modalCityDropdownOpen = ref(false);
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

const filteredCitiesForFilter = computed(() => {
  let list = cities.value;
  if (scopeFilterStateId.value) {
    list = list.filter((c) => String(c.state_id) === String(scopeFilterStateId.value));
  }
  const q = citySearch.value.trim().toLowerCase();
  if (q) {
    list = list.filter((c) => (c.name || '').toLowerCase().includes(q));
  }
  return list;
});

const filteredCitiesForModal = computed(() => {
  const q = modalCitySearch.value.trim().toLowerCase();
  let list = citiesForState.value;
  if (q) {
    list = list.filter((c) => (c.name || '').toLowerCase().includes(q));
  }
  return list;
});

/** Public path preview in the editor (matches Page::publicPath()). */
const formPublicPathPreview = computed(() => {
  if (!showModal.value) return '';
  const slug = (form.slug || '').trim();
  if (!slug) return '';
  if (scopeType.value === 'city' && form.city_id) {
    const c = cities.value.find((x) => String(x.id) === String(form.city_id));
    const s = states.value.find((x) => String(x.id) === String(form.state_id));
    if (c?.slug && s?.slug) return `/${slugifyTitle(s.slug)}/${slug}-in-${slugifyTitle(c.slug)}`;
    if (c?.slug) return `/${slug}-in-${slugifyTitle(c.slug)}`;
  }
  if (scopeType.value === 'state' && form.state_id) {
    const s = states.value.find((x) => String(x.id) === String(form.state_id));
    if (s?.slug) return `/${slug}-in-${slugifyTitle(s.slug)}`;
  }
  if (scopeType.value === 'global') return `/${slug}`;
  return '';
});

function slugifyTitle(str) {
  return String(str || '')
    .normalize('NFKD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
}

function selectCityFilter(city) {
  if (city === '') {
    scopeFilterCityId.value = '';
    citySearch.value = '';
    scopeFilter.value = scopeFilterStateId.value ? 'state' : '';
  } else {
    scopeFilterCityId.value = String(city.id);
    citySearch.value = city.name;
    scopeFilter.value = 'city';
    if (city.state_id != null) {
      scopeFilterStateId.value = String(city.state_id);
    }
  }
  cityDropdownOpen.value = false;
}

const visiblePages = computed(() => {
  const current = listPage.value;
  const last = totalPages.value;
  const delta = 2;
  const left = current - delta;
  const right = current + delta + 1;
  const range = [];
  let l;

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= left && i < right)) {
      range.push(i);
    }
  }

  const rangeWithDots = [];
  for (const i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1);
      } else if (i - l !== 1) {
        rangeWithDots.push('...');
      }
    }
    rangeWithDots.push(i);
    l = i;
  }

  return rangeWithDots.filter((p) => typeof p === 'number');
});

function selectCityForModal(city) {
  form.city_id = String(city.id);
  modalCitySearch.value = city.name;
  modalCityDropdownOpen.value = false;
}

function pageUrl(page) {
  const base = window.location.origin;
  if (page.city_id && page.city?.slug && page.state?.slug) {
    return `${base}/${slugifyTitle(page.state.slug)}/${page.slug}-in-${slugifyTitle(page.city.slug)}`;
  }
  if (page.state_id && page.state?.slug) {
    return `${base}/${page.slug}-in-${slugifyTitle(page.state.slug)}`;
  }
  return `${base}/${page.slug}`;
}

async function loadStates() {
  const r = await axios.get('/api/admin/states');
  states.value = r.data;
}
async function loadCities() {
  const r = await axios.get('/api/admin/cities');
  cities.value = r.data;
}
function buildPagesListParams() {
  const params = { page: listPage.value, per_page: perPage };
  // Order matters: avoid relying on scopeFilter === 'state' (it updates in another watcher and races).
  if (scopeFilter.value === 'global') {
    params.scope = 'global';
    return params;
  }
  if (scopeFilter.value === 'city' && scopeFilterCityId.value) {
    params.scope = 'city';
    params.city_id = Number(scopeFilterCityId.value);
    return params;
  }
  if (scopeFilterStateId.value) {
    params.scope = 'state';
    params.state_id = Number(scopeFilterStateId.value);
    return params;
  }
  return params;
}

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/pages', { params: buildPagesListParams() });
    items.value = r.data.data;
    total.value = r.data.total;
    from.value = r.data.from ?? 0;
    to.value = r.data.to ?? 0;
    totalPages.value = r.data.last_page;
  } finally {
    loading.value = false;
  }
}
watch([scopeFilter, scopeFilterStateId, scopeFilterCityId], () => {
  if (listPage.value !== 1) {
    listPage.value = 1;
  } else {
    load();
  }
});
watch(listPage, load);

/** Keep list scope in sync if state id changes without native change event (e.g. programmatic). */
watch(scopeFilterStateId, (id) => {
  if (scopeFilter.value === 'city' && scopeFilterCityId.value) {
    const city = cities.value.find((c) => String(c.id) === String(scopeFilterCityId.value));
    if (city && id && String(city.state_id) !== String(id)) {
      scopeFilterCityId.value = '';
      citySearch.value = '';
      scopeFilter.value = id ? 'state' : '';
    }
    return;
  }
  if (id) {
    scopeFilter.value = 'state';
    scopeFilterCityId.value = '';
    citySearch.value = '';
  } else if (!scopeFilterCityId.value) {
    scopeFilter.value = '';
  }
});

/** New pages: auto slug from title (server still normalizes on save). */
watch(
  () => form.title,
  (title) => {
    if (!showModal.value || editing.value) return;
    form.slug = slugifyTitle(title);
  },
);
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
    if (item.city) modalCitySearch.value = item.city.name;
  } else {
    form.title = form.slug = form.content = form.meta_title = form.meta_description = '';
    form.status = 'draft';
    form.state_id = form.city_id = '';
    modalCitySearch.value = '';
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
      slug: (form.slug || '').trim() || undefined,
      content: form.content || undefined,
      meta_title: form.meta_title || undefined,
      meta_description: form.meta_description || undefined,
      status: form.status,
      state_id:
        scopeType.value === 'global'
          ? null
          : form.state_id
            ? Number(form.state_id)
            : null,
      city_id:
        scopeType.value === 'city' && form.city_id ? Number(form.city_id) : null,
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
