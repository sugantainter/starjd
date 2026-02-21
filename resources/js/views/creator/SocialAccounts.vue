<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Social Accounts</h1>
    <p class="mt-1 text-[#64748b]">Connect your social profiles so brands can see your reach and contact you.</p>
    <div v-if="error" class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
    <div class="mt-6 grid gap-4 sm:grid-cols-2">
      <div
        v-for="acc in accounts"
        :key="acc.platform"
        class="flex flex-col rounded-xl border border-[#e2e8f0] bg-white shadow-sm overflow-hidden transition hover:border-[#10b981]/30 hover:shadow"
      >
        <div class="flex items-start gap-4 p-5">
          <SocialPlatformIcon :platform="acc.platform" :size="48" class="shrink-0" />
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <h3 class="font-semibold text-[#1a1a1a]">{{ platformName(acc.platform) }}</h3>
              <span v-if="acc.is_connected" class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Connected</span>
              <span v-else class="rounded-full bg-[#e2e8f0] px-2.5 py-0.5 text-xs font-medium text-[#64748b]">Not connected</span>
            </div>
            <div v-if="acc.is_connected && (acc.username || acc.followers_count)" class="mt-2 text-sm text-[#64748b]">
              <span v-if="acc.username">@{{ acc.username }}</span>
              <span v-if="acc.followers_count" class="ml-1">· {{ formatFollowers(acc.followers_count) }} followers</span>
            </div>
            <div v-else-if="!acc.is_connected" class="mt-2 text-sm text-[#64748b]">Add your profile link and follower count.</div>
          </div>
        </div>
        <form v-if="editing === acc.platform" @submit.prevent="sync(acc.platform)" class="border-t border-[#e2e8f0] bg-[#f8fafc] p-5 space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Username / handle</label>
            <input v-model="syncForm.username" type="text" placeholder="e.g. yourhandle" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Profile URL</label>
            <input v-model="syncForm.profile_url" type="url" placeholder="https://..." class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Follower count</label>
            <input v-model.number="syncForm.followers_count" type="number" min="0" placeholder="0" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" />
          </div>
          <div class="flex flex-wrap gap-2">
            <button type="submit" class="cursor-link rounded-xl bg-[#10b981] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#059669]">Save</button>
            <button type="button" class="cursor-link rounded-xl border border-[#e2e8f0] bg-white px-4 py-2.5 text-sm font-medium text-[#1a1a1a] hover:bg-[#f1f5f9]" @click="editing = null">Cancel</button>
          </div>
        </form>
        <div v-else class="flex flex-wrap gap-2 border-t border-[#e2e8f0] p-4">
          <button type="button" class="cursor-link rounded-lg border border-[#e2e8f0] bg-white px-3 py-2 text-sm font-medium text-[#1a1a1a] hover:bg-[#f1f5f9]" @click="startEdit(acc)">{{ acc.is_connected ? 'Edit' : 'Connect' }}</button>
          <button v-if="acc.is_connected" type="button" class="cursor-link rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50" @click="disconnect(acc.platform)">Disconnect</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import SocialPlatformIcon from '../../components/SocialPlatformIcon.vue';
import { platformDisplayName } from '../../lib/socialPlatforms.js';

const accounts = ref([]);
const editing = ref(null);
const syncForm = reactive({ username: '', profile_url: '', followers_count: null });
const error = ref('');

function platformName(platform) {
  return platformDisplayName(platform);
}

function formatFollowers(n) {
  if (n == null || n === '') return '';
  const num = Number(n);
  if (num >= 1e6) return (num / 1e6).toFixed(1) + 'M';
  if (num >= 1e3) return (num / 1e3).toFixed(1) + 'K';
  return num.toLocaleString();
}

onMounted(load);

async function load() {
  const res = await axios.get('/api/creator/social-accounts', { withCredentials: true });
  accounts.value = res.data;
}

function startEdit(acc) {
  syncForm.username = acc.username ?? '';
  syncForm.profile_url = acc.profile_url ?? '';
  syncForm.followers_count = acc.followers_count ?? null;
  editing.value = acc.platform;
}

async function sync(platform) {
  error.value = '';
  try {
    await axios.post('/api/creator/social-accounts/sync', { platform, ...syncForm }, { withCredentials: true });
    editing.value = null;
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to save.';
  }
}

async function disconnect(platform) {
  if (!confirm('Disconnect this account? Your profile link and follower count will be removed.')) return;
  try {
    await axios.delete('/api/creator/social-accounts/' + platform, { withCredentials: true });
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to disconnect.';
  }
}
</script>
