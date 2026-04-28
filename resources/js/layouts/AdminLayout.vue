<template>
  <div class="flex min-h-screen bg-[#f8fafc]">
    <aside class="w-64 shrink-0 border-r border-[#e2e8f0] bg-white shadow-sm">
      <div class="sticky top-0 flex h-screen flex-col">
        <div class="border-b border-[#e2e8f0] px-4 py-5">
          <router-link to="/admin" class="flex items-center gap-2">
            <img src="/logo.png" alt="StarJD" class="h-16 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
            <span class="hidden text-lg font-bold text-[#1a1a1a]">StarJD</span>
            <span class="text-xs font-semibold uppercase tracking-wider text-[#64748b]">Admin</span>
          </router-link>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4">
          <router-link
            to="/admin"
            class="mb-1 flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]"
            active-class="!bg-[#e63946]/10 !text-[#e63946]"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" /></svg>
            Dashboard
          </router-link>

          <router-link
            to="/admin/users"
            class="mb-1 flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]"
            active-class="!bg-[#e63946]/10 !text-[#e63946]"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            Users Analytics
          </router-link>

          <!-- Content -->
          <div class="mt-5">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.content = !navOpen.content">
              <span>Content &amp; CMS</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.content }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.content" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/legal-pages" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 .552-.448 1-1 1s-1-.448-1-1 .448-1 1-1 1 .448 1 1zm0 0V8m8 4c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8z" /></svg>
                Legal Pages
              </router-link>
              <router-link to="/admin/pages" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Pages
              </router-link>
              <router-link to="/admin/seo-pages" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                Location CMS
              </router-link>
              <router-link to="/admin/posts" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6m4-4v12m-3-2h6" /></svg>
                Blog Posts
              </router-link>
              <router-link to="/admin/videos" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                Video Guides
              </router-link>
              <router-link to="/admin/success-stories" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                Success Stories
              </router-link>
            </div>
          </div>

          <!-- Locations -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.locations = !navOpen.locations">
              <span>Locations</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.locations }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.locations" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/states" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                States
              </router-link>
              <router-link to="/admin/cities" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                Cities
              </router-link>
            </div>
          </div>

          <!-- Site building -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.site = !navOpen.site">
              <span>Site building</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.site }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.site" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/categories" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                Categories
              </router-link>
              <router-link to="/admin/sub-categories" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                Sub Categories
              </router-link>
              <router-link to="/admin/hero" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" /></svg>
                Hero Section
              </router-link>
              <router-link to="/admin/banners" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM4 21a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2z" /></svg>
                Banners
              </router-link>
              <router-link to="/admin/testimonials" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Testimonials</router-link>
              <router-link to="/admin/faqs" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">FAQs</router-link>
              <router-link to="/admin/steps" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Steps</router-link>
              <router-link to="/admin/partners" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Partners</router-link>
              <router-link to="/admin/services" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">Services</router-link>
            </div>
          </div>

          <!-- Studio marketplace -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.studios = !navOpen.studios">
              <span>Studio marketplace</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.studios }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.studios" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/studios" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                Studios
              </router-link>
            </div>
          </div>

          <!-- Commerce & Finance -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.commerce = !navOpen.commerce">
              <span>Finance & Commerce</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.commerce }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.commerce" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/withdrawals" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Withdrawals
              </router-link>
              <router-link to="/admin/coupons" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                Coupon codes
              </router-link>
            </div>
          </div>

          <!-- Inbox -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.inbox = !navOpen.inbox">
              <span>Inbox</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.inbox }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.inbox" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/contacts" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                Contacts
              </router-link>
              <router-link to="/admin/support" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                Help & Support
              </router-link>
              <router-link to="/admin/messages" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                User Messages
              </router-link>
            </div>
          </div>

          <!-- System -->
          <div class="mt-4">
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-[#94a3b8]" @click="navOpen.system = !navOpen.system">
              <span>System</span>
              <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': navOpen.system }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-show="navOpen.system" class="mt-0.5 space-y-0.5">
              <router-link to="/admin/ai-usage" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                AI Usage Monitoring
              </router-link>
              <router-link to="/admin/marketing" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                Marketing
              </router-link>
              <router-link to="/admin/sitemap" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-[#64748b] transition hover:bg-[#f1f5f9] hover:text-[#1a1a1a]" active-class="!bg-[#e63946]/10 !text-[#e63946]">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Sitemap
              </router-link>
            </div>
          </div>
        </nav>
        <div class="border-t border-[#e2e8f0] px-3 py-4">
          <p class="mb-2 px-3 text-xs text-[#94a3b8]">StarJD powered by Suganta International</p>
          <a href="/" class="mb-1 flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1a1a1a]">View Site</a>
          <button type="button" class="w-full rounded-lg px-3 py-2 text-left text-sm text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#e63946]" @click="logout">Logout</button>
        </div>
      </div>
    </aside>
    <main class="min-w-0 flex-1 overflow-auto p-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { reactive, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const navOpen = reactive({ content: true, locations: true, site: true, studios: true, inbox: true, system: true });

onMounted(async () => {
  try {
    await axios.get('/api/admin/user');
  } catch (e) {
    if (e.response?.status === 401 || e.response?.status === 403) {
      router.replace('/login?redirect=' + encodeURIComponent(router.currentRoute.value.fullPath));
    }
  }
});

watch(() => route.path, (path) => {
  if (path.startsWith('/admin/legal-pages') || path.startsWith('/admin/pages') || path.startsWith('/admin/seo-pages') || path.startsWith('/admin/posts') || path.startsWith('/admin/videos') || path.startsWith('/admin/success-stories')) navOpen.content = true;
  if (path.startsWith('/admin/states') || path.startsWith('/admin/cities')) navOpen.locations = true;
  if (path.startsWith('/admin/categories') || path.startsWith('/admin/hero') || path.startsWith('/admin/banners') || path.startsWith('/admin/testimonials') || path.startsWith('/admin/faqs') || path.startsWith('/admin/steps') || path.startsWith('/admin/partners') || path.startsWith('/admin/services')) navOpen.site = true;
  if (path.startsWith('/admin/studios')) navOpen.studios = true;
  if (path.startsWith('/admin/coupons')) navOpen.commerce = true;
  if (path.startsWith('/admin/contacts') || path.startsWith('/admin/support') || path.startsWith('/admin/messages')) navOpen.inbox = true;
  if (path.startsWith('/admin/ai-usage') || path.startsWith('/admin/marketing') || path.startsWith('/admin/sitemap')) navOpen.system = true;

}, { immediate: true });

function logout() {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  axios.post('/api/logout', {}, { withCredentials: true, headers: token ? { 'X-CSRF-TOKEN': token } : {} })
    .then(() => { window.location.href = '/login'; })
    .catch(() => { window.location.href = '/login'; });
}
</script>
