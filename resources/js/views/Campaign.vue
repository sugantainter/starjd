<template>
  <div class="min-h-screen">
    <!-- Hero: dark blue, left = headline + description + CTA, right = vertical scrolling video cards + brand badges (image jesa) -->
    <section class="campaign-hero relative overflow-hidden border-b border-[#1e3a5f] bg-[#0f172a] px-4">
      <div class="relative mx-auto max-w-6xl">
        <div class="flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between lg:gap-12">
          <!-- Left: content -->
          <div class="order-2 max-w-xl text-center lg:order-1 lg:text-left">
            <h1 class="text-3xl font-bold leading-tight text-white sm:text-4xl md:text-[2.5rem] lg:text-[2.75rem]">
              Best Influencer<br />
              <span class="text-[#fef08a]">Marketing</span> Agency in India
            </h1>
            <p class="mt-5 text-base leading-relaxed text-white/90 sm:text-lg">
              StarJD is the best Influencer marketing agency in India which provides the top influencer marketing platform to help brands and visionary marketers leverage social media influencers' content in marketing and advertising their products/services.
            </p>
            <router-link
              to="/contact"
              class="mt-8 inline-flex items-center justify-center rounded-xl bg-[#7c3aed] px-8 py-3.5 text-base font-semibold text-white transition hover:bg-[#6d28d9]"
            >
              Contact Us
            </router-link>
          </div>
          <!-- Right: 4 columns – col1 & col3 video scroll UP, col2 & col4 video scroll DOWN -->
          <div class="order-1 relative flex flex-1 justify-end lg:order-2 lg:max-w-[65%]">
            <div class="campaign-hero-four-cols grid w-full max-w-[340px] grid-cols-4 gap-2 md:max-w-[440px] md:gap-3 lg:max-w-[520px] lg:gap-3">
              <div
                v-for="(colVideos, colIdx) in heroVideoColumns"
                :key="colIdx"
                class="campaign-hero-col relative h-[360px] overflow-hidden rounded-lg md:h-[420px] lg:h-[480px]"
              >
                <div
                  class="campaign-hero-video-track flex flex-col gap-2"
                  :class="colIdx % 2 === 0 ? 'campaign-hero-scroll-up' : 'campaign-hero-scroll-down'"
                >
                  <template v-for="(_, copyIdx) in 2" :key="copyIdx">
                    <div
                      v-for="(v, vIdx) in colVideos"
                      :key="`${copyIdx}-${vIdx}`"
                      class="campaign-hero-video-card relative flex-shrink-0 overflow-hidden rounded-lg"
                    >
                      <video
                        class="h-full w-full object-cover"
                        :poster="v.poster"
                        muted
                        loop
                        playsinline
                        preload="metadata"
                      >
                        <source :src="v.src" type="video/mp4" />
                      </video>
                      <div class="absolute bottom-1.5 left-1/2 flex -translate-x-1/2 items-center justify-center">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-[9px] font-bold text-[#111827]">{{ v.brand }}</span>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Live Campaigns: alternating rows – (text left | image right) then (image left | text right) -->
    <section class="border-b border-[#e5e7eb] px-4 py-12 md:py-16">
      <div class="mx-auto max-w-6xl">
        <div class="space-y-12 md:space-y-16">
          <article
            v-for="(campaign, idx) in liveCampaigns"
            :key="idx"
            class="campaign-row flex flex-col gap-8 rounded-2xl border border-[#e5e7eb] p-6 md:gap-10 md:p-8 lg:flex-row lg:items-center lg:gap-12"
            :class="idx % 2 === 1 ? 'lg:flex-row-reverse' : ''"
          >
            <!-- Content block -->
            <div class="min-w-0 flex-1 lg:flex-[1_1_50%]">
              <p class="inline-block rounded-md px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-[#92400e]">
                Why choose {{ campaign.brand }}?
              </p>
              <h2 class="mt-3 text-xl font-bold text-[#111827] md:text-2xl">{{ campaign.title }}</h2>
              <p class="mt-3 text-sm leading-relaxed text-[#6b7280] md:text-base">
                {{ campaign.description }}
              </p>
              <ul class="mt-5 space-y-2 text-sm text-[#4b5563]">
                <li class="flex items-start gap-2">
                  <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-[#e5e7eb] text-xs font-bold text-[#111827]">1</span>
                  {{ campaign.spec1 }}
                </li>
                <li class="flex items-start gap-2">
                  <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-[#e5e7eb] text-xs font-bold text-[#111827]">2</span>
                  {{ campaign.spec2 }}
                </li>
                <li class="flex items-start gap-2">
                  <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-[#e5e7eb] text-xs font-bold text-[#111827]">3</span>
                  {{ campaign.spec3 }}
                </li>
              </ul>
              <router-link
                :to="campaign.applyLink || '/register?type=creator'"
                class="mt-6 inline-flex items-center justify-center rounded-xl bg-[#fc4402] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#e63d02]"
              >
                Apply Now
              </router-link>
            </div>
            <!-- Media: vertical image with play button -->
            <div class="relative flex-shrink-0 lg:flex-[0_0_280px] xl:flex-[0_0_320px]">
              <div class="relative aspect-[9/16] w-full max-w-[200px] overflow-hidden rounded-xl md:max-w-[240px] lg:max-w-full">
                <img
                  :src="campaign.thumbnail"
                  :alt="campaign.title"
                  class="h-full w-full object-cover"
                />
                <div class="absolute inset-0 flex items-center justify-center bg-black/20 transition hover:bg-black/30">
                  <span class="flex h-14 w-14 items-center justify-center rounded-full bg-red-600/95 text-white">
                    <svg class="ml-1 h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  </span>
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>
    <!-- Bottom CTA: Sign Up Now + Call Now (image jesa) -->
    <section class="border-t border-[#e5e7eb] px-4 py-12 md:py-16">
      <div class="mx-auto max-w-4xl">
        <div class="flex flex-col items-center justify-center gap-6 sm:flex-row sm:gap-8">
          <router-link
            to="/register"
            class="inline-flex w-full items-center justify-center rounded-xl bg-[#fc4402] px-8 py-4 text-base font-semibold text-white transition hover:bg-[#e63d02] sm:w-auto"
          >
            Sign Up Now
          </router-link>
          <a
            href="tel:+911234567890"
            class="inline-flex w-full items-center justify-center rounded-xl bg-[#fc4402] px-8 py-4 text-base font-semibold text-white transition hover:bg-[#e63d02] sm:w-auto"
          >
            Call Now
          </a>
        </div>
      </div>
    </section>
  </div>
</template>
<script setup>
// Hero right: 4 columns – col1 & col3 scroll UP, col2 & col4 scroll DOWN; each item is a video
const sampleVideo = {
  src: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
  poster: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc4?w=400&h=300&fit=crop',
};
const heroVideoColumns = [
  [
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc4?w=400&h=240&fit=crop', brand: 'IMAGINE' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=400&h=240&fit=crop', brand: 'BEYOUNG' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=240&fit=crop', brand: 'AJMAL' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=240&fit=crop', brand: 'CHINESE' },
  ],
  [
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=400&h=240&fit=crop', brand: 'CHINESE' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=240&fit=crop', brand: 'SONY' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=240&fit=crop', brand: "Re'equil" },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=240&fit=crop', brand: 'KAPIVA' },
  ],
  [
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=400&h=240&fit=crop', brand: 'CASIO' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=240&fit=crop', brand: 'AJMAL' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=400&h=240&fit=crop', brand: 'PERFORMA' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400&h=240&fit=crop', brand: 'STARJD' },
  ],
  [
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=400&h=240&fit=crop', brand: 'APP' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1525134479668-1bee5c7c6845?w=400&h=240&fit=crop', brand: 'Saffola' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1544723795-3fb0b90cffc6?w=400&h=240&fit=crop', brand: 'KAPIVA' },
    { ...sampleVideo, poster: 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=400&h=240&fit=crop', brand: 'STARJD' },
  ],
];

const liveCampaigns = [
  {
    brand: 'BLACKBUCK',
    title: 'Blackbuck Demand',
    description: "BlackBuck is India's largest trucking network, delivering reliability, efficiency and seamless experience for shippers and truckers.",
    spec1: 'Entertainment/Lifestyle - All Languages',
    spec2: 'Performance',
    spec3: '10K - 1M',
    applyLink: '/register?type=creator',
    thumbnail: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=400&h=700&fit=crop',
  },
  {
    brand: 'TATA CLIQ',
    title: 'Tata CLiQ',
    description: 'To promote products available on the website.',
    spec1: 'Fashion, Lifestyle, Beauty, Tech',
    spec2: 'Instagram',
    spec3: '50K - 1M',
    applyLink: '/register?type=creator',
    thumbnail: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=700&fit=crop',
  },
  {
    brand: 'GO COLORS',
    title: 'Celebrating Women - Phase 2',
    description: 'The campaign is centered around product promotion and optimising sales.',
    spec1: 'Female influencers from all categories. Hindi/English',
    spec2: 'Instagram',
    spec3: '20K - 150K',
    applyLink: '/register?type=creator',
    thumbnail: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=700&fit=crop',
  },
  {
    brand: 'PUMA',
    title: 'PropahLady',
    description: 'To promote their new collection during International Women\'s Week by giving several offers and perks through photoshoot at select stores. Rolled out through Influencer Marketing.',
    spec1: 'Fashion',
    spec2: 'Instagram',
    spec3: '10K - 1M',
    applyLink: '/register?type=creator',
    thumbnail: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=400&h=700&fit=crop',
  },
  {
    brand: 'WETTER',
    title: 'Wetter Online',
    description: 'The campaign was to create awareness for the farmers especially about their weather app called Mausam & Radar app.',
    spec1: 'Farming - Hindi',
    spec2: 'Instagram, Youtube',
    spec3: '100K - 1M',
    applyLink: '/register?type=creator',
    thumbnail: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=700&fit=crop',
  },
  {
    brand: 'LEAP FINANCE',
    title: 'Leap Finance',
    description: 'Leap finance is a one-stop destination for Indian students to fund their overseas education. Performance campaign to get more students to apply for loans for abroad studies.',
    spec1: 'Lifestyle/Finance - Hindi, English',
    spec2: 'Performance',
    spec3: '20K - 200K',
    applyLink: '/register?type=creator',
    thumbnail: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=400&h=700&fit=crop',
  },
];
</script>
<style scoped>
/* Hero: 4 columns – col1 & col3 scroll UP, col2 & col4 scroll DOWN (videos) */
.campaign-hero-video-card {
  height: 100px;
}
@media (min-width: 768px) {
  .campaign-hero-video-card {
    height: 115px;
  }
}
@media (min-width: 1024px) {
  .campaign-hero-video-card {
    height: 125px;
  }
}
.campaign-hero-video-track {
  width: 100%;
}
.campaign-hero-scroll-up {
  animation: campaignHeroScrollUp 40s linear infinite;
}
.campaign-hero-scroll-down {
  animation: campaignHeroScrollDown 40s linear infinite;
}
@keyframes campaignHeroScrollUp {
  0% {
    transform: translateY(0);
  }
  100% {
    transform: translateY(-50%);
  }
}
@keyframes campaignHeroScrollDown {
  0% {
    transform: translateY(-50%);
  }
  100% {
    transform: translateY(0);
  }
}
@media (max-width: 1023px) {
  .campaign-row .relative.aspect-\[9\/16\] {
    margin-left: auto;
    margin-right: auto;
  }
}
</style>
