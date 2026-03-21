<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';

const loading = ref(true);
const listings = ref([]);
const categories = ref([]);
const showEditor = ref(false);
const currentStep = ref(1);
const saving = ref(false);
const uploading = ref(false);
const suggestingTitle = ref(false);
const suggestingDescription = ref(false);
const suggestingTags = ref(false);
const suggestingPricing = ref(false);
const suggestingFAQs = ref(false);
const fileInput = ref(null);
const titleSuggestions = ref([]);
const showTitleSuggestions = ref(false);

const form = reactive({
  id: null,
  service_id: '',
  title: '',
  slug: '',
  description: '',
  pricing_tiers: [
    { name: 'Basic', description: '', price: '', delivery: '', revisions: '', features: {} },
    { name: 'Standard', description: '', price: '', delivery: '', revisions: '', features: {} },
    { name: 'Premium', description: '', price: '', delivery: '', revisions: '', features: {} }
  ],
  faqs: [],
  gallery: [],
  tags: [],
  metadata: {
    industry: [],
    platform: [],
    purpose: []
  }
});

const newFaq = ref({ question: '', answer: '' });
const newTag = ref('');

onMounted(async () => {
  await fetchData();
});

async function fetchData() {
  loading.value = true;
  try {
    const [listingsRes, categoriesRes] = await Promise.all([
      axios.get('/api/professional/listings'),
      axios.get('/api/professional/categories')
    ]);
    listings.value = listingsRes.data;
    categories.value = categoriesRes.data;
  } catch (e) {
    console.error('Failed to fetch data', e);
  } finally {
    loading.value = false;
  }
}

function openEditor(listing = null) {
  if (listing) {
    const data = JSON.parse(JSON.stringify(listing));
    data.gallery = data.gallery || [];
    data.faqs = data.faqs || [];
    data.tags = data.tags || [];
    Object.assign(form, data);
  } else {
    resetForm();
  }
  showEditor.value = true;
  currentStep.value = 1;
}

function resetForm() {
  Object.assign(form, {
    id: null,
    service_id: '',
    title: '',
    slug: '',
    description: '',
    pricing_tiers: [
      { name: 'Basic', description: '', price: '', delivery: '', revisions: '', features: {} },
      { name: 'Standard', description: '', price: '', delivery: '', revisions: '', features: {} },
      { name: 'Premium', description: '', price: '', delivery: '', revisions: '', features: {} }
    ],
    faqs: [],
    gallery: [],
    tags: [],
    metadata: { industry: [], platform: [], purpose: [] }
  });
}

function updateSlug() {
  if (!form.id) {
    form.slug = form.title.toLowerCase().replace(/[^a-z0-0]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
  }
}

async function saveListing() {
  saving.value = true;
  try {
    await axios.post('/api/professional/listings', form);
    await fetchData();
    showEditor.value = false;
  } catch (e) {
    console.error('Failed to save listing', e);
    alert('Failed to save listing. Please check all fields.');
  } finally {
    saving.value = false;
  }
}

function addFaq() {
  if (newFaq.value.question && newFaq.value.answer) {
    form.faqs.push({ ...newFaq.value });
    newFaq.value = { question: '', answer: '' };
  }
}

function removeFaq(index) {
  form.faqs.splice(index, 1);
}

function addTag() {
  if (newTag.value && !form.tags.includes(newTag.value)) {
    form.tags.push(newTag.value);
    newTag.value = '';
  }
}

function removeTag(tag) {
  form.tags = form.tags.filter(t => t !== tag);
}

function formatCurrency(amt) {
  return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amt || 0);
}

function triggerUpload() {
  fileInput.value.click();
}

async function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  if (form.gallery.length >= 4) {
    alert('Maximum 4 images allowed');
    return;
  }

  const formData = new FormData();
  formData.append('image', file);

  uploading.value = true;
  try {
    const res = await axios.post('/api/professional/upload-image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    form.gallery.push(res.data.url);
  } catch (e) {
    console.error('Upload failed', e);
    alert('Upload failed. Please try a different image.');
  } finally {
    uploading.value = false;
    event.target.value = ''; // Reset input
  }
}

