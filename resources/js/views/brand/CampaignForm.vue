<template>
  <div class="max-w-4xl mx-auto pb-12">
    <nav class="mb-6 text-sm text-[#64748b]">
      <router-link to="/brand/post-campaign" class="hover:text-[#e63946]">Post Campaign</router-link>
      <span class="mx-2">/</span>
      <span class="text-[#1a1a1a]">{{ isEdit ? 'Edit Campaign' : 'Create Campaign' }}</span>
    </nav>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">{{ isEdit ? 'Edit Campaign' : 'Post a Campaign' }}</h1>
      <p class="mt-1 text-[#64748b]">Fill out the details below to {{ isEdit ? 'update your campaign targeting' : 'launch a new campaign and find creators' }}.</p>
    </div>

    <div v-if="loading" class="rounded-xl border border-[#e2e8f0] bg-white p-10 text-center shadow-sm">
      <p class="text-[#64748b]">Loading…</p>
    </div>
    <form v-else @submit.prevent="submitForm" class="space-y-8 rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm sm:p-10">
      
      <!-- Basic Details -->
      <div>
        <h2 class="text-lg font-bold text-[#1a1a1a] mb-4">Basic Details</h2>
        <div class="grid gap-6 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Campaign Title <span class="text-red-500">*</span></label>
            <input v-model="form.title" required type="text" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="e.g. Summer Fashion Haul" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Campaign Type <span class="text-red-500">*</span></label>
            <select v-model="form.campaign_type" required class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
              <option v-for="opt in campaignTypes" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Influencer Count <span class="text-red-500">*</span></label>
            <select v-model.number="form.influencer_count" required class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
              <option v-for="n in 100" :key="n" :value="n">{{ n }}</option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-sm font-medium text-[#475569]">Campaign description <span class="text-[#94a3b8]">(optional)</span></label>
              <button type="button" @click="suggestAI('campaign_description')" :disabled="aiLoading['campaign_description']" class="text-xs font-semibold text-[#e63946] hover:text-[#c1121f] flex items-center gap-1 disabled:opacity-50">
                <span v-if="aiLoading['campaign_description']" class="h-3 w-3 animate-spin border-2 border-[#e63946] border-t-transparent rounded-full"></span>
                <span v-else>✨</span> {{ aiLoading['campaign_description'] ? (form.description ? 'Enhancing...' : 'Generating...') : (form.description ? 'Enhance with AI' : 'Suggest with AI') }}
              </button>
            </div>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
              placeholder="Describe what this campaign is about and what you expect from creators…"
            ></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Embed post/video link <span class="text-[#94a3b8]">(optional)</span></label>
            <input
              v-model="form.embed_url"
              type="url"
              class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-sm text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
              placeholder="Paste a YouTube, Instagram, or other public post link"
            />
            <p class="mt-1 text-xs text-[#94a3b8]">We will show this post or video on your campaign page where possible.</p>
          </div>
        </div>
      </div>

      <!-- Targeting Details -->
      <div class="border-t border-[#e2e8f0] pt-8">
        <h2 class="text-lg font-bold text-[#1a1a1a] mb-4">Targeting Details</h2>
        <p class="text-sm text-[#64748b] mb-6">Narrow down the perfect creators for your campaign. All targeting options are optional.</p>
        
        <div class="grid gap-6 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Niches / Categories</label>
            <select v-model="form.niches" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 120px">
              <option v-for="c in filterOptions.categories" :key="c.id" :value="c.name">{{ c.name }}</option>
            </select>
            <p class="mt-1 text-xs text-[#94a3b8]">Hold Ctrl/Cmd to select multiple</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Follower Ranges</label>
            <select v-model="form.follower_ranges" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 120px">
              <option v-for="r in followerRanges" :key="r" :value="r">{{ r }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Countries</label>
            <select v-model="form.countries" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 120px">
              <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Cities / Regions</label>
            <select v-model="form.state_id" class="mb-2 w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
              <option value="">Filter by state/region…</option>
              <option v-for="s in statesList" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
            <select v-model="form.cities" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 72px">
              <option v-for="city in citiesList" :key="city.id" :value="city.name">{{ city.name }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Genders</label>
            <select v-model="form.genders" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
              <option v-for="(label, key) in filterOptions.genders" :key="key" :value="key">{{ label }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Ages</label>
            <select v-model="form.ages" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
              <option v-for="age in ageRanges" :key="age" :value="age">{{ age }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Ethnicities</label>
            <select v-model="form.ethnicities" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
              <option v-for="e in ethnicities" :key="e" :value="e">{{ e }}</option>
            </select>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-[#475569]">Languages</label>
            <select v-model="form.languages" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
              <option v-for="lang in filterOptions.languages" :key="lang" :value="lang">{{ lang }}</option>
            </select>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-4 border-t border-[#e2e8f0] pt-6">
        <router-link to="/brand/post-campaign" class="text-sm font-medium text-[#64748b] hover:text-[#1a1a1a]">Cancel</router-link>
        <button
          type="submit"
          :disabled="saving"
          class="rounded-xl bg-[#e63946] px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-[#e63946]/30 transition hover:bg-[#c1121f] focus:outline-none focus:ring-2 focus:ring-[#e63946] focus:ring-offset-2 disabled:opacity-60"
        >
          {{ saving ? 'Saving…' : (isEdit ? 'Save Changes' : 'Create Campaign') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { notify } from '../../lib/notify.js';

const route = useRoute();
const router = useRouter();

const isEdit = ref(false);
const loading = ref(false);
const saving = ref(false);
const aiLoading = reactive({});
const brandProfile = ref(null);

const campaignTypes = [
  { value: 'instagram', label: 'Instagram', icon: '📷' },
  { value: 'tiktok', label: 'TikTok', icon: '🎵' },
  { value: 'ugc', label: 'User generated content', icon: '✨' },
  { value: 'youtube', label: 'YouTube', icon: '▶️' },
];

const followerRanges = ['1K–10K', '10K–50K', '50K–100K', '100K–500K', '500K–1M', '1M+'];
const ageRanges = ['13–17', '18–24', '25–34', '35–44', '45–54', '55+'];
const ethnicities = [
  'Caucasian',
  'Hispanic or Latino',
  'Black or African American',
  'Asian/Pacific Islander',
  'Native American or American Indian',
  'Other',
];
const countries = [
  'India', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France', 'Spain', 'Italy', 'Brazil', 'Mexico', 'Japan', 'South Korea', 'Singapore', 'UAE', 'Other',
];

const filterOptions = reactive({ categories: [], genders: {}, languages: [] });
const statesList = ref([]);
const citiesList = ref([]);

const form = reactive({
  title: '',
  campaign_type: 'instagram',
  influencer_count: 5,
  description: '',
  embed_url: '',
  niches: [],
  follower_ranges: [],
  countries: [],
  state_id: '',
  cities: [],
  genders: [],
  ages: [],
  ethnicities: [],
  languages: [],
});

async function loadCities(stateId) {
  if (!stateId) {
    citiesList.value = [];
    return;
  }
  try {
    const res = await axios.get('/api/cities', { params: { state_id: stateId } });
    citiesList.value = res.data ?? [];
  } catch (_) {
    citiesList.value = [];
  }
}

watch(() => form.state_id, (id) => {
  loadCities(id);
  if (!id) form.cities = [];
});

onMounted(async () => {
  loading.value = true;
  
  // Load lookup data
  try {
    const [resFilters, resStates] = await Promise.all([
      axios.get('/api/creators/options/filters'),
      axios.get('/api/states')
    ]);
    filterOptions.categories = resFilters.data.categories ?? [];
    filterOptions.genders = resFilters.data.genders ?? {};
    filterOptions.languages = resFilters.data.languages ?? [];
    statesList.value = resStates.data ?? [];
  } catch (e) {
    console.error('Failed to load filter options');
  }

  if (route.params.id) {
    isEdit.value = true;
    await loadCampaign(route.params.id);
  }
  
  loading.value = false;
});

async function loadCampaign(id) {
  try {
    const res = await axios.get('/api/brand/campaigns/' + id, { withCredentials: true });
    const c = res.data;
    
    form.title = c.title || '';
    form.campaign_type = c.campaign_type || 'instagram';
    form.influencer_count = c.max_applications || 5;
    form.description = c.description || '';
    
    const targeting = c.targeting || {};
    form.embed_url = targeting.embed_url || '';
    form.niches = targeting.niches || [];
    form.follower_ranges = targeting.follower_ranges || [];
    form.countries = targeting.countries || [];
    form.cities = targeting.cities || [];
    form.genders = targeting.genders || [];
    form.ages = targeting.ages || [];
    form.ethnicities = targeting.ethnicities || [];
    form.languages = targeting.languages || [];
    
  } catch (e) {
    notify.error('Failed to load campaign details.');
    router.push('/brand/post-campaign');
  }
}

async function suggestAI(type) {
  aiLoading[type] = true;
  try {
    if (!brandProfile.value) {
        const resProfile = await axios.get('/api/brand/profile', { withCredentials: true });
        brandProfile.value = resProfile.data.profile ?? resProfile.data;
    }
    const context = {
        company_name: brandProfile.value?.company_name,
        campaign_type: form.campaign_type,
        current_description: type === 'campaign_description' ? form.description : undefined
    };
    const res = await axios.post('/api/ai-suggest/generic', { type, context }, { withCredentials: true });
    if (res.data.suggestion) {
      if (type === 'campaign_description') form.description = res.data.suggestion;
    }
  } catch (e) {
    notify.error(e.response?.data?.error || 'AI suggestion failed.');
  } finally {
    aiLoading[type] = false;
  }
}

async function submitForm() {
  saving.value = true;
  try {
    const payload = {
      title: form.title,
      campaign_type: form.campaign_type,
      influencer_count: form.influencer_count,
      description: form.description || null,
      embed_url: form.embed_url || null,
      niches: Array.isArray(form.niches) ? [...form.niches] : [],
      follower_ranges: Array.isArray(form.follower_ranges) ? [...form.follower_ranges] : [],
      countries: Array.isArray(form.countries) ? [...form.countries] : [],
      cities: Array.isArray(form.cities) ? [...form.cities] : [],
      genders: Array.isArray(form.genders) ? [...form.genders] : [],
      ages: Array.isArray(form.ages) ? [...form.ages] : [],
      ethnicities: Array.isArray(form.ethnicities) ? [...form.ethnicities] : [],
      languages: Array.isArray(form.languages) ? [...form.languages] : [],
    };
    
    if (isEdit.value) {
      await axios.put('/api/brand/campaigns/' + route.params.id, payload, { withCredentials: true });
      notify.success('Campaign updated successfully.');
      router.push('/brand/campaigns/' + route.params.id);
    } else {
      const res = await axios.post('/api/brand/campaigns', payload, { withCredentials: true });
      notify.success('Campaign created successfully.');
      // After creation, show plan selection page (or redirect to detail)
      router.push('/brand/choose-plan');
    }
  } catch (e) {
    console.error(e);
    notify.error(e.response?.data?.message || 'Failed to save campaign.');
  } finally {
    saving.value = false;
  }
}
</script>
