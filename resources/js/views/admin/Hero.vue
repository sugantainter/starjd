<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Hero Section</h1>
      <p class="mt-1 text-sm text-[#64748b]">Edit headline, background images, and CTA buttons for the homepage hero.</p>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <form v-else class="space-y-6 rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm" @submit.prevent="save">
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Headline</label>
        <input v-model="form.headline" type="text" class="w-full max-w-xl rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="Influencer Marketing Made Easy" />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Subheadline (optional)</label>
        <input v-model="form.subheadline" type="text" class="w-full max-w-xl rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Wallpaper background images</label>
        <p class="mb-2 text-xs text-[#64748b]">Enter one URL per line, or upload images below.</p>
        <textarea v-model="wallpaperText" rows="4" class="mb-2 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 font-mono text-sm text-[#1a1a1a]" placeholder="https://example.com/image1.jpg"></textarea>
        <div class="flex items-center gap-2">
          <input ref="wallpaperFileInput" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" @change="onWallpaperFileSelect" />
          <button type="button" class="rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-sm font-medium text-[#1a1a1a] hover:bg-[#f1f5f9]" :disabled="uploadingWallpaper" @click="$refs.wallpaperFileInput?.click()">
            {{ uploadingWallpaper ? 'Uploading…' : 'Upload image' }}
          </button>
        </div>
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-[#1a1a1a]">Gallery cascade images (center of hero)</label>
        <div class="space-y-3">
          <div v-for="(item, i) in form.cascade_images" :key="i" class="flex flex-wrap items-start gap-2 rounded-lg border border-[#e2e8f0] p-3">
            <div class="h-14 w-20 shrink-0 overflow-hidden rounded bg-[#f1f5f9]">
              <img v-if="item.src" :src="item.src" :alt="item.alt" class="h-full w-full object-cover" />
              <span v-else class="flex h-full w-full items-center justify-center text-xs text-[#94a3b8]">No image</span>
            </div>
            <div class="min-w-0 flex-1 space-y-1">
              <input v-model="item.src" type="text" placeholder="Image URL or upload" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a]" />
              <input v-model="item.link" type="text" placeholder="Link URL (optional)" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a]" />
            </div>
            <input v-model="item.alt" type="text" placeholder="Alt text" class="w-24 rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a]" />
            <input :ref="el => setCascadeFileRef(el, i)" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" @change="e => onCascadeFileSelect(e, i)" />
            <button type="button" class="rounded-lg border border-[#e63946] bg-white px-3 py-2 text-sm font-medium text-[#e63946] hover:bg-[#e63946]/5" :disabled="uploadingCascadeIndex !== null" @click="triggerCascadeUpload(i)">
              {{ uploadingCascadeIndex === i ? 'Uploading…' : 'Upload' }}
            </button>
          </div>
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Button 1 (Creator) label</label>
          <input v-model="form.btn_creator_label" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Button 1 URL</label>
          <input v-model="form.btn_creator_url" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="#join-creator or /creator-landing" />
        </div>
        <div class="sm:col-span-2 lg:col-span-1" />
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Button 2 (Brand) label</label>
          <input v-model="form.btn_brand_label" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Button 2 URL</label>
          <input v-model="form.btn_brand_url" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="#join-brand or /brand-landing" />
        </div>
        <div class="sm:col-span-2 lg:col-span-1" />
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Button 3 (Browse) label</label>
          <input v-model="form.btn_browse_label" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Button 3 URL</label>
          <input v-model="form.btn_browse_url" type="text" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-[#1a1a1a]" placeholder="#featured or /creators" />
        </div>
      </div>

      <div class="flex gap-2 pt-2">
        <button type="submit" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]">Save Hero</button>
        <button type="button" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]" @click="load">Reset</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const uploadingWallpaper = ref(false);
