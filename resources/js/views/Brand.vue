<template>
  <div class="min-h-screen bg-[#020617]">
    <!-- Hero section inspired by Upfluence Marketplace -->
    <section class="relative overflow-hidden border-b border-[#1f2937] bg-[radial-gradient(circle_at_top,_#2563eb,_#020617_55%)] px-4 py-20 md:py-10">
      <!-- subtle glow -->
      <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_rgba(252,68,2,0.4),_transparent_60%)]" />

      <div class="relative mx-auto flex max-w-6xl flex-col items-center gap-12 md:flex-row md:items-center md:justify-between">
        <!-- Left: heading + copy -->
        <div class="max-w-xl text-center md:text-left">
          <p class="inline-flex items-center gap-2 rounded-full bg-black/20 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-[#c7d2fe]">
            For brands & agencies
            <span class="h-1 w-8 rounded-full bg-[#fc4402]" />
          </p>
          <h1 class="mt-5 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-[2.7rem]">
            I'm a Brand
          </h1>
          <p class="mt-4 text-sm text-[#e5e7eb] sm:text-base">
            Find vetted creators. Brief, book, and get content delivered — all in one place.
          </p>

          <div class="mt-8 flex flex-wrap items-center justify-center gap-4 md:justify-start">
            <router-link
              to="/register?type=brand"
              class="cursor-link inline-flex items-center gap-2 rounded-full bg-[#fc4402] px-7 py-3 text-sm font-semibold text-white shadow-lg shadow-[#fc4402]/40 transition hover:-translate-y-0.5 hover:bg-[#e63d02]"
            >
              Join as Brand
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </router-link>
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white/90 backdrop-blur-sm transition hover:border-[#c7d2fe] hover:text-white"
            >
              Watch platform demo
              <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/10">
                <svg class="ml-0.5 h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M8 5v14l11-7z" />
                </svg>
              </span>
            </button>
          </div>

          <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-xs text-[#cbd5f5] md:justify-start">
            <div class="flex items-center gap-2">
              <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/10 text-[11px] font-semibold text-emerald-400">
                4.9
              </span>
              <span>Average campaign rating</span>
            </div>
            <div class="h-4 w-px bg-white/20" />
            <p><span class="font-semibold text-white">Top brands</span> trust StarJD for creator content.</p>
          </div>
        </div>

        <!-- Right: orbiting avatars around central card (centered, 8 images, larger orbit) -->
        <div class="relative flex w-full max-w-xl flex-shrink-0 justify-center py-6 md:w-1/2">
          <!-- outer glow -->
          <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_center,_rgba(59,130,246,0.8),_transparent_60%)] blur-3xl opacity-70" />

          <div class="brand-orbit-wrap relative flex h-[240px] w-[240px] items-center justify-center sm:h-[360px] sm:w-[300px]">
            <!-- central card (smaller so orbit stands out) -->
            <div
              class="relative z-10 flex h-32 w-32 flex-col justify-between rounded-3xl bg-white/95 px-3 py-2.5 text-left text-xs text-[#111827] shadow-2xl sm:h-36 sm:w-36 sm:px-4 sm:py-3"
            >
              <div class="flex items-center justify-between gap-1">
                <p class="text-[10px] font-semibold text-[#6b21a8]">Let’s work together</p>
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#fff7ed] text-[#ea580c]">👋</span>
              </div>
              <div class="mt-1.5 space-y-1.5">
                <div class="h-2 w-20 rounded-full bg-[#e5e7eb]" />
                <div class="h-2 w-28 rounded-full bg-[#e5e7eb]" />
                <div class="h-2 w-16 rounded-full bg-[#e5e7eb]" />
              </div>
              <div class="mt-2 flex items-center justify-between gap-1">
                <span class="rounded-full bg-[#eef2ff] px-2 py-0.5 text-[10px] font-medium text-[#4f46e5]">Brief sent</span>
                <span class="text-[10px] text-[#6b7280]">18 pitches</span>
              </div>
            </div>

            <!-- orbiting avatars: 8 images on a larger circle, bigger avatar size -->
            <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
              <div class="brand-orbit relative h-full w-full animate-orbit-spin">
                <div
                  v-for="(avatar, idx) in orbitAvatars"
                  :key="idx"
                  class="brand-orbit-dot absolute left-1/2 top-1/2 h-14 w-14 overflow-hidden rounded-full border-2 border-white/70 bg-[#020617] shadow-md sm:h-20 sm:w-20"
                  :style="avatar.orbitStyle"
                >
                  <img :src="avatar.src" :alt="avatar.alt" class="h-full w-full object-cover" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">Why choose us</p>
        <h2 class="mt-3 text-2xl font-bold text-[#111827] md:text-3xl">Why Brands Choose StarJD</h2>
        <div class="mt-12 grid gap-8 md:grid-cols-3">
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#111827]">Vetted Creators</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Search by niche, platform, and budget. Every creator is verified.</p>
          </div>
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#111827]">Escrow Payments</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Payments held securely until you approve content. No risk.</p>
          </div>
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#111827]">Campaign Tracking</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Track briefs, approvals, and delivery from your dashboard.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Creators in India: cards move – 2nd card→1st, 3rd→2nd, 4th→3rd, 5th→4th (positions with W-curve) -->
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
            <!-- Image + overlays (text jesa aapki image me) -->
            <div class="creators-card-img-wrap relative h-40 overflow-hidden rounded-t-[1.75rem] sm:h-48 md:h-56">
              <img
                :src="creatorsImages[card.imageIdx].src"
                :alt="creatorsImages[card.imageIdx].alt"
                class="creators-card-img h-full w-full object-cover transition-transform duration-500 ease-out"
              />
              <!-- Top-left: Instagram icon + count (10.7k) -->
              <div class="absolute left-2 top-2 flex items-center gap-1.5 rounded-md border border-white/20 bg-black/50 px-2 py-1 text-white backdrop-blur-sm sm:left-2.5 sm:top-2.5">
                <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
                <span class="text-[10px] font-medium sm:text-xs">{{ creatorsImages[card.imageIdx].followers }}</span>
              </div>
              <!-- Bottom-left: name + star 5.0 -->
              <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 to-transparent pt-8 pb-2 px-2 sm:px-2.5">
                <p class="truncate text-sm font-bold text-white sm:text-base">{{ creatorsImages[card.imageIdx].name }}</p>
                <p class="mt-0.5 flex items-center gap-1 text-white">
                  <span class="text-amber-400">★</span>
                  <span class="text-[11px] font-semibold sm:text-xs">{{ creatorsImages[card.imageIdx].rating }}</span>
                </p>
              </div>
            </div>
            <!-- Neeche: title, location, price -->
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

    <!-- Campaigns section (animated, responsive) -->
    <section class="border-b border-[#e5e7eb] bg-[radial-gradient(circle_at_top,_#f9fafb,_#ffffff)] px-4 py-16 md:py-24">
      <div class="mx-auto flex max-w-6xl flex-col items-center gap-12 lg:flex-row lg:items-stretch">
        <!-- Left: campaigns visual (background image card + floating brief card) -->
        <div class="relative w-full lg:w-1/2">
          <!-- background image card -->
          <div
            class="campaigns-bg-card pointer-events-none absolute -left-6 top-[120px] hidden h-64 w-full max-w-md overflow-hidden rounded-3xl shadow-lg sm:block"
          >
            <div
              class="h-full w-full bg-[url('https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=900&q=80')] bg-cover bg-center"
            />
          </div>

          <!-- front card -->
          <div
            class="campaigns-card group relative mx-auto w-full max-w-md overflow-hidden rounded-3xl border border-[#e5e7eb] bg-white/95 shadow-xl shadow-black/10 backdrop-blur-sm transition-transform duration-500 ease-out hover:-translate-y-2 hover:shadow-2xl"
          >
            <div class="grid grid-cols-2 gap-0">
              <!-- left: single creator image -->
              <div class="h-full min-h-[220px] w-full overflow-hidden">
                <div
                  class="h-full w-full bg-[url('https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=700&q=80')] bg-cover bg-center"
                />
              </div>

              <!-- right: campaign rows -->
              <div class="flex flex-col justify-between bg-white/95 p-4 text-xs text-[#111827] sm:text-sm">
                <div class="space-y-3">
                  <div>
                    <p class="text-[11px] font-semibold text-[#111827]">$55</p>
                    <p class="text-[11px] text-[#6b7280] sm:text-xs">Fashion &amp; Beauty Content</p>
                  </div>
                  <div class="flex flex-col gap-1">
                    <button class="rounded-full bg-[#111827] px-3 py-1 text-[11px] font-medium text-white shadow-sm shadow-black/20">
                      View Profile
                    </button>
                    <div class="flex gap-1">
                      <button class="flex-1 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-700">
                        Accept
                      </button>
                      <button class="flex-1 rounded-full bg-rose-50 px-2 py-1 text-[11px] font-medium text-rose-600">
                        Decline
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mt-4 space-y-3 border-t border-[#f3f4f6] pt-4">
                  <div>
                    <p class="text-[11px] font-semibold text-[#111827]">$150</p>
                    <p class="text-[11px] text-[#6b7280] sm:text-xs">Lifestyle Content Creator</p>
                  </div>
                  <div class="flex flex-col gap-1">
                    <button class="rounded-full bg-[#111827] px-3 py-1 text-[11px] font-medium text-white shadow-sm shadow-black/20">
                      View Profile
                    </button>
                    <div class="flex gap-1">
                      <button class="flex-1 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-700">
                        Accept
                      </button>
                      <button class="flex-1 rounded-full bg-rose-50 px-2 py-1 text-[11px] font-medium text-rose-600">
                        Decline
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: campaigns copy -->
        <div class="w-full lg:w-1/2">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">Campaigns</p>
          <h2 class="mt-3 text-2xl font-bold text-[#111827] md:text-3xl">
            Post campaigns and have 550,000+ influencers come to you.
          </h2>
          <div class="mt-6 space-y-6 text-sm text-[#4b5563]">
            <div>
              <h3 class="font-semibold text-[#111827]">Set Targeting</h3>
              <p class="mt-1">
                Specify demographics including niche, location and following size of the influencers you want to target.
              </p>
            </div>
            <div>
              <h3 class="font-semibold text-[#111827]">Post Campaign</h3>
              <p class="mt-1">
                Centralize your images, requirements, and more in a campaign brief sent to 550,000+ influencers.
              </p>
            </div>
            <div>
              <h3 class="font-semibold text-[#111827]">Influencers Apply</h3>
              <p class="mt-1">
                Targeted influencers submit their pricing, and you choose who to collaborate with.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Track Post Analytics – two columns: text left, stacked dashboard cards right (responsive) -->
    <section class="border-t border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-12 lg:flex-row lg:items-center lg:gap-16">
          <!-- Left: tag + heading + three features -->
          <div class="w-full lg:max-w-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">Track</p>
            <h2 class="mt-3 text-2xl font-bold leading-snug text-[#111827] md:text-3xl">
              Track Post Analytics and Performance in Real Time
            </h2>
            <div class="mt-8 space-y-8">
              <div>
                <h3 class="text-base font-bold text-[#111827] sm:text-lg">One-Click Tracking</h3>
                <p class="mt-2 text-sm leading-relaxed text-[#6b7280] sm:text-base">
                  Track Instagram, TikTok, and YouTube content in real time from a single dashboard. Say goodbye to manual tracking and messy spreadsheets.
                </p>
              </div>
              <div>
                <h3 class="text-base font-bold text-[#111827] sm:text-lg">Advanced Analytics &amp; Reporting</h3>
                <p class="mt-2 text-sm leading-relaxed text-[#6b7280] sm:text-base">
                  Analyze content performance over time, including impressions, engagement and more. Organize performance by campaign and effortlessly build reports.
                </p>
              </div>
              <div>
                <h3 class="text-base font-bold text-[#111827] sm:text-lg">Fully Automated</h3>
                <p class="mt-2 text-sm leading-relaxed text-[#6b7280] sm:text-base">
                  Metrics are updated every 24 hours, ensuring performance data is always up-to-date.
                </p>
              </div>
            </div>
          </div>

          <!-- Right: 2 cards – larger back (Campaign), smaller front (Instagram) overlapping top-right, image jesa -->
          <div class="group relative w-full min-h-[360px] sm:min-h-[400px] transition-transform duration-500 ease-out hover:-translate-y-2 hover:shadow-2xl lg:w-1/2">
            <!-- Back card (larger): Campaign – Golf Activewear Line Launch, + Track New Post, summary -->
            <div class="analytics-card analytics-card--back analytics-card-anim-back absolute inset-x-0 bottom-0 z-0 min-h-[280px] rounded-2xl border border-[#e5e7eb] bg-white p-4 shadow-[0_4px_20px_rgba(0,0,0,0.06)] sm:min-h-[300px] sm:p-5 md:min-h-[320px] md:p-6">
              <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
                <span class="text-sm font-semibold text-[#111827] sm:text-base">Golf Activewear Line Launch</span>
              </div>
              <div class="mt-3">
                <button type="button" class="rounded-lg bg-[#111827] px-4 py-2 text-xs font-medium text-white shadow-sm sm:text-sm">+ Track New Post</button>
              </div>
              <div class="mt-4 grid grid-cols-3 gap-2 sm:gap-3">
                <div class="rounded-xl border border-[#e5e7eb] bg-[#fafafa] p-2.5 text-center sm:p-3">
                  <p class="text-lg font-bold text-[#111827] sm:text-xl">3.9M</p>
                  <p class="mt-0.5 text-[10px] text-[#6b7280] sm:text-xs">Total Views</p>
                </div>
                <div class="rounded-xl border border-[#e5e7eb] bg-[#fafafa] p-2.5 text-center sm:p-3">
                  <p class="text-lg font-bold text-[#111827] sm:text-xl">4.6M</p>
                  <p class="mt-0.5 text-[10px] text-[#6b7280] sm:text-xs">Total Likes</p>
                </div>
                <div class="rounded-xl border border-[#e5e7eb] bg-[#fafafa] p-2.5 text-center sm:p-3">
                  <p class="text-lg font-bold text-[#111827] sm:text-xl">11.4K</p>
                  <p class="mt-0.5 text-[10px] text-[#6b7280] sm:text-xs">Total Comments</p>
                </div>
              </div>
              <div class="mt-3 flex gap-4 text-xs text-[#6b7280] sm:gap-6 sm:text-sm">
                <span><span class="font-bold text-[#111827]">137K</span> Engagements</span>
                <span><span class="font-bold text-[#111827]">3.5%</span> Engagement Rate</span>
              </div>
            </div>

            <!-- Front card (smaller, overlapping top-right): Instagram profile, View Post, metrics -->
            <div class="analytics-card analytics-card--front analytics-card-anim-front absolute right-0 top-4 z-10 w-full max-w-[280px] rounded-2xl border border-[#e5e7eb] bg-white p-4 shadow-[0_10px_40px_rgba(0,0,0,0.12)] sm:top-6 sm:right-4 sm:p-5">
              <div class="flex items-start justify-between gap-2">
                <div class="flex min-w-0 items-center gap-3">
                  <div class="relative h-11 w-11 shrink-0 overflow-hidden rounded-full bg-[#e5e7eb] sm:h-12 sm:w-12">
                    <img src="https://images.unsplash.com/photo-1611942122129-29977ae1398c?w=100&h=100&fit=crop" alt="Profile" class="h-full w-full object-cover" />
                  </div>
                  <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                      <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="url(#brand-ig)" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="brand-ig" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#f9ed32"/><stop offset="50%" stop-color="#ee2a7b"/><stop offset="100%" stop-color="#002aff"/></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                      <span class="truncate text-sm font-semibold text-[#111827]">@nike</span>
                    </div>
                    <button type="button" class="mt-2 rounded-lg border border-[#d1d5db] bg-[#f9fafb] px-3 py-1.5 text-xs font-medium text-[#374151] transition hover:bg-[#f3f4f6]">View Post</button>
                  </div>
                </div>
              </div>
              <p class="mt-3 text-[10px] text-[#6b7280] sm:text-xs">View Performance over Time …</p>
              <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[10px] sm:gap-x-5 sm:text-xs">
                <span><span class="font-semibold text-[#111827]">0</span> Views</span>
                <span><span class="font-semibold text-[#111827]">317K</span> Likes</span>
                <span><span class="font-semibold text-[#111827]">11K</span> Comments</span>
                <span><span class="font-semibold text-[#111827]">318K</span> Engagements</span>
                <span><span class="font-semibold text-[#111827]">11.0%</span> Engagement Rate</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- New animated brand workflow / platform section -->
    <section
      class="brand-workflow border-t border-[#e5e7eb] bg-[radial-gradient(circle_at_top,_#f9fafb,_#ffffff)] px-4 pb-16 pt-20 md:pb-24 md:pt-24"
    >
      <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-12 lg:flex-row lg:items-start">
          <!-- Left: narrative + stats -->
          <div class="lg:w-[40%]">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">For modern brands</p>
            <h2 class="mt-3 text-2xl font-bold leading-snug text-[#111827] md:text-3xl">
              Build, run and measure creator campaigns
              <span class="block text-[#4b5563]">in one simple flow.</span>
            </h2>
            <p class="mt-4 text-sm text-[#6b7280]">
              Inspired by the best creator platforms, StarJD gives you the tools to brief, discover, approve and analyse — without
              spreadsheets or endless DMs.
            </p>
            <div class="mt-6 grid gap-4 text-sm text-[#4b5563]">
              <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#fee2e2] text-xs font-semibold text-[#b91c1c]">
                  80k+
                </span>
                <p class="text-[#6b7280]">Creators you can tap into across niches, platforms and regions.</p>
              </div>
              <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#dcfce7] text-xs font-semibold text-emerald-700">
                  2x
                </span>
                <p class="text-[#6b7280]">Faster from brief to live content compared with manual workflows.</p>
              </div>
            </div>
          </div>

          <!-- Right: three-step animated timeline -->
          <div class="lg:w-[60%]">
            <ol class="space-y-5 lg:space-y-6">
              <li
                class="brand-step-card brand-step-card--1 group relative overflow-hidden rounded-2xl border border-[#e5e7eb] bg-gradient-to-r from-white via-white to-[#f9fafb] p-5 shadow-[0_18px_40px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:border-[#fc4402]"
              >
                <div class="flex items-start gap-4">
                  <div
                    class="mt-1 flex h-8 w-8 items-center justify-center rounded-full bg-[#fc4402]/10 text-sm font-semibold text-[#fc4402]"
                  >
                    1
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-[#111827]">Build your brief</h3>
                    <p class="mt-1 text-sm text-[#6b7280]">
                      Describe the content you want, your budget and timelines. Save presets for launches, evergreen UGC or always‑on
                      retention campaigns.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2 text-[11px] text-[#6b7280]">
                      <span class="rounded-full bg-[#f3f4f6] px-3 py-1">TikTok • Reels • Shorts</span>
                      <span class="rounded-full bg-[#f3f4f6] px-3 py-1">Usage rights included</span>
                      <span class="rounded-full bg-[#f3f4f6] px-3 py-1">Multi‑market briefs</span>
                    </div>
                  </div>
                </div>
              </li>
              <li
                class="brand-step-card brand-step-card--2 group relative overflow-hidden rounded-2xl border border-[#e5e7eb] bg-gradient-to-r from-white via-white to-[#f9fafb] p-5 shadow-[0_14px_30px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:border-[#22c55e]"
              >
                <div class="flex items-start gap-4">
                  <div
                    class="mt-1 flex h-8 w-8 items-center justify-center rounded-full bg-[#22c55e]/10 text-sm font-semibold text-[#22c55e]"
                  >
                    2
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-[#111827]">Manage creators &amp; content</h3>
                    <p class="mt-1 text-sm text-[#6b7280]">
                      Review pitches, approve concepts, collect drafts and keep your team aligned with one shared pipeline across all
                      campaigns.
                    </p>
                    <div class="mt-3 grid gap-2 text-[11px] text-[#6b7280] sm:grid-cols-3">
                      <div class="rounded-xl bg-[#f3f4f6] p-2">
                        <p class="text-[11px] text-[#6b7280]">Inbox</p>
                        <p class="text-xs font-semibold text-[#111827]">New creator pitches</p>
                      </div>
                      <div class="rounded-xl bg-[#f3f4f6] p-2">
                        <p class="text-[11px] text-[#6b7280]">Approvals</p>
                        <p class="text-xs font-semibold text-[#111827]">Content review queue</p>
                      </div>
                      <div class="rounded-xl bg-[#f3f4f6] p-2">
                        <p class="text-[11px] text-[#6b7280]">Payments</p>
                        <p class="text-xs font-semibold text-emerald-600">Escrow‑protected payouts</p>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <li
                class="brand-step-card brand-step-card--3 group relative overflow-hidden rounded-2xl border border-[#e5e7eb] bg-gradient-to-r from-white via-white to-[#f9fafb] p-5 shadow-[0_12px_24px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:border-[#38bdf8]"
              >
                <div class="flex items-start gap-4">
                  <div
                    class="mt-1 flex h-8 w-8 items-center justify-center rounded-full bg-[#38bdf8]/10 text-sm font-semibold text-[#38bdf8]"
                  >
                    3
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-[#111827]">Measure, boost &amp; scale</h3>
                    <p class="mt-1 text-sm text-[#6b7280]">
                      Track performance by creator, platform and asset. Turn top posts into ads and grow a reusable content library you
                      control.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-[#6b7280]">
                      <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/15 text-[11px] text-emerald-400">
                          ↑
                        </span>
                        <span>Best‑performing content auto‑tagged</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#fc4402]/15 text-[11px] text-[#fc4402]">
                          ★
                        </span>
                        <span>License‑ready creator library</span>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- One Place for All of Your Content Needs: left = 3 videos (click to play in place), right = text -->
    <section class="content-needs border-t border-[#e5e7eb] bg-white px-4 pb-16 pt-16 md:pb-20 md:pt-20">
      <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-12 lg:flex-row lg:items-center lg:gap-16">
          <!-- Left: 3 overlapping video cards – image jesa: left bottom-left, middle higher-right, right highest (diagonal up-right, bigger cards, proper gapping) -->
          <div class="content-needs-videos order-2 flex justify-center lg:order-1 lg:w-[48%]">
            <div class="content-needs-stack relative w-full max-w-[340px] sm:max-w-[400px] md:max-w-[440px]">
              <!-- Card 1: leftmost, bottom-left of cluster -->
              <div
                class="content-needs-card content-needs-card--1 absolute bottom-0 left-0 z-0 cursor-pointer overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-[0_8px_28px_rgba(0,0,0,0.1)] transition hover:z-10 hover:shadow-xl"
                @click="toggleContentVideo(0)"
              >
                <div class="content-needs-card-inner relative aspect-[9/16] w-full bg-[#f3f4f6]">
                  <video
                    :ref="(el) => setContentVideoRef(el, 0)"
                    class="h-full w-full object-cover"
                    playsinline
                    muted
                    :poster="contentVideos[0].poster"
                    @click.stop="toggleContentVideo(0)"
                    @ended="onContentVideoEnded(0)"
                  >
                    <source :src="contentVideos[0].src" type="video/mp4" />
                  </video>
                  <div
                    v-show="!contentVideoPlaying[0]"
                    class="absolute inset-0 flex items-center justify-center bg-black/20 transition"
                  >
                    <span class="content-needs-play-btn flex items-center justify-center rounded-full bg-white/95 text-[#111827] shadow-lg">
                      <svg class="ml-1 h-6 w-6 sm:h-8 sm:w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                  </div>
                  <div class="absolute bottom-2 right-2 h-6 w-6 opacity-90 md:h-7 md:w-7" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-full w-full"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                  </div>
                </div>
              </div>
              <!-- Card 2: middle, overlaps top-right of card1, slightly higher and right -->
              <div
                class="content-needs-card content-needs-card--2 absolute z-10 cursor-pointer overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-[0_10px_32px_rgba(0,0,0,0.12)] transition hover:z-20 hover:shadow-xl"
                @click="toggleContentVideo(1)"
              >
                <div class="content-needs-card-inner relative aspect-[9/16] w-full bg-[#f3f4f6]">
                  <video
                    :ref="(el) => setContentVideoRef(el, 1)"
                    class="h-full w-full object-cover"
                    playsinline
                    muted
                    :poster="contentVideos[1].poster"
                    @click.stop="toggleContentVideo(1)"
                    @ended="onContentVideoEnded(1)"
                  >
                    <source :src="contentVideos[1].src" type="video/mp4" />
                  </video>
                  <div
                    v-show="!contentVideoPlaying[1]"
                    class="absolute inset-0 flex items-center justify-center bg-black/20 transition"
                  >
                    <span class="content-needs-play-btn flex items-center justify-center rounded-full bg-white/95 text-[#111827] shadow-lg">
                      <svg class="ml-1 h-6 w-6 sm:h-8 sm:w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                  </div>
                  <div class="absolute bottom-2 right-2 h-6 w-6 opacity-90 md:h-7 md:w-7" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-full w-full"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                  </div>
                </div>
              </div>
              <!-- Card 3: rightmost, overlaps top-right of card2, highest and right -->
              <div
                class="content-needs-card content-needs-card--3 absolute z-20 cursor-pointer overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-[0_12px_36px_rgba(0,0,0,0.14)] transition hover:z-30 hover:shadow-xl"
                @click="toggleContentVideo(2)"
              >
                <div class="content-needs-card-inner relative aspect-[9/16] w-full bg-[#f3f4f6]">
                  <video
                    :ref="(el) => setContentVideoRef(el, 2)"
                    class="h-full w-full object-cover"
                    playsinline
                    muted
                    :poster="contentVideos[2].poster"
                    @click.stop="toggleContentVideo(2)"
                    @ended="onContentVideoEnded(2)"
                  >
                    <source :src="contentVideos[2].src" type="video/mp4" />
                  </video>
                  <div
                    v-show="!contentVideoPlaying[2]"
                    class="absolute inset-0 flex items-center justify-center bg-black/20 transition"
                  >
                    <span class="content-needs-play-btn flex items-center justify-center rounded-full bg-white/95 text-[#111827] shadow-lg">
                      <svg class="ml-1 h-6 w-6 sm:h-8 sm:w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                  </div>
                  <div class="absolute bottom-2 right-2 h-6 w-6 opacity-90 md:h-7 md:w-7" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-full w-full"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: heading + 3 features -->
          <div class="content-needs-text order-1 lg:order-2 lg:w-[55%]">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">Content that converts</p>
            <h2 class="mt-3 text-2xl font-bold leading-snug text-[#111827] md:text-3xl">
              One Place for All of Your Content Needs
            </h2>
            <div class="mt-8 space-y-6 md:mt-10 md:space-y-8">
              <div>
                <h3 class="text-base font-bold text-[#111827] md:text-lg">Sponsored Posts</h3>
                <p class="mt-1.5 text-sm text-[#6b7280] md:text-base">
                  Purchase sponsored content on Instagram, TikTok, and YouTube.
                </p>
              </div>
              <div>
                <h3 class="text-base font-bold text-[#111827] md:text-lg">Product Tutorials</h3>
                <p class="mt-1.5 text-sm text-[#6b7280] md:text-base">
                  Use influencers to create high-quality product tutorials.
                </p>
              </div>
              <div>
                <h3 class="text-base font-bold text-[#111827] md:text-lg">Testimonials</h3>
                <p class="mt-1.5 text-sm text-[#6b7280] md:text-base">
                  Get influencers to generate testimonials for your product or business.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- As seen in -->
        <p class="mt-14 text-center text-sm text-[#9ca3af] md:mt-16">As seen in</p>
      </div>
    </section>

    <!-- FAQ section -->
    <section
      class="relative overflow-hidden border-t border-[#e5e7eb] bg-[#020617] px-4 py-16 md:py-24"
    >
      <!-- background image + soft gradient overlay -->
      <div
        class="pointer-events-none absolute inset-0 bg-[url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1400&q=80')] bg-cover bg-center opacity-100"
      ></div>
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/95 via-white/92 to-[#f9fafb]/98"></div>

      <div class="relative mx-auto max-w-5xl">
        <div class="flex flex-col gap-10 md:flex-row md:items-start">
          <!-- Left: intro -->
          <div class="md:w-1/3">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">FAQ</p>
            <h2 class="mt-3 text-2xl font-bold text-[#111827] md:text-3xl">
              All the details,
              <span class="block text-[#4b5563]">before you hit “Post”.</span>
            </h2>
            <p class="mt-4 text-sm text-[#6b7280]">
              Answers to the most common questions brands have about posting campaigns and working with creators on StarJD.
            </p>
          </div>

          <!-- Right: accordions -->
          <div class="md:w-2/3">
            <div class="grid gap-4 md:grid-cols-2">
              <!-- Q1 -->
              <details
                class="faq-item group col-span-1 rounded-2xl border border-[#e5e7eb] bg-white/80 p-4 text-sm text-[#111827] shadow-sm shadow-black/5 open:shadow-md open:border-[#fc4402]/80"
                open
              >
                <summary
                  class="flex cursor-pointer list-none items-center gap-3 text-left font-semibold text-[#111827] md:text-[15px]"
                >
                  <span
                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-[#fee2e2] text-xs font-semibold text-[#b91c1c]"
                  >
                    1
                  </span>
                  <span class="flex-1">How does StarJD vet creators?</span>
                  <span
                    class="ml-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#f3f4f6] text-xs text-[#4b5563] transition-transform group-open:rotate-90"
                  >
                    →
                  </span>
                </summary>
                <p class="mt-3 text-xs text-[#6b7280] md:text-sm">
                  Every creator is reviewed for audience quality, engagement authenticity and past collaborations. Only profiles that
                  meet our brand‑safety bar are approved to pitch on campaigns.
                </p>
              </details>

              <!-- Q2 -->
              <details
                class="faq-item group col-span-1 rounded-2xl border border-[#e5e7eb] bg-white/80 p-4 text-sm text-[#111827] shadow-sm shadow-black/5"
              >
                <summary
                  class="flex cursor-pointer list-none items-center gap-3 text-left font-semibold text-[#111827] md:text-[15px]"
                >
                  <span
                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-[#dbeafe] text-xs font-semibold text-[#1d4ed8]"
                  >
                    2
                  </span>
                  <span class="flex-1">What does a typical campaign budget look like?</span>
                  <span
                    class="ml-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#f3f4f6] text-xs text-[#4b5563] transition-transform group-open:rotate-90"
                  >
                    →
                  </span>
                </summary>
                <p class="mt-3 text-xs text-[#6b7280] md:text-sm">
                  Brands test concepts from under ₹25k and scale into always‑on programs across regions. You control your total budget,
                  caps per creator and what’s included in each deliverable.
                </p>
              </details>

              <!-- Q3 -->
              <details
                class="faq-item group col-span-1 rounded-2xl border border-[#e5e7eb] bg-white/80 p-4 text-sm text-[#111827] shadow-sm shadow-black/5"
              >
                <summary
                  class="flex cursor-pointer list-none items-center gap-3 text-left font-semibold text-[#111827] md:text-[15px]"
                >
                  <span
                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-[#dcfce7] text-xs font-semibold text-emerald-700"
                  >
                    3
                  </span>
                  <span class="flex-1">How are payments and contracts handled?</span>
                  <span
                    class="ml-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#f3f4f6] text-xs text-[#4b5563] transition-transform group-open:rotate-90"
                  >
                    →
                  </span>
                </summary>
                <p class="mt-3 text-xs text-[#6b7280] md:text-sm">
                  Payments are held in secure escrow until content is delivered and approved. Standard terms, usage rights and
                  delivery windows are included in every brief, so you don’t chase paperwork.
                </p>
              </details>

              <!-- Q4 -->
              <details
                class="faq-item group col-span-1 rounded-2xl border border-[#e5e7eb] bg-white/80 p-4 text-sm text-[#111827] shadow-sm shadow-black/5"
              >
                <summary
                  class="flex cursor-pointer list-none items-center gap-3 text-left font-semibold text-[#111827] md:text-[15px]"
                >
                  <span
                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-[#fef3c7] text-xs font-semibold text-[#b45309]"
                  >
                    4
                  </span>
                  <span class="flex-1">Can I invite creators I already work with?</span>
                  <span
                    class="ml-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#f3f4f6] text-xs text-[#4b5563] transition-transform group-open:rotate-90"
                  >
                    →
                  </span>
                </summary>
                <p class="mt-3 text-xs text-[#6b7280] md:text-sm">
                  Absolutely. Bring your existing creator partners into StarJD so briefs, approvals, files and payments live in a
                  single, auditable workspace.
                </p>
              </details>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Post a campaign / CTA section -->
    <section class="border-t border-[#e5e7eb] bg-[radial-gradient(circle_at_top,_#fee2e2,_#ffffff_55%)] px-4 py-16 md:py-20">
      <div class="mx-auto max-w-5xl rounded-3xl bg-white/80 p-8 shadow-[0_18px_40px_rgba(15,23,42,0.12)] md:px-10 md:py-10">
        <div class="flex flex-col items-start gap-8 md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#fc4402]">Post a campaign</p>
            <h2 class="mt-3 text-2xl font-bold text-[#111827] md:text-3xl">
              Ready to brief your next creator campaign?
            </h2>
            <p class="mt-3 text-sm text-[#6b7280]">
              Share your goals, budget and timelines in a single workflow. StarJD will surface the right creators and keep every step
              on track.
            </p>
          </div>

          <div class="flex w-full flex-col items-stretch gap-3 md:w-auto md:items-end">
            <router-link
              to="/register?type=brand"
              class="inline-flex items-center justify-center gap-2 rounded-full bg-[#fc4402] px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-[#fc4402]/40 transition hover:-translate-y-0.5 hover:bg-[#e63d02]"
            >
              Post your first campaign
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </router-link>
            <p class="text-xs text-[#9ca3af] md:text-[13px]">
              No long‑term contracts. Start with a single brief and scale when you’re ready.
            </p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// 8 avatars evenly on orbit circle (45° each); orbit radius and angle applied via style
// Increase this value to push avatars further away from the center card
const orbitRadiusPx = 150;

// Creators in India: cards move (2nd→1st, 3rd→2nd, ...) and the wrapping card goes "behind".
// Also supports more images (the wrap-to-end card gets a new image after the move finishes).
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
  { src: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=600&fit=crop', alt: 'Creator 11', name: 'Aditya Rao', rating: '4.8', followers: '19.6k', title: 'Lifestyle Creator', location: 'Goa, GA, IN', price: '$58' },
  { src: 'https://images.unsplash.com/photo-1544723795-3fb0b9c54af1?w=400&h=600&fit=crop', alt: 'Creator 12', name: 'Meera Krishnan', rating: '5.0', followers: '8.9k', title: 'Beauty & Wellness', location: 'Kochi, KL, IN', price: '$38' },
];

const creatorStaggerClasses = [
  '-translate-y-10 md:-translate-y-14', // slot 0: highest
  '-translate-y-4 md:-translate-y-6',    // slot 1: medium
  'translate-y-6 md:translate-y-8',      // slot 2: lowest (center)
  '-translate-y-4 md:-translate-y-6',     // slot 3: medium
  '-translate-y-10 md:-translate-y-14',   // slot 4: highest
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

let creatorsIntervalId = null;
onMounted(() => {
  creatorsIntervalId = setInterval(advanceCreatorsCarousel, CREATORS_INTERVAL_MS);
});
onUnmounted(() => {
  if (creatorsIntervalId) clearInterval(creatorsIntervalId);
});

const orbitAvatars = [
  { src: 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=200&h=200&fit=crop', alt: 'Creator 1', angle: 0 },
  { src: 'https://images.unsplash.com/photo-1544723795-3fb0b90cffc6?w=200&h=200&fit=crop', alt: 'Creator 2', angle: 45 },
  { src: 'https://images.unsplash.com/photo-1525134479668-1bee5c7c6845?w=200&h=200&fit=crop', alt: 'Creator 3', angle: 90 },
  { src: 'https://images.unsplash.com/photo-1544723795-3fb0b9c54af1?w=200&h=200&fit=crop', alt: 'Creator 4', angle: 135 },
  { src: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=200&h=200&fit=crop', alt: 'Creator 5', angle: 180 },
  { src: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=200&h=200&fit=crop', alt: 'Creator 6', angle: 225 },
  { src: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop', alt: 'Creator 7', angle: 270 },
  { src: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop', alt: 'Creator 8', angle: 315 },
].map((a) => ({
  ...a,
  orbitStyle: {
    transform: `translate(-50%, -50%) rotate(${a.angle}deg) translateY(-${orbitRadiusPx}px) rotate(-${a.angle}deg)`,
  },
}));

// Content Needs section: 3 videos, click to play in place
const contentVideos = [
  { poster: 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=400&h=600&fit=crop', src: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4' },
  { poster: 'https://images.unsplash.com/photo-1529636798458-92182e662485?w=400&h=600&fit=crop', src: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4' },
  { poster: 'https://images.unsplash.com/photo-1536240478700-b869070f9279?w=400&h=600&fit=crop', src: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4' },
];
const contentVideoRefs = ref([null, null, null]);
const contentVideoPlaying = ref([false, false, false]);

function setContentVideoRef(el, index) {
  if (el) contentVideoRefs.value[index] = el;
}

function toggleContentVideo(index) {
  const refs = contentVideoRefs.value;
  const video = refs[index];
  if (!video) return;
  if (video.paused) {
    refs.forEach((v, i) => { if (v && i !== index) v.pause(); });
    const next = [false, false, false];
    next[index] = true;
    contentVideoPlaying.value = next;
    video.play().catch(() => { next[index] = false; contentVideoPlaying.value = [...next]; });
  } else {
    video.pause();
    contentVideoPlaying.value = contentVideoPlaying.value.map((_, i) => (i === index ? false : contentVideoPlaying.value[i]));
  }
}

function onContentVideoEnded(index) {
  const next = [...contentVideoPlaying.value];
  next[index] = false;
  contentVideoPlaying.value = next;
}

</script>

<style scoped>
@keyframes orbit-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.brand-orbit {
  transform-origin: center;
}

.animate-orbit-spin {
  animation: orbit-spin 26s linear infinite;
}

/* Creators in India: cards move – 2nd→1st, 3rd→2nd, etc. */
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

/* The 1st card that scrolls to 5th – hide during move (do not show) */
.creators-card--wrap {
  visibility: hidden;
  pointer-events: none;
  z-index: 0;
}

/* Track Analytics section: 2 stacked cards, responsive + animations */
@keyframes analyticsCardBackIn {
  0% {
    opacity: 0;
    transform: translateY(24px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes analyticsCardFrontIn {
  0% {
    opacity: 0;
    transform: translateX(16px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

.analytics-card-anim-back {
  animation: analyticsCardBackIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.analytics-card-anim-front {
  opacity: 0;
  animation: analyticsCardFrontIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.2s forwards;
}

@media (min-width: 1024px) {
  .analytics-card--back { left: 0; right: 0; }
  .analytics-card--front { max-width: 300px; }
}

/* Content Needs: 3 video cards – image jesa: left bottom-left, middle/right step up-right; bigger cards, proper gapping, responsive */
.content-needs-stack {
  min-height: 320px;
  padding-bottom: 0.5rem;
}
@media (min-width: 640px) {
  .content-needs-stack {
    min-height: 380px;
  }
}
@media (min-width: 768px) {
  .content-needs-stack {
    min-height: 420px;
  }
}
.content-needs-card {
  border-radius: 1rem;
  width: 46%;
}
@media (min-width: 640px) {
  .content-needs-card {
    width: 48%;
  }
}
@media (min-width: 768px) {
  .content-needs-card {
    width: 50%;
  }
}
/* diagonal: card1 bottom-left, card2 higher+right, card3 highest+right; consistent gapping */
.content-needs-card--1 { bottom: 0; left: 0; }
.content-needs-card--2 { bottom: 1.5rem; left: 30%; }
.content-needs-card--3 { bottom: 3rem; left: 60%; }
@media (min-width: 640px) {
  .content-needs-card--2 { bottom: 2rem; left: 32%; }
  .content-needs-card--3 { bottom: 4rem; left: 62%; }
}
@media (min-width: 768px) {
  .content-needs-card--2 { bottom: 2.5rem; left: 32%; }
  .content-needs-card--3 { bottom: 5rem; left: 62%; }
}
/* play button – prominent, scales with card */
.content-needs-play-btn {
  height: 2.75rem;
  width: 2.75rem;
}
@media (min-width: 640px) {
  .content-needs-play-btn {
    height: 3.25rem;
    width: 3.25rem;
  }
}
@media (min-width: 768px) {
  .content-needs-play-btn {
    height: 3.5rem;
    width: 3.5rem;
  }
}
</style>

<style scoped>
@keyframes brandGlow {
  0% {
    box-shadow: 0 0 0 rgba(15, 23, 42, 0.6);
  }
  50% {
    box-shadow: 0 0 40px rgba(252, 68, 2, 0.45), 0 0 60px rgba(56, 189, 248, 0.35);
  }
  100% {
    box-shadow: 0 0 0 rgba(15, 23, 42, 0.6);
  }
}

.brand-step-card::before {
  content: '';
  position: absolute;
  inset: -1px;
  border-radius: 1.25rem;
  background:
    radial-gradient(circle at top left, rgba(252, 68, 2, 0.35), transparent 55%),
    radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.3), transparent 55%);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}

.brand-step-card:hover::before {
  opacity: 1;
}

.brand-step-card {
  animation: brandGlow 7s ease-in-out infinite;
}

/* subtle slide-in on load for workflow cards */
.brand-step-card--1 {
  opacity: 0;
  transform: translateY(20px);
  animation: brandGlow 7s ease-in-out infinite, workflowSlideIn 700ms ease-out forwards;
}

.brand-step-card--2 {
  opacity: 0;
  transform: translateY(24px);
  animation: brandGlow 7s ease-in-out infinite, workflowSlideIn 900ms ease-out forwards;
}

.brand-step-card--3 {
  opacity: 0;
  transform: translateY(28px);
  animation: brandGlow 7s ease-in-out infinite, workflowSlideIn 1100ms ease-out forwards;
}

@keyframes workflowSlideIn {
  0% {
    opacity: 0;
    transform: translateY(28px);
  }
  100% {
    opacity: 1;
    transform: translateY(0px);
  }
}

@keyframes campaignsFloat {
  0% {
    transform: translateY(0px) translateX(0px);
  }
  50% {
    transform: translateY(-10px) translateX(6px);
  }
  100% {
    transform: translateY(0px) translateX(0px);
  }
}

@keyframes campaignsFloatBg {
  0% {
    transform: translateY(8px) translateX(-6px);
    opacity: 0.9;
  }
  50% {
    transform: translateY(16px) translateX(-2px);
    opacity: 1;
  }
  100% {
    transform: translateY(8px) translateX(-6px);
    opacity: 0.9;
  }
}

.campaigns-card {
  animation: campaignsFloat 11s ease-in-out infinite;
}

.campaigns-bg-card {
  animation: campaignsFloatBg 15s ease-in-out infinite;
}
</style>
