<template>
  <div class="mx-auto max-w-4xl px-4 py-8 md:py-12">
    <div v-if="loading" class="flex justify-center py-16">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-[#e63946] border-t-transparent" />
    </div>
    <template v-else-if="campaign">
      <nav class="mb-6 text-sm text-[#64748b]">
        <router-link to="/campaigns" class="hover:text-[#e63946]">Campaigns</router-link>
        <span class="mx-2">/</span>
        <span class="text-[#1a1a1a]">{{ campaign.title }}</span>
      </nav>

      <article class="rounded-2xl border border-[#e2e8f0] bg-white shadow-sm overflow-hidden">
        <div class="p-6 md:p-8">
          <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-[#e63946]/10 px-3 py-1 text-sm font-semibold text-[#e63946]">{{ typeLabel(campaign.campaign_type) }}</span>
            <span v-if="campaign.brand?.name" class="text-sm text-[#64748b]">by {{ campaign.brand.name }}</span>
          </div>
          <h1 class="mt-4 text-2xl font-bold text-[#1a1a1a] md:text-3xl">{{ campaign.title }}</h1>
          <div class="mt-4 flex flex-wrap gap-3 text-sm text-[#64748b]">
            <span v-if="campaign.budget != null && campaign.budget > 0">Budget: ₹{{ formatNumber(campaign.budget) }}</span>
            <span v-if="campaign.max_applications">Looking for {{ campaign.max_applications }} creators</span>
            <span v-if="campaign.applications_count != null">{{ campaign.applications_count }} applied</span>
          </div>
          <div v-if="campaign.description" class="mt-6 prose prose-[#475569] max-w-none">
            <p class="whitespace-pre-wrap text-[#475569]">{{ campaign.description }}</p>
          </div>
          <div v-if="campaign.deliverables" class="mt-6">
            <h3 class="text-sm font-semibold text-[#1a1a1a]">Deliverables</h3>
            <p class="mt-1 whitespace-pre-wrap text-sm text-[#64748b]">{{ campaign.deliverables }}</p>
          </div>
          <div v-if="campaign.targeting && hasTargeting(campaign.targeting)" class="mt-6">
            <h3 class="text-sm font-semibold text-[#1a1a1a]">Targeting</h3>
            <ul class="mt-2 space-y-1 text-sm text-[#64748b]">
              <li v-if="campaign.targeting.niches?.length">Niches: {{ campaign.targeting.niches.join(', ') }}</li>
              <li v-if="campaign.targeting.countries?.length">Countries: {{ campaign.targeting.countries.join(', ') }}</li>
              <li v-if="campaign.targeting.languages?.length">Languages: {{ campaign.targeting.languages.join(', ') }}</li>
              <li v-if="campaign.targeting.follower_ranges?.length">Follower ranges: {{ campaign.targeting.follower_ranges.join(', ') }}</li>
            </ul>
          </div>
        </div>

        <!-- Actions: Apply + Chat -->
        <div class="border-t border-[#e2e8f0] bg-[#f8fafc] p-6 md:p-8">
          <h3 class="text-lg font-semibold text-[#1a1a1a]">Show your interest</h3>
          <p class="mt-1 text-sm text-[#64748b]">Apply to this campaign and optionally start a conversation with the brand.</p>

          <!-- Already applied -->
          <div v-if="hasApplied" class="mt-4 rounded-xl bg-[#ecfdf5] border border-[#a7f3d0] p-4">
            <p class="font-medium text-[#059669]">You have already applied to this campaign.</p>
            <router-link v-if="isCreator" to="/creator/collaborations" class="mt-2 inline-block text-sm text-[#e63946] hover:underline">View my applications</router-link>
          </div>

          <!-- Apply form (creator, not applied) -->
          <template v-else-if="isCreator">
            <form class="mt-4 space-y-4" @submit.prevent="submitApply">
              <div>
                <label class="mb-1 block text-sm font-medium text-[#475569]">Cover message (optional)</label>
                <textarea
                  v-model="applyForm.cover_message"
                  rows="4"
                  class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                  placeholder="Introduce yourself and why you're a good fit..."
                />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-[#475569]">Quoted amount (₹, optional)</label>
                <input
                  v-model.number="applyForm.quoted_amount"
                  type="number"
                  min="0"
                  step="100"
                  class="w-full max-w-xs rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                  placeholder="0"
                />
              </div>
              <div v-if="applyError" class="rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700">{{ applyError }}</div>
              <div class="flex flex-wrap gap-3">
                <button
                  type="submit"
                  :disabled="applying"
                  class="rounded-xl bg-[#e63946] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#c1121f] disabled:opacity-60"
                >
                  {{ applying ? 'Submitting…' : 'Apply to campaign' }}
                </button>
                <router-link
                  to="/login"
                  class="rounded-xl border border-[#e2e8f0] bg-white px-6 py-3 text-sm font-medium text-[#64748b] hover:bg-[#f1f5f9]"
                >
                  Save draft
                </router-link>
              </div>
            </form>
          </template>

          <!-- Not logged in or not creator -->
          <div v-else class="mt-4 flex flex-wrap gap-3">
            <router-link
              :to="'/login?redirect=' + encodeURIComponent($route.fullPath)"
              class="inline-flex items-center rounded-xl bg-[#e63946] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#c1121f]"
            >
              Log in to apply
            </router-link>
            <router-link
              to="/register?type=creator"
              class="inline-flex items-center rounded-xl border border-[#e2e8f0] bg-white px-6 py-3 text-sm font-medium text-[#64748b] hover:bg-[#f1f5f9]"
            >
              Join as creator
            </router-link>
          </div>

          <!-- Chat section: show after apply or as "Contact brand" -->
          <div class="mt-8 pt-6 border-t border-[#e2e8f0]">
            <h3 class="text-base font-semibold text-[#1a1a1a]">Chat with brand</h3>
            <p class="mt-1 text-sm text-[#64748b]">After applying, you can message the brand here to discuss the campaign.</p>
            <div v-if="hasApplied && isCreator && campaign.brand?.id" class="mt-4">
              <router-link
                :to="'/creator/messages?user=' + campaign.brand.id + '&name=' + encodeURIComponent(campaign.brand.name || 'Brand')"
                class="inline-flex items-center gap-2 rounded-xl bg-[#1a1a1a] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#374151]"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                Open chat
              </router-link>
            </div>
            <p v-else class="mt-3 text-sm text-[#94a3b8]">Apply first to unlock chat with the brand.</p>
          </div>
        </div>
      </article>
    </template>

    <div v-else class="rounded-2xl border border-[#e2e8f0] bg-white p-12 text-center">
      <p class="text-[#64748b]">Campaign not found or no longer open.</p>
      <router-link to="/campaigns" class="mt-4 inline-block text-[#e63946] font-medium hover:underline">Browse campaigns</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const campaign = ref(null);
const loading = ref(true);
const isCreator = ref(false);
const hasApplied = ref(false);
const applying = ref(false);
const applyError = ref('');
const applyForm = reactive({ cover_message: '', quoted_amount: '' });

function typeLabel(type) {
  const map = { instagram: 'Instagram', tiktok: 'TikTok', ugc: 'UGC', youtube: 'YouTube' };
  return type ? (map[type] || type) : '';
}

function formatNumber(n) {
  if (n >= 100000) return (n / 100000).toFixed(1) + 'L';
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
  return n;
}

function hasTargeting(t) {
  return (t.niches?.length || t.countries?.length || t.languages?.length || t.follower_ranges?.length);
}

async function loadCampaign() {
  const slug = route.params.slug;
  if (!slug) return;
  loading.value = true;
  try {
    const res = await axios.get('/api/campaigns/slug/' + encodeURIComponent(slug));
    campaign.value = res.data;
    await checkMeAndApplication();
  } catch (e) {
    if (e.response?.status === 404) campaign.value = null;
  } finally {
    loading.value = false;
  }
}

async function checkMeAndApplication() {
  try {
    const me = await axios.get('/api/me', { withCredentials: true });
    const user = me.data;
    isCreator.value = user?.role === 'creator';
    if (isCreator.value && campaign.value?.id) {
      const appRes = await axios.get('/api/creator/campaign-applications', {
        withCredentials: true,
        params: { per_page: 100 },
      });
      const data = appRes.data?.data ?? appRes.data;
      const list = Array.isArray(data) ? data : (data && data.data) ? data.data : [];
      hasApplied.value = list.some((a) => a.campaign_id === campaign.value.id || a.campaign?.id === campaign.value.id);
    }
  } catch (_) {
    isCreator.value = false;
    hasApplied.value = false;
  }
}

async function submitApply() {
  applyError.value = '';
  applying.value = true;
  try {
    await axios.post(
      '/api/creator/campaign-applications',
      {
        campaign_id: campaign.value.id,
        cover_message: applyForm.cover_message || undefined,
        quoted_amount: applyForm.quoted_amount || undefined,
      },
      { withCredentials: true }
    );
    hasApplied.value = true;
    applyForm.cover_message = '';
    applyForm.quoted_amount = '';
  } catch (e) {
    applyError.value = e.response?.data?.message || 'Failed to apply.';
  } finally {
    applying.value = false;
  }
}

// SEO
function setSeo() {
  if (!campaign.value) return;
  const title = (campaign.value.title || 'Campaign') + ' | StarJD Campaigns';
  document.title = title;
  let meta = document.querySelector('meta[name="description"]');
  const desc = campaign.value.description ? campaign.value.description.slice(0, 160) : 'Apply to this influencer marketing campaign on StarJD.';
  if (!meta) {
    meta = document.createElement('meta');
    meta.name = 'description';
    document.head.appendChild(meta);
  }
  meta.setAttribute('content', desc);
}

onMounted(loadCampaign);
watch(() => route.params.slug, loadCampaign);
watch(campaign, (c) => { if (c) setSeo(); }, { deep: true });
</script>