const uploadingCascadeIndex = ref(null);
const cascadeFileInputRefs = {};
function setCascadeFileRef(el, i) {
  if (el) cascadeFileInputRefs[i] = el;
}
function triggerCascadeUpload(i) {
  cascadeFileInputRefs[i]?.click();
}
async function uploadFile(file) {
  const fd = new FormData();
  fd.append('image', file);
  const r = await axios.post('/api/admin/hero/upload', fd, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return r.data?.url;
}
async function onWallpaperFileSelect(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  uploadingWallpaper.value = true;
  e.target.value = '';
  try {
    const url = await uploadFile(file);
    if (url) {
      const list = form.wallpaper_images || [];
      list.push({ src: url });
      form.wallpaper_images = list;
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Upload failed');
  } finally {
    uploadingWallpaper.value = false;
  }
}
async function onCascadeFileSelect(e, i) {
  const file = e.target.files?.[0];
  if (!file) return;
  uploadingCascadeIndex.value = i;
  e.target.value = '';
  try {
    const url = await uploadFile(file);
    if (url && form.cascade_images[i]) form.cascade_images[i].src = url;
  } catch (err) {
    alert(err.response?.data?.message || 'Upload failed');
  } finally {
    uploadingCascadeIndex.value = null;
  }
}
const form = reactive({
  headline: 'Influencer Marketing Made Easy',
  subheadline: '',
  wallpaper_images: [],
  cascade_images: [
    { src: '', alt: 'Inside left', link: '' },
    { src: '', alt: 'Middle', link: '' },
    { src: '', alt: 'Inside right', link: '' },
    { src: '', alt: 'Outer right', link: '' },
    { src: '', alt: 'Outer left', link: '' },
  ],
  btn_creator_label: 'Join as Creator',
  btn_creator_url: '#join-creator',
  btn_brand_label: 'Join as Brand',
  btn_brand_url: '#join-brand',
  btn_browse_label: 'Browse Creators',
  btn_browse_url: '#featured',
});

const wallpaperText = computed({
  get() {
    return (form.wallpaper_images || []).map((i) => (typeof i === 'string' ? i : i?.src || '')).filter(Boolean).join('\n');
  },
  set(val) {
    form.wallpaper_images = (val || '')
      .split('\n')
      .map((s) => s.trim())
      .filter(Boolean)
      .map((src) => ({ src }));
  },
});

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/hero');
    const h = r.data;
    form.headline = h.headline ?? form.headline;
    form.subheadline = h.subheadline ?? '';
    form.wallpaper_images = Array.isArray(h.wallpaper_images) && h.wallpaper_images.length
      ? h.wallpaper_images.map((i) => (typeof i === 'string' ? { src: i } : { src: i?.src || '' }))
      : [];
    if (Array.isArray(h.cascade_images) && h.cascade_images.length) {
      form.cascade_images = h.cascade_images.map((i) => ({ src: i.src || '', alt: i.alt || '', link: i.link || '' }));
      while (form.cascade_images.length < 5) form.cascade_images.push({ src: '', alt: '', link: '' });
    }
    form.btn_creator_label = h.btn_creator_label ?? form.btn_creator_label;
    form.btn_creator_url = h.btn_creator_url ?? form.btn_creator_url;
    form.btn_brand_label = h.btn_brand_label ?? form.btn_brand_label;
    form.btn_brand_url = h.btn_brand_url ?? form.btn_brand_url;
    form.btn_browse_label = h.btn_browse_label ?? form.btn_browse_label;
    form.btn_browse_url = h.btn_browse_url ?? form.btn_browse_url;
  } finally {
    loading.value = false;
  }
}

async function save() {
  const payload = {
    headline: form.headline,
    subheadline: form.subheadline || null,
    wallpaper_images: form.wallpaper_images?.length ? form.wallpaper_images : null,
    cascade_images: form.cascade_images?.filter((i) => i.src)?.length ? form.cascade_images.filter((i) => i.src).map((i) => ({ src: i.src, alt: i.alt || '', link: i.link || '' })) : null,
    btn_creator_label: form.btn_creator_label,
    btn_creator_url: form.btn_creator_url,
    btn_brand_label: form.btn_brand_label,
    btn_brand_url: form.btn_brand_url,
    btn_browse_label: form.btn_browse_label,
    btn_browse_url: form.btn_browse_url,
  };
  try {
    await axios.put('/api/admin/hero', payload);
    alert('Hero section saved.');
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving');
  }
}

onMounted(load);
</script>
