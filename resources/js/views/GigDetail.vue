<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import RichTextContent from '../components/RichTextContent.vue';

const route = useRoute();
const GIG_IMAGE_FALLBACK =
  'https://images.unsplash.com/photo-1560439514-4e9645039924?w=1200&h=675&fit=crop';

function onGigImageError(e) {
  const el = e?.target;
  if (!el || el.dataset.gigImgFallback === '1') return;
  el.dataset.gigImgFallback = '1';
  el.src = GIG_IMAGE_FALLBACK;
}

const loading = ref(true);
const notFound = ref(false);
const gig = ref(null);
const activeTab = ref('');
const galleryIndex = ref(0);

function normalizeFeatureList(features) {
  if (features == null) return [];
  if (Array.isArray(features)) {
    return features.filter((x) => x != null && x !== '');
  }
  if (typeof features === 'object') {
    return Object.entries(features)
      .filter(([, v]) => v === true || v === 1 || v === '1')
      .map(([k]) => k);
  }
  return [];
}

const pricingTiers = computed(() => {
  const t = gig.value?.pricing_tiers;
  return Array.isArray(t) ? t : [];
});

const activePackage = computed(() => {
  const tiers = pricingTiers.value;
  if (!tiers.length) return null;
  const match = tiers.find((x) => x?.name === activeTab.value);
  return match ?? tiers[0];
});

const packageFeaturesList = computed(() =>
  normalizeFeatureList(activePackage.value?.features),
);

const currentGallerySrc = computed(() => {
  const g = gig.value?.gallery;
  if (!Array.isArray(g) || !g.length) return null;
  const i = Math.min(Math.max(0, galleryIndex.value), g.length - 1);
  return g[i] || null;
});

const sellerLanguagesDisplay = computed(() => {
  const langs = gig.value?.user?.professional_profile?.languages;
  if (!Array.isArray(langs) || !langs.length) return '';
  return langs
    .map((l) => (typeof l === 'string' ? l : l?.name || ''))
    .filter(Boolean)
    .join(', ');
});

const sellerSkills = computed(() => {
  const skills = gig.value?.user?.professional_profile?.skills;
  if (!Array.isArray(skills) || !skills.length) return [];
  return skills
    .map((s) => (typeof s === 'string' ? s : s?.name || ''))
    .filter(Boolean);
});

const sellerEducation = computed(() => {
  const e = gig.value?.user?.professional_profile?.education;
  return Array.isArray(e) ? e : [];
});

const sellerCertifications = computed(() => {
  const c = gig.value?.user?.professional_profile?.certifications;
  return Array.isArray(c) ? c : [];
});

watch(
  () => route.params.slug,
  () => {
    galleryIndex.value = 0;
    fetchGig();
  },
);

watch(pricingTiers, (tiers) => {
  if (!tiers.length) {
    activeTab.value = '';
    return;
  }
  const names = tiers.map((t) => t?.name).filter(Boolean);
  if (!names.length) return;
  if (!names.includes(activeTab.value)) {
    activeTab.value = names[0];
  }
});

async function fetchGig() {
  loading.value = true;
  notFound.value = false;
  gig.value = null;
  try {
    const res = await axios.get(`/api/gigs/${route.params.slug}`);
    const payload = res.data?.data ?? res.data;
    if (!payload || typeof payload !== 'object') {
      notFound.value = true;
      return;
    }
    gig.value = payload;
    
    // Dynamic SEO will be handled by the reactive useHead call below
    galleryIndex.value = 0;
    const tiers = Array.isArray(payload.pricing_tiers) ? payload.pricing_tiers : [];
    activeTab.value = tiers[0]?.name || '';
  } catch (e) {
    console.error('Failed to load gig', e);
    notFound.value = true;
  } finally {
    loading.value = false;
  }
}

// Reactive SEO
const seoTitle = computed(() => {
  if (gig.value) {
    return `${gig.value.title} | ${gig.value.category?.name || 'Professional Service'} | Marketplace | StarJD`;
  }
  return 'Loading Service... | StarJD';
});

const seoDescription = computed(() => {
  if (gig.value) {
    return gig.value.description?.substring(0, 160).replace(/<[^>]*>/g, '') || `Hire ${gig.value.user?.name} for ${gig.value.title}. Explore professional creative services on the StarJD Marketplace.`;
  }
  return 'Explore professional creative services on the StarJD Marketplace.';
});

const seoImage = computed(() => {
  return gig.value?.gallery?.[0] || (window.location.origin + '/logo.png');
});

