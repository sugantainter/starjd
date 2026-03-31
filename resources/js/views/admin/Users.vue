<template>
  <div class="space-y-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-[#1a1a1a]">User Management</h1>
        <p class="mt-1 text-sm text-[#64748b]">View and manage all registered users across different roles.</p>
      </div>
    </header>

    <!-- Filters & Search -->
    <div class="flex flex-col gap-4 rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm md:flex-row md:items-center">
      <div class="relative flex-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-[#94a3b8]">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </span>
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="Search by name or email..." 
          class="w-full rounded-lg border-[#e2e8f0] py-2 pl-10 pr-4 text-sm transition focus:border-[#e63946] focus:ring-[#e63946]"
          @input="debounceSearch"
        />
      </div>
      <div class="flex gap-4">
        <select 
          v-model="filters.role" 
          class="rounded-lg border-[#e2e8f0] py-2 text-sm transition focus:border-[#e63946] focus:ring-[#e63946]"
          @change="fetchUsers"
        >
          <option value="">All Roles</option>
          <option v-for="role in roles" :key="role.slug" :value="role.slug">{{ role.name }}</option>
        </select>
      </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#f8fafc] text-xs font-semibold uppercase tracking-wider text-[#64748b]">
            <tr>
              <th class="px-6 py-4">User</th>
              <th class="px-6 py-4">Role</th>
              <th class="px-6 py-4">Profile Completion</th>
              <th class="px-6 py-4">Joined At</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#e2e8f0]">
            <tr v-if="loading && !users.length" v-for="i in 5" :key="i">
              <td class="px-6 py-4"><div class="h-10 w-48 animate-pulse rounded bg-[#f1f5f9]"></div></td>
              <td class="px-6 py-4"><div class="h-6 w-20 animate-pulse rounded bg-[#f1f5f9]"></div></td>
              <td class="px-6 py-4"><div class="h-4 w-32 animate-pulse rounded bg-[#f1f5f9]"></div></td>
              <td class="px-6 py-4"><div class="h-4 w-24 animate-pulse rounded bg-[#f1f5f9]"></div></td>
              <td class="px-6 py-4 text-right"><div class="ml-auto h-8 w-16 animate-pulse rounded bg-[#f1f5f9]"></div></td>
            </tr>
            <tr v-else-if="!users.length">
              <td colspan="5" class="px-6 py-12 text-center text-[#64748b]">No users found matching your criteria.</td>
            </tr>
            <tr v-for="user in users" :key="user.id" class="group transition hover:bg-[#f8fafc]">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-full bg-[#f1f5f9] ring-2 ring-white">
                    <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center font-bold text-[#94a3b8]">
                      {{ user.name.charAt(0) }}
                    </div>
                  </div>
                  <div>
                    <div class="font-semibold text-[#1a1a1a]">{{ user.name }}</div>
                    <div class="text-xs text-[#64748b]">{{ user.email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span 
                  class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                  :class="{
                    'bg-blue-50 text-blue-600': user.role === 'creator',
                    'bg-purple-50 text-purple-600': user.role === 'brand',
                    'bg-emerald-50 text-emerald-600': user.role === 'professional',
                    'bg-orange-50 text-orange-600': user.role === 'studio_owner',
                    'bg-gray-50 text-gray-600': !user.role
                  }"
                >
                  {{ user.role?.replace('_', ' ') || 'Customer' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="h-2 w-24 overflow-hidden rounded-full bg-[#f1f5f9]">
                    <div 
                      class="h-full rounded-full transition-all duration-500"
                      :class="user.profile_completion >= 80 ? 'bg-emerald-500' : user.profile_completion >= 40 ? 'bg-amber-500' : 'bg-[#e63946]'"
                      :style="{ width: user.profile_completion + '%' }"
                    ></div>
                  </div>
                  <span class="text-xs font-medium" :class="user.profile_completion >= 80 ? 'text-emerald-600' : user.profile_completion >= 40 ? 'text-amber-600' : 'text-[#e63946]'">
                    {{ user.profile_completion }}%
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 text-[#64748b]">
                {{ new Date(user.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
              </td>
              <td class="px-6 py-4 text-right">
                <router-link 
                  :to="`/admin/users/${user.id}`" 
                  class="inline-flex items-center gap-1.5 rounded-lg border border-[#e2e8f0] bg-white px-3 py-1.5 text-xs font-medium text-[#1a1a1a] shadow-sm transition hover:bg-[#f8fafc] hover:shadow"
                >
                  View Details
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex flex-col items-center justify-between gap-4 border-t border-[#e2e8f0] bg-[#f8fafc] px-6 py-4 sm:flex-row">
        <p class="text-xs text-[#64748b]">
          Showing <span class="font-medium text-[#1a1a1a]">{{ from }}</span> to <span class="font-medium text-[#1a1a1a]">{{ to }}</span> of <span class="font-medium text-[#1a1a1a]">{{ total }}</span> users
        </p>
        <div class="flex items-center gap-1">
          <button 
            @click="page--" 
            :disabled="page <= 1"
            class="rounded-lg border border-[#e2e8f0] bg-white p-1.5 text-[#64748b] transition hover:bg-[#f1f5f9] disabled:opacity-50"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button 
            v-for="p in visiblePages" 
            :key="p"
            @click="page = p"
            class="min-w-[32px] rounded-lg border px-2 py-1.5 text-xs font-medium transition"
            :class="page === p ? 'bg-[#e63946] border-[#e63946] text-white shadow-sm' : 'bg-white border-[#e2e8f0] text-[#64748b] hover:bg-[#f1f5f9]'"
          >
            {{ p }}
          </button>
          <button 
            @click="page++" 
            :disabled="page >= totalPages"
            class="rounded-lg border border-[#e2e8f0] bg-white p-1.5 text-[#64748b] transition hover:bg-[#f1f5f9] disabled:opacity-50"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue';
import axios from 'axios';

// Simple debounce implementation
const debounce = (fn, delay) => {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), delay);
  };
};

const users = ref([]);
const roles = ref([]);
const loading = ref(true);
const page = ref(1);
const total = ref(0);
const from = ref(0);
const to = ref(0);
const totalPages = ref(1);

const filters = reactive({
  search: '',
  role: ''
});

const fetchUsers = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/users', {
      params: {
        page: page.value,
        search: filters.search,
        role: filters.role,
        per_page: 15
      }
    });
    users.value = response.data.data;
    total.value = response.data.total;
    from.value = response.data.from;
    to.value = response.data.to;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error('Error fetching users:', error);
  } finally {
    loading.value = false;
  }
};

const fetchRoles = async () => {
  try {
    const response = await axios.get('/api/admin/roles');
    roles.value = response.data;
  } catch (error) {
    console.error('Error fetching roles:', error);
  }
};

const debounceSearch = debounce(() => {
  page.value = 1;
  fetchUsers();
}, 500);

const visiblePages = computed(() => {
  const current = page.value;
  const last = totalPages.value;
  const delta = 2;
  const left = current - delta;
  const right = current + delta + 1;
  const range = [];
  const rangeWithDots = [];
  let l;

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= left && i < right)) {
      range.push(i);
    }
  }

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

  return rangeWithDots.filter(p => typeof p === 'number'); // For simplicity, only numbers for now
});

watch(page, fetchUsers);

onMounted(() => {
  fetchUsers();
  fetchRoles();
});
</script>
