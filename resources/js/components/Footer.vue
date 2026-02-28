<template>
  <footer class="relative overflow-visible text-[#94a3b8]">
    <!-- Background image blended with dark overlay -->
    <div
      class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat rounded-t-[100px]"
      :style="{
        backgroundImage: `linear-gradient(180deg, rgba(15,23,42,0.92) 0%, rgba(15,23,42,0.88) 100%), url('/images/footer-img.jpg')`,
      }"
      aria-hidden="true"
    />
    <div class="relative z-10 mx-auto max-w-6xl px-4 pt-0 pb-14 md:pb-16">
      <!-- Start Your Influencer Marketing Journey: footer ke center me (overlap white + dark blue) -->
      <div class="flex w-full justify-center px-0">
        <div
          class="footer-cta-overlap relative z-10 -mt-[36px] flex min-h-[40px] w-full max-w-4xl flex-col items-center justify-center gap-3 rounded-full bg-[#fc4402] px-5 py-2 md:-mt-[40px] md:min-h-[48px] md:flex-row md:justify-between md:gap-4 md:px-8 md:py-2.5"
        >
          <h3 class="text-center font-serif text-xl italic leading-tight text-white md:text-left md:text-xl">
            Start Your Influencer Marketing Journey
          </h3>
          <router-link
            to="/contact"
            class="shrink-0 rounded-full border-2 border-[#fc4402] bg-white px-5 py-1.5 text-sm font-semibold text-[#fc4402] transition hover:bg-[#fef2f2] md:px-6 md:py-2 md:text-base"
          >
            Talk To Our Experts &gt;&gt;
          </router-link>
        </div>
      </div>

      <!-- Top: logo + tagline (left) | Newsletter (right, same as 1st image) -->
      <div class="border-b border-[#334155] pb-10 pt-10 md:pb-12 md:pt-12">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-start">
          <!-- Left: logo + tagline -->
          <div>
            <router-link to="/" class="inline-block">
              <img
                src="/logo.png"
                alt="StarJD"
                class="h-11 w-auto object-contain"
                onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');"
              />
              <span class="hidden text-xl font-bold text-white">StarJD</span>
            </router-link>
            <p class="mt-3 max-w-sm text-sm leading-relaxed text-[#94a3b8]">
              Connect with creators. Build your brand. India's premium creative media studio & production company.
            </p>
            <p class="mt-2 text-xs text-[#64748b]">StarJD powered by Suganta International</p>
          </div>

          <!-- Right: Newsletter (1st image style: orange heading, tagline, capsule form, social icons) -->
          <div class="lg:max-w-md">
            <h4 class="text-xl font-bold text-[#FF6633]">Newsletter</h4>
            <p class="mt-2 text-sm leading-relaxed text-white">
              Subscribe to our newsletter to receive the latest updates and offers on creators, content and services.
            </p>
            <form
              class="mt-4 flex overflow-hidden rounded-full bg-white shadow-sm"
              @submit.prevent="onNewsletterSubmit"
            >
              <input
                v-model="newsletterEmail"
                type="email"
                placeholder="Your email address"
                class="min-w-0 flex-1 border-0 bg-transparent px-4 py-3 text-sm text-[#1a1a1a] placeholder-[#9CA3AF] focus:outline-none focus:ring-0"
                required
              />
              <button
                type="submit"
                class="shrink-0 bg-[#FF6633] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#e55a2b]"
              >
                Subscribe
              </button>
            </form>
            <!-- Social icons: white circles, dark icons (X, Facebook, Pinterest, Instagram, LinkedIn, YouTube, Email, TikTok) -->
            <div class="mt-5 flex flex-wrap gap-1.5 justify-end">
              <a href="https://x.com/starjd" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-[#374151] transition hover:bg-white hover:text-[#fc4402]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
              <a href="https://facebook.com/starjd" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-[#374151] transition hover:bg-white hover:text-[#fc4402]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
              <a href="https://pinterest.com/starjd" target="_blank" rel="noopener noreferrer" aria-label="Pinterest" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-[#374151] transition hover:bg-white hover:text-[#fc4402]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.013 11.985.013z"/></svg></a>
              <a href="https://instagram.com/starjd" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-[#374151] transition hover:bg-white hover:text-[#fc4402]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
              <a href="https://linkedin.com/company/starjd" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-[#374151] transition hover:bg-white hover:text-[#fc4402]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
              <a href="https://youtube.com/@starjd" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-[#374151] transition hover:bg-white hover:text-[#fc4402]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
              <a href="mailto:hello@starjd.com" aria-label="Email" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-[#374151] transition hover:bg-white hover:text-[#fc4402]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Category & link sections (influencer.in style: each section = category group with page links) -->
      <div class="grid gap-8 pt-10 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Blog & Articles (category pages: /blog, /blog?category={slug}) -->
        <div>
          <h4 class="text-sm font-bold text-white">Blog & Articles</h4>
          <p class="mt-1 text-xs text-[#64748b]">Browse by category</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li>
              <router-link to="/blog" class="transition hover:text-[#fc4402]">All Articles</router-link>
            </li>
            <li v-for="c in blogCategories" :key="c.slug">
              <router-link :to="'/blog?category=' + encodeURIComponent(c.slug)" class="transition hover:text-[#fc4402]">{{ c.label }}</router-link>
            </li>
            <li v-if="!blogCategories.length" class="text-[#64748b]">—</li>
          </ul>
        </div>

        <!-- Company -->
        <div>
          <h4 class="text-sm font-bold text-white">Company</h4>
          <p class="mt-1 text-xs text-[#64748b]">About & contact</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><router-link to="/about" class="transition hover:text-[#fc4402]">About Us</router-link></li>
            <li><router-link to="/contact" class="transition hover:text-[#fc4402]">Contact Us</router-link></li>
            <li><router-link to="/creators" class="transition hover:text-[#fc4402]">Discover Creators</router-link></li>
            <li><router-link to="/videos" class="transition hover:text-[#fc4402]">Videos</router-link></li>
          </ul>
        </div>

        <!-- Resources -->
        <div>
          <h4 class="text-sm font-bold text-white">Resources</h4>
          <p class="mt-1 text-xs text-[#64748b]">Guides & support</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><a href="/#how-it-works" class="transition hover:text-[#fc4402]">How It Works</a></li>
            <li><a href="/#featured" class="transition hover:text-[#fc4402]">Featured Creators</a></li>
            <li><a href="/#faq" class="transition hover:text-[#fc4402]">FAQ</a></li>
            <li><a href="/#articles" class="transition hover:text-[#fc4402]">Articles & Resources</a></li>
          </ul>
        </div>

        <!-- Join -->
        <div>
          <h4 class="text-sm font-bold text-white">Join</h4>
          <p class="mt-1 text-xs text-[#64748b]">Get started</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><router-link to="/creator-landing" class="transition hover:text-[#34d399]">Join as Creator</router-link></li>
            <li><router-link to="/brand-landing" class="transition hover:text-[#f87171]">Join as Brand</router-link></li>
          </ul>
        </div>

        <!-- Useful links / Legal (like influencer.in) -->
        <div>
          <h4 class="text-sm font-bold text-white">Useful Links</h4>
          <p class="mt-1 text-xs text-[#64748b]">Legal & info</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><router-link to="/privacy" class="transition hover:text-[#fc4402]">Privacy Policy</router-link></li>
            <li><router-link to="/terms" class="transition hover:text-[#fc4402]">Terms of Service</router-link></li>
            <li><router-link to="/contact" class="transition hover:text-[#fc4402]">Contact</router-link></li>
          </ul>
        </div>
      </div>

      <!-- Our Services: above bottom bar, hr, heading on top then options below (6 per line responsive) -->
      <hr class="mt-12 border-[#334155]" />
      <div class="flex flex-col gap-4 pt-6">
        <h4 class="text-sm font-bold text-white">Our Services</h4>
        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
          <router-link to="/services" class="transition hover:text-[#fc4402]">All Services</router-link>
          <router-link
            v-for="s in displayServices"
            :key="s.id"
            :to="'/services/' + s.slug"
            class="transition hover:text-[#fc4402]"
          >
            {{ s.name }}
          </router-link>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-[#334155] pt-8 md:flex-row">
        <p class="text-sm text-[#64748b]">© {{ new Date().getFullYear() }} StarJD, powered by Suganta International. All rights reserved.</p>
        <div class="flex flex-wrap justify-center gap-6 text-sm">
          <router-link to="/about" class="transition hover:text-[#fc4402]">About</router-link>
          <router-link to="/contact" class="transition hover:text-[#fc4402]">Contact</router-link>
          <router-link to="/privacy" class="transition hover:text-[#fc4402]">Privacy</router-link>
          <router-link to="/terms" class="transition hover:text-[#fc4402]">Terms</router-link>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

/** Fallback list so "Our Services" always shows all options from design (All Services + these 9). */
const defaultFooterServices = [
  { id: 'video-production', name: 'Video Production', slug: 'video-production' },
  { id: 'podcast-interview', name: 'Podcast & Interview', slug: 'podcast-interview' },
  { id: 'professional-model-portfolio', name: 'Professional Model Portfolio', slug: 'professional-model-portfolio' },
  { id: 'live-streaming', name: 'Live Streaming', slug: 'live-streaming' },
  { id: 'advertisement', name: 'Advertisement', slug: 'advertisement' },
  { id: 'professional-photography', name: 'Professional Photography', slug: 'professional-photography' },
  { id: 'video-editing', name: 'Video Editing', slug: 'video-editing' },
  { id: 'studio-for-rentals', name: 'Studio for Rentals', slug: 'studio-for-rentals' },
  { id: 'kid-portfolio-shoot', name: 'Kid Portfolio Shoot', slug: 'kid-portfolio-shoot' },
];

const footerServices = ref([]);
const blogCategories = ref([]);
const newsletterEmail = ref('');

function onNewsletterSubmit() {
  const email = newsletterEmail.value?.trim();
  if (!email) return;
  // Optional: call API when backend is ready, e.g. POST /api/newsletter/subscribe
  // For now show a simple confirmation
  alert('Thank you for subscribing! We\'ll send updates to ' + email);
  newsletterEmail.value = '';
}

/** Show API services when available; otherwise show full default list so all options from image are visible. */
const displayServices = computed(() => {
  const fromApi = footerServices.value?.length ? footerServices.value : [];
  return fromApi.length ? fromApi : defaultFooterServices;
});

onMounted(async () => {
  try {
    const [servicesRes, categoriesRes] = await Promise.all([
      axios.get('/api/services'),
      axios.get('/api/posts/categories'),
    ]);
    footerServices.value = Array.isArray(servicesRes.data) ? servicesRes.data : servicesRes.data?.data ?? [];
    blogCategories.value = categoriesRes.data?.categories ?? [];
  } catch {
    footerServices.value = [];
    blogCategories.value = [];
  }
});
</script>
