<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import CitySearchSelect from '../../components/CitySearchSelect.vue';

const loading = ref(true);
const saving = ref(false);
const message = ref({ type: '', text: '' });

const profile = reactive({
  tagline: '',
  bio: '',
  languages: [],
  skills: [],
  education: [],
  certifications: [],
  state_id: null,
  city_id: null,
});

const states = ref([]);
const cities = ref([]);

const newLang = ref({ name: '', level: 'Fluent' });
const newSkill = ref({ name: '', level: 'Expert' });
const newEdu = ref({ school: '', degree: '', year: '' });
const newCert = ref({ name: '', from: '', year: '' });

const completionPercentage = computed(() => {
  let score = 0;
  if (profile.tagline) score += 10;
  if (profile.bio && profile.bio.length > 50) score += 30;
  if (profile.languages.length > 0) score += 15;
  if (profile.skills.length > 0) score += 15;
  if (profile.education.length > 0) score += 15;
  if (profile.certifications.length > 0) score += 15;
  return Math.min(score, 100);
});

onMounted(async () => {
  try {
    const res = await axios.get('/api/professional/dashboard');
    if (res.data.profile) {
      Object.assign(profile, res.data.profile);
      profile.state_id = res.data.user.state_id;
      profile.city_id = res.data.user.city_id;
    }

    const statesRes = await axios.get('/api/states');
    states.value = statesRes.data;

    if (profile.state_id) {
      const res = await axios.get('/api/cities?state_id=' + profile.state_id);
      cities.value = res.data;
    }
  } catch (e) {
    console.error('Failed to load profile', e);
  } finally {
    loading.value = false;
  }
});

async function saveProfile() {
  saving.value = true;
  message.value = { type: '', text: '' };
  try {
    await axios.post('/api/professional/profile', profile);
    message.value = { type: 'success', text: 'Profile updated successfully!' };
  } catch (e) {
    message.value = { type: 'error', text: 'Failed to update profile.' };
  } finally {
    saving.value = false;
  }
}

function addItem(list, item, reset) {
  if (!item.name && !item.school) return;
  profile[list].push({ ...item });
  Object.assign(item, reset);
}

function removeItem(list, index) {
  profile[list].splice(index, 1);
}

async function onStateChange() {
  profile.city_id = null;
  if (profile.state_id) {
    const res = await axios.get('/api/cities?state_id=' + profile.state_id);
    cities.value = res.data;
  } else {
    cities.value = [];
  }
}
</script>

