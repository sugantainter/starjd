<template>
  <div>
    <AdminPageHeader
      title="Legal Pages"
      description="Manage Terms, Privacy Policy, and Cookie Policy separately from the regular CMS pages."
      :breadcrumbs="[{ label: 'Content & CMS', path: '/admin' }, { label: 'Legal Pages', path: '/admin/legal-pages' }]"
    />

    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
        <div class="h-5 w-40 animate-pulse rounded bg-[#e2e8f0]" />
        <div class="mt-4 h-10 animate-pulse rounded bg-[#f1f5f9]" />
        <div class="mt-3 h-40 animate-pulse rounded bg-[#f8fafc]" />
      </div>
    </div>

    <div v-else class="space-y-6">
      <section
        v-for="page in pages"
        :key="page.slug"
        class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm"
      >
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
          <div>
            <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ page.title }}</h2>
            <p class="mt-1 text-sm text-[#64748b]">
              Public URL:
              <a :href="publicUrl(page.slug)" target="_blank" class="text-[#e63946] hover:underline">
                {{ publicUrl(page.slug) }}
              </a>
            </p>
          </div>
          <span
            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
            :class="page.status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
          >
            {{ page.status }}
          </span>
        </div>

        <form class="space-y-4" @submit.prevent="save(page.slug)">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Title</label>
            <input
              v-model="page.title"
              type="text"
              class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Content</label>
            <RichTextEditor
              v-model="page.content"
              placeholder="Write the content shown on website and mobile."
              upload-image-url="/api/admin/posts/upload"
            />
          </div>

          <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] p-3">
            <h3 class="mb-2 text-sm font-semibold text-[#1a1a1a]">SEO</h3>
            <div class="space-y-2">
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta title</label>
                <input
                  v-model="page.meta_title"
                  type="text"
                  maxlength="70"
                  class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]"
                />
              </div>
              <div>
                <label class="mb-0.5 block text-xs font-medium text-[#64748b]">Meta description</label>
                <textarea
                  v-model="page.meta_description"
                  rows="2"
                  maxlength="160"
                  class="w-full rounded border border-[#e2e8f0] px-2 py-1.5 text-sm text-[#1a1a1a]"
                />
              </div>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Status</label>
            <select
              v-model="page.status"
              class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]"
            >
              <option value="draft">Draft</option>
              <option value="published">Published</option>
            </select>
          </div>

          <div class="flex flex-wrap items-center gap-3 border-t border-[#e2e8f0] pt-4">
            <button
              type="submit"
              class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f] disabled:opacity-60"
              :disabled="savingSlug === page.slug"
            >
              {{ savingSlug === page.slug ? 'Saving...' : 'Save' }}
            </button>
            <span v-if="savedSlug === page.slug" class="text-sm text-emerald-600">Saved</span>
            <span v-if="errorSlug === page.slug" class="text-sm text-red-600">{{ errorMessage }}</span>
          </div>
        </form>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import AdminPageHeader from '../../components/admin/AdminPageHeader.vue';
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const loading = ref(true);
const pages = ref([]);
const savingSlug = ref('');
const savedSlug = ref('');
const errorSlug = ref('');
const errorMessage = ref('');

function publicUrl(slug) {
  if (slug === 'privacy') return `${window.location.origin}/privacy`;
  if (slug === 'terms') return `${window.location.origin}/terms`;
  return `${window.location.origin}/cookie-policy`;
}

async function load() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/admin/legal-pages');
    pages.value = data;
  } finally {
    loading.value = false;
  }
}

async function save(slug) {
  const page = pages.value.find((item) => item.slug === slug);
  if (!page) return;

  savingSlug.value = slug;
  savedSlug.value = '';
  errorSlug.value = '';
  errorMessage.value = '';

  try {
    await axios.put(`/api/admin/legal-pages/${slug}`, {
      title: page.title,
      content: page.content || '',
      meta_title: page.meta_title || '',
      meta_description: page.meta_description || '',
      status: page.status,
    });
    savedSlug.value = slug;
  } catch (e) {
    errorSlug.value = slug;
    errorMessage.value = e.response?.data?.message || 'Unable to save this page.';
  } finally {
    savingSlug.value = '';
  }
}

onMounted(load);
</script>
