<template>
  <div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Profile</h1>
    <p class="mt-1 text-[#64748b]">Manage your brand profile.</p>
    <form v-if="profile" @submit.prevent="save" class="mt-6 space-y-4 rounded-xl border border-[#e2e8f0] bg-white p-6">
      <div v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>

      <div class="flex flex-col sm:flex-row items-start gap-6">
        <div class="shrink-0">
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Company logo</label>
          <div class="relative mt-1">
            <div class="h-28 w-28 rounded-xl overflow-hidden border-2 border-[#e2e8f0] bg-[#f1f5f9] flex items-center justify-center">
              <img v-if="logoPreview" :src="logoPreview" alt="Logo" class="h-full w-full object-contain p-1" />
              <span v-else class="text-sm text-[#94a3b8] text-center px-2">No logo</span>
            </div>
            <input
              ref="logoInput"
              type="file"
              accept="image/jpeg,image/png,image/jpg,image/webp"
              class="absolute inset-0 h-28 w-28 cursor-pointer opacity-0"
              @change="onLogoChange"
            />
          </div>
          <p class="mt-2 text-xs text-[#64748b]">JPEG, PNG or WebP. Max 2 MB.</p>
          <button v-if="logoPreview && logoFile" type="button" class="mt-2 text-sm text-[#64748b] hover:text-[#e63946]" @click="clearLogo">Remove new logo</button>
        </div>
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Company name</label>
        <input v-model="form.company_name" type="text" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Website</label>
        <input v-model="form.website" type="url" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Bio</label>
        <textarea v-model="form.bio" rows="4" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"></textarea>
      </div>
      <button type="submit" :disabled="loading" class="cursor-link rounded-xl bg-[#e63946] px-6 py-3 font-semibold text-white hover:bg-[#c1121f] disabled:opacity-50">Save</button>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const profile = ref(null);
const form = reactive({ company_name: '', website: '', bio: '' });
const logoFile = ref(null);
const logoPreview = ref('');
const logoInput = ref(null);
const loading = ref(false);
const error = ref('');

onMounted(async () => {
  const res = await axios.get('/api/brand/profile', { withCredentials: true });
  profile.value = res.data;
  logoPreview.value = res.data.logo_url || '';
  form.company_name = res.data.company_name ?? '';
  form.website = res.data.website ?? '';
  form.bio = res.data.bio ?? '';
});

function onLogoChange(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  if (!file.type.startsWith('image/') || !['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
    error.value = 'Please choose a JPEG, PNG or WebP image.';
    return;
  }
  if (file.size > 2 * 1024 * 1024) {
    error.value = 'Image must be under 2 MB.';
    return;
  }
  error.value = '';
  logoFile.value = file;
  const reader = new FileReader();
  reader.onload = () => { logoPreview.value = reader.result; };
  reader.readAsDataURL(file);
}

function clearLogo() {
  logoFile.value = null;
  logoPreview.value = profile.value?.logo_url || '';
  if (logoInput.value) logoInput.value.value = '';
}

async function save() {
  loading.value = true;
  error.value = '';
  try {
    if (logoFile.value) {
      const fd = new FormData();
      fd.append('company_name', form.company_name);
      fd.append('website', form.website);
      fd.append('bio', form.bio);
      fd.append('logo', logoFile.value);
      const r = await axios.put('/api/brand/profile', fd, {
        withCredentials: true,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      profile.value = r.data;
      logoPreview.value = r.data.logo_url || '';
      logoFile.value = null;
      if (logoInput.value) logoInput.value.value = '';
    } else {
      await axios.put('/api/brand/profile', form, { withCredentials: true });
      const r = await axios.get('/api/brand/profile', { withCredentials: true });
      profile.value = r.data;
      logoPreview.value = r.data.logo_url || '';
    }
  } catch (e) {
    const msg = e.response?.data?.errors?.logo?.[0] || e.response?.data?.message || 'Failed to save.';
    error.value = typeof msg === 'string' ? msg : 'Failed to save.';
  } finally {
    loading.value = false;
  }
}
</script>
