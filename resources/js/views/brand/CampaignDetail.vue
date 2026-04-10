<template>
  <div>
    <div v-if="loading" class="rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
      <p class="text-[#64748b]">Loading campaign…</p>
    </div>
    <template v-else-if="campaign">
      <nav class="mb-6 text-sm text-[#64748b]">
        <router-link to="/brand/post-campaign" class="hover:text-[#e63946]">Post Campaign</router-link>
        <span class="mx-2">/</span>
        <span class="text-[#1a1a1a]">{{ campaign.title }}</span>
      </nav>

      <div class="rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-2">
          <span class="rounded-full bg-[#e63946]/10 px-3 py-1 text-sm font-semibold text-[#e63946]">{{ typeLabel(campaign.campaign_type) }}</span>
          <span
            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
            :class="statusClass(campaign.status)"
          >
            {{ statusLabel(campaign.status) }}
          </span>
        </div>
        <h1 class="mt-4 text-2xl font-bold text-[#1a1a1a]">{{ campaign.title }}</h1>
        <div class="mt-3 flex flex-wrap gap-4 text-sm text-[#64748b]">
          <span v-if="campaign.budget != null && campaign.budget > 0">Budget: ₹{{ formatNumber(campaign.budget) }}</span>
          <span v-if="campaign.max_applications">Max applications: {{ campaign.max_applications }}</span>
          <span>Created {{ formatDate(campaign.created_at) }}</span>
        </div>
        <p v-if="campaign.description" class="mt-4 whitespace-pre-wrap text-[#475569]">{{ campaign.description }}</p>
        <div v-if="campaign.embed_url" class="mt-4">
          <div
            v-if="isYouTube(campaign.embed_url)"
            class="relative w-full overflow-hidden rounded-xl bg-black pt-[56.25%]"
          >
            <iframe
              class="absolute inset-0 h-full w-full"
              :src="youtubeEmbedUrl(campaign.embed_url)"
              title="Campaign video"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen
            ></iframe>
          </div>
          <div
            v-else-if="isInstagram(campaign.embed_url)"
            class="relative w-full overflow-hidden rounded-xl bg-[#f8fafc] pt-[125%]"
          >
            <iframe
              class="absolute inset-0 h-full w-full"
              :src="instagramEmbedUrl(campaign.embed_url)"
              title="Instagram post"
              frameborder="0"
              scrolling="no"
              allowtransparency="true"
            ></iframe>
          </div>
          <div v-else class="rounded-xl bg-[#f8fafc] p-4 text-sm text-[#475569]">
            <p class="mb-2 font-medium text-[#1a1a1a]">Campaign post</p>
            <a
              :href="campaign.embed_url"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center text-sm font-medium text-[#e63946] hover:underline"
            >
              View brand post
              <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h4m0 0v4m0-4L10 14" />
              </svg>
            </a>
          </div>
        </div>
        <p v-if="campaign.deliverables" class="mt-3 text-sm text-[#64748b]"><span class="font-medium text-[#1a1a1a]">Deliverables:</span> {{ campaign.deliverables }}</p>

        <!-- Simple edit section for description & embed URL -->
        <div class="mt-6 border-t border-[#e2e8f0] pt-4">
          <h2 class="text-sm font-semibold text-[#1a1a1a]">Edit campaign details</h2>
          <p class="mt-1 text-xs text-[#94a3b8]">Update the description and embedded post/video link for this campaign.</p>
          <form class="mt-3 space-y-3 max-w-xl" @submit.prevent="saveEdits">
            <div>
              <label class="mb-1 block text-xs font-medium text-[#475569]">Campaign description</label>
              <textarea
                v-model="editDescription"
                rows="3"
                class="w-full rounded-xl border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
              />
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-[#475569]">Embed post/video link</label>
              <input
                v-model="editEmbedUrl"
                type="url"
                class="w-full rounded-xl border border-[#e2e8f0] px-3 py-2 text-sm text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                placeholder="Paste Instagram, YouTube or other public post link"
              />
            </div>
            <p v-if="editError" class="text-xs text-red-600">{{ editError }}</p>
            <div class="flex gap-2">
              <button
                type="submit"
                :disabled="savingEdits"
                class="rounded-xl bg-[#e63946] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#c1121f] disabled:opacity-60"
              >
                {{ savingEdits ? 'Saving…' : 'Save changes' }}
              </button>
              <button
                type="button"
                class="rounded-xl border border-[#e2e8f0] px-4 py-2 text-xs font-medium text-[#64748b] hover:bg-[#f1f5f9]"
                @click="resetEdits"
              >
                Reset
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Applications -->
      <div class="mt-8">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Applications</h2>
        <p class="mt-1 text-sm text-[#64748b]">Review creator applications and reach out to collaborate.</p>
        <div v-if="!campaign.applications?.length" class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
          <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f1f5f9]">
            <svg class="h-7 w-7 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
          </div>
          <p class="mt-4 text-[#64748b]">No applications yet.</p>
          <p class="mt-1 text-sm text-[#94a3b8]">When creators apply, they will appear here.</p>
          <router-link to="/campaigns" class="mt-4 inline-block text-sm font-medium text-[#e63946] hover:underline">View public campaign page</router-link>
        </div>
        <div v-else class="mt-4 overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
              <thead>
                <tr class="border-b border-[#e2e8f0] bg-[#f8fafc]">
                  <th class="px-5 py-4 font-semibold text-[#475569]">Creator</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Message</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Quoted amount</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Status</th>
                  <th class="px-5 py-4 font-semibold text-[#475569]">Applied</th>
                  <th class="px-5 py-4 font-semibold text-[#475569] text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="app in campaign.applications"
                  :key="app.id"
                  class="border-b border-[#f1f5f9] transition hover:bg-[#fafafa]"
                >
                  <td class="px-5 py-4">
                    <router-link
                      v-if="app.creator?.profile_slug"
                      :to="'/creator-profile/' + app.creator.profile_slug"
                      class="font-medium text-[#e63946] hover:underline"
                    >
                      {{ app.creator?.name || 'Creator' }}
                    </router-link>
                    <span v-else class="font-medium text-[#1a1a1a]">{{ app.creator?.name || 'Creator' }}</span>
                  </td>
                  <td class="max-w-xs px-5 py-4">
                    <p class="line-clamp-2 text-[#475569]">{{ app.cover_message || '—' }}</p>
                  </td>
                  <td class="px-5 py-4">
                    <span v-if="app.quoted_amount != null && app.quoted_amount > 0" class="font-medium text-[#1a1a1a]">₹{{ formatNumber(app.quoted_amount) }}</span>
                    <span v-else class="text-[#94a3b8]">—</span>
                  </td>
                  <td class="px-5 py-4">
                    <span
                      class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                      :class="applicationStatusClass(app.status)"
                    >
                      {{ applicationStatusLabel(app.status) }}
                    </span>
                  </td>
                  <td class="px-5 py-4 text-[#64748b]">{{ formatDate(app.created_at) }}</td>
                  <td class="px-5 py-4 text-right">
                    <router-link
                      v-if="app.creator?.profile_slug"
                      :to="'/creator-profile/' + app.creator.profile_slug"
                      class="mr-2 inline-flex items-center text-sm text-[#e63946] hover:underline"
                    >
                      View profile
                    </router-link>
                    <router-link
                      v-if="app.creator?.id"
                      :to="{ name: 'brand-messages', query: { user: app.creator.id } }"
                      class="mr-2 inline-flex items-center text-sm text-[#3b82f6] hover:underline"
                    >
                      Message
                    </router-link>
                    <template v-if="app.status === 'pending'">
                      <button
                        type="button"
                        class="mr-2 inline-flex items-center text-sm font-medium text-[#059669] hover:underline disabled:opacity-50"
                        :disabled="applicationActionLoading === app.id"
                        @click="approveApplication(app)"
                      >
                        {{ applicationActionLoading === app.id ? '…' : 'Approve' }}
                      </button>
                      <button
                        type="button"
                        class="mr-2 inline-flex items-center text-sm text-[#dc2626] hover:underline disabled:opacity-50"
                        :disabled="applicationActionLoading === app.id"
                        @click="rejectApplication(app)"
                      >
                        Reject
                      </button>
                      <button
                        type="button"
                        class="inline-flex items-center text-sm font-medium text-[#e63946] hover:underline"
                        @click="openCollaborateModal(app)"
                      >
                        Collaborate
                      </button>
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
    <div v-else class="rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
      <p class="text-[#64748b]">Campaign not found.</p>
      <router-link to="/brand/post-campaign" class="mt-2 inline-block text-[#e63946] hover:underline">Back to Post Campaign</router-link>
    </div>

    <!-- Collaborate modal -->
    <div v-if="showCollabModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showCollabModal = false">
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Send collaboration request</h2>
        <p class="mt-1 text-sm text-[#64748b]">Send a formal collaboration offer to {{ selectedAppForCollab?.creator?.name || 'creator' }}. They can accept in their Collaborations.</p>
        <form class="mt-4 space-y-4" @submit.prevent="submitCollaboration">
          <div>
            <label class="mb-1 block text-sm font-medium text-[#475569]">Amount (₹)</label>
            <input
              v-model.number="collabForm.amount"
              type="number"
              min="1"
              step="100"
              required
              class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#475569]">Notes (optional)</label>
            <textarea
              v-model="collabForm.brand_notes"
              rows="3"
              class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
              placeholder="Deliverables, timeline, or instructions..."
            />
          </div>
          <p v-if="collabError" class="rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700">{{ collabError }}</p>
          <div class="flex gap-2 pt-2">
            <button
              type="submit"
              :disabled="collabLoading"
              class="cursor-link rounded-xl bg-[#e63946] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f] disabled:opacity-50"
            >
              {{ collabLoading ? 'Sending…' : 'Send request' }}
            </button>
            <button
              type="button"
              class="cursor-link rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm hover:bg-[#f1f5f9]"
              @click="showCollabModal = false"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
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
const applicationActionLoading = ref(null);
const showCollabModal = ref(false);
const selectedAppForCollab = ref(null);
const collabForm = reactive({ amount: 0, brand_notes: '' });
const collabError = ref('');
const collabLoading = ref(false);
const editDescription = ref('');
const editEmbedUrl = ref('');
const savingEdits = ref(false);
const editError = ref('');

function typeLabel(type) {
  const map = { instagram: 'Instagram', tiktok: 'TikTok', ugc: 'UGC', youtube: 'YouTube' };
  return type ? (map[type] || type) : '—';
}

function statusLabel(status) {
  const map = { draft: 'Draft', open: 'Open', in_progress: 'In progress', completed: 'Completed', cancelled: 'Cancelled' };
  return status ? (map[status] || status) : '—';
}

function statusClass(status) {
  const map = {
    draft: 'bg-slate-100 text-slate-700',
    open: 'bg-emerald-100 text-emerald-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-slate-100 text-slate-600',
    cancelled: 'bg-red-100 text-red-700',
  };
  return map[status] || 'bg-slate-100 text-slate-600';
}

function applicationStatusLabel(status) {
  const map = { pending: 'Pending', approved: 'Approved', rejected: 'Rejected' };
  return status ? (map[status] || status) : '—';
}

function applicationStatusClass(status) {
  const map = {
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-red-100 text-red-700',
  };
  return map[status] || 'bg-slate-100 text-slate-600';
}

function formatNumber(n) {
  if (n == null) return '—';
  const num = Number(n);
  if (num >= 100000) return (num / 100000).toFixed(1) + 'L';
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
  return num.toLocaleString();
}

function formatDate(iso) {
  if (!iso) return '—';
  const d = new Date(iso);
  return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
}

function isYouTube(url) {
  if (!url) return false;
  return /youtu\.be\/|youtube\.com\/watch\?v=|youtube\.com\/embed\//i.test(url);
}

function youtubeEmbedUrl(url) {
  if (!url) return '';
  try {
    const u = new URL(url);
    if (u.hostname.includes('youtu.be')) {
      const id = u.pathname.replace('/', '');
      return id ? `https://www.youtube.com/embed/${id}` : '';
    }
    if (u.searchParams.get('v')) {
      const id = u.searchParams.get('v');
      return `https://www.youtube.com/embed/${id}`;
    }
    if (u.pathname.includes('/embed/')) {
      return url;
    }
  } catch {
    return '';
  }
  return '';
}

function isInstagram(url) {
  if (!url) return false;
  return /instagram\.com\/(p|reel|tv)\//i.test(url);
}

function instagramEmbedUrl(url) {
  if (!url) return '';
  try {
    const u = new URL(url);
    if (!u.pathname.includes('/embed')) {
      if (!u.pathname.endsWith('/')) {
        u.pathname = u.pathname + '/';
      }
      u.pathname = u.pathname + 'embed/';
    }
    u.searchParams.set('cr', '1');
    u.searchParams.set('v', '14');
    u.searchParams.set('wp', '1');
    return u.toString();
  } catch {
    return url;
  }
}

async function loadCampaign() {
  const id = route.params.id;
  if (!id) return;
  loading.value = true;
  try {
    const res = await axios.get('/api/brand/campaigns/' + id, { withCredentials: true });
    campaign.value = res.data;
    editDescription.value = campaign.value.description || '';
    editEmbedUrl.value = campaign.value.embed_url || '';
  } catch {
    campaign.value = null;
  } finally {
    loading.value = false;
  }
}

async function saveEdits() {
  if (!campaign.value?.id) return;
  savingEdits.value = true;
  editError.value = '';
  try {
    const res = await axios.patch(
      '/api/brand/campaigns/' + campaign.value.id,
      {
        description: editDescription.value || null,
        embed_url: editEmbedUrl.value || null,
      },
      { withCredentials: true }
    );
    campaign.value = res.data.campaign;
  } catch (e) {
    editError.value = e.response?.data?.message || 'Failed to save changes.';
  } finally {
    savingEdits.value = false;
  }
}

function resetEdits() {
  if (!campaign.value) return;
  editDescription.value = campaign.value.description || '';
  editEmbedUrl.value = campaign.value.embed_url || '';
  editError.value = '';
}

async function approveApplication(app) {
  applicationActionLoading.value = app.id;
  try {
    await axios.patch('/api/brand/campaign-applications/' + app.id, { status: 'approved' }, { withCredentials: true });
    await loadCampaign();
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to approve.');
  } finally {
    applicationActionLoading.value = null;
  }
}

async function rejectApplication(app) {
  if (!confirm('Reject this application? The creator will no longer be marked as pending.')) return;
  applicationActionLoading.value = app.id;
  try {
    await axios.patch('/api/brand/campaign-applications/' + app.id, { status: 'rejected' }, { withCredentials: true });
    await loadCampaign();
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to reject.');
  } finally {
    applicationActionLoading.value = null;
  }
}

function openCollaborateModal(app) {
  selectedAppForCollab.value = app;
  collabForm.amount = app.quoted_amount > 0 ? Number(app.quoted_amount) : '';
  collabForm.brand_notes = '';
  collabError.value = '';
  showCollabModal.value = true;
}

async function submitCollaboration() {
  const app = selectedAppForCollab.value;
  if (!app?.creator?.id) return;
  const amount = Number(collabForm.amount);
  if (!amount || amount < 1) {
    collabError.value = 'Enter a valid amount (₹1 or more).';
    return;
  }
  collabError.value = '';
  collabLoading.value = true;
  try {
    await axios.post(
      '/api/collaborations',
      {
        creator_id: app.creator.id,
        amount,
        brand_notes: collabForm.brand_notes || null,
      },
      { withCredentials: true }
    );
    await axios.patch('/api/brand/campaign-applications/' + app.id, { status: 'approved' }, { withCredentials: true });
    await loadCampaign();
    showCollabModal.value = false;
    selectedAppForCollab.value = null;
    alert('Collaboration request sent. The creator can accept it in their Collaborations.');
  } catch (e) {
    collabError.value = e.response?.data?.message || 'Failed to send collaboration request.';
  } finally {
    collabLoading.value = false;
  }
}

onMounted(loadCampaign);
watch(() => route.params.id, loadCampaign);
</script>
