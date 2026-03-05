<template>
  <div class="min-h-screen">
    <!-- Hero: dark blue, left = headline + description + CTA, right = vertical scrolling video cards + brand badges (image jesa) -->
    <section class="campaign-hero relative overflow-hidden border-b border-[#1e3a5f] bg-[#0f172a] px-4">
      <div class="relative mx-auto max-w-6xl">
        <div class="flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between lg:gap-12">
          <!-- Left: content -->
          <div class="order-2 max-w-xl text-center lg:order-1 lg:text-left">
            <h1 class="text-3xl font-bold leading-tight text-white sm:text-4xl md:text-[2.5rem] lg:text-[2.75rem]">
              Powerful Influencer <br />
              <span class="text-[#fef08a]">Marketing</span> Campaigns
            </h1>
            <p class="mt-5 text-base leading-relaxed text-white/90 sm:text-lg">
              StarJD delivers impactful influencer marketing campaigns that help brands connect with their target audience. By collaborating with top influencers, we create authentic content that drives engagement and effectively markets your products or services, ensuring maximum reach and brand impact.
            </p>
            <router-link
              to="/login"
              class="mt-8 inline-flex items-center justify-center rounded-xl bg-[#fc4402] px-8 py-3.5 text-base font-semibold text-white transition hover:bg-[#0046d2]"
            >
              Post a Campaign
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
    <!-- Explore by Category (same as home page) -->
    <section
      id="categories"
      class="overflow-x-hidden border-b border-[#e5e7eb] bg-[#faf8f5] px-4 py-10 md:py-16"
    >
      <div class="mx-auto max-w-6xl">
        <h2 class="section-title mb-2 text-2xl font-bold text-[#1a1a1a] md:text-3xl">
          Explore by Category
        </h2>
        <p class="section-subtitle mb-10 text-[#6b7280] md:mb-12">
          Find creators in your niche.
        </p>
        <div
          class="category-carousel relative mx-auto flex items-center justify-center"
          style="--category-card-w: 230px; --category-card-gap: 16px;"
        >
          <button
            type="button"
            class="category-carousel-arrow absolute left-0 z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#fc4402] text-white shadow-md transition hover:bg-[#e63d02] md:left-2 md:h-12 md:w-12"
            aria-label="Previous category"
            @click="categoryCarouselPrev()"
          >
            <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <div
            class="category-carousel-track overflow-hidden px-4 py-4 md:px-6 md:py-5"
          >
            <div
              class="category-carousel-inner flex items-stretch gap-3 transition-transform duration-500 ease-out md:gap-4"
              :style="categoryCarouselTrackStyle"
            >
              <div
                v-for="(cat, i) in categories"
                :key="cat.name"
                class="category-carousel-card flex shrink-0 cursor-pointer flex-col overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-500"
                :class="i === categoryCenterIndex ? 'category-carousel-card--center border-[#fc4402] shadow-md ring-2 ring-[#fc4402]/20' : 'border-[#e5e7eb] hover:border-[#fc4402]/50'"
                :style="{
                  width: 'var(--category-card-w)',
                  opacity: i === categoryCenterIndex ? 1 : 0.4,
                  transform: i === categoryCenterIndex ? 'scale(1.1)' : 'scale(1)',
                  transformOrigin: 'center center',
                }"
                @click="categoryCarouselGoTo(i)"
              >
                <div class="relative aspect-[3/4] w-full overflow-hidden">
                  <img
                    :src="cat.image_url || cat.image"
                    :alt="cat.name"
                    class="h-full w-full object-cover transition duration-300"
                  />
                  <span
                    class="absolute right-2 top-2 rounded-full bg-[#fc4402] px-2 py-0.5 text-[10px] font-semibold uppercase text-white"
                  >
                    Categories
                  </span>
                </div>
                <div class="flex flex-1 flex-col justify-center border-t border-[#e5e7eb] bg-white p-3">
                  <h3 class="font-semibold text-[#1a1a1a]">{{ cat.name }}</h3>
                  <p class="mt-0.5 text-xs text-[#6b7280]">{{ cat.count }} creators</p>
                </div>
              </div>
            </div>
          </div>
          <button
            type="button"
            class="category-carousel-arrow absolute right-0 z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#fc4402] text-white shadow-md transition hover:bg-[#e63d02] md:right-2 md:h-12 md:w-12"
            aria-label="Next category"
            @click="categoryCarouselNext()"
          >
            <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
        <div class="mt-6 flex justify-center gap-2">
          <button
            v-for="(cat, i) in categories"
            :key="'dot-' + cat.name"
            type="button"
            class="h-2 w-2 rounded-full transition-all md:h-2.5 md:w-2.5"
            :class="i === categoryCenterIndex ? 'scale-125 bg-[#fc4402]' : 'bg-[#e5e7eb] hover:bg-[#d1d5db]'"
            :aria-label="'Go to ' + cat.name"
            @click="categoryCarouselGoTo(i)"
          />
        </div>
      </div>
    </section>
    <!-- Creators in India: cards move (same as Brand page) -->
    <section class="border-b border-[#e5e7eb] bg-white overflow-x-hidden py-10 md:py-14">
      <div class="mx-auto max-w-6xl px-4">
        <p class="text-center text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">Discover creators</p>
        <h2 class="mt-3 text-center text-2xl font-bold text-[#111827] md:text-3xl">
          Creators in India
        </h2>
      </div>
      <div class="relative mt-14 md:mt-20 flex justify-center px-2 md:px-4">
        <TransitionGroup
          name="creators-move"
          tag="div"
          class="creators-cards-row flex justify-center gap-4 pb-12 md:gap-6 md:pb-16"
        >
          <div
            v-for="(card, positionIndex) in creatorsCards"
            :key="card.id"
            class="creators-card flex-shrink-0 w-36 overflow-hidden rounded-[1.75rem] bg-white shadow-[0_8px_24px_rgba(0,0,0,0.12)] sm:w-44 md:w-52"
            :class="[creatorStaggerBySlot(positionIndex), { 'creators-card--wrap': card.id === wrappingCardId }]"
          >
            <router-link
              :to="'/creators'"
              class="block h-full cursor-pointer"
            >
              <div class="creators-card-img-wrap relative h-40 overflow-hidden rounded-t-[1.75rem] sm:h-48 md:h-56">
                <img
                  :src="creatorsImages[card.imageIdx].src"
                  :alt="creatorsImages[card.imageIdx].alt"
                  class="creators-card-img h-full w-full object-cover transition-transform duration-500 ease-out"
                />
                <div class="absolute left-2 top-2 flex items-center gap-1.5 rounded-md border border-white/20 bg-black/50 px-2 py-1 text-white backdrop-blur-sm sm:left-2.5 sm:top-2.5">
                  <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                  <span class="text-[10px] font-medium sm:text-xs">{{ creatorsImages[card.imageIdx].followers }}</span>
                </div>
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 to-transparent pt-8 pb-2 px-2 sm:px-2.5">
                  <p class="truncate text-sm font-bold text-white sm:text-base">{{ creatorsImages[card.imageIdx].name }}</p>
                  <p class="mt-0.5 flex items-center gap-1 text-white">
                    <span class="text-amber-400">★</span>
                    <span class="text-[11px] font-semibold sm:text-xs">{{ creatorsImages[card.imageIdx].rating }}</span>
                  </p>
                </div>
              </div>
              <div class="flex items-start justify-between gap-2 border-t border-[#f3f4f6] bg-white px-2 py-2 sm:px-2.5 sm:py-2.5">
                <div class="min-w-0 flex-1">
                  <p class="truncate text-xs font-semibold text-[#111827] sm:text-sm">{{ creatorsImages[card.imageIdx].title }}</p>
                  <p class="mt-0.5 truncate text-[10px] text-[#6b7280] sm:text-xs">{{ creatorsImages[card.imageIdx].location }}</p>
                </div>
                <p class="flex-shrink-0 text-sm font-bold text-[#111827] sm:text-base">{{ creatorsImages[card.imageIdx].price }}</p>
              </div>
            </router-link>
          </div>
        </TransitionGroup>
      </div>
    </section>
    <!-- Live Campaigns: hidden for now (set showLiveCampaigns = true to show) -->
    <section v-if="showLiveCampaigns" class="border-b border-[#e5e7eb] px-4 py-12 md:py-16">
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
    <!-- Services: largest section – heading + one tab per column, main content left, slide on change -->
    <section class="services-tabs-section relative overflow-hidden border-b border-[#e5e7eb] bg-[#faf8f5] px-4 py-16 md:py-20 lg:px-8 lg:py-24">
      <!-- Subtle background shapes -->
      <div class="pointer-events-none absolute -top-20 left-1/4 h-64 w-64 rounded-full bg-[#f5f0ea]/60 blur-3xl" aria-hidden="true"></div>
      <div class="pointer-events-none absolute -bottom-20 right-1/4 h-80 w-80 rounded-full bg-[#ebe5dc]/50 blur-3xl" aria-hidden="true"></div>
      <div class="relative mx-auto w-full max-w-[90rem]">
        <!-- Section heading (above services block) -->
        <div class="mb-12 text-center md:mb-16">
          <h2 class="text-3xl font-bold tracking-tight text-[#111827] md:text-4xl lg:text-5xl xl:text-[3.25rem]">
            Custom Content <span class="font-extrabold">Creator</span> Campaigns
          </h2>
          <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-[#6b7280] md:text-lg lg:max-w-3xl lg:text-xl">
            The wide range of creator network services we provide keep you plugged into what's going on at all times. We help you refocus your branding and marketing efforts so you're in sync with the pulse of what's new.
          </p>
        </div>
        <div class="services-tabs-wrapper flex flex-col rounded-3xl bg-[#f5f0ea] lg:flex-row lg:min-h-[540px] xl:min-h-[600px] 2xl:min-h-[640px]">
          <!-- Main content panel: content slides in on tab change -->
          <div
            class="services-content order-1 flex-1 overflow-hidden rounded-2xl lg:rounded-r-none lg:mx-0 lg:rounded-l-3xl"
            :style="{ backgroundColor: activeService?.panelColor || '#e8eef4' }"
          >
            <Transition name="service-slide" mode="out-in">
              <div
                v-if="activeService"
                :key="activeServiceId"
                class="flex h-full flex-col p-6 md:p-10 lg:flex-row lg:items-stretch lg:gap-10 lg:p-12"
              >
                <div class="min-w-0 flex-1">
                  <h3 class="text-2xl font-bold text-[#111827] md:text-3xl lg:text-4xl xl:text-[2.25rem]">{{ activeService.title }}</h3>
                  <p class="mt-5 text-sm leading-relaxed text-[#6b7280] md:text-base lg:mt-6 lg:text-lg lg:leading-relaxed">
                    {{ activeService.description }}
                  </p>
                  <router-link
                    :to="activeService.learnMoreLink || '/contact'"
                    class="mt-6 inline-flex items-center justify-center rounded-xl bg-[#111827] px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-[#374151] lg:mt-8 lg:px-8 lg:py-4 lg:text-base"
                  >
                    learn more
                  </router-link>
                </div>
                <div class="relative mt-6 flex-shrink-0 lg:mt-0 lg:w-[48%] lg:max-w-[440px] xl:max-w-[480px]">
                  <div class="relative aspect-[4/3] w-full overflow-hidden rounded-2xl lg:aspect-auto lg:h-full lg:min-h-[320px] xl:min-h-[360px]">
                    <img
                      :src="activeService.image"
                      :alt="activeService.title"
                      class="h-full w-full object-cover"
                    />
                    <div
                      v-if="activeService.handle"
                      class="absolute bottom-3 left-3 rounded-full bg-black/70 px-3 py-1.5 text-xs font-medium text-white lg:bottom-4 lg:left-4 lg:px-4 lg:py-2 lg:text-sm"
                    >
                      {{ activeService.handle }}
                    </div>
                  </div>
                </div>
              </div>
            </Transition>
          </div>
          <!-- Right: one column per tab (ek column meek hi ek button), staggered overlap -->
          <div class="services-tabs-columns order-2 flex shrink-0 gap-2 overflow-x-auto pb-4 pt-4 lg:order-2 lg:flex-row lg:gap-0 lg:overflow-visible lg:py-0 lg:pr-0">
            <div
              v-for="(item, idx) in servicesTabs"
              :key="item.id"
              class="services-tab-column flex shrink-0"
            >
              <button
                type="button"
                class="services-tab-btn flex h-full min-h-[52px] w-full items-center justify-center rounded-2xl px-5 py-4 text-sm font-semibold transition-all lg:min-h-0 lg:h-full lg:min-w-[56px] lg:max-w-[56px] xl:min-w-[64px] xl:max-w-[64px] lg:rounded-l-2xl lg:rounded-r-none lg:py-6"
                :class="[
                  activeServiceId === item.id ? 'services-tab-active ring-2 ring-offset-2' : 'hover:opacity-90',
                  activeServiceId === item.id && item.tabColor === '#111827' ? 'ring-white' : '',
                  activeServiceId === item.id && item.tabColor !== '#111827' ? 'ring-[#111827]' : '',
                ]"
                :style="{
                  backgroundColor: item.tabColor,
                  color: item.tabTextColor || (item.tabColor === '#111827' ? '#fff' : '#111827'),
                }"
                @click="activeServiceId = item.id"
              >
                {{ item.title }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';

// Services vertical tabs: 6 items; left 3, right 3; click to show details
const servicesTabs = [
  {
    id: 'share-of-influence',
    title: 'Share of Influence',
    description: 'Discover unique insights into your brand, identify competitors, benchmark using our proprietary AI platform and technology. The analysis, which is exclusive to Obvious, features your competitor\'s influence spending, creator volume, and top performing content trends, allowing you to gain a better understanding of your influencer marketing competitive landscape.',
    image: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=600&h=400&fit=crop',
    handle: '@kyleecampbell',
    tabColor: '#e8dcc8',
    panelColor: '#f5f0ea',
    learnMoreLink: '/contact',
  },
  {
    id: 'quick-turn-content',
    title: 'Quick-Turn Content',
    description: 'Co-create culture with your network of creators or tap into our roster of vetted talent. We produce scroll-stopping content that drives awareness and conversion—from UGC and unboxing to lifestyle and hero shoots—tailored for paid social, e‑commerce, and retail.',
    image: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc4?w=600&h=400&fit=crop',
    handle: '@_bysarv',
    tabColor: '#a8c5e0',
    panelColor: '#d4e4f4',
    learnMoreLink: '/contact',
  },
  {
    id: 'always-on-ambassador',
    title: 'Always-On Ambassador Programs',
    description: 'Long-term partnerships that keep your brand in the feed. We design and run always-on ambassador programs—identifying, onboarding, and managing creators who consistently represent your brand and drive sustained reach and engagement.',
    image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&h=400&fit=crop',
    handle: '@wrjght_',
    tabColor: '#b8c4b8',
    panelColor: '#e0e8dc',
    learnMoreLink: '/contact',
  },
  {
    id: 'seeding-custom-boxes',
    title: 'Seeding and Custom Boxes',
    description: 'Qualified influencers will be identified and vetted based on your targeting and campaign strategies. We handle end-to-end box design configuration, printing, fulfillment and shipping so your product lands in the right hands with maximum impact.',
    image: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&h=400&fit=crop',
    handle: '@wrjght_',
    tabColor: '#b0bcc8',
    panelColor: '#e8eef4',
    tabTextColor: '#111827',
    learnMoreLink: '/contact',
  },
  {
    id: 'ugc-for-commerce',
    title: 'UGC For Commerce',
    description: 'User-generated content built for performance. We brief, produce, and optimize UGC specifically for commerce—product pages, ads, and social—so you get authentic-looking assets that convert.',
    image: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=400&fit=crop',
    handle: '@creator',
    tabColor: '#e8e4dc',
    panelColor: '#f0ece4',
    tabTextColor: '#111827',
    learnMoreLink: '/contact',
  },
  {
    id: 'proprietary-reporting',
    title: 'Proprietary Custom Reporting',
    description: 'Transparent, actionable reporting built on our own tools and methodology. Get clear visibility into spend, performance, and ROI with dashboards and insights tailored to your goals—so you can optimize and scale with confidence.',
    image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop',
    handle: null,
    tabColor: '#111827',
    panelColor: '#f5f5f5',
    tabTextColor: '#ffffff',
    learnMoreLink: '/contact',
  },
];
const activeServiceId = ref(servicesTabs[0].id);
const activeService = computed(() => servicesTabs.find((s) => s.id === activeServiceId.value) || servicesTabs[0]);

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

// Explore by Category (same as home)
const categories = ref([
  { name: 'Fashion', count: '1.2k', image: 'https://picsum.photos/seed/fashion/400/500' },
  { name: 'Beauty', count: '890', image: 'https://picsum.photos/seed/beauty/400/500' },
  { name: 'Tech', count: '650', image: 'https://picsum.photos/seed/tech/400/500' },
  { name: 'Travel', count: '720', image: 'https://picsum.photos/seed/travel/400/500' },
]);
const categoryCarouselIndex = ref(0);
const CATEGORY_CARD_WIDTH = 220;
const CATEGORY_CARD_GAP = 16;
const CATEGORY_CARDS_PER_ROW = 4;
const categoryCarouselViewportWidth =
  CATEGORY_CARDS_PER_ROW * CATEGORY_CARD_WIDTH + (CATEGORY_CARDS_PER_ROW - 1) * CATEGORY_CARD_GAP;
const categoryCarouselTrackStyle = computed(() => {
  const cardW = CATEGORY_CARD_WIDTH;
  const gap = CATEGORY_CARD_GAP;
  const n = categoryCarouselIndex.value;
  return { transform: `translateX(${-n * (cardW + gap)}px)` };
});
const categoryCenterIndex = computed(() => {
  const n = categoryCarouselIndex.value;
  const len = categories.value.length;
  const centerOffset = Math.floor(CATEGORY_CARDS_PER_ROW / 2);
  return Math.min(len - 1, n + centerOffset);
});
function categoryCarouselNext() {
  categoryCarouselIndex.value =
    (categoryCarouselIndex.value + 1) % Math.max(1, categories.value.length);
}
function categoryCarouselPrev() {
  categoryCarouselIndex.value =
    (categoryCarouselIndex.value - 1 + categories.value.length) % Math.max(1, categories.value.length);
}
function categoryCarouselGoTo(i) {
  const centerOffset = Math.floor(CATEGORY_CARDS_PER_ROW / 2);
  const len = categories.value.length;
  const targetScroll = Math.max(0, Math.min(len - CATEGORY_CARDS_PER_ROW, i - centerOffset));
  categoryCarouselIndex.value = targetScroll;
}
watch(categories, (val) => {
  const len = val?.length ?? 0;
  if (len > 0 && categoryCarouselIndex.value >= len) categoryCarouselIndex.value = len - 1;
}, { deep: true });

// Creators in India: cards move (2nd→1st, 3rd→2nd, …)
const creatorsImages = [
  { src: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=600&fit=crop', alt: 'Creator 1', name: 'Priya Sharma', rating: '5.0', followers: '12.4k', title: 'Beauty Influencer', location: 'Mumbai, MH, IN', price: '$45' },
  { src: 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=400&h=600&fit=crop', alt: 'Creator 2', name: 'Rahul Verma', rating: '4.9', followers: '28.1k', title: 'Lifestyle Creator', location: 'Delhi, DL, IN', price: '$65' },
  { src: 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=400&h=600&fit=crop', alt: 'Creator 3', name: 'Ananya Reddy', rating: '5.0', followers: '10.7k', title: 'Fashion & Beauty', location: 'Bangalore, KA, IN', price: '$50' },
  { src: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=600&fit=crop', alt: 'Creator 4', name: 'Sneha Kapoor', rating: '4.8', followers: '15.2k', title: 'Beauty Influencer', location: 'Pune, MH, IN', price: '$55' },
  { src: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400&h=600&fit=crop', alt: 'Creator 5', name: 'Kavya Nair', rating: '5.0', followers: '9.3k', title: 'Lifestyle Creator', location: 'Chennai, TN, IN', price: '$40' },
  { src: 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=400&h=600&fit=crop', alt: 'Creator 6', name: 'Arjun Mehta', rating: '4.9', followers: '22k', title: 'Fitness Creator', location: 'Hyderabad, TG, IN', price: '$70' },
  { src: 'https://images.unsplash.com/photo-1525134479668-1bee5c7c6845?w=400&h=600&fit=crop', alt: 'Creator 7', name: 'Ishita Singh', rating: '5.0', followers: '18.5k', title: 'Beauty Influencer', location: 'Kolkata, WB, IN', price: '$52' },
  { src: 'https://images.unsplash.com/photo-1544723795-3fb0b90cffc6?w=400&h=600&fit=crop', alt: 'Creator 8', name: 'Vikram Joshi', rating: '4.7', followers: '31k', title: 'Tech Creator', location: 'Ahmedabad, GJ, IN', price: '$80' },
  { src: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=600&fit=crop', alt: 'Creator 9', name: 'Neha Gupta', rating: '4.9', followers: '14.8k', title: 'Fashion Creator', location: 'Jaipur, RJ, IN', price: '$48' },
  { src: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=600&fit=crop', alt: 'Creator 10', name: 'Riya Patel', rating: '5.0', followers: '11.2k', title: 'Beauty Influencer', location: 'Surat, GJ, IN', price: '$42' },
];
const creatorStaggerClasses = [
  '-translate-y-10 md:-translate-y-14',
  '-translate-y-4 md:-translate-y-6',
  'translate-y-6 md:translate-y-8',
  '-translate-y-4 md:-translate-y-6',
  '-translate-y-10 md:-translate-y-14',
];
function creatorStaggerBySlot(slotIdx) {
  return creatorStaggerClasses[slotIdx % 5];
}
const VISIBLE_CREATORS_COUNT = 5;
const creatorsCards = ref(
  Array.from({ length: VISIBLE_CREATORS_COUNT }, (_, i) => ({ id: i, imageIdx: i }))
);
const wrappingCardId = ref(null);
let nextImageCursor = VISIBLE_CREATORS_COUNT;
const CREATORS_INTERVAL_MS = 3200;
const CREATORS_MOVE_MS = 600;
function pickNextImageIdx() {
  const used = new Set(creatorsCards.value.map((c) => c.imageIdx));
  const maxTries = creatorsImages.length + 2;
  for (let t = 0; t < maxTries; t++) {
    const idx = nextImageCursor % creatorsImages.length;
    nextImageCursor++;
    if (!used.has(idx)) return idx;
  }
  return nextImageCursor++ % creatorsImages.length;
}
function advanceCreatorsCarousel() {
  const cards = creatorsCards.value;
  const wrappedId = cards[0].id;
  wrappingCardId.value = wrappedId;
  creatorsCards.value = [...cards.slice(1), cards[0]];
  window.setTimeout(() => {
    const idx = creatorsCards.value.findIndex((c) => c.id === wrappedId);
    if (idx !== -1) creatorsCards.value[idx].imageIdx = pickNextImageIdx();
    wrappingCardId.value = null;
  }, CREATORS_MOVE_MS + 40);
}

let categoryCarouselIntervalId = null;
let creatorsIndiaIntervalId = null;
onMounted(() => {
  categoryCarouselIntervalId = setInterval(() => categoryCarouselNext(), 4000);
  creatorsIndiaIntervalId = setInterval(advanceCreatorsCarousel, CREATORS_INTERVAL_MS);
});
onBeforeUnmount(() => {
  if (categoryCarouselIntervalId) clearInterval(categoryCarouselIntervalId);
  if (creatorsIndiaIntervalId) clearInterval(creatorsIndiaIntervalId);
});

const showLiveCampaigns = ref(false); // set true to show Live Campaigns section
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

/* Services tabs: one button per column, staggered overlap, vertical text on desktop */
@media (min-width: 1024px) {
  .services-tabs-columns {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    padding: 1rem 0;
  }
  .services-tab-column {
    width: 56px;
    min-width: 56px;
    margin-left: -8px;
    z-index: 1;
  }
  @media (min-width: 1280px) {
    .services-tab-column {
      width: 64px;
      min-width: 64px;
    }
  }
  .services-tab-column:first-child {
    margin-left: 0;
    z-index: 6;
  }
  .services-tab-column:nth-child(2) { z-index: 5; }
  .services-tab-column:nth-child(3) { z-index: 4; }
  .services-tab-column:nth-child(4) { z-index: 3; }
  .services-tab-column:nth-child(5) { z-index: 2; }
  .services-tab-column:nth-child(6) { z-index: 1; }
  .services-tab-column:last-child .services-tab-btn {
    border-radius: 0 1.5rem 1.5rem 0;
  }
  .services-tab-btn {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    transform: rotate(180deg);
    width: 100%;
    height: 100%;
    min-height: 100%;
  }
}

/* Services content: slide transition when switching tabs */
.service-slide-enter-active,
.service-slide-leave-active {
  transition: transform 0.35s ease, opacity 0.35s ease;
}
.service-slide-enter-from {
  transform: translateX(24px);
  opacity: 0;
}
.service-slide-leave-to {
  transform: translateX(-24px);
  opacity: 0;
}

/* Explore by Category carousel */
.category-carousel {
  min-height: 280px;
}
.category-carousel-track {
  flex: 1;
  min-width: 0;
  overflow: hidden;
  max-width: 100%;
}
.category-carousel-inner {
  will-change: transform;
}
@media (min-width: 768px) {
  .category-carousel {
    min-height: 320px;
  }
}
@media (min-width: 1024px) {
  .category-carousel {
    min-height: 360px;
  }
}

/* Creators in India: cards move */
.creators-move-move {
  transition: transform 0.6s ease-out;
}
.creators-card {
  position: relative;
  z-index: 2;
}
.creators-card:hover .creators-card-img {
  transform: scale(1.08);
}
.creators-card--wrap {
  visibility: hidden;
  pointer-events: none;
  z-index: 0;
}
</style>
