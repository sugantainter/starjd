<template>
  <div>
    <!-- Step 1: Trigger button -->
    <button
      type="button"
      class="inline-flex items-center gap-2 rounded-xl bg-[#e63946] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#e63946]/30 transition hover:bg-[#c1121f] hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-[#e63946] focus:ring-offset-2"
      @click="openStep(1)"
    >
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      Post a Campaign
    </button>

    <!-- Modal 1: Campaign type -->
    <Teleport to="body">
      <div
        v-if="step === 1"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="campaign-type-title"
      >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close" />
        <div class="relative w-full max-w-lg rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-2xl">
          <div class="flex items-center justify-between">
            <h2 id="campaign-type-title" class="text-xl font-bold text-[#1a1a1a]">What type of campaign do you want to run?</h2>
            <button type="button" class="rounded-lg p-1 text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" aria-label="Close" @click="close">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <div class="mt-6 grid grid-cols-2 gap-3">
            <button
              v-for="opt in campaignTypes"
              :key="opt.value"
              type="button"
              class="flex flex-col items-center gap-2 rounded-xl border-2 p-5 text-left transition"
              :class="form.campaign_type === opt.value ? 'border-[#e63946] bg-[#e63946]/5' : 'border-[#e2e8f0] hover:border-[#e63946]/50 hover:bg-[#f8fafc]'"
              @click="selectType(opt.value)"
            >
              <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f1f5f9] text-2xl">{{ opt.icon }}</span>
              <span class="font-semibold text-[#1a1a1a]">{{ opt.label }}</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal 2: Set Campaign Targeting -->
    <Teleport to="body">
      <div
        v-if="step === 2"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="targeting-title"
      >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close" />
        <div class="relative my-8 w-full max-w-2xl rounded-2xl border border-[#e2e8f0] bg-white shadow-2xl">
          <div class="sticky top-0 z-10 flex items-center justify-between border-b border-[#e2e8f0] bg-white px-6 py-4">
            <h2 id="targeting-title" class="text-xl font-bold text-[#1a1a1a]">Set Campaign Targeting</h2>
            <button type="button" class="rounded-lg p-1 text-[#64748b] hover:bg-[#f1f5f9]" aria-label="Close" @click="close">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <form class="max-h-[70vh] overflow-y-auto px-6 pb-6" @submit.prevent="submitTargeting">
            <div class="space-y-5 pt-6">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-[#475569]">What type of campaign do you want to run?</label>
                <select v-model="form.campaign_type" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
                  <option v-for="opt in campaignTypes" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-[#475569]">How many influencers do you want to hire for this campaign?</label>
                <select v-model.number="form.influencer_count" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
                  <option v-for="n in 50" :key="n" :value="n">{{ n }}</option>
                </select>
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-[#475569]">What niches do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                <select v-model="form.niches" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
                  <option v-for="c in filterOptions.categories" :key="c" :value="c">{{ c }}</option>
                </select>
                <p class="mt-1 text-xs text-[#94a3b8]">Hold Ctrl/Cmd to select multiple</p>
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-[#475569]">What follower ranges do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                <select v-model="form.follower_ranges" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
                  <option v-for="r in followerRanges" :key="r" :value="r">{{ r }}</option>
                </select>
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-[#475569]">What countries do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                <select v-model="form.countries" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
                  <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
                </select>
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-[#475569]">What cities/regions do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                <select v-model="form.state_id" class="mb-2 w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]">
                  <option value="">Select state/region…</option>
                  <option v-for="s in statesList" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
                <select v-model="form.cities" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 88px">
                  <option v-for="city in citiesList" :key="city.id" :value="city.name">{{ city.name }}</option>
                </select>
                <p class="mt-1 text-xs text-[#94a3b8]">Hold Ctrl/Cmd to select multiple</p>
              </div>

              <!-- View Advanced Filters -->
              <div class="border-t border-[#e2e8f0] pt-5">
                <button
                  type="button"
                  class="flex w-full items-center justify-between rounded-lg py-2 text-sm font-medium text-[#475569] hover:bg-[#f1f5f9]"
                  @click="showAdvanced = !showAdvanced"
                >
                  View Advanced Filters
                  <svg class="h-5 w-5 transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div v-show="showAdvanced" class="mt-4 space-y-4">
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#475569]">What genders do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                    <select v-model="form.genders" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 72px">
                      <option v-for="(label, key) in filterOptions.genders" :key="key" :value="key">{{ label }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#475569]">What ages do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                    <select v-model="form.ages" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 72px">
                      <option v-for="age in ageRanges" :key="age" :value="age">{{ age }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#475569]">What ethnicities do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                    <select v-model="form.ethnicities" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 72px">
                      <option v-for="e in ethnicities" :key="e" :value="e">{{ e }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#475569]">What languages do you want to target? <span class="text-[#94a3b8]">(optional)</span></label>
                    <select v-model="form.languages" multiple class="w-full rounded-xl border border-[#e2e8f0] px-4 py-2.5 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" style="min-height: 72px">
                      <option v-for="lang in filterOptions.languages" :key="lang" :value="lang">{{ lang }}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-6 flex justify-end border-t border-[#e2e8f0] pt-4">
              <button
                type="submit"
                :disabled="saving"
                class="rounded-xl bg-[#e63946] px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-[#c1121f] disabled:opacity-60"
              >
                {{ saving ? 'Saving…' : 'Continue' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Modal 3: Plan -->
    <Teleport to="body">
      <div
        v-if="step === 3"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="plan-title"
      >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close" />
        <div class="relative w-full max-w-lg rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-2xl">
          <div class="flex items-center justify-between">
            <h2 id="plan-title" class="text-xl font-bold text-[#1a1a1a]">Choose your plan</h2>
            <button type="button" class="rounded-lg p-1 text-[#64748b] hover:bg-[#f1f5f9]" aria-label="Close" @click="close">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <p class="mt-2 text-sm text-[#64748b]">Your campaign has been saved as a draft. Select a plan to launch and start receiving creator applications.</p>
          <div v-if="createdCampaign" class="mt-4 rounded-xl bg-[#f8fafc] p-4 text-sm text-[#475569]">
            <p><span class="font-medium text-[#1a1a1a]">Campaign:</span> {{ createdCampaign.title }}</p>
            <p class="mt-1"><span class="font-medium text-[#1a1a1a]">Influencers:</span> {{ createdCampaign.max_applications }}</p>
          </div>
          <div class="mt-6 flex flex-wrap gap-3">
            <router-link
              to="/brand/choose-plan"
              class="inline-flex items-center gap-2 rounded-xl bg-[#e63946] px-6 py-3 text-sm font-semibold text-white shadow transition hover:bg-[#c1121f]"
            >
              Choose plan & launch
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
            </router-link>
            <button type="button" class="rounded-xl border border-[#e2e8f0] px-6 py-3 text-sm font-medium text-[#64748b] hover:bg-[#f1f5f9]" @click="close">
              Done
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';

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

const emit = defineEmits(['created']);

const step = ref(0);
const showAdvanced = ref(false);
const saving = ref(false);
const createdCampaign = ref(null);

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
  campaign_type: 'instagram',
  influencer_count: 5,
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

function openStep(s) {
  step.value = s;
  if (s === 2) {
    form.campaign_type = form.campaign_type || 'instagram';
  }
}

function close() {
  step.value = 0;
  showAdvanced.value = false;
}

function selectType(value) {
  form.campaign_type = value;
  step.value = 2;
}

onMounted(async () => {
  try {
    const res = await axios.get('/api/creators/options/filters');
    filterOptions.categories = res.data.categories ?? [];
    filterOptions.genders = res.data.genders ?? {};
    filterOptions.languages = res.data.languages ?? [];
  } catch (_) {}
  try {
    const statesRes = await axios.get('/api/states');
    statesList.value = statesRes.data ?? [];
  } catch (_) {}
});

watch(() => form.state_id, (id) => {
  loadCities(id);
  if (!id) form.cities = [];
});

async function submitTargeting() {
  saving.value = true;
  try {
    const payload = {
      campaign_type: form.campaign_type,
      influencer_count: form.influencer_count,
      niches: Array.isArray(form.niches) ? [...form.niches] : [],
      follower_ranges: Array.isArray(form.follower_ranges) ? [...form.follower_ranges] : [],
      countries: Array.isArray(form.countries) ? [...form.countries] : [],
      cities: Array.isArray(form.cities) ? [...form.cities] : [],
      genders: Array.isArray(form.genders) ? [...form.genders] : [],
      ages: Array.isArray(form.ages) ? [...form.ages] : [],
      ethnicities: Array.isArray(form.ethnicities) ? [...form.ethnicities] : [],
      languages: Array.isArray(form.languages) ? [...form.languages] : [],
    };
    const res = await axios.post('/api/brand/campaigns', payload, { withCredentials: true });
    createdCampaign.value = res.data.campaign ?? null;
    emit('created', createdCampaign.value);
    step.value = 3;
  } catch (e) {
    console.error(e);
    alert(e.response?.data?.message || 'Failed to create campaign.');
  } finally {
    saving.value = false;
  }
}
</script>
