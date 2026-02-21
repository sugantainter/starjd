<template>
  <div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Profile</h1>
    <p class="mt-1 text-[#64748b]">Manage your public creator profile.</p>
    <form v-if="profile" @submit.prevent="save" class="mt-6 space-y-4 rounded-xl border border-[#e2e8f0] bg-white p-6">
      <div v-if="successMessage" class="rounded-lg bg-[#d1fae5] px-4 py-3 text-sm text-[#065f46]">{{ successMessage }}</div>
      <div v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>

      <div class="flex flex-col sm:flex-row items-start gap-6">
        <div class="shrink-0">
          <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Profile photo</label>
          <label class="relative mt-1 inline-block cursor-pointer">
            <div class="h-28 w-28 rounded-full overflow-hidden border-2 border-[#e2e8f0] bg-[#f1f5f9] flex items-center justify-center ring-2 ring-transparent transition hover:border-[#10b981] hover:ring-[#10b981]/20">
              <img
                v-if="avatarPreview && !avatarLoadError"
                :key="avatarPreview"
                :src="avatarPreview"
                alt="Your profile"
                class="h-full w-full object-cover"
                @error="onAvatarImageError"
                @load="avatarLoadError = false"
              />
              <span v-else class="text-4xl font-semibold text-[#94a3b8]">{{ (profile?.user?.name || '?').charAt(0) }}</span>
            </div>
            <div v-if="avatarLoadError" class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
              <strong>Photo could not be loaded.</strong> The file was saved but the image is not accessible. On the server, run: <code class="bg-amber-100 px-1 rounded">php artisan storage:link</code>
            </div>
            <span class="mt-2 block text-center text-xs font-medium text-[#10b981]">Click to upload or change</span>
            <input
              ref="avatarInput"
              type="file"
              accept="image/jpeg,image/png,image/jpg,image/webp"
              class="absolute inset-0 w-full h-full cursor-pointer opacity-0"
              aria-label="Upload profile photo"
              @change="onAvatarChange"
            />
          </label>
          <p class="mt-1 text-xs text-[#64748b]">JPEG, PNG or WebP. Max 2 MB.</p>
          <p v-if="avatarFile" class="mt-1 text-xs text-[#10b981] font-medium">Photo selected. Click "Save profile" below to update.</p>
          <button v-if="avatarPreview && avatarFile" type="button" class="mt-2 text-sm text-[#64748b] hover:text-red-600" @click="clearAvatar">Remove selected photo</button>
        </div>
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Profile URL slug</label>
        <input v-model="form.slug" type="text" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Tagline</label>
        <input v-model="form.tagline" type="text" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Bio</label>
        <textarea v-model="form.bio" rows="4" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]"></textarea>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Location</label>
        <input v-model="form.location" type="text" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Category</label>
        <select v-model="form.category" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]">
          <option value="">Select category</option>
          <option v-for="c in filterOptions.categories" :key="c" :value="c">{{ c }}</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Gender</label>
        <select v-model="form.gender" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]">
          <option value="">Select gender</option>
          <option v-for="(label, key) in filterOptions.genders" :key="key" :value="key">{{ label }}</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Language</label>
        <select v-model="form.language" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]">
          <option value="">Select language</option>
          <option v-for="lang in filterOptions.languages" :key="lang" :value="lang">{{ lang }}</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Min rate (₹)</label>
        <input v-model.number="form.min_rate" type="number" step="0.01" min="0" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
      </div>
      <div class="flex items-center gap-2">
        <input v-model="form.is_public" type="checkbox" id="is_public" class="rounded border-[#e2e8f0] text-[#10b981] focus:ring-[#10b981]" />
        <label for="is_public" class="text-sm text-[#1a1a1a]">Show my profile on Discover</label>
      </div>
      <button type="submit" :disabled="loading" class="cursor-link rounded-xl bg-[#10b981] px-6 py-3 font-semibold text-white hover:bg-[#059669] disabled:opacity-50 disabled:cursor-not-allowed">
        {{ loading ? 'Saving...' : 'Save profile' }}
      </button>
    </form>

    <section class="mt-10">
      <h2 class="text-xl font-semibold text-[#1a1a1a]">Image posts</h2>
      <p class="mt-1 text-sm text-[#64748b]">Add images to show on your public profile (portfolio / gallery).</p>
      <div v-if="postError" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ postError }}</div>
      <div class="mt-4 flex flex-wrap gap-4">
        <div v-for="post in imagePosts" :key="post.id" class="group relative h-32 w-32 shrink-0 overflow-hidden rounded-xl border border-[#e2e8f0] bg-[#f1f5f9]">
          <img :src="post.image_url" :alt="post.caption" class="h-full w-full object-cover" />
          <p v-if="post.caption" class="absolute bottom-0 left-0 right-0 bg-black/60 px-2 py-1 text-xs text-white truncate">{{ post.caption }}</p>
          <button type="button" class="absolute right-1 top-1 rounded bg-red-500/90 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition" @click="deletePost(post)">Delete</button>
        </div>
        <label class="flex h-32 w-32 shrink-0 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#e2e8f0] bg-[#f8fafc] text-[#64748b] hover:border-[#10b981] hover:bg-[#10b981]/5 hover:text-[#10b981]">
          <span class="text-2xl">+</span>
          <span class="mt-1 text-xs">Add image</span>
          <input ref="postImageInput" type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" @change="onPostImageChange" />
        </label>
      </div>
      <div v-if="showPostModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showPostModal = false">
        <div class="w-full max-w-sm rounded-xl bg-white p-6">
          <h3 class="font-semibold text-[#1a1a1a]">Add image post</h3>
          <p class="mt-1 text-sm text-[#64748b]">Optional caption</p>
          <input v-model="newPostCaption" type="text" class="mt-2 w-full rounded-xl border border-[#e2e8f0] px-4 py-2 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" placeholder="Caption" />
          <div class="mt-4 flex gap-2">
            <button type="button" class="cursor-link rounded-xl bg-[#10b981] px-4 py-2 text-white hover:bg-[#059669]" @click="uploadPostImage">Upload</button>
            <button type="button" class="cursor-link rounded-xl border border-[#e2e8f0] px-4 py-2 hover:bg-[#f1f5f9]" @click="cancelPostImage">Cancel</button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const profile = ref(null);
