<template>
  <section class="relative overflow-hidden rounded-3xl bg-[#0f172a] text-white">
    <!-- Decorative Glows -->
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-[#fc4402]/20 blur-[80px]" />
    <div class="absolute -right-20 -bottom-20 h-64 w-64 rounded-full bg-[#38bdf8]/10 blur-[80px]" />

    <div class="relative z-10 mx-auto max-w-6xl px-6 py-12 md:px-12 md:py-16">
      <div class="grid items-center gap-10 md:grid-cols-2">
        <!-- Text Content -->
        <div class="text-center md:text-left">
          <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-[#38bdf8]">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
            </svg>
            Mobile Experience
          </div>
          <h2 class="text-3xl font-bold tracking-tight md:text-5xl">
            Experience StarJD <span class="text-[#fc4402]">on the Go</span>
          </h2>
          <p class="mt-6 text-lg leading-relaxed text-[#94a3b8]">
            Manage collaborations, track campaign performance, and stay connected with creators and brands anywhere, anytime. Our mobile app is built for speed and efficiency.
          </p>
          
          <div class="mt-10 flex flex-wrap justify-center gap-4 md:justify-start">
            <a 
              href="https://play.google.com/store/apps/details?id=com.starjd.app" 
              target="_blank" 
              rel="noopener noreferrer"
              class="group flex items-center gap-3 rounded-2xl bg-black px-6 py-3 ring-1 ring-white/20 transition hover:bg-[#1a1a1a] hover:ring-[#fc4402]"
            >
              <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" class="h-10 w-auto" />
              <div class="text-left">
                <p class="text-[10px] uppercase text-[#94a3b8]">Get it on</p>
                <p class="text-lg font-bold leading-none">Google Play</p>
              </div>
            </a>
          </div>

          <div class="mt-8 flex items-center justify-center gap-6 md:justify-start">
            <div class="flex flex-col items-center md:items-start">
              <span class="text-2xl font-bold text-white">4.8/5</span>
              <span class="text-xs text-[#64748b]">App Rating</span>
            </div>
            <div class="h-8 w-px bg-white/10" />
            <div class="flex flex-col items-center md:items-start">
              <span class="text-2xl font-bold text-white">50k+</span>
              <span class="text-xs text-[#64748b]">Downloads</span>
            </div>
          </div>
        </div>

        <!-- Phone Mockup -->
        <div class="relative flex justify-center lg:justify-end">
          <div class="relative w-full max-w-[320px] transition-transform duration-500 hover:scale-105">
            <!-- Professional Mockup Frame (CSS) -->
            <div class="relative z-10 overflow-hidden rounded-[3rem] border-[8px] border-[#1e293b] bg-[#1e293b] shadow-2xl">
              <img 
                src="/images/app-mockup.png" 
                alt="App Preview" 
                class="block w-full"
              />
            </div>
            <!-- Glow behind phone -->
            <div class="absolute inset-0 z-0 bg-gradient-to-tr from-[#fc4402]/40 to-transparent blur-3xl" />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// No props needed for this specific implementation
</script>

<style scoped>
/* Add any custom micro-animations here */
</style>
