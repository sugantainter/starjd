<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <!-- Hero Section -->
    <section class="relative overflow-hidden border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-[#0f172a]/5 md:h-48 md:w-48" aria-hidden="true" />
      <div class="relative mx-auto max-w-6xl">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
          <div>
            <p class="inline-flex items-center gap-2 rounded-lg bg-[#fc4402]/10 px-3 py-1.5 text-sm font-semibold text-[#fc4402]">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#fc4402]/20 text-[#fc4402]">★</span>
              About StarJD
            </p>
            <h1 class="mt-4 text-3xl font-bold leading-tight text-[#1a1a1a] md:text-4xl lg:text-[2.5rem]">
              India's Premium Creative Media Studio & Production Company
            </h1>
            <p class="mt-2 text-sm text-[#64748b]">StarJD is powered by Suganta International.</p>
            <p class="mt-4 max-w-lg text-[#64748b]">
              Welcome to StarJD — a full-service creative video production and professional media agency based in New Delhi, India. We specialize in transforming ideas into visually powerful stories with strategic impact. Our work goes beyond simple content creation — we design experiences that connect, engage, and inspire audiences across digital platforms.
            </p>
            <div class="mt-8 flex flex-wrap items-center gap-4">
              <router-link
                to="/contact"
                class="inline-flex items-center gap-2 rounded-lg bg-[#fc4402] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#e53d02]"
              >
                Schedule A Consultation →
              </router-link>
              <a
                href="/#videos"
                class="inline-flex items-center gap-2 text-[#1a1a1a] transition hover:text-[#fc4402]"
              >
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-[#0f172a] text-white">
                  <svg class="ml-0.5 h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </span>
                Play Video
              </a>
            </div>
          </div>
          <!-- Hero right: 3 round images centered — 1 large (dominant), 2 same size; overlapping cluster -->
          <div class="about-hero-section relative flex justify-center py-6 sm:py-8 lg:min-h-[380px]">
            <div class="relative mx-auto h-[300px] w-[300px] sm:h-[340px] sm:w-[340px] md:h-[400px] md:w-[400px] lg:h-[420px] lg:w-[420px]">
              <!-- 01 — large circle (left), dominant like reference -->
              <div class="absolute bottom-0 left-0 z-0 h-52 w-52 overflow-hidden rounded-full border-2 border-[#e5e7eb] bg-white shadow-lg sm:h-60 sm:w-60 md:h-72 md:w-72 lg:h-80 lg:w-80">
                <img
                  src="/images/about-1.png"
                  alt="StarJD"
                  class="h-full w-full object-cover object-top"
                  onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&fit=crop';"
                />
              </div>
              <!-- 02 — top-right round (same size as 03) -->
              <div class="absolute right-2 top-2 z-10 h-32 w-32 overflow-hidden rounded-full border-2 border-[#e5e7eb] bg-white shadow-lg sm:right-4 sm:top-4 sm:h-36 sm:w-36 md:h-40 md:w-40 lg:right-6 lg:top-6 lg:h-44 lg:w-44">
                <img src="/images/about-2.png" alt="Team with laptop" class="h-full w-full object-cover" />
              </div>
              <!-- 03 — bottom-right round (same size as 02) -->
              <div class="absolute bottom-2 right-6 z-20 h-32 w-32 overflow-hidden rounded-full border-2 border-[#e5e7eb] bg-white shadow-lg sm:bottom-3 sm:right-8 sm:h-36 sm:w-36 md:bottom-4 md:right-8 md:h-40 md:w-40 lg:bottom-5 lg:right-10 lg:h-44 lg:w-44">
                <img src="/images/about-3.png" alt="Creative production" class="h-full w-full object-cover" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- About Us Section: intro + Vision & Creative Philosophy -->
    <section class="border-b border-[#e5e7eb] bg-[#fafaf9] px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
          <div class="relative order-2 lg:order-1">
            <div class="relative flex items-center justify-center">
              <!-- Back: 2 images with gapping between -->
              <div class="flex gap-4 md:gap-6 lg:gap-8">
                <div class="h-56 w-40 overflow-hidden rounded-xl shadow-lg md:h-64 md:w-44 lg:h-72 lg:w-52" style="background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);">
                  <img src="/images/about-4.png" alt="Creative work" class="h-full w-full object-cover" />
                </div>
                <div class="h-56 w-40 overflow-hidden rounded-xl shadow-lg md:h-64 md:w-44 lg:h-72 lg:w-52" style="background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);">
                  <img src="/images/about-5.png" alt="Creative work" class="h-full w-full object-cover" />
                </div>
              </div>
              <!-- Front: 1 larger central image overlapping both -->
              <div class="absolute left-1/2 top-4/5 z-10 h-64 w-48 -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-xl shadow-lg md:h-80 md:w-56" style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);">
                <img src="/images/about-6.png" alt="Team collaboration" class="h-full w-full object-cover" />
              </div>
            </div>
          </div>
          <div class="order-1 lg:order-2">
            <p class="text-sm font-semibold uppercase tracking-wider text-[#fc4402]">About StarJD</p>
            <h2 class="mt-2 text-2xl font-bold text-[#1a1a1a] md:text-3xl">
              Our Vision & Creative Philosophy
            </h2>
            <p class="mt-4 text-[#64748b]">
              As a leading video production company in India, StarJD offers an integrated range of services including professional video shoots, advertisement production, studio rentals, podcast recording, live streaming, photography, drone services, video editing, equipment rental, and portfolio shoots. Our mission is to be your creative partner from concept to final delivery — helping brands, creators, and professionals build a strong and lasting visual presence.
            </p>
            <p class="mt-4 text-[#64748b]">
              We believe compelling visual content should be meaningful, memorable, and aligned with your brand goals. Whether you are a startup, influencer, model, entrepreneur, or established corporation, we create customized media solutions designed to attract the right audience and drive measurable growth.
            </p>
            <p class="mt-4 text-[#64748b]">
              By combining creative storytelling, professional execution, and advanced production techniques, we ensure every project reflects quality, clarity, and impact.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <span class="inline-flex items-center gap-2 rounded-lg border border-[#e5e7eb] bg-white px-4 py-2.5 text-sm font-medium text-[#1a1a1a]">
                <svg class="h-5 w-5 text-[#fc4402]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Tailored Solutions
              </span>
              <span class="inline-flex items-center gap-2 rounded-lg border border-[#e5e7eb] bg-white px-4 py-2.5 text-sm font-medium text-[#1a1a1a]">
                <svg class="h-5 w-5 text-[#fc4402]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                Proven Expertise
              </span>
            </div>
            <router-link
              to="/services"
              class="mt-8 inline-flex items-center gap-2 rounded-lg bg-[#fc4402] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#e53d02]"
            >
              Discover Our Solutions →
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Comprehensive Services: 6 cards + intro -->
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <p class="text-center text-sm font-semibold uppercase tracking-wider text-[#fc4402]">Our Comprehensive Services</p>
        <h2 class="mt-2 text-center text-2xl font-bold text-[#1a1a1a] md:text-3xl">
          Empowering Your Success Through Expert Creative Production
        </h2>
        <p class="mx-auto mt-4 max-w-2xl text-center text-[#64748b]">
          StarJD brings all creative production services under one roof, making us a reliable partner for businesses and individuals seeking professional media solutions.
        </p>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div class="group rounded-xl border border-[#e5e7eb] bg-white p-6 shadow-sm transition hover:bg-[#fc4402] hover:shadow-md">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#f1f5f9] text-[#fc4402] group-hover:bg-white/20 group-hover:text-white">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="mt-4 font-bold text-[#1a1a1a] group-hover:text-white">Professional Video Production</h3>
            <p class="mt-2 text-sm text-[#64748b] group-hover:text-white/90">Corporate video shoots and brand films, advertisement and promotional campaign videos, product demo and launch videos, event filming and highlight reels, social media optimized video content.</p>
            <p class="mt-4 text-xs text-[#94a3b8] group-hover:text-white/80">01</p>
            <hr class="mt-1 w-8 border-[#e5e7eb] group-hover:border-white/30" />
          </div>
          <div class="rounded-xl bg-[#fc4402] p-6 text-white shadow-lg transition hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 text-white">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="mt-4 font-bold">Professional Photography</h3>
            <p class="mt-2 text-sm text-white/90">Corporate headshots and executive portraits, brand and lifestyle photography, product photography for e-commerce platforms, fashion and commercial photography.</p>
            <p class="mt-4 text-xs text-white/80">02</p>
            <hr class="mt-1 w-8 border-white/30" />
          </div>
          <div class="group rounded-xl border border-[#e5e7eb] bg-white p-6 shadow-sm transition hover:bg-[#fc4402] hover:shadow-md">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#f1f5f9] text-[#fc4402] group-hover:bg-white/20 group-hover:text-white">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="mt-4 font-bold text-[#1a1a1a] group-hover:text-white">Model & Portfolio Shoots</h3>
            <p class="mt-2 text-sm text-[#64748b] group-hover:text-white/90">Model portfolio photography, actor headshot sessions, fashion and editorial shoots, personal branding photoshoots, influencer profile shoots.</p>
            <p class="mt-4 text-xs text-[#94a3b8] group-hover:text-white/80">03</p>
            <hr class="mt-1 w-8 border-[#e5e7eb] group-hover:border-white/30" />
          </div>
          <div class="group rounded-xl border border-[#e5e7eb] bg-white p-6 shadow-sm transition hover:bg-[#fc4402] hover:shadow-md">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#f1f5f9] text-[#fc4402] group-hover:bg-white/20 group-hover:text-white">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v3m0 0V6a3 3 0 016 0v3m0 0v10h-6v-4m-6 4v-4h12v4"/></svg>
            </div>
            <h3 class="mt-4 font-bold text-[#1a1a1a] group-hover:text-white">Podcast & Content Studio</h3>
            <p class="mt-2 text-sm text-[#64748b] group-hover:text-white/90">Multi-camera podcast recording, studio-quality audio production, video podcast editing, short-form content creation for social media.</p>
            <p class="mt-4 text-xs text-[#94a3b8] group-hover:text-white/80">04</p>
            <hr class="mt-1 w-8 border-[#e5e7eb] group-hover:border-white/30" />
          </div>
          <div class="group rounded-xl border border-[#e5e7eb] bg-white p-6 shadow-sm transition hover:bg-[#fc4402] hover:shadow-md">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#f1f5f9] text-[#fc4402] group-hover:bg-white/20 group-hover:text-white">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="mt-4 font-bold text-[#1a1a1a] group-hover:text-white">Live Streaming & Virtual</h3>
            <p class="mt-2 text-sm text-[#64748b] group-hover:text-white/90">Corporate webinars, hybrid and virtual events, product launches, online conferences and panel discussions.</p>
            <p class="mt-4 text-xs text-[#94a3b8] group-hover:text-white/80">05</p>
            <hr class="mt-1 w-8 border-[#e5e7eb] group-hover:border-white/30" />
          </div>
          <div class="group rounded-xl border border-[#e5e7eb] bg-white p-6 shadow-sm transition hover:bg-[#fc4402] hover:shadow-md">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#f1f5f9] text-[#fc4402] group-hover:bg-white/20 group-hover:text-white">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h3 class="mt-4 font-bold text-[#1a1a1a] group-hover:text-white">Video Editing & Studio Rental</h3>
            <p class="mt-2 text-sm text-[#64748b] group-hover:text-white/90">Post-production, color grading, motion graphics, sound design. Drone services. Fully equipped studio rental and equipment.</p>
            <p class="mt-4 text-xs text-[#94a3b8] group-hover:text-white/80">06</p>
            <hr class="mt-1 w-8 border-[#e5e7eb] group-hover:border-white/30" />
          </div>
        </div>
        <!-- Full services detail (collapsed prose) -->
        <div class="prose prose-sm mt-16 max-w-none text-[#64748b]">
          <p class="text-[#64748b]">Our end-to-end video production process includes concept development, scripting, storyboarding, filming, editing, and final post-production to ensure a polished and professional outcome. Our studio uses professional lighting setups, high-resolution camera systems, and expert creative direction to produce visually striking results. We guide clients through styling, posing, and expression to create casting-ready portfolios. Flexible rental options allow creators and agencies to execute projects without investing in expensive infrastructure.</p>
          <p class="mt-4 text-[#64748b]">Our drone services provide dynamic aerial visuals ideal for real estate projects, event coverage, construction documentation, travel promotions, and brand storytelling.</p>
        </div>
      </div>
    </section>

    <!-- Why Choose StarJD -->
    <section class="border-b border-[#e5e7eb] bg-[#fafaf9] px-4 py-16 md:py-24">
      <div class="mx-auto max-w-4xl">
        <h2 class="text-center text-2xl font-bold text-[#1a1a1a] md:text-3xl">Why Choose StarJD</h2>
        <p class="mt-4 text-center text-[#64748b]">
          StarJD stands out because of our commitment to quality, transparency, and creativity. We collaborate closely with clients to ensure every project aligns with their brand vision and business objectives.
        </p>
        <p class="mt-2 text-center text-[#64748b]">Our strengths include:</p>
        <ul class="mt-6 grid gap-4 sm:grid-cols-2">
          <li class="flex items-start gap-2 rounded-xl border border-[#e5e7eb] bg-white p-4">
            <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#fc4402]" />
            <span class="text-[#374151]">End-to-end creative production</span>
          </li>
          <li class="flex items-start gap-2 rounded-xl border border-[#e5e7eb] bg-white p-4">
            <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#fc4402]" />
            <span class="text-[#374151]">Professional photography and cinematography expertise</span>
          </li>
          <li class="flex items-start gap-2 rounded-xl border border-[#e5e7eb] bg-white p-4">
            <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#fc4402]" />
            <span class="text-[#374151]">Modern studio infrastructure</span>
          </li>
          <li class="flex items-start gap-2 rounded-xl border border-[#e5e7eb] bg-white p-4">
            <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#fc4402]" />
            <span class="text-[#374151]">Strategic content planning</span>
          </li>
          <li class="flex items-start gap-2 rounded-xl border border-[#e5e7eb] bg-white p-4">
            <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#fc4402]" />
            <span class="text-[#374151]">Timely project delivery</span>
          </li>
          <li class="flex items-start gap-2 rounded-xl border border-[#e5e7eb] bg-white p-4">
            <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#fc4402]" />
            <span class="text-[#374151]">Results-driven visual storytelling</span>
          </li>
        </ul>
      </div>
    </section>

    <!-- Frequently Asked Questions -->
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-3xl">
        <h2 class="text-center text-2xl font-bold text-[#1a1a1a] md:text-3xl">Frequently Asked Questions</h2>
        <div class="mt-10 space-y-6">
          <div class="rounded-xl border border-[#e5e7eb] bg-[#fafaf9] p-5">
            <h3 class="font-semibold text-[#1a1a1a]">1. What services does StarJD provide?</h3>
            <p class="mt-2 text-[#64748b]">StarJD offers professional video production, photography services, model and actor portfolio shoots, podcast studio recording, live streaming, drone videography, video editing, advertisement production, and studio & equipment rental services in India.</p>
          </div>
          <div class="rounded-xl border border-[#e5e7eb] bg-[#fafaf9] p-5">
            <h3 class="font-semibold text-[#1a1a1a]">2. Is StarJD suitable for corporate video production?</h3>
            <p class="mt-2 text-[#64748b]">Yes. We specialize in corporate video production including brand films, company profile videos, product launch videos, and promotional advertisements designed to strengthen brand presence and marketing impact.</p>
          </div>
          <div class="rounded-xl border border-[#e5e7eb] bg-[#fafaf9] p-5">
            <h3 class="font-semibold text-[#1a1a1a]">3. Do you provide professional model portfolio shoots?</h3>
            <p class="mt-2 text-[#64748b]">Yes. StarJD provides professional portfolio shoots for models, actors, influencers, and personal brands. Our sessions include styling guidance, creative direction, and high-resolution edited images suitable for casting and agency submissions.</p>
          </div>
          <div class="rounded-xl border border-[#e5e7eb] bg-[#fafaf9] p-5">
            <h3 class="font-semibold text-[#1a1a1a]">4. Can I rent the studio without booking a full production service?</h3>
            <p class="mt-2 text-[#64748b]">Yes. We offer flexible studio rental and equipment rental options for photographers, videographers, podcasters, and creative agencies who need professional infrastructure.</p>
          </div>
          <div class="rounded-xl border border-[#e5e7eb] bg-[#fafaf9] p-5">
            <h3 class="font-semibold text-[#1a1a1a]">5. Do you offer podcast recording services?</h3>
            <p class="mt-2 text-[#64748b]">Yes. Our podcast studio includes professional audio systems, multi-camera setups, and editing support to help creators produce high-quality podcast episodes ready for YouTube, Spotify, and other platforms.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Let's Create Something Impactful -->
    <section class="border-b border-[#e5e7eb] bg-[#fafaf9] px-4 py-16 md:py-24">
      <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-2xl font-bold text-[#1a1a1a] md:text-3xl">Let's Create Something Impactful</h2>
        <p class="mt-4 text-[#64748b]">
          Strong visuals build strong brands. If you are looking for high-quality video production, professional photography, model portfolio shoots, drone videography, live streaming services, or studio rentals in India, StarJD is ready to partner with you.
        </p>
        <p class="mt-2 text-[#64748b]">Contact us today to transform your ideas into powerful visual experiences.</p>
        <router-link
          to="/contact"
          class="mt-8 inline-flex items-center gap-2 rounded-lg bg-[#fc4402] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#e53d02]"
        >
          Contact Us →
        </router-link>
        <p class="mt-8 text-sm text-[#64748b]">StarJD is powered by Suganta International.</p>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>

<style scoped>
/* Whole hero section: fade up (entrance) then gentle fade up-and-down loop */
@keyframes aboutSectionEnter {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@keyframes aboutSectionFloat {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.about-hero-section {
  opacity: 0;
  animation: aboutSectionEnter 0.9s ease-out forwards, aboutSectionFloat 4s ease-in-out 0.9s infinite;
}
</style>