async function suggestTitleAI() {
  if (!form.service_id) {
    alert('Please select a category first');
    return;
  }
  suggestingTitle.value = true;
  try {
    const res = await axios.post('/api/professional/ai/suggest-title', { service_id: form.service_id });
    titleSuggestions.value = res.data.suggestions;
    showTitleSuggestions.value = true;
  } catch (e) {
    console.error('AI suggestion failed', e);
  } finally {
    suggestingTitle.value = false;
  }
}

async function generateDescriptionAI() {
  if (!form.service_id) {
    alert('Please select a category first');
    return;
  }
  suggestingDescription.value = true;
  try {
    const res = await axios.post('/api/professional/ai/suggest-description', { 
      service_id: form.service_id,
      title: form.title 
    });
    form.description = res.data.description;
  } catch (e) {
    console.error('AI generation failed', e);
  } finally {
    suggestingDescription.value = false;
  }
}

function applyTitleSuggestion(title) {
  form.title = title;
  updateSlug();
  showTitleSuggestions.value = false;
}

async function suggestTagsAI() {
  if (!form.service_id) { alert('Select a category'); return; }
  suggestingTags.value = true;
  try {
    const res = await axios.post('/api/professional/ai/suggest-tags', { service_id: form.service_id });
    const newTags = res.data.tags.filter(t => !form.tags.includes(t));
    form.tags = [...form.tags, ...newTags].slice(0, 5);
  } catch (e) {
    console.error('AI Tags failed', e);
  } finally {
    suggestingTags.value = false;
  }
}

async function smartFillPricingAI() {
  if (!form.service_id) { alert('Select a category'); return; }
  suggestingPricing.value = true;
  try {
    const res = await axios.post('/api/professional/ai/suggest-pricing', { service_id: form.service_id });
    const suggested = res.data.pricing;
    form.pricing_tiers.forEach(tier => {
      if (suggested[tier.name]) {
        tier.description = suggested[tier.name].desc;
        tier.features = { ...tier.features, ...suggested[tier.name].features };
      }
    });
  } catch (e) {
    console.error('AI Pricing failed', e);
  } finally {
    suggestingPricing.value = false;
  }
}

async function suggestFAQsAI() {
  if (!form.service_id) { alert('Select a category'); return; }
  suggestingFAQs.value = true;
  try {
    const res = await axios.post('/api/professional/ai/suggest-faqs', { service_id: form.service_id });
    const existingQs = form.faqs.map(f => f.question);
    const newFAQs = res.data.faqs.filter(f => !existingQs.includes(f.question));
    form.faqs = [...form.faqs, ...newFAQs];
  } catch (e) {
    console.error('AI FAQs failed', e);
  } finally {
    suggestingFAQs.value = false;
  }
}
</script>

