<template>
  <div v-if="profile" class="mx-auto max-w-4xl px-4 py-8">
    <div class="rounded-2xl border border-[#e2e8f0] bg-white p-8 shadow-sm">
      <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
        <div class="h-24 w-24 shrink-0 rounded-full overflow-hidden border-2 border-[#e2e8f0] bg-[#f1f5f9] flex items-center justify-center">
          <img v-if="profile.avatar_url" :src="profile.avatar_url" :alt="profile.user?.name" class="h-full w-full object-cover" />
          <span v-else class="text-3xl font-semibold text-[#94a3b8]">{{ (profile.user?.name || '?').charAt(0) }}</span>
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-[#1a1a1a]">{{ profile.user?.name }}</h1>
          <p v-if="profile.tagline" class="mt-1 text-[#64748b]">{{ profile.tagline }}</p>
          <p v-if="profile.location" class="mt-1 text-sm text-[#64748b]">{{ profile.location }}</p>
          <p v-if="profile.category" class="mt-1 text-sm text-[#64748b]">{{ profile.category }}</p>
          <p v-if="profile.bio" class="mt-4 text-[#1a1a1a]">{{ profile.bio }}</p>
          <div v-if="connectedSocialAccounts.length" class="mt-5">
            <p class="mb-3 text-sm font-medium text-[#64748b]">Connect & reach</p>
            <div class="flex flex-wrap gap-3">
              <a
                v-for="s in connectedSocialAccounts"
                :key="s.platform"
                :href="s.profile_url || '#'"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-3 rounded-xl border border-[#e2e8f0] bg-white px-4 py-3 text-left shadow-sm transition hover:border-[#e63946]/40 hover:shadow-md"
              >
                <SocialPlatformIcon :platform="s.platform" :size="36" />
                <div>
                  <span class="block font-medium text-[#1a1a1a]">{{ platformName(s.platform) }}</span>
                  <span v-if="s.username" class="block text-sm text-[#64748b]">@{{ s.username }}</span>
                  <span v-if="s.followers_count" class="block text-xs font-medium text-[#e63946]">{{ formatFollowers(s.followers_count) }} followers</span>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="profile.user?.creator_image_posts?.length" class="mt-8">
      <h2 class="text-xl font-semibold text-[#1a1a1a]">Portfolio</h2>
      <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
        <div v-for="post in profile.user.creator_image_posts" :key="post.id" class="aspect-square rounded-xl overflow-hidden border border-[#e2e8f0] bg-[#f1f5f9]">
          <img :src="post.image_url" :alt="post.caption" class="h-full w-full object-cover" />
          <p v-if="post.caption" class="p-2 text-xs text-[#64748b] truncate">{{ post.caption }}</p>
        </div>
      </div>
    </div>
    <div class="mt-8">
      <h2 class="text-xl font-semibold text-[#1a1a1a]">Packages & rates</h2>
      <p class="mt-1 text-sm text-[#64748b]">Collaboration packages with transparent pricing.</p>
      <div class="mt-4 space-y-4">
        <div v-for="pkg in profile.user?.packages" :key="pkg.id" class="rounded-xl border border-[#e2e8f0] bg-white p-5 shadow-sm">
          <div class="flex flex-wrap items-center gap-2">
            <span v-if="pkg.package_category" class="rounded-full bg-[#e63946]/10 px-2.5 py-0.5 text-xs font-medium text-[#c1121f]">{{ pkg.package_category.name }}</span>
          </div>
          <h3 class="mt-2 font-semibold text-[#1a1a1a]">{{ pkg.name }}</h3>
          <div class="mt-1 text-2xl font-bold text-[#e63946]">₹{{ formatPrice(pkg.price) }}</div>
          <div v-if="pkg.items?.length" class="mt-3 rounded-lg bg-[#f8fafc] px-4 py-3 text-sm">
            <table class="w-full text-left text-[#64748b]">
              <thead>
                <tr class="border-b border-[#e2e8f0]">
                  <th class="pb-2 font-medium text-[#1a1a1a]">Item</th>
                  <th class="pb-2 text-right font-medium text-[#1a1a1a]">Qty</th>
                  <th class="pb-2 text-right font-medium text-[#1a1a1a]">Unit price</th>
                  <th class="pb-2 text-right font-medium text-[#1a1a1a]">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(it, i) in pkg.items" :key="i" class="border-b border-[#e2e8f0] last:border-0">
                  <td class="py-2">{{ it.name }}</td>
                  <td class="py-2 text-right">{{ it.quantity }}</td>
                  <td class="py-2 text-right">₹{{ formatPrice(it.unit_price) }}</td>
                  <td class="py-2 text-right font-medium">₹{{ formatPrice(it.quantity * it.unit_price) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-if="pkg.description" class="mt-3 text-sm text-[#64748b]">{{ pkg.description }}</p>
          <p v-if="pkg.deliverables" class="mt-1 text-xs text-[#94a3b8]">Deliverables: {{ pkg.deliverables }}</p>
          <button v-if="isBrand" type="button" class="mt-4 cursor-link rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openCollab(pkg)">Collaborate</button>
        </div>
      </div>
      <p v-if="isBrand && !profile.user?.packages?.length" class="mt-4 text-[#64748b]">No packages listed. Contact the creator directly.</p>
      <p v-if="!isBrand" class="mt-4 text-[#64748b]">Log in as a brand to request a collaboration.</p>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Request collaboration</h2>
        <form @submit.prevent="submitCollab" class="mt-4 space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium">Package</label>
            <div class="rounded-lg border border-[#e2e8f0] bg-[#f8fafc] px-4 py-2 text-sm">{{ selectedPackage?.name }} – ₹{{ selectedPackage?.price }}</div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Amount (₹)</label>
            <input v-model.number="collabForm.amount" type="number" step="0.01" min="0" required class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Notes (optional)</label>
            <textarea v-model="collabForm.brand_notes" rows="3" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"></textarea>
          </div>
          <div v-if="platformFee" class="rounded-lg bg-[#f8fafc] px-4 py-2 text-sm text-[#64748b]">Platform fee (10%): ₹{{ platformFee }} · Total: ₹{{ collabForm.amount }}</div>
          <div v-if="error" class="rounded-lg bg-red-50 px-4 py-2 text-sm text-red-800">{{ error }}</div>
          <div class="flex gap-2 pt-2">
            <button type="submit" :disabled="loadingCollab" class="cursor-link rounded-xl bg-[#e63946] px-4 py-2 text-white hover:bg-[#c1121f] disabled:opacity-50">Send request</button>
            <button type="button" class="cursor-link rounded-xl border px-4 py-2 hover:bg-[#f1f5f9]" @click="showModal = false">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div v-else-if="!loading" class="mx-auto max-w-4xl px-4 py-12 text-center text-[#64748b]">Creator not found.</div>
  <div v-else class="mx-auto max-w-4xl px-4 py-12 text-center text-[#64748b]">Loading…</div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import SocialPlatformIcon from '../components/SocialPlatformIcon.vue';
import { platformDisplayName } from '../lib/socialPlatforms.js';

const route = useRoute();
const profile = ref(null);
const loading = ref(true);
const showModal = ref(false);
const selectedPackage = ref(null);
const collabForm = reactive({ amount: 0, brand_notes: '' });
const error = ref('');
const loadingCollab = ref(false);
const isBrand = ref(false);

const platformFee = computed(() => {
  const amt = Number(collabForm.amount);
  if (!amt || amt <= 0) return '0.00';
  return (amt * 0.1).toFixed(2);
});

function formatPrice(n) {
  const x = Number(n);
  return Number.isFinite(x) ? x.toFixed(2) : '0.00';
}

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

const connectedSocialAccounts = computed(() => {
  const list = profile.value?.user?.social_accounts || [];
  return list.filter((s) => s.profile_url || s.username);
});

onMounted(async () => {
  try {
    const slug = route.params.slug;
    const res = await axios.get('/api/creators/' + slug);
    profile.value = res.data;
    if (selectedPackage.value) collabForm.amount = Number(selectedPackage.value.price);
  } catch (e) {
    profile.value = null;
  } finally {
    loading.value = false;
  }
  try {
    const userRes = await axios.get('/api/me', { withCredentials: true });
    isBrand.value = userRes.data?.role === 'brand';
  } catch {
    isBrand.value = false;
  }
});

watch(selectedPackage, (p) => {
  if (p) collabForm.amount = Number(p.price);
}, { immediate: true });

function openCollab(pkg) {
  selectedPackage.value = pkg;
  collabForm.amount = Number(pkg.price);
  collabForm.brand_notes = '';
  error.value = '';
  showModal.value = true;
}

async function submitCollab() {
  error.value = '';
  loadingCollab.value = true;
  try {
    await axios.post('/api/collaborations', {
      creator_id: profile.value.user.id,
      package_id: selectedPackage.value?.id ?? null,
      amount: collabForm.amount,
      brand_notes: collabForm.brand_notes || null,
    }, { withCredentials: true });
    showModal.value = false;
    alert('Collaboration request sent. Check your Brand dashboard for updates.');
  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) {
      window.location.href = '/login?redirect=' + encodeURIComponent(route.fullPath);
      return;
    }
    if (e.response?.status === 402) {
      window.location.href = '/brand/choose-plan';
      return;
    }
    error.value = e.response?.data?.message || 'Failed to send request.';
  } finally {
    loadingCollab.value = false;
  }
}
</script>