const form = reactive({ slug: '', tagline: '', bio: '', location: '', category: '', gender: '', language: '', min_rate: null, is_public: true });
const filterOptions = reactive({ categories: [], genders: {}, languages: [] });
const avatarFile = ref(null);
const avatarPreview = ref('');
const avatarLoadError = ref(false);
const avatarInput = ref(null);
const loading = ref(false);
const error = ref('');
const successMessage = ref('');
const imagePosts = ref([]);
const postError = ref('');
const showPostModal = ref(false);
const newPostFile = ref(null);
const newPostCaption = ref('');
const postImageInput = ref(null);

onMounted(async () => {
  const [profileRes, optionsRes, postsRes] = await Promise.all([
    axios.get('/api/creator/profile', { withCredentials: true }),
    axios.get('/api/creators/options/filters'),
    axios.get('/api/creator/image-posts', { withCredentials: true }),
  ]);
  profile.value = profileRes.data;
  avatarLoadError.value = false;
  avatarPreview.value = profileRes.data.avatar_url || '';
  form.slug = profileRes.data.slug ?? '';
  form.tagline = profileRes.data.tagline ?? '';
  form.bio = profileRes.data.bio ?? '';
  form.location = profileRes.data.location ?? '';
  form.category = profileRes.data.category ?? '';
  form.gender = profileRes.data.gender ?? '';
  form.language = profileRes.data.language ?? '';
  form.min_rate = profileRes.data.min_rate ?? null;
  form.is_public = profileRes.data.is_public ?? true;
  filterOptions.categories = optionsRes.data.categories ?? [];
  filterOptions.genders = optionsRes.data.genders ?? {};
  filterOptions.languages = optionsRes.data.languages ?? [];
  imagePosts.value = postsRes.data ?? [];
});

function onAvatarImageError() {
  avatarLoadError.value = true;
  avatarPreview.value = '';
}

