<template>
  <div class="min-h-screen bg-gradient-to-br from-[#fafaf9] via-white to-[#fef2f2] relative">
    <!-- Fixed Creative Background (All over the page) -->
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
      <!-- Animated Threads/Lines (Wider and more frequent) -->
      <svg class="absolute inset-0 h-full w-full opacity-10" xmlns="http://www.w3.org/2000/svg">
        <path v-for="i in 8" :key="'thread-' + i" 
          :d="`M ${-300 + i * 150} ${100 + i * 100} Q ${800 + i * 200} ${i * 300}, ${2000} ${500 + i * 400}`"
          fill="none" 
          stroke="#e63946" 
          stroke-width="1"
          class="thread-path"
          :style="{ animationDelay: (i * 2) + 's', animationDuration: (10 + i * 3) + 's' }"
        />
      </svg>

      <!-- Glitter & Broken Stars (Full page coverage) -->
      <div v-for="i in 60" :key="'misc-' + i" 
        :class="i % 4 === 0 ? 'broken-star' : 'star-glitter'"
        class="absolute"
        :style="{
          top: Math.random() * 100 + '%',
          left: Math.random() * 100 + '%',
          width: (i % 4 === 0 ? 14 : 5) + 'px',
          height: (i % 4 === 0 ? 14 : 5) + 'px',
          animationDelay: (Math.random() * 10) + 's',
          animationDuration: (Math.random() * 4 + 2) + 's',
          opacity: Math.random() * 0.6 + 0.3
        }"
      >
        <svg v-if="i % 4 === 0" viewBox="0 0 24 24" fill="#e63946">
          <path d="M12 2l2.4 7.2h7.6l-6.1 4.4 2.3 7.2-6.2-4.5-6.2 4.5 2.3-7.2-6.1-4.4h7.6z" />
          <line x1="0" y1="0" x2="24" y2="24" stroke="white" stroke-width="3" />
        </svg>
        <div v-else class="h-full w-full rounded-full bg-[#e63946] shadow-[0_0_10px_#e63946]"></div>
      </div>

      <!-- Social Counters/Particles (Floating numbers) -->
      <div v-for="i in 10" :key="'counter-' + i" 
        class="absolute font-bold text-[#e63946]/10 text-xl select-none"
        :style="{
          top: Math.random() * 100 + '%',
          left: Math.random() * 100 + '%',
          animation: 'driftUp ' + (20 + i * 5) + 's linear infinite',
          animationDelay: (i * -2) + 's'
        }"
      >
        {{ (Math.random() * 1000).toFixed(0) }}+
      </div>

      <!-- Floating Orbs -->
      <div class="absolute -left-20 top-0 h-[600px] w-[600px] rounded-full bg-[#e63946]/10 blur-[150px] animate-pulse"></div>
      <div class="absolute right-0 bottom-0 h-[800px] w-[800px] rounded-full bg-emerald-400/5 blur-[200px] animate-pulse" style="animation-delay: 3s"></div>
      
      <!-- Light Beams (Stronger and all page) -->
      <div v-for="i in 5" :key="'beam-' + i" 
        class="absolute -top-20 h-[300%] w-48 bg-gradient-to-r from-transparent via-[#e63946]/5 to-transparent blur-3xl"
        :style="{
          left: (10 + i * 20) + '%',
          transform: 'rotate(-20deg)',
          animation: 'beamShift ' + (15 + i * 5) + 's ease-in-out infinite alternate'
        }"
      ></div>
    </div>

    <template v-if="loading">
      <div class="relative z-10 flex min-h-[60vh] flex-col items-center justify-center space-y-6">
        <div class="relative">
          <div class="h-24 w-24 animate-spin rounded-full border-[8px] border-[#e63946]/5 border-t-[#e63946] shadow-2xl"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-2xl animate-pulse">✨</span>
          </div>
        </div>
        <div class="text-center">
           <p class="text-xl font-black text-[#1a1a1a] tracking-tight">Crafting Creative Perfection</p>
           <p class="text-[10px] text-[#e63946] font-bold tracking-[0.3em] uppercase mt-2">StarJD Professional Hub</p>
        </div>
      </div>
    </template>
    <template v-else-if="cmsPage">
      <!-- Hero Section (New Split Layout) -->
      <section class="relative z-10 pt-16 pb-20 md:pt-24 md:pb-32">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
          <div class="grid items-center gap-16 lg:grid-cols-2">
            <!-- Hero Text content -->
            <div class="text-center lg:text-left">
              <!-- Breadcrumbs for Tree Structure -->
              <nav class="mb-6 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-[#64748b]">
                <router-link to="/" class="hover:text-[#e63946] transition">Home</router-link>
                <span>/</span>
                <router-link to="/creators" class="hover:text-[#e63946] transition">Creators</router-link>
                <template v-if="stateName">
                  <span>/</span>
                  <router-link :to="'/' + cmsPage.slug + '-in-' + (cmsPage.state?.slug || stateName.toLowerCase().replace(/ /g, '-'))" class="hover:text-[#e63946] transition">{{ stateName }}</router-link>
                </template>
                <template v-if="cityName">
                  <span>/</span>
                  <span class="text-[#e63946]">{{ cityName }}</span>
                </template>
              </nav>

              <div class="mb-8 inline-flex items-center gap-3 rounded-2xl border border-[#e63946]/10 bg-white/40 p-2 pr-6 text-xs font-bold text-[#1a1a1a] backdrop-blur-xl shadow-lg ring-1 ring-[#e63946]/5 animate-fade-in">
                <span class="flex items-center justify-center h-8 w-8 rounded-xl bg-[#e63946] text-white">★</span>
                Verified Hub {{ locationName ? 'in ' + locationName : '' }}
              </div>
              <h1 class="animate-fade-in-up text-5xl font-black leading-[1.05] tracking-tight text-[#1a1a1a] sm:text-6xl md:text-7xl lg:text-7xl">
                {{ cmsPage.title }}
              </h1>
              <p v-if="locationName" class="mt-10 animate-fade-in-up animation-delay-200 text-lg leading-relaxed text-[#64748b] md:text-xl lg:max-w-xl">
                 Experience personal branding at its peak. We provide <span class="text-[#1a1a1a] font-bold underline decoration-[#e63946]/30 decoration-4">high-performance solutions</span> for top-tier creators and professional studios in <span class="bg-gradient-to-r from-[#e63946] to-[#c1121f] bg-clip-text font-black text-transparent">{{ locationName }}</span>.
              </p>
              <div class="mt-12 flex flex-wrap justify-center gap-6 animate-fade-in-up animation-delay-300 lg:justify-start">
                <router-link to="/creators" class="group relative flex items-center justify-center overflow-hidden rounded-[1.25rem] bg-[#e63946] px-10 py-5 text-lg font-bold text-white shadow-[0_20px_40px_rgba(230,57,70,0.4)] transition-all duration-300 hover:scale-105 hover:bg-[#c1121f]">
                  <span class="relative z-10">Discover Creators</span>
                  <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent transition-transform duration-700 group-hover:translate-x-full"></div>
                </router-link>
                <router-link to="/studios" class="group rounded-[1.25rem] border-2 border-[#e5e7eb] bg-white px-10 py-5 text-lg font-bold text-[#1a1a1a] shadow-xl transition-all duration-300 hover:border-[#e63946] hover:shadow-2xl">
                  Rent a Studio
                </router-link>
              </div>
            </div>

            <!-- Hero Visual Content (Creative Image) -->
            <div class="relative hidden lg:block">
               <div class="relative z-10 animate-float translate-x-4">
                 <img src="/hero_creative.png" alt="Creative Elements" class="h-auto w-full drop-shadow-[0_30px_60px_rgba(230,57,70,0.3)]" />
               </div>
               <!-- Interactive Badges -->
               <div class="absolute -right-8 top-1/4 z-20 animate-bounce-slow rounded-3xl bg-white/90 p-6 shadow-2xl backdrop-blur-md border border-white/50">
                  <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600">
                      <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    </div>
                    <div>
                      <p class="text-sm font-black text-[#1a1a1a] tracking-tight">Premium Only</p>
                      <p class="text-xs text-[#64748b]">Hand-vetted professionals</p>
                    </div>
                  </div>
               </div>
               <div class="absolute -left-16 bottom-10 z-20 animate-bounce-slower rounded-3xl bg-[#1a1a1a] p-6 shadow-[0_25px_50px_rgba(0,0,0,0.3)]">
                  <div class="flex items-center gap-4">
                    <div class="flex -space-x-4">
                      <img v-for="i in 4" :key="i" :src="`https://i.pravatar.cc/100?img=${i+20}`" class="h-10 w-10 rounded-full border-2 border-[#1a1a1a] shadow-lg" />
                    </div>
                    <p class="text-xs font-black text-white uppercase tracking-[0.2em]">Star Community</p>
                  </div>
               </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Content Section with Sidebar (Glassmorphism) -->
      <section v-if="cmsPage.content" class="relative z-10 py-12 md:py-20 overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
          <div class="grid gap-12 lg:grid-cols-[1fr_360px]">
             <!-- Main Content (Left) -->
             <div class="min-w-0">
               <div class="group relative rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-[0_20px_50px_rgba(0,0,0,0.05)] backdrop-blur-xl md:p-14">
                 <div class="absolute -left-2 top-10 h-12 w-1.5 rounded-r-full bg-[#e63946] transition-all group-hover:h-24"></div>
                 <RichTextContent :content="cmsPage.content" class="prose-lg max-w-none" />
               </div>
             </div>
             
             <!-- Sidebar (Right) -->
             <aside class="space-y-8 lg:sticky lg:top-24 lg:self-start">
               <!-- Sidebar Search Filter (Vibrant) -->
               <div class="rounded-[2rem] border border-[#e63946]/10 bg-gradient-to-br from-white to-[#fdf2f2] p-8 shadow-2xl transition-all hover:shadow-[#e63946]/5">
                 <h3 class="text-lg font-black tracking-tight text-[#1a1a1a]">Search {{ locationName ? 'in ' + locationName : '' }}</h3>
                 <p class="text-xs text-[#64748b] mt-1">Find the perfect creative match.</p>
                 <form class="mt-4 space-y-4" @submit.prevent="handleSidebarSearch">
                   <div>
                     <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-[#94a3b8]">Keywords</label>
                     <input v-model="sidebarSearch" type="text" placeholder="Creators, studios..." class="w-full rounded-xl border border-[#e2e8f0] bg-white px-4 py-2.5 text-sm focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" />
                   </div>
                   <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-[#94a3b8]">Category</label>
                    <div class="relative">
                      <input
                        v-model="categoryQuery"
                        type="text"
                        placeholder="All Categories"
                        class="w-full rounded-xl border border-[#e2e8f0] bg-white px-4 py-2.5 text-sm focus:border-[#e63946] focus:outline-none"
                        @focus="categoryDropdownOpen = true"
                        @blur="setTimeout(() => { categoryDropdownOpen = false }, 160)"
                      />
                      <div v-if="categoryDropdownOpen" class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-[#e2e8f0] bg-white shadow-lg">
                        <button
                          type="button"
                          class="block w-full px-4 py-2 text-left text-sm text-[#1a1a1a] hover:bg-[#f8fafc]"
                          @mousedown.prevent
                          @click="selectSidebarCategory('')"
                        >
                          All Categories
                        </button>
                        <button
                          v-for="c in filteredCreatorCategories"
                          :key="c"
                          type="button"
                          class="block w-full px-4 py-2 text-left text-sm text-[#1a1a1a] hover:bg-[#f8fafc]"
                          @mousedown.prevent
                          @click="selectSidebarCategory(c)"
                        >
                          {{ c }}
                        </button>
                        <div v-if="!filteredCreatorCategories.length" class="px-4 py-2 text-sm text-[#64748b]">No category found</div>
                      </div>
                    </div>
                   </div>
                   <button type="submit" class="w-full rounded-xl bg-[#e63946] py-3 text-sm font-bold text-white shadow-md transition hover:bg-[#c1121f]">
                     Search Now
                   </button>
                 </form>
               </div>
               
               <!-- Recent Blogs / Trending -->
               <div v-if="blogs.length" class="rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm">
                 <h3 class="text-sm font-bold uppercase tracking-wider text-[#1a1a1a]">Latest News</h3>
                 <div class="mt-5 space-y-5">
                   <router-link v-for="b in blogs" :key="b.id" :to="'/blog/' + b.slug" class="group flex gap-4">
                      <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-[#f1f5f9]">
                        <img :src="b.image || b.featured_image_url || 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=200&fit=crop'" class="h-full w-full object-cover transition duration-300 group-hover:scale-110" />
                      </div>
                      <div class="min-w-0 flex-1">
                        <h4 class="line-clamp-2 text-sm font-bold transition group-hover:text-[#e63946] leading-tight">{{ b.title }}</h4>
                        <div class="mt-1 flex items-center justify-between">
                          <span class="text-[9px] font-bold uppercase tracking-widest text-[#e63946]">{{ typeof b.category === 'string' ? b.category : (b.category?.name || 'Blog') }}</span>
                          <span class="text-[9px] text-[#94a3b8]">{{ b.reading_time || '5 min' }}</span>
                        </div>
                      </div>
                   </router-link>
                 </div>
                 <router-link to="/blog" class="mt-6 block text-center text-xs font-bold text-[#64748b] hover:text-[#e63946] transition">View all blog posts →</router-link>
               </div>

              <!-- Hierarchical Tree Navigation -->
              <div v-if="locationName" class="rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm overflow-hidden relative group">
                <div class="absolute -right-6 -top-6 h-12 w-12 rounded-full bg-[#e63946]/5 transition group-hover:scale-150"></div>
                <h3 class="text-xs font-black uppercase tracking-widest text-[#1a1a1a] border-b border-[#f1f5f9] pb-3 mb-4">
                  Location Hierarchy
                </h3>
                
                <div class="space-y-4">
                  <!-- Root level -->
                  <div class="flex items-center gap-3">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                    <router-link to="/creators" class="text-xs font-bold text-[#475569] hover:text-[#e63946]">India Hub</router-link>
                  </div>
                  
                  <!-- State level -->
                  <div v-if="stateName" class="ml-4 flex items-center gap-3 border-l-2 border-[#f1f5f9] pl-3">
                    <div class="h-1.5 w-1.5 rounded-full bg-[#fbbf24]"></div>
                    <router-link 
                      :to="'/' + cmsPage.slug + '-in-' + (cmsPage.state?.slug || stateName.toLowerCase().replace(/ /g, '-'))" 
                      class="text-xs font-bold text-[#475569] hover:text-[#e63946]"
                      :class="{ 'text-[#e63946]': !cityName }"
                    >
                      {{ stateName }}
                    </router-link>
                  </div>
                  
                  <!-- City level (Current or sibling) -->
                  <div v-if="cityName" class="ml-8 flex items-center gap-3 border-l-2 border-[#f1f5f9] pl-3">
                    <div class="h-1.5 w-1.5 rounded-full bg-[#e63946]"></div>
                    <span class="text-xs font-black text-[#e63946]">{{ cityName }}</span>
                  </div>

                  <!-- Direct Links to Creators/Studios Search -->
                  <div class="mt-6 pt-4 border-t border-[#f1f5f9] flex flex-wrap gap-2">
                    <router-link
                      :to="{ name: 'creators', query: { location: locationName } }"
                      class="rounded-lg bg-[#fff1f1] px-3 py-1.5 text-[10px] font-black text-[#e63946] hover:bg-[#e63946] hover:text-white transition uppercase tracking-tighter"
                    >
                      Visit {{ cityName || locationName }} Hall
                    </router-link>
                  </div>
                </div>

                   <!-- Explore more in same State -->
                <div v-if="stateName && siblingPages.length" class="mt-8 pr-2">
                   <p class="text-[9px] font-black text-[#94a3b8] uppercase tracking-[0.2em] mb-4 ml-1">Explore Neighboring Cities</p>
                   <div class="grid grid-cols-2 gap-3">
                      <router-link 
                        v-for="sp in siblingPages" 
                        :key="sp.id" 
                        :to="'/' + (sp.full_slug || sp.slug)"
                        class="px-4 py-3.5 rounded-xl bg-[#fafaf9] border border-[#f1f5f9] text-[10px] font-bold text-[#475569] hover:border-[#e63946]/40 hover:bg-white hover:text-[#e63946] transition-all line-clamp-2 min-h-[54px] flex items-center leading-tight shadow-sm hover:shadow-md"
                      >
                        {{ sp.city?.name || sp.title.split(' in ').pop() }}
                      </router-link>
                   </div>
                </div>
              </div>
               
               <!-- Quick Promotion -->
               <div class="relative overflow-hidden rounded-3xl bg-[#1a1a1a] p-8 text-white shadow-xl">
                 <div class="absolute -right-10 -bottom-10 h-32 w-32 rounded-full bg-[#e63946]/20 blur-2xl"></div>
                 <div class="relative z-10">
                   <h3 class="text-xl font-bold">Grow Your Influence</h3>
                   <p class="mt-3 text-xs text-gray-400 leading-relaxed">Join StarJD today and get access to exclusive brand campaigns and professional creative spaces.</p>
                   <router-link to="/register" class="mt-6 inline-block rounded-xl bg-[#e63946] px-6 py-2.5 text-xs font-bold text-white transition hover:bg-white hover:text-[#e63946]">Join for Free</router-link>
                 </div>
               </div>
             </aside>
          </div>
        </div>
      </section>

      <!-- Stats / Why Us Section -->
      <section class="bg-white py-12">
        <div class="mx-auto max-w-6xl px-4">
          <div class="grid grid-cols-2 gap-6 md:grid-cols-4 text-center">
            <div v-for="(stat, i) in stats" :key="i" class="rounded-2xl bg-[#fafaf9] p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
              <p class="text-3xl font-bold text-[#e63946]">{{ stat.value }}</p>
              <p class="mt-1 text-xs font-semibold text-[#64748b] uppercase tracking-wider">{{ stat.label }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Creators Section -->
      <section v-if="creators.length" class="py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4">
          <div class="mb-10 flex flex-col items-center text-center md:flex-row md:items-end md:justify-between md:text-left">
            <div>
              <h2 class="text-3xl font-bold text-[#1a1a1a]">Featured Creators</h2>
              <p class="mt-2 text-[#64748b]">Top-rated creators located in {{ locationName || 'your area' }}.</p>
            </div>
            <router-link to="/creators" class="mt-4 text-sm font-bold text-[#e63946] hover:underline md:mt-0">View all creators →</router-link>
          </div>
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <router-link
              v-for="p in creators"
              :key="p.id"
              :to="'/creator-profile/' + p.slug"
              class="group flex flex-col overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md"
            >
              <div class="relative aspect-[1/1] shrink-0 overflow-hidden bg-[#f1f5f9]">
                <img
                  :src="p.avatar_url || 'https://ui-avatars.com/api?name=' + encodeURIComponent(p.user?.name || '') + '&size=400&background=e63946&color=fff'"
                  :alt="p.user?.name"
                  class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></div>
                <div v-if="p.category" class="absolute bottom-3 left-3 rounded-lg bg-white/90 px-2.5 py-1 text-xs font-bold text-[#1a1a1a] shadow-sm">{{ p.category }}</div>
              </div>
              <div class="p-4">
                <h3 class="font-bold text-[#1a1a1a] group-hover:text-[#e63946]">{{ p.user?.name }}</h3>
                <p v-if="p.tagline" class="mt-1 line-clamp-1 text-xs text-[#64748b]">{{ p.tagline }}</p>
                <div class="mt-4 flex items-center justify-between">
                  <span v-if="p.min_rate != null" class="text-sm font-bold text-[#e63946]">₹{{ p.min_rate }}+</span>
                  <span class="text-xs font-medium text-[#64748b]">View Profile →</span>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </section>

      <!-- Campaigns Section -->
      <section v-if="campaigns.length" class="bg-[#1a1a1a] py-16 md:py-24 text-white">
        <div class="mx-auto max-w-7xl px-4">
          <div class="mb-10 flex flex-col items-center text-center md:flex-row md:items-end md:justify-between md:text-left">
            <div>
              <h2 class="text-3xl font-bold">Recent Campaigns</h2>
              <p class="mt-2 text-gray-400">Join the latest brand collaborations.</p>
            </div>
            <router-link to="/campaign" class="mt-4 text-sm font-bold text-[#e63946] hover:underline md:mt-0">Browse all →</router-link>
          </div>
          <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
             <article
                v-for="c in campaigns"
                :key="c.id"
                class="group relative flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 p-6 min-h-[300px] border border-gray-800 transition hover:border-[#e63946]/50"
              >
                <div class="absolute inset-0 z-0 opacity-20 pointer-events-none transition duration-500 group-hover:opacity-30">
                  <img v-if="c.featured_image" :src="c.featured_image" class="h-full w-full object-cover" />
                </div>
                <div class="absolute inset-x-0 top-0 h-1 w-full bg-[#e63946] transform origin-left scale-x-0 transition duration-300 group-hover:scale-x-100"></div>
                <div class="relative z-10">
                  <span class="inline-block rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-[#e63946] backdrop-blur-md border border-white/5">
                    {{ c.campaign_type || 'Collaboration' }}
                  </span>
                  <h3 class="mt-4 text-xl font-bold text-white">
                    <router-link :to="'/campaigns/' + (c.slug || c.id)" class="hover:text-[#e63946]">{{ c.title }}</router-link>
                  </h3>
                  <p v-if="c.description" class="mt-2 line-clamp-2 text-sm text-gray-400">{{ c.description }}</p>
                  <div class="mt-6 flex items-center justify-between border-t border-white/5 pt-4">
                    <span class="text-xs text-gray-500">by {{ c.brand?.name || 'Top Brand' }}</span>
                    <router-link :to="'/campaigns/' + (c.slug || c.id)" class="text-sm font-bold text-[#e63946] transition group-hover:translate-x-1">Apply Now →</router-link>
                  </div>
                </div>
              </article>
          </div>
        </div>
      </section>

      <!-- Studios Section -->
      <section v-if="studios.length" class="py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4">
          <div class="mb-10 flex flex-col items-center text-center md:flex-row md:items-end md:justify-between md:text-left">
            <div>
              <h2 class="text-3xl font-bold text-[#1a1a1a]">Creative Studios</h2>
              <p class="mt-2 text-[#64748b]">Best studios available in {{ locationName || 'your city' }}.</p>
            </div>
            <router-link to="/studios" class="mt-4 text-sm font-bold text-[#e63946] hover:underline md:mt-0">View all studios →</router-link>
          </div>
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <StudioCard v-for="s in studios" :key="s.id" :studio="s" />
          </div>
        </div>
      </section>

      <!-- Services Section -->
      <section v-if="services.length" class="bg-white py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4">
          <div class="mb-12 text-center">
            <h2 class="text-3xl font-bold text-[#1a1a1a]">Professional Services</h2>
            <p class="mt-3 text-[#64748b]">Everything you need to grow your influence and brand.</p>
          </div>
          <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            <router-link
              v-for="s in services"
              :key="s.id"
              :to="'/services/' + s.slug"
              class="group relative flex flex-col items-center justify-center rounded-2xl border border-[#e2e8f0] p-6 text-center transition hover:border-[#e63946] hover:bg-[#e63946]/5"
            >
              <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#fafaf9] text-2xl transition group-hover:bg-white group-hover:scale-110 group-hover:rotate-12 duration-300">
                {{ getServiceIcon(s.slug) }}
              </div>
              <h3 class="mt-4 text-sm font-bold text-[#1a1a1a] transition group-hover:text-[#e63946]">{{ s.name }}</h3>
            </router-link>
          </div>
        </div>
      </section>

      <!-- Blog Section -->
      <section v-if="blogs.length" class="bg-[#fafaf9] py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-4">
          <div class="mb-10 flex flex-col items-center text-center md:flex-row md:items-end md:justify-between md:text-left">
            <div>
              <h2 class="text-3xl font-bold text-[#1a1a1a]">Latest Articles</h2>
              <p class="mt-2 text-[#64748b]">Tips, trends, and stories from the creative world.</p>
            </div>
            <router-link to="/blog" class="mt-4 text-sm font-bold text-[#e63946] hover:underline md:mt-0">View all posts →</router-link>
          </div>
          <div class="grid gap-8 md:grid-cols-3">
             <router-link
                v-for="p in blogs"
                :key="p.id"
                :to="'/blog/' + p.slug"
                class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm transition hover:shadow-xl"
              >
                <div class="relative aspect-video overflow-hidden">
                  <img :src="p.featured_image_url || 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&fit=crop'" :alt="p.title" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                  <div v-if="p.category" class="absolute top-4 left-4 rounded-lg bg-white/95 px-2.5 py-1 text-[10px] font-bold uppercase text-[#e63946] shadow-sm">{{ p.category.name }}</div>
                </div>
                <div class="flex flex-1 flex-col p-6">
                  <h3 class="line-clamp-2 text-xl font-bold text-[#1a1a1a] group-hover:text-[#e63946]">{{ p.title }}</h3>
                  <p class="mt-3 line-clamp-2 text-sm text-[#64748b] leading-relaxed">{{ p.excerpt || 'Discover the latest perspectives and insights from our team.' }}</p>
                  <div class="mt-auto pt-6 flex items-center justify-between">
                    <span class="text-xs font-bold text-[#e63946] uppercase tracking-widest">Read Article</span>
                    <span class="text-[10px] text-[#94a3b8]">{{ p.reading_time || '5 min read' }}</span>
                  </div>
                </div>
              </router-link>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="py-16 md:py-24">
        <div class="mx-auto max-w-5xl px-4">
          <div class="rounded-3xl bg-[#e63946] bg-gradient-to-br from-[#e63946] to-[#c1121f] p-8 text-center text-white shadow-2xl md:p-16 overflow-hidden relative">
            <div class="absolute -right-20 -bottom-20 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>
            <div class="relative z-10">
              <h2 class="text-3xl font-bold md:text-5xl text-white">Join the StarJD Community</h2>
              <p class="mx-auto mt-6 max-w-2xl text-lg text-white/80">Connect with local brands, find creative spaces, and take your creator journey to the next level.</p>
              <div class="mt-10 flex flex-wrap justify-center gap-4">
                <router-link to="/register" class="rounded-xl bg-white px-8 py-4 text-base font-bold text-[#e63946] shadow-lg transition hover:bg-white hover:scale-105 hover:shadow-xl">Get Started for Free</router-link>
                <router-link to="/contact-us" class="rounded-xl border-2 border-white/20 px-8 py-4 text-base font-bold text-white transition hover:bg-white/10 hover:border-white">Contact Sales</router-link>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>
    <template v-else>
      <div class="flex min-h-[60vh] flex-col items-center justify-center px-4">
        <div class="text-6xl mb-4 group-hover:rotate-12 transition duration-300">🔍</div>
        <h2 class="text-2xl font-bold text-[#1a1a1a]">Page not found</h2>
        <p class="mt-2 text-center text-[#64748b]">The page you're looking for doesn't exist or is not published yet.</p>
        <router-link to="/" class="mt-8 rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-[#c1121f]">Back to home</router-link>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useRouter } from 'vue-router';
import RichTextContent from '../components/RichTextContent.vue';
import StudioCard from '../components/studio/StudioCard.vue';

const route = useRoute();
const router = useRouter();
const cmsPage = ref(null);
const loading = ref(true);

const creators = ref([]);
const campaigns = ref([]);
const studios = ref([]);
const blogs = ref([]);
const services = ref([]);
const creatorCategories = ref([]);
const siblingPages = ref([]);

const sidebarSearch = ref('');
const sidebarCategory = ref('');
const categoryQuery = ref('');
const categoryDropdownOpen = ref(false);

const stats = [
  { value: '2500+', label: 'Local Creators' },
  { value: '150+', label: 'Creative Studios' },
  { value: '400+', label: 'Active Campaigns' },
  { value: '25k+', label: 'Success Bookings' },
];

const headTitle = ref('StarJD');
const headDescription = ref('');
const headKeywords = ref('');
const canonicalUrl = computed(() => {
  return window.location.origin + route.path;
});

useHead({
  title: headTitle,
  link: [
    { rel: 'canonical', href: canonicalUrl }
  ],
  meta: [
    { name: 'description', content: headDescription },
    { name: 'keywords', content: headKeywords },
    { property: 'og:title', content: headTitle },
    { property: 'og:description', content: headDescription },
    { property: 'og:url', content: () => window.location.origin + route.fullPath },
    { property: 'og:type', content: 'website' }
  ]
});

const locationName = computed(() => {
  if (cmsPage.value?.city) return cmsPage.value.city.name;
  if (cmsPage.value?.state) return cmsPage.value.state.name;
  return '';
});

const cityName = computed(() => cmsPage.value?.city?.name || '');
const stateName = computed(() => cmsPage.value?.state?.name || '');

const filteredCreatorCategories = computed(() => {
  const q = categoryQuery.value.trim().toLowerCase();
  if (!q) return creatorCategories.value;
  return creatorCategories.value.filter((c) => String(c).toLowerCase().includes(q));
});

function selectSidebarCategory(category) {
  sidebarCategory.value = category || '';
  categoryQuery.value = category || '';
  categoryDropdownOpen.value = false;
}

function getServiceIcon(slug) {
  const icons = {
    'influencer-marketing': '🚀',
    'photography': '📸',
    'video-production': '🎬',
    'social-media-management': '📱',
    'brand-identity': '🎨',
    'graphic-design': '🖌️',
    'content-writing': '✍️',
    'pr-marketing': '📢',
  };
  return icons[slug] || '✨';
}

function handleSidebarSearch() {
  const query = {};
  if (sidebarSearch.value) query.search = sidebarSearch.value;
  if (sidebarCategory.value) query.category = sidebarCategory.value;
  if (locationName.value) query.location = locationName.value;
  
  router.push({ name: 'creators', query });
}

// Helper to ensure we always have an array
const toArr = (res) => (Array.isArray(res.data) ? res.data : (res.data?.data || []));

async function fetchRelatedData() {
  try {
    const loc = locationName.value;
    
    // Fetch creators (filtered by location if possible)
    const crRes = await axios.get('/api/creators', { params: { per_page: 4, location: loc } });
    creators.value = toArr(crRes).slice(0, 4);
    
    // Fallback: If no creators in specific city, fetch trending/global creators
    if (creators.value.length === 0) {
      const globalCrRes = await axios.get('/api/creators', { params: { per_page: 4, featured: true } });
      creators.value = toArr(globalCrRes).slice(0, 4);
    }
    
    // Fetch studios (filtered by city string)
    const stRes = await axios.get('/api/studios', { params: { per_page: 4, city: loc } });
    studios.value = toArr(stRes).slice(0, 4);
    
    // Fetch campaigns
    const cpRes = await axios.get('/api/campaigns', { params: { per_page: 3 } });
    campaigns.value = toArr(cpRes).slice(0, 3);
    
    // Fetch blogs
    const blRes = await axios.get('/api/posts', { params: { per_page: 3 } });
    blogs.value = toArr(blRes).slice(0, 3);
    
    // Fetch services (only once)
    if (!services.value.length) {
      const svRes = await axios.get('/api/services');
      services.value = toArr(svRes).slice(0, 6);
    }

    // Fetch creator categories for sidebar search filter
    if (!creatorCategories.value.length) {
      const cfRes = await axios.get('/api/creators/options/filters');
      if (cfRes.data && cfRes.data.categories) {
        creatorCategories.value = cfRes.data.categories;
      }
    }

    // Fetch sibling pages (cities in same state for the tree)
    if (cmsPage.value?.state_id) {
       const pgRes = await axios.get('/api/pages', { params: { state_id: cmsPage.value.state_id, per_page: 8 } });
       siblingPages.value = toArr(pgRes).filter(p => p.id !== cmsPage.value.id && p.slug === cmsPage.value.slug);
    }
  } catch (err) {
    // Fail silently in production or handle via monitoring service
  }
}

async function loadPage() {
  let slug = route.meta?.pageSlug || route.params.slug;
  const stateSlugFromUrl = route.params.state_slug;
  
  if (!slug) {
    loading.value = false;
    cmsPage.value = null;
    return;
  }

  // Handle SEO-friendly slug format: slug-in-location
  let locationSlug = null;
  if (slug.includes('-in-')) {
    const parts = slug.split('-in-');
    locationSlug = parts.pop();
    slug = parts.join('-in-');
  }

  loading.value = true;
  cmsPage.value = null;
  try {
    const params = {};
    if (route.query.state_slug) params.state_slug = route.query.state_slug;
    if (route.query.city_slug) params.city_slug = route.query.city_slug;
    
    if (stateSlugFromUrl) {
      params.state_slug = stateSlugFromUrl;
      if (locationSlug) {
        params.city_slug = locationSlug;
      }
    } else if (locationSlug && !params.state_slug && !params.city_slug) {
      params.state_slug = locationSlug;
    }

    const r = await axios.get(`/api/pages/${encodeURIComponent(slug)}`, { params });
    
    if (r.data && typeof r.data === 'object' && r.data.id) {
       cmsPage.value = r.data;
       
       // Update SEO Reactive Object
       headTitle.value = cmsPage.value.meta_title || cmsPage.value.title || 'StarJD';
       headDescription.value = cmsPage.value.meta_description || '';
       headKeywords.value = cmsPage.value.meta_keywords || '';
       
       fetchRelatedData();
    } else {
       cmsPage.value = null;
    }
  } catch (e) {
    cmsPage.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(loadPage);
watch(() => [route.meta?.pageSlug, route.params.slug, route.params.state_slug, route.query.state_slug, route.query.city_slug], loadPage);
</script>

<style scoped>
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-300 {
  animation-delay: 0.3s;
}

.prose :deep(img) {
  border-radius: 1rem;
}

/* New Hero Animations */
@keyframes driftUp {
  0% { transform: translateY(0) scale(1); opacity: 0; }
  20% { opacity: 0.1; }
  80% { opacity: 0.1; }
  100% { transform: translateY(-100vh) scale(1.5); opacity: 0; }
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0); }
  50% { transform: translateY(-20px) rotate(2deg); }
}
.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes bounceSlow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}
.animate-bounce-slow {
  animation: bounceSlow 4s ease-in-out infinite;
}

.animate-bounce-slower {
  animation: bounceSlow 5s ease-in-out infinite;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fade-in {
  animation: fadeIn 1s ease-out forwards;
}

/* Beam and Drift Animations */
@keyframes beamShift {
  from { transform: translateX(-10%) rotate(15deg); opacity: 0.3; }
  to { transform: translateX(10%) rotate(15deg); opacity: 0.6; }
}

@keyframes drift {
  from { transform: translate(0, 0) rotate(0deg); opacity: 0.2; }
  50% { transform: translate(20px, 30px) rotate(180deg); opacity: 0.5; }
  to { transform: translate(0, 0) rotate(360deg); opacity: 0.2; }
}

/* Glitter/Star Animation */
@keyframes twinkle {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.4); box-shadow: 0 0 10px rgba(230, 57, 70, 0.4); }
}
.star-glitter {
  animation: twinkle linear infinite;
}

@keyframes flicker {
  0%, 100% { opacity: 0.2; transform: rotate(-5deg) scale(0.9); }
  50% { opacity: 0.8; transform: rotate(5deg) scale(1.1); }
}
.broken-star {
  animation: flicker ease-in-out infinite;
}

/* Sparkle Animation */
@keyframes crystal {
  0%, 100% { transform: scale(0); opacity: 0; }
  50% { transform: scale(1.2); opacity: 1; }
}
.sparkle {
  animation: crystal 2s ease-in-out infinite;
}

/* Thread Animation */
@keyframes drawLine {
  0% { stroke-dashoffset: 1000; opacity: 0; transform: translateX(-10%); }
  50% { opacity: 1; }
  100% { stroke-dashoffset: 0; opacity: 0; transform: translateX(10%); }
}
.thread-path {
  stroke-dasharray: 500;
  animation: drawLine linear infinite;
}

/* Bubble Float Animation */
@keyframes floatUp {
  0% { transform: translateY(0) scale(0.8); opacity: 0; }
  10% { opacity: 0.4; }
  90% { opacity: 0.4; }
  100% { transform: translateY(-130vh) scale(1.2); opacity: 0; }
}
.floating-bubble {
  animation: floatUp linear infinite;
}
</style>
