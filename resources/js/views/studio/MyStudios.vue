<template>
  <div>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#1a1a1a]">My studios</h1>
        <p class="mt-1 text-[#64748b]">Manage your listed studios. Add photos and availability from each studio’s Manage page.</p>
      </div>
      <router-link to="/studio/studios/new" class="rounded-xl bg-[#e63946] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f]">
        + Add studio
      </router-link>
    </div>
    <div v-if="loading" class="mt-6 text-[#64748b]">Loading…</div>
    <div v-else-if="listError" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
      {{ listError }}
      <button type="button" class="mt-2 underline" @click="load">Try again</button>
    </div>
    <div v-else-if="!list.length" class="mt-6 rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]">
      <p>You haven’t added any studios yet.</p>
      <p class="mt-2 text-sm">Add your first studio to start receiving bookings.</p>
      <router-link to="/studio/studios/new" class="mt-4 inline-block rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f]">Add your first studio</router-link>
    </div>
    <ul v-else class="mt-6 space-y-4">
      <li v-for="s in list" :key="s.id" class="rounded-xl border border-[#e2e8f0] bg-white p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-0 flex-1">
          <h2 class="font-semibold text-[#1a1a1a]">{{ s.name }}</h2>
          <p v-if="s.city" class="mt-1 text-sm text-[#64748b]">{{ s.city }}{{ s.state ? ', ' + s.state : '' }}</p>
          <p class="mt-1 text-xs text-[#94a3b8]">Add photos, set availability & pricing here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <router-link :to="'/studio/studios/' + s.id + '/edit#photos'" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]">
            Manage (photos & slots)
          </router-link>
          <a :href="'/studios/' + (s.slug || s.id)" target="_blank" class="rounded-lg border border-[#e2e8f0] px-4 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9]">View</a>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted, onActivated } from 'vue';
import axios from 'axios';

const list = ref([]);
const loading = ref(true);
const listError = ref('');

async function load() {
  loading.value = true;
  listError.value = '';
  try {
    const res = await axios.get('/api/studio-owner/studios', { withCredentials: true });
    const d = res.data;
    list.value = (d && Array.isArray(d.data)) ? d.data : (Array.isArray(d) ? d : []);
  } catch (e) {
    list.value = [];
    listError.value = e.response?.status === 401 ? 'Please log in again.' : (e.response?.data?.message || 'Could not load studios. Try again.');
  } finally {
    loading.value = false;
  }
}

onMounted(load);
onActivated(load);
</script>