<template>
  <div v-if="!loading" class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-[#1a1a1a]">Professional Profile</h1>
        <p class="mt-1 text-[#64748b]">Complete your profile to build trust with clients.</p>
      </div>
      <div class="flex flex-col items-end">
        <span class="text-xs font-semibold text-[#64748b] mb-1">Completion: {{ completionPercentage }}%</span>
        <div class="w-48 h-2 bg-[#e2e8f0] rounded-full overflow-hidden">
          <div class="h-full bg-[#f59e0b] transition-all duration-500" :style="{ width: completionPercentage + '%' }"></div>
        </div>
      </div>
    </div>

    <div v-if="message.text" :class="['mb-6 p-4 rounded-xl border', message.type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700']">
      {{ message.text }}
    </div>

    <div class="space-y-6">
      <!-- Basic Info -->
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1a1a1a] mb-4">Account Overview</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-[#1a1a1a] mb-1">Professional Tagline</label>
            <input v-model="profile.tagline" type="text" placeholder="e.g. Certified Social Media Strategist & Meta Ads Expert" class="w-full rounded-lg border border-[#e2e8f0] px-4 py-2.5 outline-none focus:border-[#f59e0b] focus:ring-1 focus:ring-[#f59e0b]/20" />
            <p class="mt-1 text-xs text-[#64748b]">Briefly describe what you do best.</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-[#1a1a1a] mb-1">Professional Bio</label>
            <textarea v-model="profile.bio" rows="5" placeholder="Share your experience, passion, and how you help clients achieve their goals..." class="w-full rounded-lg border border-[#e2e8f0] px-4 py-2.5 outline-none focus:border-[#f59e0b] focus:ring-1 focus:ring-[#f59e0b]/20"></textarea>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[#1a1a1a] mb-1">State</label>
              <select v-model="profile.state_id" @change="onStateChange" class="w-full rounded-lg border border-[#e2e8f0] px-4 py-2.5 outline-none focus:border-[#f59e0b] focus:ring-1 focus:ring-[#f59e0b]/20">
                <option :value="null">Select state</option>
                <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-[#1a1a1a] mb-1">City</label>
              <CitySearchSelect
                v-model="profile.city_id"
                :options="cities"
                :disabled="!profile.state_id"
                placeholder="Search and select city"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Skills & Languages -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-[#1a1a1a] mb-4">Skills</h2>
          <div class="flex gap-2 mb-4">
            <input v-model="newSkill.name" type="text" placeholder="Add Skill" class="flex-1 rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
            <select v-model="newSkill.level" class="rounded-lg border border-[#e2e8f0] px-2 py-2 text-sm outline-none focus:border-[#f59e0b]">
              <option>Beginner</option>
              <option>Intermediate</option>
              <option>Expert</option>
            </select>
            <button @click="addItem('skills', newSkill, { name: '', level: 'Expert' })" class="p-2 bg-[#f59e0b] text-white rounded-lg hover:bg-[#d97706]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
          </div>
          <div class="flex flex-wrap gap-2">
            <span v-for="(s, i) in profile.skills" :key="i" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#f8fafc] border border-[#e2e8f0] text-sm text-[#475569]">
              {{ s.name }} ({{ s.level }})
              <button @click="removeItem('skills', i)" class="text-[#94a3b8] hover:text-red-500">×</button>
            </span>
          </div>
        </div>

        <div class="rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-[#1a1a1a] mb-4">Languages</h2>
          <div class="flex gap-2 mb-4">
            <input v-model="newLang.name" type="text" placeholder="Add Language" class="flex-1 rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
            <select v-model="newLang.level" class="rounded-lg border border-[#e2e8f0] px-2 py-2 text-sm outline-none focus:border-[#f59e0b]">
              <option>Basic</option>
              <option>Conversational</option>
              <option>Fluent</option>
              <option>Native</option>
            </select>
            <button @click="addItem('languages', newLang, { name: '', level: 'Fluent' })" class="p-2 bg-[#f59e0b] text-white rounded-lg hover:bg-[#d97706]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
          </div>
          <div class="flex flex-wrap gap-2">
            <span v-for="(l, i) in profile.languages" :key="i" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#f8fafc] border border-[#e2e8f0] text-sm text-[#475569]">
              {{ l.name }} ({{ l.level }})
              <button @click="removeItem('languages', i)" class="text-[#94a3b8] hover:text-red-500">×</button>
            </span>
          </div>
        </div>
      </div>

      <!-- Education & Certification -->
      <div class="rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1a1a1a] mb-4">Education</h2>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-4">
          <input v-model="newEdu.school" type="text" placeholder="University/College" class="sm:col-span-2 rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
          <input v-model="newEdu.degree" type="text" placeholder="Degree" class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
          <div class="flex gap-2">
            <input v-model="newEdu.year" type="number" placeholder="Year" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
            <button @click="addItem('education', newEdu, { school: '', degree: '', year: '' })" class="p-2 bg-[#f59e0b] text-white rounded-lg hover:bg-[#d97706]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
          </div>
        </div>
        <div class="space-y-2">
          <div v-for="(e, i) in profile.education" :key="i" class="flex items-center justify-between p-3 rounded-lg bg-[#f8fafc] border border-[#e2e8f0]">
            <div>
              <div class="font-medium text-[#1a1a1a]">{{ e.degree }}</div>
              <div class="text-sm text-[#64748b]">{{ e.school }}, {{ e.year }}</div>
            </div>
            <button @click="removeItem('education', i)" class="text-[#94a3b8] hover:text-red-500">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>

       <div class="rounded-xl border border-[#e2e8f0] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1a1a1a] mb-4">Certifications</h2>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-4">
          <input v-model="newCert.name" type="text" placeholder="Certification Name" class="sm:col-span-2 rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
          <input v-model="newCert.from" type="text" placeholder="Issuer" class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
          <div class="flex gap-2">
            <input v-model="newCert.year" type="number" placeholder="Year" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm outline-none focus:border-[#f59e0b]" />
            <button @click="addItem('certifications', newCert, { name: '', from: '', year: '' })" class="p-2 bg-[#f59e0b] text-white rounded-lg hover:bg-[#d97706]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
          </div>
        </div>
        <div class="space-y-2">
          <div v-for="(c, i) in profile.certifications" :key="i" class="flex items-center justify-between p-3 rounded-lg bg-[#f8fafc] border border-[#e2e8f0]">
            <div>
              <div class="font-medium text-[#1a1a1a]">{{ c.name }}</div>
              <div class="text-sm text-[#64748b]">{{ c.from }}, {{ c.year }}</div>
            </div>
            <button @click="removeItem('certifications', i)" class="text-[#94a3b8] hover:text-red-500">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>

      <div class="flex justify-end pt-4">
        <button @click="saveProfile" :disabled="saving" class="px-8 py-3 bg-[#f59e0b] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-[#d97706] transition-all disabled:opacity-50">
          <span v-if="saving">Saving...</span>
          <span v-else>Update Profile</span>
        </button>
      </div>
    </div>
  </div>
  <div v-else class="flex h-96 items-center justify-center">
    <div class="h-8 w-8 animate-spin rounded-full border-4 border-[#f59e0b] border-t-transparent"></div>
  </div>
</template>
