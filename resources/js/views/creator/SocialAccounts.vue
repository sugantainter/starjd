<template>
  <div>
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Social Accounts</h1>
    <p class="mt-1 text-[#64748b]">Add your social profile links for each platform so brands can see your reach.</p>
    <div v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800">{{ error }}</div>
    <div class="mt-6 space-y-4">
      <div v-for="acc in accounts" :key="acc.platform" class="rounded-xl border border-[#e2e8f0] bg-white p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="font-semibold capitalize text-[#1a1a1a]">{{ acc.platform }}</span>
            <span v-if="acc.is_connected" class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-800">Connected</span>
          </div>
          <button v-if="acc.is_connected" type="button" class="cursor-link text-sm text-red-600 hover:underline" @click="disconnect(acc.platform)">Disconnect</button>
        </div>
        <form v-if="editing === acc.platform" @submit.prevent="sync(acc.platform)" class="mt-4 space-y-3">
          <input v-model="syncForm.username" type="text" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" placeholder="Username" />
          <input v-model="syncForm.profile_url" type="url" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" placeholder="Profile URL" />
          <input v-model.number="syncForm.followers_count" type="number" min="0" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2 text-sm focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981]" placeholder="Followers" />
          <div class="flex gap-2">
            <button type="submit" class="cursor-link rounded-lg bg-[#10b981] px-4 py-2 text-sm text-white hover:bg-[#059669]">Save</button>
            <button type="button" class="cursor-link rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm hover:bg-[#f1f5f9]" @click="editing = null">Cancel</button>
          </div>
        </form>
        <div v-else class="mt-3 text-sm text-[#64748b]">
          <span v-if="acc.username">@{{ acc.username }}</span>
          <span v-if="acc.followers_count"> · {{ acc.followers_count }} followers</span>
          <button type="button" class="ml-2 cursor-link text-[#10b981] hover:underline" @click="startEdit(acc)">Edit</button>
        </div>
        <button v-if="!acc.is_connected && editing !== acc.platform" type="button" class="mt-3 cursor-link rounded-lg border border-[#10b981] px-4 py-2 text-sm text-[#10b981] hover:bg-[#10b981]/5" @click="startEdit(acc)">Connect</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const accounts = ref([]);
const editing = ref(null);
const syncForm = reactive({ username: '', profile_url: '', followers_count: null });
const error = ref('');

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
    error.value = e.response?.data?.message || 'Failed to sync.';
  }
}

async function disconnect(platform) {
  if (!confirm('Disconnect this account?')) return;
  try {
    await axios.delete('/api/creator/social-accounts/' + platform, { withCredentials: true });
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to disconnect.';
  }
}
</script>