function onAvatarChange(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  error.value = '';
  successMessage.value = '';
  avatarLoadError.value = false;
  if (!file.type.startsWith('image/') || !['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
    error.value = 'Invalid file type. Please choose a JPEG, PNG, or WebP image.';
    if (avatarInput.value) avatarInput.value.value = '';
    return;
  }
  if (file.size > 2 * 1024 * 1024) {
    error.value = 'File is too large. Please choose an image under 2 MB.';
    if (avatarInput.value) avatarInput.value.value = '';
    return;
  }
  avatarFile.value = file;
  const reader = new FileReader();
  reader.onload = () => { avatarPreview.value = reader.result; };
  reader.readAsDataURL(file);
}

function clearAvatar() {
  avatarFile.value = null;
  avatarLoadError.value = false;
  avatarPreview.value = profile.value?.avatar_url || '';
  if (avatarInput.value) avatarInput.value.value = '';
}

function getApiErrorMessage(e) {
  const data = e.response?.data;
  if (!data) return 'Something went wrong. Please try again.';
  if (data.errors?.avatar?.length) return data.errors.avatar[0];
  if (data.errors && typeof data.errors === 'object') {
    const first = Object.values(data.errors).flat();
    if (first.length) return first[0];
  }
  if (data.message) return data.message;
  return 'Something went wrong. Please try again.';
}

async function save() {
  loading.value = true;
  error.value = '';
  successMessage.value = '';
  try {
    if (avatarFile.value) {
      const fd = new FormData();
      fd.append('slug', form.slug);
      fd.append('tagline', form.tagline);
      fd.append('bio', form.bio);
      fd.append('location', form.location);
      fd.append('category', form.category);
      fd.append('gender', form.gender);
      fd.append('language', form.language);
      if (form.min_rate != null) fd.append('min_rate', form.min_rate);
      fd.append('is_public', form.is_public ? '1' : '0');
      fd.append('avatar', avatarFile.value);
      const r = await axios.put('/api/creator/profile', fd, {
        withCredentials: true,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      profile.value = r.data;
      avatarLoadError.value = false;
      // Keep showing the photo they just selected (data URL) so it's visible immediately after save.
      // If we switched to the server URL here and the image failed to load, they'd see a placeholder.
      // avatarPreview already has the data URL from the selected file; leave it so the photo shows.
      avatarFile.value = null;
      if (avatarInput.value) avatarInput.value.value = '';
      successMessage.value = 'Profile and photo saved successfully.';
    } else {
      await axios.put('/api/creator/profile', form, { withCredentials: true });
      const r = await axios.get('/api/creator/profile', { withCredentials: true });
      profile.value = r.data;
      avatarLoadError.value = false;
      avatarPreview.value = r.data.avatar_url || '';
      successMessage.value = 'Profile saved successfully.';
    }
    setTimeout(() => { successMessage.value = ''; }, 5000);
  } catch (e) {
    error.value = getApiErrorMessage(e);
  } finally {
    loading.value = false;
  }
}

function onPostImageChange(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
    postError.value = 'Please choose a JPEG, PNG or WebP image.';
    return;
  }
  if (file.size > 2 * 1024 * 1024) {
    postError.value = 'Image must be under 2 MB.';
    return;
  }
  postError.value = '';
  newPostFile.value = file;
  newPostCaption.value = '';
  showPostModal.value = true;
}

function cancelPostImage() {
  newPostFile.value = null;
  newPostCaption.value = '';
  showPostModal.value = false;
  if (postImageInput.value) postImageInput.value.value = '';
}

async function uploadPostImage() {
  if (!newPostFile.value) return;
  postError.value = '';
  try {
    const fd = new FormData();
    fd.append('image', newPostFile.value);
    fd.append('caption', newPostCaption.value);
    const r = await axios.post('/api/creator/image-posts', fd, { withCredentials: true, headers: { 'Accept': 'application/json' } });
    imagePosts.value = [r.data, ...imagePosts.value];
    cancelPostImage();
  } catch (e) {
    postError.value = e.response?.data?.errors?.image?.[0] || e.response?.data?.message || 'Upload failed.';
  }
}

async function deletePost(post) {
  if (!confirm('Remove this image from your profile?')) return;
  postError.value = '';
  try {
    await axios.delete('/api/creator/image-posts/' + post.id, { withCredentials: true });
    imagePosts.value = imagePosts.value.filter((p) => p.id !== post.id);
  } catch (e) {
    postError.value = e.response?.data?.message || 'Delete failed.';
  }
}
</script>