useHead({
  title: seoTitle,
  meta: [
    { name: 'description', content: seoDescription },
    { property: 'og:title', content: seoTitle },
    { property: 'og:description', content: seoDescription },
    { property: 'og:image', content: seoImage },
    { property: 'og:type', content: 'website' },
    { name: 'twitter:card', content: 'summary_large_image' }
  ],
  script: [
    {
      type: 'application/ld+json',
      children: computed(() => {
        if (!gig.value) return '';
        return JSON.stringify({
          "@context": "https://schema.org",
          "@type": "Service",
          "name": gig.value.title,
          "description": seoDescription.value,
          "provider": {
            "@type": "Person",
            "name": gig.value.user?.name,
            "image": gig.value.user?.avatar_url
          },
          "category": gig.value.category?.name,
          "offers": {
            "@type": "Offer",
            "priceCurrency": "INR",
            "price": gig.value.pricing_tiers?.[0]?.price || 0,
            "url": typeof window !== 'undefined' ? window.location.href : ''
          },
          "aggregateRating": gig.value.user?.professional_profile?.avg_rating ? {
            "@type": "AggregateRating",
            "ratingValue": gig.value.user.professional_profile.avg_rating,
            "reviewCount": gig.value.user.professional_profile.total_reviews || 1
          } : undefined
        });
      })
    }
  ]
});


onMounted(() => {
  fetchGig();
});

function formatCurrency(amt) {
  const n = Number(amt);
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    maximumFractionDigits: 0,
  }).format(Number.isFinite(n) ? n : 0);
}
</script>