<template>
  <div v-if="!loading">
    <div v-if="!showEditor">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-[#1a1a1a]">My Professional Services</h1>
        <button @click="openEditor()" class="px-6 py-2.5 bg-[#f59e0b] text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all">
          Create New Gig
        </button>
      </div>

      <div v-if="!listings.length" class="rounded-2xl border border-[#e2e8f0] bg-white p-12 text-center shadow-sm">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#fef3c7] text-[#f59e0b] mb-4">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <h2 class="text-xl font-bold text-[#1a1a1a]">Ready to start selling?</h2>
        <p class="mt-2 text-[#64748b]">Create your first professional service listing and reach thousands of clients.</p>
        <button @click="openEditor()" class="mt-8 px-8 py-3 bg-[#f59e0b] text-white font-bold rounded-xl hover:bg-[#d97706] transition-all">
          Get Started
        </button>
      </div>

      <div v-else class="flex flex-col gap-6">
        <div v-for="listing in listings" :key="listing.id" class="group relative flex flex-col md:flex-row rounded-3xl border border-[#e2e8f0] bg-white overflow-hidden shadow-sm transition hover:shadow-xl hover:border-[#f59e0b]/30">
          <div class="w-full md:w-64 shrink-0 bg-[#f8fafc] border-b md:border-b-0 md:border-r border-[#f1f5f9] flex items-center justify-center overflow-hidden h-48 md:h-64">
             <img v-if="listing.gallery?.[0]" :src="listing.gallery[0]" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
             <svg v-else class="w-12 h-12 text-[#cbd5e1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <div class="flex-1 p-6 flex flex-col">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-4">
              <div>
                <div class="flex items-center gap-2 mb-2">
                  <span class="px-2 py-0.5 rounded text-[10px] font-black bg-[#f1f5f9] text-[#64748b] uppercase tracking-wider">{{ listing.service_category?.name }}</span>
                  <span :class="['px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider', listing.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                    {{ listing.is_active ? 'Active' : 'Draft' }}
                  </span>
                </div>
                <h3 class="text-xl font-black text-[#1a1a1a] leading-tight group-hover:text-[#f59e0b] transition-colors">{{ listing.title }}</h3>
              </div>
              <div class="text-right">
                <span class="text-[10px] font-black text-[#94a3b8] uppercase tracking-widest block mb-1">Starting Price</span>
                <div class="font-black text-[#1a1a1a] text-2xl">{{ formatCurrency(listing.pricing_tiers?.[0]?.price) }}</div>
              </div>
            </div>
            
            <div class="mt-auto pt-4 border-t border-[#f1f5f9] flex items-center justify-between">
              <div class="flex gap-4">
                 <div class="flex flex-col">
                    <span class="text-[8px] font-black text-[#94a3b8] uppercase">Created</span>
                    <span class="text-xs font-bold text-[#1a1a1a]">{{ new Date(listing.created_at).toLocaleDateString() }}</span>
                 </div>
                 <div class="flex flex-col">
                    <span class="text-[8px] font-black text-[#94a3b8] uppercase">Views</span>
                    <span class="text-xs font-bold text-[#1a1a1a]">{{ listing.views_count || 0 }}</span>
                 </div>
              </div>
              <div class="flex gap-2">
                <button @click="openEditor(listing)" class="px-5 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-black text-[#64748b] hover:bg-[#f8fafc] hover:text-[#f59e0b] shadow-sm transition-all flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                  Edit Gig
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Multi-step Editor -->
    <div v-else class="max-w-5xl mx-auto pb-20">
      <div class="flex items-center gap-4 mb-8">
        <button @click="showEditor = false" class="p-2 rounded-lg border border-[#e2e8f0] text-[#64748b] hover:bg-white transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div>
          <h1 class="text-2xl font-bold text-[#1a1a1a]">{{ form.id ? 'Edit Service' : 'Create New Service' }}</h1>
          <nav class="flex gap-4 mt-2">
            <button v-for="i in 4" :key="i" @click="currentStep = i" 
              :class="['text-sm font-semibold transition-all pb-1 border-b-2', currentStep === i ? 'text-[#f59e0b] border-[#f59e0b]' : 'text-[#94a3b8] border-transparent']">
              {{ ['Overview', 'Pricing', 'Description', 'Gallery'][i-1] }}
            </button>
          </nav>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-[#e2e8f0] shadow-sm overflow-hidden">
        <!-- Step 1: Overview -->
        <div v-if="currentStep === 1" class="p-8 space-y-6">
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-bold text-[#1a1a1a] uppercase tracking-wide">Gig Title</label>
              <button @click="suggestTitleAI" :disabled="suggestingTitle" class="flex items-center gap-1.5 text-xs font-bold text-[#f59e0b] hover:text-[#d97706] transition-colors disabled:opacity-50">
                <svg v-if="!suggestingTitle" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L14.5 9H22L16 14L18.5 21L12 17L5.5 21L8 14L2 9H9.5L12 2Z"/></svg>
                <div v-else class="w-3.5 h-3.5 animate-spin rounded-full border-2 border-[#f59e0b] border-t-transparent"></div>
                {{ suggestingTitle ? 'AI Suggesting...' : 'Suggest with AI' }}
              </button>
            </div>
            <textarea v-model="form.title" @input="updateSlug" rows="2" maxlength="80" 
              placeholder="I will do something I'm really good at..."
              class="w-full text-xl font-medium rounded-xl border border-[#e2e8f0] px-5 py-4 outline-none focus:border-[#f59e0b] focus:ring-4 focus:ring-[#f59e0b]/10 resize-none"></textarea>
            
            <!-- AI Title Suggestions -->
            <transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform -translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100">
              <div v-if="showTitleSuggestions" class="mt-3 p-4 rounded-xl border border-[#fbbf24]/30 bg-[#fffbeb] space-y-2">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[10px] font-bold text-[#b45309] uppercase tracking-widest flex items-center gap-1">✨ AI Generated Suggestions</span>
                  <button @click="showTitleSuggestions = false" class="text-[#b45309] hover:text-[#92400e]">✕</button>
                </div>
                <div class="grid gap-2">
                  <button v-for="s in titleSuggestions" :key="s" @click="applyTitleSuggestion(s)" 
                    class="text-left p-3 rounded-lg bg-white border border-[#fbbf24]/20 text-xs font-medium text-[#1a1a1a] hover:border-[#f59e0b] hover:bg-[#fff7ed] transition-all">
                    {{ s }}
                  </button>
                </div>
              </div>
            </transition>
            <div class="flex justify-between mt-1">
               <p class="text-xs text-[#94a3b8]">Keep it short, clear and professional.</p>
               <p class="text-xs font-medium" :class="form.title.length > 70 ? 'text-amber-500' : 'text-[#94a3b8]'">{{ form.title.length }}/80</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-[#1a1a1a] mb-2 uppercase tracking-wide">Category</label>
              <select v-model="form.service_id" class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3.5 outline-none focus:border-[#f59e0b]">
                <option value="">Select a Category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <div>
              <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-bold text-[#1a1a1a] uppercase tracking-wide">Search Tags</label>
              <button @click="suggestTagsAI" :disabled="suggestingTags" class="flex items-center gap-1.5 text-xs font-bold text-[#f59e0b] hover:text-[#d97706] transition-colors disabled:opacity-50">
                <svg v-if="!suggestingTags" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L14.5 9H22L16 14L18.5 21L12 17L5.5 21L8 14L2 9H9.5L12 2Z"/></svg>
                <div v-else class="w-3.5 h-3.5 animate-spin rounded-full border-2 border-[#f59e0b] border-t-transparent"></div>
                {{ suggestingTags ? 'AI Suggesting...' : 'Suggest tags' }}
              </button>
            </div>
              <div class="flex gap-2">
                <input v-model="newTag" @keyup.enter="addTag" type="text" placeholder="Add up to 5 tags" class="flex-1 rounded-xl border border-[#e2e8f0] px-4 py-3 outline-none focus:border-[#f59e0b]" />
                <button @click="addTag" class="px-4 py-2 bg-[#f8fafc] text-[#64748b] border border-[#e2e8f0] rounded-xl font-bold hover:bg-white transition-all">Add</button>
              </div>
              <div class="flex flex-wrap gap-2 mt-3">
                <span v-for="t in form.tags" :key="t" class="px-3 py-1 rounded-full bg-[#f1f5f9] text-[#475569] text-xs font-bold border border-[#e2e8f0] flex items-center gap-1">
                  {{ t }} <button @click="removeTag(t)" class="text-[#94a3b8] hover:text-red-500">×</button>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Pricing -->
        <div v-if="currentStep === 2" class="p-0 overflow-x-auto">
           <div class="px-8 py-5 bg-[#fffbeb] border-b border-[#f59e0b]/20 flex items-center justify-between min-w-[800px]">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-white rounded-lg border border-[#f59e0b]/30 text-[#f59e0b]">
                   <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L14.5 9H22L16 14L18.5 21L12 17L5.5 21L8 14L2 9H9.5L12 2Z"/></svg>
                </div>
                <div>
                   <h3 class="text-sm font-bold text-[#b45309]">AI Smart-Fill Packages</h3>
                   <p class="text-[10px] text-[#d97706]/70 uppercase tracking-widest font-bold">Instantly generate high-converting details</p>
                </div>
              </div>
              <button @click="smartFillPricingAI" :disabled="suggestingPricing" 
                class="px-6 py-2.5 bg-[#f59e0b] text-white rounded-xl font-bold text-xs shadow-lg shadow-[#f59e0b]/20 hover:bg-[#d97706] transition-all disabled:opacity-50 flex items-center gap-2">
                <div v-if="suggestingPricing" class="w-3.5 h-3.5 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                {{ suggestingPricing ? 'Generating...' : '✨ Smart-Fill Table with AI' }}
              </button>
           </div>
           <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
              <tr class="bg-[#f8fafc]">
                 <th class="p-6 border-b border-[#e2e8f0] w-1/4">Feature</th>
                 <th v-for="tier in form.pricing_tiers" :key="tier.name" class="p-6 border-b border-[#e2e8f0] text-center w-1/4">
                   <div class="font-bold text-[#1a1a1a] uppercase tracking-widest text-xs">{{ tier.name }}</div>
                 </th>
              </tr>
            </thead>
            <tbody>
               <tr>
                 <td class="p-6 border-b border-[#e2e8f0] font-medium text-sm text-[#475569]">Description</td>
                 <td v-for="(tier, i) in form.pricing_tiers" :key="i" class="p-4 border-b border-[#e2e8f0]">
                   <textarea v-model="tier.description" rows="3" class="w-full text-sm rounded-lg border border-[#e2e8f0] p-3 outline-none focus:border-[#f59e0b]" placeholder="What's included in this tier?"></textarea>
                 </td>
               </tr>
               <tr>
                 <td class="p-6 border-b border-[#e2e8f0] font-medium text-sm text-[#475569]">Delivery Time</td>
                 <td v-for="(tier, i) in form.pricing_tiers" :key="i" class="p-4 border-b border-[#e2e8f0]">
                   <select v-model="tier.delivery" class="w-full text-sm rounded-lg border border-[#e2e8f0] p-3 outline-none focus:border-[#f59e0b]">
                      <option v-for="d in [1,2,3,4,5,7,10,14,21,30]" :key="d" :value="d">{{ d }} Days Delivery</option>
                   </select>
                 </td>
               </tr>
               <tr>
                 <td class="p-6 border-b border-[#e2e8f0] font-medium text-sm text-[#475569]">Revisions</td>
                 <td v-for="(tier, i) in form.pricing_tiers" :key="i" class="p-4 border-b border-[#e2e8f0]">
                   <select v-model="tier.revisions" class="w-full text-sm rounded-lg border border-[#e2e8f0] p-3 outline-none focus:border-[#f59e0b]">
                      <option v-for="r in [0,1,2,3,5,10,20]" :key="r" :value="r">{{ r === 20 ? 'Unlimited' : r }} Revisions</option>
                   </select>
                 </td>
               </tr>
               <tr class="bg-[#fcfcfc]">
                 <td class="p-6 border-b border-[#e2e8f0] font-bold text-sm text-[#1a1a1a]">Price (₹)</td>
                 <td v-for="(tier, i) in form.pricing_tiers" :key="i" class="p-6 border-b border-[#e2e8f0]">
                   <div class="relative">
                     <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8] font-bold">₹</span>
                     <input v-model="tier.price" type="number" class="w-full rounded-xl border border-[#e2e8f0] pl-10 pr-4 py-3 font-bold text-lg outline-none focus:border-[#f59e0b]" />
                   </div>
                 </td>
               </tr>
            </tbody>
          </table>
        </div>

        <!-- Step 3: Description & FAQ -->
        <div v-if="currentStep === 3" class="p-8 space-y-8">
            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-bold text-[#1a1a1a] uppercase tracking-wide">Detailed Description</label>
                <button @click="generateDescriptionAI" :disabled="suggestingDescription" class="flex items-center gap-1.5 text-xs font-bold text-[#f59e0b] hover:text-[#d97706] transition-colors disabled:opacity-50">
                  <svg v-if="!suggestingDescription" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L14.5 9H22L16 14L18.5 21L12 17L5.5 21L8 14L2 9H9.5L12 2Z"/></svg>
                  <div v-else class="w-3.5 h-3.5 animate-spin rounded-full border-2 border-[#f59e0b] border-t-transparent"></div>
                  {{ suggestingDescription ? 'AI Generating...' : '✨ AI Generate Description' }}
                </button>
              </div>
              <textarea v-model="form.description" rows="10" placeholder="Describe what you are offering. Be as detailed as possible so buyers know exactly what to expect." class="w-full rounded-xl border border-[#e2e8f0] px-5 py-4 outline-none focus:border-[#f59e0b] focus:ring-4 focus:ring-[#f59e0b]/10"></textarea>
            </div>
           
           <div class="pt-8 border-t border-[#f1f5f9]">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-[#1a1a1a] uppercase tracking-wide">Frequently Asked Questions</h3>
                <button @click="suggestFAQsAI" :disabled="suggestingFAQs" class="flex items-center gap-1.5 text-xs font-bold text-[#f59e0b] hover:text-[#d97706] transition-colors disabled:opacity-50">
                  <svg v-if="!suggestingFAQs" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L14.5 9H22L16 14L18.5 21L12 17L5.5 21L8 14L2 9H9.5L12 2Z"/></svg>
                  <div v-else class="w-3.5 h-3.5 animate-spin rounded-full border-2 border-[#f59e0b] border-t-transparent"></div>
                  {{ suggestingFAQs ? 'AI Adding FAQs...' : '✨ Suggest FAQs with AI' }}
                </button>
             </div>
             <div class="space-y-4 mb-6">
                <div v-for="(faq, i) in form.faqs" :key="i" class="p-5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] group">
                   <div class="flex justify-between items-start mb-2">
                     <div class="font-bold text-[#1a1a1a]">{{ faq.question }}</div>
                     <button @click="removeFaq(i)" class="text-[#94a3b8] hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all">×</button>
                   </div>
                   <div class="text-sm text-[#64748b] leading-relaxed">{{ faq.answer }}</div>
                </div>
             </div>
             <div class="p-6 rounded-2xl border-2 border-dashed border-[#e2e8f0] bg-white">
                <div class="space-y-4">
                  <input v-model="newFaq.question" type="text" placeholder="Add a Question (e.g. Do you provide sources?)" class="w-full rounded-lg border border-[#e2e8f0] px-4 py-2.5 outline-none focus:border-[#f59e0b]" />
                  <textarea v-model="newFaq.answer" rows="3" placeholder="Add an Answer" class="w-full rounded-lg border border-[#e2e8f0] px-4 py-2.5 outline-none focus:border-[#f59e0b]"></textarea>
                  <button @click="addFaq" class="px-6 py-2 bg-[#1a1a1a] text-white font-bold rounded-lg hover:bg-black transition-all">Add FAQ</button>
                </div>
             </div>
           </div>
        </div>

        <!-- Step 4: Gallery -->
        <div v-if="currentStep === 4" class="p-8">
           <label class="block text-sm font-bold text-[#1a1a1a] mb-4 uppercase tracking-wide">Portfolio Images</label>
           
           <!-- Hidden File Input -->
           <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden" />

           <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
             <div v-for="(img, i) in form.gallery" :key="i" class="aspect-video relative rounded-xl border border-[#e2e8f0] bg-[#f1f5f9] overflow-hidden group">
                <img :src="img" class="w-full h-full object-cover" />
                <button @click="form.gallery.splice(i, 1)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
             </div>

             <!-- Upload Button -->
             <div v-if="form.gallery.length < 4" 
                @click="triggerUpload"
                class="aspect-video flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#cbd5e1] hover:border-[#f59e0b] hover:bg-[#fffbeb] transition-all cursor-pointer group relative overflow-hidden"
                :class="{'opacity-50 pointer-events-none': uploading}">
                
                <div v-if="uploading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10">
                   <div class="h-6 w-6 animate-spin rounded-full border-2 border-[#f59e0b] border-t-transparent"></div>
                </div>

                <svg class="w-8 h-8 text-[#94a3b8] group-hover:text-[#f59e0b] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <div class="mt-2 text-xs font-bold text-[#64748b]">Upload Image</div>
                <p class="mt-0.5 text-[10px] text-[#94a3b8]">Max 2MB per image</p>
             </div>
           </div>
           
           <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-100 flex gap-3">
              <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <div class="text-xs text-blue-700 leading-relaxed">
                <span class="font-bold">Pro Tip: </span> 
                High-quality images (1280x720px) help your service stand out. You can upload up to 4 images to showcase different aspects of your work.
              </div>
           </div>
        </div>
      </div>

      <div class="mt-8 flex items-center justify-between">
        <button v-if="currentStep > 1" @click="currentStep--" class="px-8 py-3 rounded-xl border border-[#e2e8f0] font-bold text-[#64748b] hover:bg-white transition-all">Previous</button>
        <div v-else></div>
        
        <div class="flex gap-4">
           <button v-if="currentStep < 4" @click="currentStep++" class="px-10 py-3 bg-[#1a1a1a] text-white font-bold rounded-xl hover:bg-black transition-all">Next Step</button>
           <button v-else @click="saveListing" :disabled="saving" class="px-10 py-3 bg-[#f59e0b] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-[#d97706] transition-all disabled:opacity-50">
             <span v-if="saving">Publishing...</span>
             <span v-else>{{ form.id ? 'Save Changes' : 'Publish Gig' }}</span>
           </button>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="flex h-96 items-center justify-center">
    <div class="h-8 w-8 animate-spin rounded-full border-4 border-[#f59e0b] border-t-transparent"></div>
  </div>
</template>
