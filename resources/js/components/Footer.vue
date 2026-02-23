<template>
  <footer class="border-t border-[#e5e7eb] bg-[#0f172a] text-[#94a3b8]">
    <div class="mx-auto max-w-6xl px-4 py-14 md:py-16">
      <!-- Top: logo + tagline -->
      <div class="border-b border-[#334155] pb-10 md:pb-12">
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

      <!-- Category & link sections (influencer.in style: each section = category group with page links) -->
      <div class="grid gap-8 pt-10 sm:grid-cols-2 lg:grid-cols-6">
        <!-- Our Services (category pages: /services, /services/{slug}) -->
        <div>
          <h4 class="text-sm font-bold text-white">Our Services</h4>
          <p class="mt-1 text-xs text-[#64748b]">Browse by service category</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li>
              <router-link to="/services" class="transition hover:text-white">All Services</router-link>
            </li>
            <li v-for="s in footerServices" :key="s.id">
              <router-link :to="'/services/' + s.slug" class="transition hover:text-white">{{ s.name }}</router-link>
            </li>
            <li v-if="!footerServices.length" class="text-[#64748b]">—</li>
          </ul>
        </div>

        <!-- Blog & Articles (category pages: /blog, /blog?category={slug}) -->
        <div>
          <h4 class="text-sm font-bold text-white">Blog & Articles</h4>
          <p class="mt-1 text-xs text-[#64748b]">Browse by category</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li>
              <router-link to="/blog" class="transition hover:text-white">All Articles</router-link>
            </li>
            <li v-for="c in blogCategories" :key="c.slug">
              <router-link :to="'/blog?category=' + encodeURIComponent(c.slug)" class="transition hover:text-white">{{ c.label }}</router-link>
            </li>
            <li v-if="!blogCategories.length" class="text-[#64748b]">—</li>
          </ul>
        </div>

        <!-- Company -->
        <div>
          <h4 class="text-sm font-bold text-white">Company</h4>
          <p class="mt-1 text-xs text-[#64748b]">About & contact</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><router-link to="/about" class="transition hover:text-white">About Us</router-link></li>
            <li><router-link to="/contact" class="transition hover:text-white">Contact Us</router-link></li>
            <li><router-link to="/creators" class="transition hover:text-white">Discover Creators</router-link></li>
            <li><router-link to="/videos" class="transition hover:text-white">Videos</router-link></li>
          </ul>
        </div>

        <!-- Resources -->
        <div>
          <h4 class="text-sm font-bold text-white">Resources</h4>
          <p class="mt-1 text-xs text-[#64748b]">Guides & support</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><a href="/#how-it-works" class="transition hover:text-white">How It Works</a></li>
            <li><a href="/#featured" class="transition hover:text-white">Featured Creators</a></li>
            <li><a href="/#faq" class="transition hover:text-white">FAQ</a></li>
            <li><a href="/#articles" class="transition hover:text-white">Articles & Resources</a></li>
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
            <li><router-link to="/privacy" class="transition hover:text-white">Privacy Policy</router-link></li>
            <li><router-link to="/terms" class="transition hover:text-white">Terms of Service</router-link></li>
            <li><router-link to="/contact" class="transition hover:text-white">Contact</router-link></li>
          </ul>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-[#334155] pt-8 md:flex-row">
        <p class="text-sm text-[#64748b]">© {{ new Date().getFullYear() }} StarJD, powered by Suganta International. All rights reserved.</p>
        <div class="flex flex-wrap justify-center gap-6 text-sm">
          <router-link to="/about" class="transition hover:text-white">About</router-link>
          <router-link to="/contact" class="transition hover:text-white">Contact</router-link>
          <router-link to="/privacy" class="transition hover:text-white">Privacy</router-link>
          <router-link to="/terms" class="transition hover:text-white">Terms</router-link>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const footerServices = ref([]);
const blogCategories = ref([]);

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