<template>
  <div v-if="notFound" class="mx-auto max-w-3xl px-4 py-24 text-center">
    <h1 class="text-2xl font-bold text-[#1a1a1a]">Service not found</h1>
    <p class="mt-2 text-[#64748b]">This listing may have been removed or the link is incorrect.</p>
    <router-link
      to="/marketplace"
      class="mt-8 inline-block rounded-xl bg-[#1a1a1a] px-6 py-3 text-sm font-bold text-white hover:bg-black"
      >Back to marketplace</router-link
    >
  </div>
  <div v-else-if="!loading && gig" class="min-h-screen bg-white pb-20">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-4 py-8 lg:grid-cols-3">
      <!-- Main column -->
      <div class="space-y-10 lg:col-span-2">
        <div class="flex flex-wrap items-center gap-2">
          <span
            v-if="gig.category?.name"
            class="rounded-full bg-[#e2e8f0] px-3 py-1 text-xs font-bold text-[#475569]"
            >{{ gig.category.name }}</span
          >
          <span
            v-for="tag in gig.tags || []"
            :key="tag"
            class="rounded-full border border-[#e2e8f0] bg-white px-3 py-1 text-xs font-medium text-[#64748b]"
            >{{ tag }}</span
          >
        </div>

        <h1 class="text-3xl font-bold leading-tight text-[#1a1a1a]">{{ gig.title }}</h1>

        <!-- Seller preview -->
        <div class="flex items-center gap-3">
          <div
            class="h-12 w-12 overflow-hidden rounded-full border border-[#e2e8f0] bg-[#f1f5f9]"
          >
            <img
              v-if="gig.user?.avatar_url"
              :src="gig.user.avatar_url"
              class="h-full w-full object-cover"
              alt=""
              @error="onGigImageError"
            />
          </div>
          <div>
            <div class="flex flex-wrap items-center gap-2">
              <span class="font-bold text-[#1a1a1a]">{{ gig.user?.name }}</span>
              <span
                class="rounded bg-[#fef3c7] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#f59e0b]"
                >Professional</span
              >
            </div>
            <div class="flex items-center gap-1 text-sm text-[#475569]">
              <svg class="h-4 w-4 text-[#f59e0b]" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
              <span class="font-bold">{{
                gig.user?.professional_profile?.avg_rating ?? '—'
              }}</span>
              <span class="text-[#94a3b8]"
                >({{ gig.user?.professional_profile?.total_reviews ?? 0 }} reviews)</span
              >
            </div>
          </div>
        </div>

        <!-- Gallery -->
        <div class="space-y-3">
          <div
            class="aspect-video overflow-hidden rounded-2xl border border-[#e2e8f0] bg-[#f8fafc] shadow-sm"
          >
            <img
              v-if="currentGallerySrc"
              :src="currentGallerySrc"
              class="h-full w-full object-cover"
              :alt="gig.title || ''"
              @error="onGigImageError"
            />
            <div
              v-else
              class="flex h-full w-full items-center justify-center text-[#94a3b8]"
            >
              <svg
                class="h-20 w-20"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
          </div>
          <div v-if="gig.gallery?.length > 1" class="flex flex-wrap gap-2">
            <button
              v-for="(url, i) in gig.gallery"
              :key="i"
              type="button"
              class="h-16 w-24 overflow-hidden rounded-lg border-2 transition-all"
              :class="
                galleryIndex === i
                  ? 'border-[#f59e0b] ring-2 ring-[#f59e0b]/30'
                  : 'border-[#e2e8f0] opacity-80 hover:opacity-100'
              "
              @click="galleryIndex = i"
            >
              <img :src="url" class="h-full w-full object-cover" alt="" @error="onGigImageError" />
            </button>
          </div>
        </div>

        <!-- About gig -->
        <div class="space-y-4">
          <h2 class="text-2xl font-bold text-[#1a1a1a]">About this service</h2>
          <RichTextContent
            class="prose max-w-none leading-relaxed text-[#475569]"
            :content="gig.description || ''"
          />
        </div>

        <!-- FAQ -->
        <div
          v-if="gig.faqs?.length"
          class="space-y-6 border-t border-[#f1f5f9] pt-10"
        >
          <h2 class="text-2xl font-bold text-[#1a1a1a]">FAQ</h2>
          <div class="space-y-4">
            <div
              v-for="(faq, i) in gig.faqs"
              :key="i"
              class="rounded-xl border border-[#e2e8f0] bg-[#f8fafc] p-5"
            >
              <div class="mb-2 font-bold text-[#1a1a1a]">{{ faq.question }}</div>
              <div class="text-sm leading-relaxed text-[#475569]">{{ faq.answer }}</div>
            </div>
          </div>
        </div>

        <!-- About seller -->
        <div class="space-y-6 rounded-2xl border border-[#e2e8f0] bg-white p-8">
          <h2 class="text-xl font-bold text-[#1a1a1a]">About the seller</h2>
          <div class="flex gap-6">
            <div
              class="h-24 w-24 shrink-0 overflow-hidden rounded-full border border-[#e2e8f0] bg-[#f1f5f9]"
            >
              <img
                v-if="gig.user?.avatar_url"
                :src="gig.user.avatar_url"
                class="h-full w-full object-cover"
                alt=""
                @error="onGigImageError"
              />
            </div>
            <div class="min-w-0 flex-1">
              <div class="text-lg font-bold text-[#1a1a1a]">{{ gig.user?.name }}</div>
              <p
                v-if="gig.user?.professional_profile?.tagline"
                class="mb-3 text-sm text-[#64748b]"
              >
                {{ gig.user.professional_profile.tagline }}
              </p>
              <div class="mb-4 flex flex-wrap items-center gap-1 text-sm font-bold text-[#f59e0b]">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
                {{ gig.user?.professional_profile?.avg_rating ?? '—' }}
                ({{ gig.user?.professional_profile?.total_reviews ?? 0 }} reviews)
              </div>
              <router-link
                to="/contact-us"
                class="inline-block rounded-lg border border-[#1a1a1a] px-6 py-2 text-sm font-bold text-[#1a1a1a] transition-all hover:bg-[#1a1a1a] hover:text-white"
                >Contact</router-link
              >
            </div>
          </div>

          <div
            v-if="gig.user?.professional_profile?.bio"
            class="border-t border-[#f1f5f9] pt-6"
          >
            <h3 class="mb-2 text-sm font-bold uppercase tracking-wide text-[#94a3b8]">
              Bio
            </h3>
            <RichTextContent
              class="prose prose-sm max-w-none text-[#475569]"
              :content="gig.user.professional_profile.bio"
            />
          </div>

          <div v-if="sellerEducation.length" class="border-t border-[#f1f5f9] pt-6">
            <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-[#94a3b8]">
              Education
            </h3>
            <ul class="space-y-3 text-sm text-[#475569]">
              <li v-for="(row, idx) in sellerEducation" :key="idx">
                <div class="font-semibold text-[#1a1a1a]">
                  <template v-if="row.degree && row.school"
                    >{{ row.degree }}, {{ row.school }}</template
                  >
                  <template v-else>{{ row.degree || row.school || '—' }}</template>
                </div>
                <div v-if="row.year" class="text-xs text-[#94a3b8]">{{ row.year }}</div>
              </li>
            </ul>
          </div>

          <div v-if="sellerCertifications.length" class="border-t border-[#f1f5f9] pt-6">
            <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-[#94a3b8]">
              Certifications
            </h3>
            <ul class="space-y-2 text-sm text-[#475569]">
              <li v-for="(row, idx) in sellerCertifications" :key="idx">
                <span class="font-medium text-[#1a1a1a]">{{ row.name || '—' }}</span>
                <span v-if="row.from" class="text-[#64748b]"> — {{ row.from }}</span>
                <span v-if="row.year" class="text-xs text-[#94a3b8]"> ({{ row.year }})</span>
              </li>
            </ul>
          </div>

          <div v-if="sellerSkills.length" class="border-t border-[#f1f5f9] pt-6">
            <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-[#94a3b8]">
              Skills
            </h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="skill in sellerSkills"
                :key="skill"
                class="rounded-lg bg-[#f1f5f9] px-3 py-1.5 text-sm font-medium text-[#475569]"
                >{{ skill }}</span
              >
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 border-t border-[#f1f5f9] pt-6 sm:grid-cols-2">
            <div v-if="sellerLanguagesDisplay">
              <div class="mb-1 text-[10px] font-bold uppercase tracking-wider text-[#94a3b8]">
                Languages
              </div>
              <div class="text-sm font-medium text-[#475569]">{{ sellerLanguagesDisplay }}</div>
            </div>
            <div>
              <div class="mb-1 text-[10px] font-bold uppercase tracking-wider text-[#94a3b8]">
                Avg. response time
              </div>
              <div class="text-sm font-medium text-[#475569]">{{
                gig.user?.professional_profile?.response_time || '—'
              }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing sidebar -->
      <div class="lg:col-span-1">
        <div
          class="sticky top-24 overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-xl"
        >
          <template v-if="pricingTiers.length">
            <div class="flex flex-wrap border-b border-[#e2e8f0]">
              <button
                v-for="tier in pricingTiers"
                :key="tier.name"
                type="button"
                :class="[
                  'flex-1 min-w-[5rem] py-4 text-sm font-bold transition-all border-b-2',
                  activeTab === tier.name
                    ? 'border-[#f59e0b] bg-[#fffbeb]/50 text-[#f59e0b]'
                    : 'border-transparent text-[#64748b] hover:text-[#1a1a1a]',
                ]"
                @click="activeTab = tier.name"
              >
                {{ tier.name }}
              </button>
            </div>

            <div class="space-y-6 p-8">
              <div class="flex items-center justify-between gap-4">
                <h3 class="font-bold text-[#1a1a1a]">{{ activePackage?.name }} package</h3>
                <div class="text-2xl font-bold text-[#1a1a1a]">
                  {{ formatCurrency(activePackage?.price) }}
                </div>
              </div>

              <RichTextContent
                v-if="activePackage?.description"
                class="text-sm leading-relaxed text-[#475569]"
                :content="activePackage.description"
              />

              <div
                class="flex flex-wrap items-center gap-4 text-sm font-bold text-[#1a1a1a]"
              >
                <div class="flex items-center gap-2">
                  <svg
                    class="h-4 w-4 text-[#94a3b8]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  {{ activePackage?.delivery ?? '—' }} day(s) delivery
                </div>
                <div class="flex items-center gap-2">
                  <svg
                    class="h-4 w-4 text-[#94a3b8]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                    />
                  </svg>
                  {{
                    activePackage?.revisions === 20
                      ? 'Unlimited'
                      : activePackage?.revisions ?? '—'
                  }}
                  revisions
                </div>
              </div>

              <ul v-if="packageFeaturesList.length" class="space-y-2 pt-2">
                <li
                  v-for="(label, idx) in packageFeaturesList"
                  :key="idx"
                  class="flex items-start gap-3 text-sm text-[#475569]"
                >
                  <svg
                    class="mt-0.5 h-4 w-4 shrink-0 text-green-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  {{ label }}
                </li>
              </ul>

              <button
                type="button"
                class="w-full transform rounded-xl bg-[#1a1a1a] py-4 text-sm font-bold text-white shadow-lg transition-all hover:bg-black hover:shadow-xl active:scale-[0.98]"
              >
                Continue ({{ formatCurrency(activePackage?.price) }})
              </button>
              <p class="text-center text-xs text-[#94a3b8]">You won't be charged yet</p>
            </div>
          </template>
          <div v-else class="p-8 text-center text-sm text-[#64748b]">
            No packages configured for this listing.
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="flex min-h-[60vh] items-center justify-center">
    <div
      class="h-10 w-10 animate-spin rounded-full border-4 border-[#f59e0b] border-t-transparent"
    ></div>
  </div>
</template>
