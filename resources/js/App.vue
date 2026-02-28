<template>
    <div class="min-h-screen bg-[#fafaf9] text-[#1a1a1a]">
        <!-- Nav (hidden when used inside AppLayout for home) -->
        <header
            v-if="!noHeaderFooter"
            ref="headerRef"
            class="sticky top-0 z-50 border-b border-[#e5e7eb] bg-white/90 backdrop-blur-md"
        >
            <nav
                class="relative mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:py-4 md:px-6"
            >
                <router-link
                    to="/"
                    class="flex items-center gap-2"
                    @click="navMobileOpen = false"
                >
                    <img
                        src="/logo.png"
                        alt="StarJD"
                        class="h-11 w-auto object-contain sm:h-12"
                        onerror="
                            this.style.display = 'none';
                            this.nextElementSibling?.classList.remove('hidden');
                        "
                    />
                    <span
                        class="hidden text-lg font-bold tracking-tight text-[#1a1a1a] sm:text-xl"
                        >StarJD</span
                    >
                </router-link>

                <!-- Mobile / tablet menu toggle (stop propagation so click-outside doesn't close it instantly) -->
                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-lg border border-[#e5e7eb] bg-white text-[#1a1a1a] transition hover:border-[#e63946] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20 lg:hidden"
                    aria-label="Toggle menu"
                    :aria-expanded="navMobileOpen"
                    @click.stop="navMobileOpen = !navMobileOpen"
                >
                    <svg
                        v-if="!navMobileOpen"
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                    <svg
                        v-else
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                <!-- Desktop nav (lg and up) -->
                <div class="hidden items-center gap-4 lg:flex">
                    <router-link
                        to="/about"
                        class="text-sm text-[#6b7280] transition hover:text-[#e63946]"
                        >About</router-link
                    >
                    <router-link
                        to="/contact"
                        class="text-sm text-[#6b7280] transition hover:text-[#e63946]"
                        >Contact</router-link
                    >
                    <router-link
                        to="/blog"
                        class="text-sm text-[#6b7280] transition hover:text-[#e63946]"
                        >Blog</router-link
                    >
                    <div
                        class="relative flex items-center overflow-visible"
                        ref="navServicesRef"
                    >
                        <button
                            type="button"
                            class="inline-flex min-h-[2.25rem] items-center gap-1.5 rounded-md px-2 py-1.5 text-sm leading-normal text-[#6b7280] transition hover:text-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20 focus:ring-offset-0"
                            :class="{ 'text-[#e63946]': navServicesOpen }"
                            @click="navServicesOpen = !navServicesOpen"
                        >
                            <span class="whitespace-nowrap">Services</span>
                            <svg
                                class="h-4 w-4 shrink-0 overflow-visible transition-transform duration-200"
                                :class="{ 'rotate-180': navServicesOpen }"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 10l4 4 4-4"
                                />
                            </svg>
                        </button>
                        <Transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="opacity-0 scale-95"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <div
                                v-show="navServicesOpen"
                                class="absolute left-0 top-full z-50 mt-1 min-w-[180px] rounded-xl border border-[#e2e8f0] bg-white py-1 shadow-lg"
                            >
                                <router-link
                                    to="/services"
                                    class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                    @click="navServicesOpen = false"
                                    >All Services</router-link
                                >
                                <template v-if="navServices.length">
                                    <div
                                        class="my-1 border-t border-[#e2e8f0]"
                                    ></div>
                                    <router-link
                                        v-for="s in navServices"
                                        :key="s.id"
                                        :to="'/services/' + s.slug"
                                        class="block px-4 py-2 text-sm text-[#64748b] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                        @click="navServicesOpen = false"
                                        >{{ s.name }}</router-link
                                    >
                                </template>
                            </div>
                        </Transition>
                    </div>
                    <router-link
                        to="/creators"
                        class="text-sm text-[#6b7280] transition hover:text-[#e63946]"
                        >Discover Creators</router-link
                    >
                    <router-link
                        to="/studios"
                        class="text-sm text-[#6b7280] transition hover:text-[#e63946]"
                        >Studios</router-link
                    >

                    <template v-if="navUser">
                        <div class="relative" ref="navUserMenuRef">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-lg border border-[#e5e7eb] bg-white px-4 py-2 text-sm font-medium text-[#1a1a1a] shadow-sm transition hover:border-[#e63946] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                                :class="
                                    navUser.role === 'creator'
                                        ? 'hover:border-[#10b981] focus:ring-[#10b981]/20'
                                        : navUser.role === 'admin'
                                          ? 'hover:border-[#1e293b] focus:ring-[#1e293b]/20'
                                          : ''
                                "
                                @click="navUserMenuOpen = !navUserMenuOpen"
                            >
                                <span class="max-w-[120px] truncate">{{
                                    navUser.name
                                }}</span>
                                <svg
                                    class="h-4 w-4 shrink-0 text-[#64748b] transition-transform"
                                    :class="{ 'rotate-180': navUserMenuOpen }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>
                            <Transition
                                enter-active-class="transition duration-100 ease-out"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div
                                    v-show="navUserMenuOpen"
                                    class="absolute right-0 top-full z-50 mt-1 min-w-[200px] rounded-xl border border-[#e2e8f0] bg-white py-1 shadow-lg"
                                >
                                    <div
                                        class="border-b border-[#e2e8f0] px-4 py-2.5"
                                    >
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-[#64748b]"
                                        >
                                            {{ navUser.role }}
                                        </p>
                                        <p
                                            class="truncate text-sm font-medium text-[#1a1a1a]"
                                        >
                                            {{ navUser.name }}
                                        </p>
                                    </div>
                                    <template v-if="navUser.role === 'creator'">
                                        <router-link
                                            to="/creator/dashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                            @click="navUserMenuOpen = false"
                                            >Creator Dashboard</router-link
                                        >
                                        <router-link
                                            to="/creator/profile"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                            @click="navUserMenuOpen = false"
                                            >My Profile</router-link
                                        >
                                    </template>
                                    <template
                                        v-else-if="navUser.role === 'brand'"
                                    >
                                        <router-link
                                            to="/brand/dashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                            @click="navUserMenuOpen = false"
                                            >Brand Dashboard</router-link
                                        >
                                        <router-link
                                            to="/brand/creators"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                            @click="navUserMenuOpen = false"
                                            >Discover Creators</router-link
                                        >
                                    </template>
                                    <template
                                        v-else-if="navUser.role === 'admin'"
                                    >
                                        <router-link
                                            to="/admin"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]"
                                            @click="navUserMenuOpen = false"
                                            >Admin Panel</router-link
                                        >
                                    </template>
                                    <div class="border-t border-[#e2e8f0] pt-1">
                                        <button
                                            type="button"
                                            class="block w-full px-4 py-2.5 text-left text-sm text-[#64748b] transition hover:bg-red-50 hover:text-red-600"
                                            @click="navLogout"
                                        >
                                            Logout
                                        </button>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </template>
                    <template v-else>
                        <router-link
                            to="/login"
                            class="rounded-lg border border-[#e5e7eb] px-4 py-2 text-sm font-medium transition hover:border-[#e63946] hover:text-[#e63946]"
                            >Login</router-link
                        >
                        <router-link
                            to="/creator-landing"
                            class="cursor-link join-creator rounded-lg border border-[#e5e7eb] px-4 py-2 text-sm font-medium transition hover:border-[#10b981] hover:bg-[#10b981]/5 hover:text-[#10b981]"
                            >Join as Creator</router-link
                        >
                        <router-link
                            to="/brand-landing"
                            class="join-brand rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#c1121f]"
                            >Join as Brand</router-link
                        >
                    </template>
                </div>
            </nav>

            <!-- Mobile / tablet dropdown: all nav links -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-show="navMobileOpen"
                    class="absolute left-0 right-0 top-full z-40 border-t border-[#e5e7eb] bg-white shadow-lg lg:hidden"
                >
                    <div class="mx-auto max-w-6xl px-4 py-4">
                        <div class="flex flex-col gap-1">
                            <router-link
                                to="/about"
                                class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                @click="navMobileOpen = false"
                                >About</router-link
                            >
                            <router-link
                                to="/contact"
                                class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                @click="navMobileOpen = false"
                                >Contact</router-link
                            >
                            <router-link
                                to="/blog"
                                class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                @click="navMobileOpen = false"
                                >Blog</router-link
                            >
                            <router-link
                                to="/services"
                                class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                @click="navMobileOpen = false"
                                >Services</router-link
                            >
                            <template v-if="navServices.length">
                                <router-link
                                    v-for="s in navServices"
                                    :key="s.id"
                                    :to="'/services/' + s.slug"
                                    class="rounded-lg px-4 py-2 pl-8 text-sm text-[#64748b] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                    @click="navMobileOpen = false"
                                    >{{ s.name }}</router-link
                                >
                            </template>
                            <router-link
                                to="/creators"
                                class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                @click="navMobileOpen = false"
                                >Discover Creators</router-link
                            >
                            <router-link
                                to="/studios"
                                class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                @click="navMobileOpen = false"
                                >Studios</router-link
                            >
                            <template v-if="navUser">
                                <div
                                    class="my-2 border-t border-[#e5e7eb] pt-2"
                                >
                                    <p
                                        class="px-4 py-1 text-xs font-medium uppercase text-[#64748b]"
                                    >
                                        {{ navUser.role }}
                                    </p>
                                    <p
                                        class="truncate px-4 py-1 text-sm font-medium text-[#1a1a1a]"
                                    >
                                        {{ navUser.name }}
                                    </p>
                                </div>
                                <template v-if="navUser.role === 'creator'">
                                    <router-link
                                        to="/creator/dashboard"
                                        class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                        @click="navMobileOpen = false"
                                        >Creator Dashboard</router-link
                                    >
                                    <router-link
                                        to="/creator/profile"
                                        class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                        @click="navMobileOpen = false"
                                        >My Profile</router-link
                                    >
                                </template>
                                <template v-else-if="navUser.role === 'brand'">
                                    <router-link
                                        to="/brand/dashboard"
                                        class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                        @click="navMobileOpen = false"
                                        >Brand Dashboard</router-link
                                    >
                                    <router-link
                                        to="/brand/creators"
                                        class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                        @click="navMobileOpen = false"
                                        >Discover Creators</router-link
                                    >
                                </template>
                                <template v-else-if="navUser.role === 'admin'">
                                    <router-link
                                        to="/admin"
                                        class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]"
                                        @click="navMobileOpen = false"
                                        >Admin Panel</router-link
                                    >
                                </template>
                                <button
                                    type="button"
                                    class="rounded-lg px-4 py-3 text-left text-sm text-red-600 transition hover:bg-red-50"
                                    @click="
                                        navLogout();
                                        navMobileOpen = false;
                                    "
                                >
                                    Logout
                                </button>
                            </template>
                            <template v-else>
                                <div
                                    class="mt-2 flex flex-col gap-2 border-t border-[#e5e7eb] pt-3"
                                >
                                    <router-link
                                        to="/login"
                                        class="rounded-lg border border-[#e5e7eb] px-4 py-3 text-center text-sm font-medium transition hover:border-[#e63946] hover:text-[#e63946]"
                                        @click="navMobileOpen = false"
                                        >Login</router-link
                                    >
                                    <router-link
                                        to="/creator-landing"
                                        class="rounded-lg border border-[#10b981] px-4 py-3 text-center text-sm font-medium text-[#10b981] transition hover:bg-[#10b981]/5"
                                        @click="navMobileOpen = false"
                                        >Join as Creator</router-link
                                    >
                                    <router-link
                                        to="/brand-landing"
                                        class="rounded-lg bg-[#e63946] px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-[#c1121f]"
                                        @click="navMobileOpen = false"
                                        >Join as Brand</router-link
                                    >
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </Transition>
        </header>

        <!-- Hero: text header above, gallery + video centered below -->
        <section
            class="hero-curved hero-section-appealing relative min-h-[50vh] overflow-hidden overflow-x-hidden sm:min-h-[60vh] md:min-h-[70vh]"
        >
            <!-- Wallpaper: 5–7 images auto-scrolling horizontally -->
            <div class="hero-wallpaper absolute inset-0" aria-hidden="true">
                <div class="hero-wallpaper-scroll">
                    <div
                        v-for="(img, i) in heroWallpaperScrollImagesComputed"
                        :key="i"
                        class="hero-wallpaper-item"
                        :style="{
                            backgroundImage: `url(${typeof img === 'object' && img?.src ? img.src : img})`,
                        }"
                    />
                </div>
            </div>
            <!-- Decorative gradient orbs -->
            <div class="hero-glow hero-glow-1" aria-hidden="true" />
            <div class="hero-glow hero-glow-2" aria-hidden="true" />
            <div class="hero-glow hero-glow-3" aria-hidden="true" />
            <div class="hero-glow hero-glow-4" aria-hidden="true" />

            <div
                class="hero-inner relative z-10 mx-auto flex w-full min-h-[50vh] max-w-6xl flex-col items-center justify-center px-4 py-6 text-center sm:min-h-[55vh] sm:px-5 sm:py-8 md:min-h-[60vh] md:px-6 md:py-10 lg:px-8"
            >
                <!-- Text header – above everything, centered on all viewports -->
                <div
                    class="hero-content relative w-full max-w-4xl mx-auto px-0 sm:px-2"
                >
                    <h1
                        class="hero-three-rows animate-fade-in-up w-full text-center text-3xl font-bold tracking-tight text-[#1a1a1a] drop-shadow-sm sm:text-4xl md:text-5xl lg:text-6xl"
                    >
                        <span
                            class="hero-row hero-row-1 block text-[#1a1a1a]"
                            >{{
                                heroData?.headline ||
                                "Influencer Marketing Made Easy"
                            }}</span
                        >
                    </h1>
                    <div
                        class="animate-fade-in-up animation-delay-300 mt-4 flex w-full min-w-0 flex-wrap justify-center gap-2 px-0 sm:mt-5 sm:gap-3 sm:px-2"
                    >
                        <router-link
                            v-if="isInternalHeroUrl(heroData?.btn_creator_url)"
                            :to="heroData.btn_creator_url || '/creator-landing'"
                            class="btn-hero-creator shrink-0 rounded-xl border border-emerald-400/60 bg-white/95 px-3 py-2 text-sm font-medium text-[#059669] shadow-lg backdrop-blur-sm transition hover:border-emerald-500 hover:bg-emerald-50 hover:shadow-xl sm:px-5 sm:py-2.5"
                            >{{
                                heroData?.btn_creator_label || "Join as Creator"
                            }}</router-link
                        >
                        <a
                            v-else
                            :href="heroData?.btn_creator_url || '#join-creator'"
                            class="btn-hero-creator shrink-0 rounded-xl border border-emerald-400/60 bg-white/95 px-3 py-2 text-sm font-medium text-[#059669] shadow-lg backdrop-blur-sm transition hover:border-emerald-500 hover:bg-emerald-50 hover:shadow-xl sm:px-5 sm:py-2.5"
                            >{{
                                heroData?.btn_creator_label || "Join as Creator"
                            }}</a
                        >
                        <router-link
                            v-if="isInternalHeroUrl(heroData?.btn_brand_url)"
                            :to="heroData.btn_brand_url || '/brand-landing'"
                            class="join-brand shrink-0 rounded-xl bg-[#e63946] px-3 py-2 text-sm font-medium text-white shadow-lg shadow-[#e63946]/35 transition hover:bg-[#c1121f] hover:shadow-xl hover:shadow-[#e63946]/45 sm:px-5 sm:py-2.5"
                            >{{
                                heroData?.btn_brand_label || "Join as Brand"
                            }}</router-link
                        >
                        <a
                            v-else
                            :href="heroData?.btn_brand_url || '#join-brand'"
                            class="join-brand shrink-0 rounded-xl bg-[#e63946] px-3 py-2 text-sm font-medium text-white shadow-lg shadow-[#e63946]/35 transition hover:bg-[#c1121f] hover:shadow-xl hover:shadow-[#e63946]/45 sm:px-5 sm:py-2.5"
                            >{{
                                heroData?.btn_brand_label || "Join as Brand"
                            }}</a
                        >
                        <router-link
                            v-if="isInternalHeroUrl(heroData?.btn_browse_url)"
                            :to="heroData.btn_browse_url || '/creators'"
                            class="cursor-link btn-hero-browse shrink-0 rounded-xl border border-[#e5e7eb] bg-white/95 px-3 py-2 text-sm font-medium text-[#374151] shadow-lg backdrop-blur-sm transition hover:border-pink-300 hover:bg-pink-50 hover:shadow-xl sm:px-5 sm:py-2.5"
                            >{{
                                heroData?.btn_browse_label || "Browse Creators"
                            }}</router-link
                        >
                        <a
                            v-else
                            :href="heroData?.btn_browse_url || '#featured'"
                            class="cursor-link btn-hero-browse shrink-0 rounded-xl border border-[#e5e7eb] bg-white/95 px-3 py-2 text-sm font-medium text-[#374151] shadow-lg backdrop-blur-sm transition hover:border-pink-300 hover:bg-pink-50 hover:shadow-xl sm:px-5 sm:py-2.5"
                            >{{
                                heroData?.btn_browse_label || "Browse Creators"
                            }}</a
                        >
                    </div>
                </div>

                <!-- Gallery cascade – centered, no scrollbar, contained -->
                <div
                    class="hero-media-center relative z-10 mt-3 flex w-full flex-1 justify-center overflow-hidden px-2 sm:mt-4 md:mt-6"
                >
                    <div
                        class="gallery-cascade-inner gallery-cascade-in-hero mx-auto flex scale-75 justify-center drop-shadow-2xl sm:scale-90 md:scale-100"
                    >
                        <component
                            :is="
                                cascadeHasLink(img)
                                    ? isInternalHeroUrl(img.link)
                                        ? 'router-link'
                                        : 'a'
                                    : 'div'
                            "
                            v-for="(img, i) in galleryCascadeImagesComputed"
                            :key="i"
                            :to="
                                cascadeHasLink(img) &&
                                isInternalHeroUrl(img.link)
                                    ? img.link
                                    : undefined
                            "
                            :href="
                                cascadeHasLink(img) &&
                                !isInternalHeroUrl(img.link)
                                    ? img.link
                                    : undefined
                            "
                            class="gallery-cascade-card relative block overflow-hidden rounded-2xl bg-white shadow-lg transition-all duration-300"
                            :class="[
                                cascadeHasLink(img) && 'cursor-link',
                                {
                                    'gallery-cascade-card-active':
                                        activeCascadeIndex === i,
                                },
                            ]"
                            :style="getCascadeStyle(i)"
                            @mouseenter="activeCascadeIndex = i"
                            @mouseleave="activeCascadeIndex = null"
                        >
                            <img
                                :src="img.src"
                                :alt="img.alt"
                                class="h-full w-full object-cover"
                            />
                            <span
                                class="gallery-cascade-dot absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-full pt-1"
                                aria-hidden="true"
                            />
                        </component>
                    </div>
                </div>
            </div>
            <!-- Curved bottom edge -->
            <div
                class="hero-curve-shape absolute bottom-0 left-0 w-full"
                aria-hidden="true"
            >
                <svg
                    class="block w-full"
                    viewBox="0 0 1440 120"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    preserveAspectRatio="none"
                >
                    <path
                        d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                        fill="#fafaf9"
                    />
                </svg>
            </div>
        </section>

        <!-- Search & filter section: filters above search bar, full responsive -->
        <section
            class="border-b border-[#e5e7eb] bg-[#fafaf9] px-3 py-6 sm:px-4 sm:py-8 md:py-10"
        >
            <div class="mx-auto max-w-4xl">
                <h2
                    class="mb-4 text-center text-lg font-semibold text-[#1a1a1a] sm:mb-5 sm:text-xl md:text-2xl"
                >
                    Find creators
                </h2>

                <!-- Filters row – above search bar; single line on desktop -->
                <div
                    class="mb-4 grid grid-cols-2 gap-3 sm:mb-5 sm:flex sm:flex-wrap sm:items-end sm:justify-center sm:gap-4 md:flex-nowrap"
                >
                    <div class="min-w-0 sm:w-auto sm:min-w-[130px]">
                        <label
                            for="hero-filter-category"
                            class="mb-1 block text-xs font-medium text-[#6b7280]"
                            >Category</label
                        >
                        <select
                            id="hero-filter-category"
                            v-model="heroSearchCategory"
                            class="hero-filter-select w-full cursor-pointer rounded-lg border border-[#e5e7eb] bg-white px-3 py-2.5 text-sm text-[#1a1a1a] transition hover:border-[#d1d5db] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                        >
                            <option value="">All categories</option>
                            <option
                                v-for="c in heroFilterOptions.categories"
                                :key="c"
                                :value="c"
                            >
                                {{ c }}
                            </option>
                        </select>
                    </div>
                    <div class="min-w-0 sm:w-auto sm:min-w-[130px]">
                        <label
                            for="hero-filter-gender"
                            class="mb-1 block text-xs font-medium text-[#6b7280]"
                            >Gender</label
                        >
                        <select
                            id="hero-filter-gender"
                            v-model="heroSearchGender"
                            class="hero-filter-select w-full cursor-pointer rounded-lg border border-[#e5e7eb] bg-white px-3 py-2.5 text-sm text-[#1a1a1a] transition hover:border-[#d1d5db] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                        >
                            <option value="">Any</option>
                            <option
                                v-for="(
                                    label, key
                                ) in heroFilterOptions.genders"
                                :key="key"
                                :value="key"
                            >
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    <div class="min-w-0 sm:w-auto sm:min-w-[130px]">
                        <label
                            for="hero-filter-language"
                            class="mb-1 block text-xs font-medium text-[#6b7280]"
                            >Language</label
                        >
                        <select
                            id="hero-filter-language"
                            v-model="heroSearchLanguage"
                            class="hero-filter-select w-full cursor-pointer rounded-lg border border-[#e5e7eb] bg-white px-3 py-2.5 text-sm text-[#1a1a1a] transition hover:border-[#d1d5db] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                        >
                            <option value="">Any</option>
                            <option
                                v-for="lang in heroFilterOptions.languages"
                                :key="lang"
                                :value="lang"
                            >
                                {{ lang }}
                            </option>
                        </select>
                    </div>
                    <div class="min-w-0 sm:w-auto sm:min-w-[130px]">
                        <label
                            for="hero-filter-platform"
                            class="mb-1 block text-xs font-medium text-[#6b7280]"
                            >Platform</label
                        >
                        <select
                            id="hero-filter-platform"
                            v-model="heroSearchPlatform"
                            class="hero-filter-select w-full cursor-pointer rounded-lg border border-[#e5e7eb] bg-white px-3 py-2.5 text-sm text-[#1a1a1a] transition hover:border-[#d1d5db] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                        >
                            <option value="">Any</option>
                            <option
                                v-for="(label, key) in heroPlatformOptions"
                                :key="key"
                                :value="key"
                            >
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    <div class="min-w-0 sm:w-auto sm:min-w-[110px]">
                        <label
                            for="hero-filter-minrate"
                            class="mb-1 block text-xs font-medium text-[#6b7280]"
                            >Min price (₹)</label
                        >
                        <input
                            id="hero-filter-minrate"
                            v-model.number="heroSearchMinRate"
                            type="number"
                            min="0"
                            step="100"
                            placeholder="Any"
                            class="w-full rounded-lg border border-[#e5e7eb] bg-white px-3 py-2.5 text-sm text-[#1a1a1a] placeholder-[#9ca3af] transition hover:border-[#d1d5db] focus:border-[#e63946] focus:outline-none focus:ring-2 focus:ring-[#e63946]/20"
                        />
                    </div>
                    <button
                        type="button"
                        class="col-span-2 cursor-pointer rounded-lg border border-[#e5e7eb] bg-white px-4 py-2.5 text-sm font-medium text-[#6b7280] transition hover:border-[#d1d5db] hover:bg-[#f1f5f9] hover:text-[#1a1a1a] sm:col-span-1 sm:w-auto"
                        @click="clearHeroSearch"
                    >
                        Clear All
                    </button>
                </div>

                <!-- Search bar – pill style below filters; single line on desktop -->
                <form
                    class="search-bar-pill flex flex-col gap-3 rounded-2xl border border-[#e5e7eb] bg-white px-4 py-3 shadow-sm sm:flex-row sm:flex-nowrap sm:items-center sm:gap-4 sm:rounded-full sm:px-5 sm:py-3 md:px-6 md:py-4"
                    @submit.prevent="submitHeroSearch"
                >
                    <div
                        class="order-2 min-w-0 flex-1 sm:order-none sm:max-w-[140px]"
                    >
                        <label
                            class="mb-0.5 block text-xs font-medium text-[#6b7280]"
                            >Platform</label
                        >
                        <select
                            v-model="heroSearchPlatform"
                            class="w-full cursor-pointer rounded-lg border-0 bg-transparent py-2 pr-6 text-sm text-[#1a1a1a] focus:outline-none focus:ring-0 sm:rounded-full"
                        >
                            <option value="">Any</option>
                            <option
                                v-for="(label, key) in heroPlatformOptions"
                                :key="key"
                                :value="key"
                            >
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    <div class="order-1 min-w-0 flex-1 sm:order-none">
                        <label
                            class="mb-0.5 block text-xs font-medium text-[#6b7280]"
                            >Keywords</label
                        >
                        <input
                            v-model="heroSearchQuery"
                            type="text"
                            placeholder="Enter keywords, niches or categories"
                            class="w-full rounded-lg border-0 bg-transparent py-2 text-sm text-[#1a1a1a] placeholder-[#9ca3af] focus:outline-none focus:ring-0 sm:rounded-full"
                        />
                    </div>
                    <button
                        type="submit"
                        class="order-3 cursor-pointer flex h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#1a1a1a] text-white transition hover:bg-[#374151] sm:h-12 sm:w-12 sm:rounded-full sm:self-end"
                        aria-label="Search creators"
                    >
                        <svg
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                        <span class="sm:sr-only">Search</span>
                    </button>
                </form>
            </div>
        </section>

        <!-- Choose your path -->
        <section
            id="join-creator"
            class="animate-on-scroll border-y border-[#e5e7eb] bg-white px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <h2 class="section-title mb-2 text-2xl font-bold md:text-3xl">
                    Choose Your Path
                </h2>
                <p class="section-subtitle mb-12 text-[#6b7280]">
                    Whether you create content or need it — we've got you.
                </p>
                <div class="grid gap-8 md:grid-cols-2">
                    <router-link
                        to="/creator-landing"
                        class="cursor-link path-card group relative overflow-hidden rounded-3xl border-2 border-[#e5e7eb] bg-[#fafaf9] transition hover:border-[#10b981] hover:shadow-2xl block"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img
                                src="https://picsum.photos/seed/creator/800/600"
                                alt="Join as Creator"
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-110"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
                            />
                            <div
                                class="absolute bottom-0 left-0 right-0 p-6 text-white"
                            >
                                <h3 class="text-2xl font-bold">
                                    I'm a Creator
                                </h3>
                                <p class="mt-1 text-white/90">
                                    Get discovered by brands. Set your rates.
                                    Get paid securely.
                                </p>
                            </div>
                        </div>
                        <div class="p-6">
                            <span
                                class="inline-flex items-center gap-2 font-semibold text-[#10b981]"
                                >Join as Creator →</span
                            >
                        </div>
                    </router-link>
                    <router-link
                        to="/brand-landing"
                        class="cursor-link path-card group relative overflow-hidden rounded-3xl border-2 border-[#e5e7eb] bg-[#fafaf9] transition hover:border-[#e63946] hover:shadow-2xl block"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img
                                src="https://picsum.photos/seed/brand/800/600"
                                alt="Join as Brand"
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-110"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
                            />
                            <div
                                class="absolute bottom-0 left-0 right-0 p-6 text-white"
                            >
                                <h3 class="text-2xl font-bold">
                                    AI‑ready creator campaigns.
                                    <span class="block text-white/90">Without compromise.</span>
                                </h3>
                                <p class="mt-1 text-white/90 text-sm">
                                    12M+ creators. Brief, discover, approve and measure
                                    high‑impact influencer campaigns from one modern
                                    workspace — built for speed and scale.
                                </p>
                            </div>
                        </div>
                        <div class="p-6">
                            <span
                                class="inline-flex items-center gap-2 font-semibold text-[#e63946]"
                                >Get Started →</span
                            >
                        </div>
                    </router-link>
                </div>
            </div>
        </section>

        <!-- As seen in / Partners – auto-scroll marquee -->
        <section
            v-if="partners.length"
            class="partners-marquee border-b border-[#e5e7eb] bg-white py-8"
        >
            <div class="partners-marquee-track overflow-hidden">
                <div class="partners-marquee-inner flex items-center gap-10">
                    <template
                        v-for="(set, setIdx) in [partners, partners]"
                        :key="'set-' + setIdx"
                    >
                        <template v-for="p in set" :key="setIdx + '-' + p.id">
                            <a
                                v-if="p.link"
                                :href="p.link"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="partners-marquee-item flex shrink-0 items-center justify-center transition hover:opacity-80"
                            >
                                <img
                                    v-if="p.logo_url"
                                    :src="p.logo_url"
                                    :alt="p.name || 'Partner'"
                                    class="h-10 max-h-12 w-auto max-w-[140px] object-contain"
                                />
                                <span
                                    v-else
                                    class="text-lg font-semibold text-[#9ca3af]"
                                    >{{ p.name || "Partner" }}</span
                                >
                            </a>
                            <span
                                v-else
                                class="partners-marquee-item flex shrink-0 items-center justify-center"
                            >
                                <img
                                    v-if="p.logo_url"
                                    :src="p.logo_url"
                                    :alt="p.name || 'Partner'"
                                    class="h-10 max-h-12 w-auto max-w-[140px] object-contain"
                                />
                                <span
                                    v-else
                                    class="text-lg font-semibold text-[#9ca3af]"
                                    >{{ p.name || "Partner" }}</span
                                >
                            </span>
                        </template>
                    </template>
                </div>
            </div>
        </section>

        <!-- Categories with images -->
        <section
            id="categories"
            class="animate-on-scroll border-b border-[#e5e7eb] bg-[#fafaf9] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <h2 class="section-title mb-2 text-2xl font-bold md:text-3xl">
                    Explore by Category
                </h2>
                <p class="section-subtitle mb-12 text-[#6b7280]">
                    Find creators in your niche.
                </p>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="(cat, i) in categories"
                        :key="cat.name"
                        class="category-card cursor-link group overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm transition hover:border-[#e63946]/50 hover:shadow-xl"
                    >
                        <div class="relative aspect-[3/4] overflow-hidden">
                            <img
                                :src="cat.image_url || cat.image"
                                :alt="cat.name"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
                            />
                            <div
                                class="absolute bottom-0 left-0 right-0 p-4 text-white opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                <span class="font-semibold">{{
                                    cat.name
                                }}</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-[#1a1a1a]">
                                {{ cat.name }}
                            </h3>
                            <p class="text-sm text-[#6b7280]">
                                {{ cat.count }} creators
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="border-b border-[#e5e7eb] bg-white px-4 py-10 md:py-16">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-10 text-center md:grid-cols-4">
                    <div class="stat-item animate-on-scroll">
                        <p
                            class="text-4xl font-bold text-[#e63946] md:text-5xl"
                        >
                            10k+
                        </p>
                        <p class="mt-1 text-[#6b7280]">Creators</p>
                    </div>
                    <div class="stat-item animate-on-scroll">
                        <p
                            class="text-4xl font-bold text-[#e63946] md:text-5xl"
                        >
                            5k+
                        </p>
                        <p class="mt-1 text-[#6b7280]">Brands</p>
                    </div>
                    <div class="stat-item animate-on-scroll">
                        <p
                            class="text-4xl font-bold text-[#e63946] md:text-5xl"
                        >
                            50k+
                        </p>
                        <p class="mt-1 text-[#6b7280]">Projects</p>
                    </div>
                    <div class="stat-item animate-on-scroll">
                        <p
                            class="text-4xl font-bold text-[#e63946] md:text-5xl"
                        >
                            4.9
                        </p>
                        <p class="mt-1 text-[#6b7280]">Avg. Rating</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Creators (split section) -->
        <section
            class="animate-on-scroll border-b border-[#e5e7eb] bg-[#f0fdf4] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <div class="grid items-center gap-12 md:grid-cols-2 md:gap-16">
                    <div class="order-2 md:order-1">
                        <h2
                            class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                        >
                            For Creators
                        </h2>
                        <p class="section-subtitle mt-4 text-[#6b7280]">
                            Set up your profile, add your rates, and get
                            discovered by brands. You keep full control — accept
                            only the briefs you want.
                        </p>
                        <ul class="mt-6 space-y-3 text-[#1a1a1a]">
                            <li class="flex items-center gap-2">
                                ✓ Set your own prices
                            </li>
                            <li class="flex items-center gap-2">
                                ✓ Secure payments
                            </li>
                            <li class="flex items-center gap-2">
                                ✓ Direct messaging with brands
                            </li>
                        </ul>
                        <a
                            href="#"
                            class="cursor-link join-creator mt-8 inline-block rounded-xl bg-[#10b981] px-6 py-3 font-semibold text-white transition hover:bg-[#059669]"
                            >Join as Creator</a
                        >
                    </div>
                    <div class="order-1 md:order-2">
                        <div class="overflow-hidden rounded-2xl shadow-xl">
                            <img
                                src="https://picsum.photos/seed/forcreator/600/400"
                                alt="For Creators"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Brands (split section) -->
        <section
            id="join-brand"
            class="animate-on-scroll border-b border-[#e5e7eb] bg-[#fff5f5] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <div class="grid items-center gap-12 md:grid-cols-2 md:gap-16">
                    <div>
                        <div class="overflow-hidden rounded-2xl shadow-xl">
                            <img
                                src="https://picsum.photos/seed/forbrand/600/400"
                                alt="For Brands"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                    <div>
                        <h2
                            class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                        >
                            For Brands
                        </h2>
                        <p class="section-subtitle mt-4 text-[#6b7280]">
                            Search by niche, location, and budget. Brief
                            creators, book packages, and receive content — all
                            in one place.
                        </p>
                        <ul class="mt-6 space-y-3 text-[#1a1a1a]">
                            <li class="flex items-center gap-2">
                                ✓ Vetted creator network
                            </li>
                            <li class="flex items-center gap-2">
                                ✓ Escrow payments
                            </li>
                            <li class="flex items-center gap-2">
                                ✓ Campaign tracking
                            </li>
                        </ul>
                        <a
                            href="#"
                            class="join-brand mt-8 inline-block rounded-xl bg-[#e63946] px-6 py-3 font-semibold text-white transition hover:bg-[#c1121f]"
                            >Join as Brand</a
                        >
                    </div>
                </div>
            </div>
        </section>

        <!-- Platform pills -->
        <section class="border-b border-[#e5e7eb] bg-white px-4 py-6">
            <div
                class="mx-auto flex max-w-4xl flex-wrap items-center justify-center gap-3"
            >
                <button
                    v-for="platform in platforms"
                    :key="platform"
                    class="cursor-link rounded-full border border-[#e5e7eb] px-4 py-2 text-sm font-medium transition hover:border-[#e63946] hover:bg-[#e63946]/5 hover:text-[#e63946]"
                    :class="{
                        'border-[#e63946] bg-[#e63946]/10 text-[#e63946]':
                            activePlatform === platform,
                    }"
                    @click="activePlatform = platform"
                >
                    {{ platform }}
                </button>
            </div>
        </section>

        <!-- Featured section (reference image style) -->
        <section id="featured" class="animate-on-scroll px-4 py-10 md:py-16">
            <div class="mx-auto max-w-6xl">
                <div
                    class="mb-10 flex flex-wrap items-end justify-between gap-4"
                >
                    <div>
                        <h2
                            class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                        >
                            Featured
                        </h2>
                        <p class="section-subtitle mt-1 text-[#6b7280]">
                            Hire top influencers across all platforms.
                        </p>
                    </div>
                    <router-link
                        to="/creators"
                        class="text-sm font-semibold text-[#e63946] transition hover:underline"
                        >See All</router-link
                    >
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <article
                        v-for="creator in featuredCreators"
                        :key="creator.id"
                        class="hover-lift group rounded-2xl border border-[#e5e7eb] bg-white shadow-sm overflow-hidden"
                    >
                        <router-link
                            :to="'/creators/' + (creator.slug || creator.id)"
                            class="block"
                        >
                            <div
                                class="relative aspect-[4/5] overflow-hidden bg-[#f3f4f6]"
                            >
                                <img
                                    :src="
                                        creator.image ||
                                        'https://ui-avatars.com/api?name=' +
                                            encodeURIComponent(
                                                creator.name || '',
                                            ) +
                                            '&size=400'
                                    "
                                    :alt="creator.name"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                />
                                <div
                                    class="absolute left-2 top-2 flex flex-wrap gap-1"
                                >
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-[#f59e0b] px-2 py-0.5 text-xs font-medium text-white"
                                    >
                                        <svg
                                            class="h-3 w-3"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                            />
                                        </svg>
                                        Featured
                                    </span>
                                </div>
                                <span
                                    v-if="creator.category"
                                    class="absolute bottom-2 left-2 inline-flex rounded bg-gray-800/80 px-2 py-0.5 text-xs font-medium text-white"
                                >
                                    <svg
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 13v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4"
                                        />
                                    </svg>
                                    {{ creator.category }}
                                </span>
                            </div>
                            <div class="p-4">
                                <div
                                    class="flex items-center justify-between gap-2"
                                >
                                    <h3 class="font-semibold text-[#1a1a1a]">
                                        {{ creator.name }}
                                    </h3>
                                    <span
                                        v-if="creator.rating"
                                        class="shrink-0 text-sm font-medium text-[#1a1a1a]"
                                        >★ {{ creator.rating }}</span
                                    >
                                </div>
                                <p
                                    v-if="creator.tagline"
                                    class="mt-1 line-clamp-2 text-sm text-[#6b7280]"
                                >
                                    {{ creator.tagline }}
                                </p>
                                <p
                                    v-if="creator.location"
                                    class="mt-2 text-xs text-[#6b7280]"
                                >
                                    {{ creator.location }}
                                </p>
                                <p
                                    v-if="creator.price != null"
                                    class="mt-2 text-lg font-bold text-[#e63946]"
                                >
                                    From ₹{{ creator.price }}/project
                                </p>
                            </div>
                        </router-link>
                    </article>
                </div>
            </div>
        </section>

        <!-- Featured Studios + CTA -->
        <section
            class="animate-on-scroll border-t border-[#e5e7eb] bg-[#fafaf9] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <div
                    class="mb-10 flex flex-wrap items-end justify-between gap-4"
                >
                    <div>
                        <h2
                            class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                        >
                            Featured Studios
                        </h2>
                        <p class="section-subtitle mt-1 text-[#6b7280]">
                            Photography, film, podcast & event spaces.
                        </p>
                    </div>
                    <router-link
                        to="/studios"
                        class="text-sm font-semibold text-[#e63946] transition hover:underline"
                        >Browse all</router-link
                    >
                </div>
                <div
                    v-if="featuredStudios.length"
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <router-link
                        v-for="s in featuredStudios"
                        :key="s.id"
                        :to="'/studios/' + (s.slug || s.id)"
                        class="group block overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md"
                    >
                        <div
                            class="relative aspect-[4/3] overflow-hidden bg-[#f3f4f6]"
                        >
                            <img
                                :src="
                                    s.main_image ||
                                    'https://images.unsplash.com/photo-1595846519845-68e298c2f195?w=400&h=300&fit=crop'
                                "
                                :alt="s.name"
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            />
                            <span
                                v-if="s.featured"
                                class="absolute left-2 top-2 rounded-full bg-[#f59e0b] px-2 py-0.5 text-xs font-medium text-white"
                                >Featured</span
                            >
                            <span
                                v-if="s.rating_avg != null"
                                class="absolute bottom-2 right-2 rounded bg-black/60 px-2 py-1 text-xs text-white"
                                >★ {{ s.rating_avg }}</span
                            >
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-[#1a1a1a]">
                                {{ s.name }}
                            </h3>
                            <p
                                v-if="s.city"
                                class="mt-1 text-sm text-[#6b7280]"
                            >
                                {{ s.city }}
                            </p>
                            <p
                                v-if="s.price_per_hour != null"
                                class="mt-2 font-semibold text-[#e63946]"
                            >
                                ₹{{ s.price_per_hour }}/hr
                            </p>
                            <p
                                v-else-if="s.price_per_day != null"
                                class="mt-2 font-semibold text-[#e63946]"
                            >
                                ₹{{ s.price_per_day }}/day
                            </p>
                        </div>
                    </router-link>
                </div>
                <div
                    v-else
                    class="rounded-2xl border border-[#e5e7eb] bg-white p-8 text-center text-[#6b7280]"
                >
                    No featured studios yet.
                </div>
                <div
                    class="mt-10 rounded-2xl border-2 border-dashed border-[#e63946]/30 bg-white p-8 text-center"
                >
                    <h3 class="text-lg font-semibold text-[#1a1a1a]">
                        List your studio
                    </h3>
                    <p class="mt-2 text-sm text-[#6b7280]">
                        Reach thousands of brands and creators. List your space
                        on StarJD.
                    </p>
                    <router-link
                        to="/register?type=studio_owner"
                        class="mt-4 inline-block rounded-xl bg-[#e63946] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f]"
                        >Get started</router-link
                    >
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section
            id="how-it-works"
            class="animate-on-scroll border-t border-[#e5e7eb] bg-white px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <h2 class="section-title mb-2 text-2xl font-bold md:text-3xl">
                    How It Works
                </h2>
                <p class="section-subtitle mb-14 text-[#6b7280]">
                    Three simple steps to get started.
                </p>
                <div class="grid gap-10 md:grid-cols-3">
                    <div
                        v-for="(step, i) in steps"
                        :key="step.title"
                        class="step-card rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-8 transition hover:border-[#e63946]/30 hover:shadow-lg"
                    >
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#e63946] text-2xl font-bold text-white"
                        >
                            {{ i + 1 }}
                        </div>
                        <h3 class="text-xl font-semibold text-[#1a1a1a]">
                            {{ step.title }}
                        </h3>
                        <p class="mt-2 text-[#6b7280]">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Grid cards – animated grid section -->
        <section
            id="grid-cards"
            class="grid-cards-section animate-on-scroll border-t border-[#e5e7eb] bg-[#fafaf9] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-7xl">
                <div class="grid-cards-header mb-12 text-center">
                    <span
                        class="grid-cards-badge mb-3 inline-block rounded-full bg-[#e63946]/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-[#e63946]"
                        >Why StarJD</span
                    >
                    <h2
                        class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                    >
                        Built for Creators & Brands
                    </h2>
                    <p
                        class="section-subtitle mx-auto mt-2 max-w-2xl text-[#6b7280]"
                    >
                        Everything you need to connect, collaborate, and grow.
                    </p>
                </div>
                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <article
                        v-for="(card, i) in gridCards"
                        :key="card.id"
                        class="grid-card-item cursor-link group overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-sm transition-all duration-300 hover:border-[#e63946]/40 hover:shadow-xl"
                    >
                        <div
                            class="relative aspect-[16/10] overflow-hidden bg-[#f3f4f6]"
                        >
                            <img
                                :src="card.image"
                                :alt="card.title"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            />
                            <div
                                class="absolute bottom-0 left-0 right-0 flex items-center justify-center p-4"
                            >
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/95 text-[#e63946] shadow-lg transition group-hover:scale-110"
                                    aria-hidden="true"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3
                                class="font-bold text-[#1a1a1a] transition group-hover:text-[#e63946]"
                            >
                                {{ card.title }}
                            </h3>
                            <p
                                class="mt-2 text-sm leading-relaxed text-[#6b7280]"
                            >
                                {{ card.desc }}
                            </p>
                            <a
                                :href="card.url"
                                class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#e63946] transition group-hover:gap-3"
                            >
                                {{ card.cta }}
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"
                                    />
                                </svg>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- YouTube videos – featured video + sidebar list -->
        <section
            id="videos"
            class="videos-section animate-on-scroll border-t border-[#e5e7eb] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <div class="videos-section-header mb-10 text-center">
                    <span
                        class="videos-section-badge mb-3 inline-block rounded-full bg-[#e63946]/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-[#e63946]"
                        >Watch & Learn</span
                    >
                    <h2
                        class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                    >
                        Video Guides & Success Stories
                    </h2>
                    <p
                        class="section-subtitle mx-auto mt-2 max-w-2xl text-[#6b7280]"
                    >
                        Influencer marketing tips and how-to guides from
                        experts.
                    </p>
                </div>
                <div
                    class="videos-section-layout flex flex-col gap-8 lg:flex-row lg:gap-10"
                >
                    <!-- Featured video (large embed) -->
                    <div class="videos-featured flex-1 shrink-0 lg:min-w-0">
                        <div
                            v-if="youtubeVideos.length"
                            class="overflow-hidden rounded-2xl bg-[#1a1a1a] shadow-xl ring-2 ring-[#e5e7eb]"
                        >
                            <div class="relative aspect-video">
                                <iframe
                                    :key="selectedVideoIndex"
                                    :src="
                                        youtubeVideos[selectedVideoIndex]
                                            .embedUrl
                                    "
                                    :title="
                                        youtubeVideos[selectedVideoIndex].title
                                    "
                                    class="absolute inset-0 h-full w-full"
                                    allow="
                                        accelerometer;
                                        autoplay;
                                        clipboard-write;
                                        encrypted-media;
                                        gyroscope;
                                        picture-in-picture;
                                        web-share;
                                    "
                                    allowfullscreen
                                />
                            </div>
                            <div
                                class="border-t border-[#e5e7eb] bg-white p-4 md:p-5"
                            >
                                <h3 class="font-semibold text-[#1a1a1a]">
                                    {{
                                        youtubeVideos[selectedVideoIndex].title
                                    }}
                                </h3>
                                <p
                                    v-if="
                                        youtubeVideos[selectedVideoIndex].desc
                                    "
                                    class="mt-1 text-sm text-[#6b7280]"
                                >
                                    {{ youtubeVideos[selectedVideoIndex].desc }}
                                </p>
                                <a
                                    :href="
                                        youtubeVideos[selectedVideoIndex]
                                            .watchUrl
                                    "
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="cursor-link mt-3 inline-flex items-center gap-2 rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#c1121f]"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                        />
                                    </svg>
                                    Watch on YouTube
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Sidebar: clickable list – play in main screen -->
                    <div class="videos-sidebar w-full lg:w-[340px] lg:shrink-0">
                        <div
                            class="rounded-2xl border border-[#e5e7eb] bg-white p-3 shadow-sm"
                        >
                            <p
                                class="mb-3 px-2 text-xs font-semibold uppercase tracking-wider text-[#6b7280]"
                            >
                                More videos
                            </p>
                            <button
                                v-for="(video, i) in youtubeVideos"
                                :key="i"
                                type="button"
                                class="cursor-link videos-sidebar-item group flex w-full gap-3 rounded-xl p-3 text-left transition hover:bg-[#fafaf9]"
                                :class="{
                                    'bg-[#e63946]/5 ring-1 ring-[#e63946]/30':
                                        selectedVideoIndex === i,
                                }"
                                @click="selectedVideoIndex = i"
                            >
                                <div
                                    class="relative h-16 w-28 shrink-0 overflow-hidden rounded-lg bg-[#1a1a1a]"
                                >
                                    <img
                                        :src="`https://img.youtube.com/vi/${video.videoId}/mqdefault.jpg`"
                                        :alt="video.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <span
                                        class="absolute inset-0 flex items-center justify-center bg-black/40 transition group-hover:bg-black/50"
                                    >
                                        <svg
                                            class="h-8 w-8 text-white"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4
                                        class="line-clamp-2 text-sm font-medium text-[#1a1a1a]"
                                    >
                                        {{ video.title }}
                                    </h4>
                                    <span
                                        class="mt-0.5 inline-block text-xs font-medium text-[#e63946]"
                                        >Watch →</span
                                    >
                                </div>
                            </button>
                            <router-link
                                to="/videos"
                                class="cursor-link mt-3 block rounded-xl border border-[#e5e7eb] px-4 py-2.5 text-center text-sm font-medium text-[#e63946] transition hover:bg-[#e63946]/5 hover:border-[#e63946]/50"
                            >
                                View all videos →
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Articles – featured hero + list rows -->
        <section
            id="articles"
            class="articles-section animate-on-scroll border-t border-[#e5e7eb] bg-[#fafaf9] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <div
                    class="articles-section-header mb-12 flex flex-wrap items-end justify-between gap-4"
                >
                    <div>
                        <h2
                            class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                        >
                            Articles & Resources
                        </h2>
                        <p class="section-subtitle mt-1 text-[#6b7280]">
                            Tips, trends, and insights for creators and brands.
                        </p>
                    </div>
                    <a
                        href="#"
                        class="text-sm font-semibold text-[#e63946] transition hover:underline"
                        >View All</a
                    >
                </div>
                <!-- Featured article (first): horizontal card -->
                <a
                    :href="articles[0].url"
                    class="articles-featured cursor-link group mb-10 flex flex-col overflow-hidden rounded-3xl border-2 border-[#e5e7eb] bg-white shadow-md transition hover:border-[#e63946]/50 hover:shadow-xl md:flex-row"
                >
                    <div
                        class="articles-featured-image relative h-56 w-full overflow-hidden bg-[#f3f4f6] md:h-auto md:min-h-[280px] md:w-5/12"
                    >
                        <img
                            :src="articles[0].image"
                            :alt="articles[0].title"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <span
                            class="absolute left-4 top-4 rounded-lg bg-[#1a1a1a] px-3 py-1.5 text-xs font-semibold text-white"
                            >Featured</span
                        >
                    </div>
                    <div
                        class="articles-featured-body flex flex-1 flex-col justify-center p-6 md:p-8"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-[#6b7280]"
                        >
                            {{ articles[0].category }} · {{ articles[0].date }}
                        </p>
                        <h3
                            class="mt-2 text-xl font-bold text-[#1a1a1a] transition group-hover:text-[#e63946] md:text-2xl"
                        >
                            {{ articles[0].title }}
                        </h3>
                        <p class="mt-3 text-[#6b7280]">
                            {{ articles[0].excerpt }}
                        </p>
                        <span
                            class="mt-4 inline-flex items-center gap-2 font-semibold text-[#e63946]"
                        >
                            Read article
                            <svg
                                class="h-5 w-5 transition group-hover:translate-x-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"
                                />
                            </svg>
                        </span>
                    </div>
                </a>
                <!-- List of remaining articles (rows: thumb + content) -->
                <div class="articles-list space-y-4">
                    <a
                        v-for="article in articles.slice(1)"
                        :key="article.id"
                        :href="article.url"
                        class="articles-list-item cursor-link group flex gap-4 rounded-2xl border border-[#e5e7eb] bg-white p-4 transition hover:border-[#e63946]/40 hover:shadow-lg md:gap-6 md:p-5"
                    >
                        <div
                            class="relative h-24 w-32 shrink-0 overflow-hidden rounded-xl bg-[#f3f4f6] md:h-28 md:w-40"
                        >
                            <img
                                :src="article.image"
                                :alt="article.title"
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="rounded-md bg-[#e63946]/10 px-2 py-0.5 text-xs font-medium text-[#e63946]"
                                    >{{ article.category }}</span
                                >
                                <span class="text-xs text-[#6b7280]">{{
                                    article.date
                                }}</span>
                            </div>
                            <h3
                                class="mt-1.5 font-semibold text-[#1a1a1a] transition group-hover:text-[#e63946]"
                            >
                                {{ article.title }}
                            </h3>
                            <p class="mt-1 line-clamp-2 text-sm text-[#6b7280]">
                                {{ article.excerpt }}
                            </p>
                        </div>
                        <span
                            class="hidden shrink-0 self-center text-[#e63946] md:inline-block"
                            aria-hidden="true"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Trust badges -->
        <section class="border-t border-[#e5e7eb] px-4 py-12">
            <div
                class="mx-auto flex max-w-4xl flex-wrap items-center justify-center gap-8 text-center text-sm text-[#6b7280]"
            >
                <div class="flex items-center gap-2">
                    <span class="text-2xl">✓</span> <span>No upfront cost</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl">✓</span> <span>Vetted creators</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl">✓</span> <span>Instant chat</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl">✓</span> <span>Secure payments</span>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section
            id="testimonials"
            class="animate-on-scroll border-t border-[#e5e7eb] bg-[#fafaf9] px-4 py-10 md:py-16"
        >
            <div class="mx-auto max-w-6xl">
                <h2 class="section-title mb-2 text-2xl font-bold md:text-3xl">
                    What People Say
                </h2>
                <p class="section-subtitle mb-12 text-[#6b7280]">
                    Demo testimonials.
                </p>
                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        v-for="t in testimonials"
                        :key="t.name"
                        class="testimonial-card rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm"
                    >
                        <p class="text-[#1a1a1a]">"{{ t.quote }}"</p>
                        <div class="mt-4 flex items-center gap-3">
                            <img
                                :src="t.avatar"
                                :alt="t.name"
                                class="h-10 w-10 rounded-full object-cover"
                            />
                            <div>
                                <p class="font-semibold text-[#1a1a1a]">
                                    {{ t.name }}
                                </p>
                                <p class="text-sm text-[#6b7280]">
                                    {{ t.role }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA with Join options -->
        <section class="border-t border-[#e5e7eb] px-4 py-12 md:py-16">
            <div class="mx-auto max-w-4xl">
                <div
                    class="rounded-3xl bg-[#1a1a1a] p-10 text-center text-white md:p-16"
                >
                    <h2 class="text-2xl font-bold md:text-3xl">
                        Ready to get started?
                    </h2>
                    <p class="mt-4 text-white/80">
                        Join as a creator or a brand — your choice.
                    </p>
                    <div
                        class="mt-10 flex flex-wrap items-center justify-center gap-4"
                    >
                        <a
                            href="#"
                            class="cursor-link join-creator inline-block rounded-xl bg-[#10b981] px-8 py-3 font-semibold text-white transition hover:bg-[#059669]"
                            >Join as Creator</a
                        >
                        <a
                            href="#"
                            class="join-brand inline-block rounded-xl border-2 border-white bg-transparent px-8 py-3 font-semibold text-white transition hover:bg-white hover:text-[#1a1a1a]"
                            >Join as Brand</a
                        >
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ section (above footer); extra pb so CTA banner can overlap half above -->
        <section
            id="faq"
            class="animate-on-scroll border-t border-[#e5e7eb] bg-[#fafaf9] px-4 py-16 pb-20 md:py-24 md:pb-28"
        >
            <div class="mx-auto max-w-3xl">
                <h2
                    class="section-title mb-10 text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                >
                    Frequently Asked Questions
                </h2>
                <div class="space-y-3">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="faq-item cursor-link overflow-hidden rounded-xl border border-[#e5e7eb] bg-white transition hover:border-[#e63946]/30"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-semibold text-[#1a1a1a] transition hover:text-[#e63946]"
                            @click="toggleFaq(index)"
                        >
                            <span>{{ faq.question }}</span>
                            <span
                                class="shrink-0 transition-transform"
                                :class="{ 'rotate-180': openFaq === index }"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </span>
                        </button>
                        <Transition name="faq-slide">
                            <div
                                v-show="openFaq === index"
                                class="border-t border-[#e5e7eb] px-5 pb-4 pt-2"
                            >
                                <p class="text-sm text-[#6b7280]">
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer (hidden when used inside AppLayout for home) -->
        <Footer v-if="!noHeaderFooter" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import Footer from "@/components/Footer.vue";

defineProps({ noHeaderFooter: { type: Boolean, default: false } });
import { useRouter } from "vue-router";
import axios from "axios";

const activePlatform = ref("All");
const activeCascadeIndex = ref(null);
const openFaq = ref(null);
const headerRef = ref(null);
const navServicesRef = ref(null);
const navUserMenuRef = ref(null);
const navUser = ref(null);
const navServices = ref([]);
const navUserMenuOpen = ref(false);
const navServicesOpen = ref(false);
const navMobileOpen = ref(false);
const platforms = ["All", "Instagram", "TikTok", "YouTube", "UGC", "Twitter"];

function onNavUserMenuClickOutside(e) {
    if (navUserMenuRef.value && !navUserMenuRef.value.contains(e.target)) {
        navUserMenuOpen.value = false;
    }
    if (navServicesRef.value && !navServicesRef.value.contains(e.target)) {
        navServicesOpen.value = false;
    }
}

function navLogout() {
    navUserMenuOpen.value = false;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    axios
        .post("/api/logout", {}, { withCredentials: true, headers: token ? { 'X-CSRF-TOKEN': token } : {} })
        .then(() => {
            navUser.value = null;
            navMobileOpen.value = false;
            window.location.href = "/login";
        })
        .catch(() => {
            navUser.value = null;
            navMobileOpen.value = false;
            window.location.href = "/login";
        });
}

/* Hero wallpaper: 5–7 soft images as background (theme: creator, lifestyle, pastel) */
const heroWallpaperImages = [
    {
        src: "https://images.unsplash.com/photo-1557804506-669a67965ba0?w=800&q=80",
    },
    {
        src: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80",
    },
    {
        src: "https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&q=80",
    },
    {
        src: "https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&q=80",
    },
    {
        src: "https://images.unsplash.com/photo-1556761175-b413da4baf72?w=800&q=80",
    },
    {
        src: "https://images.unsplash.com/photo-1552581234-26160f608093?w=800&q=80",
    },
    {
        src: "https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&q=80",
    },
];
/* Duplicated for seamless infinite scroll */
const heroWallpaperScrollImages = [
    ...heroWallpaperImages,
    ...heroWallpaperImages,
];

const router = useRouter();
const heroData = ref(null);
const heroSearchQuery = ref("");
const heroSearchPlatform = ref("");
const heroSearchCategory = ref("");
const heroSearchGender = ref("");
const heroSearchLanguage = ref("");
const heroSearchMinRate = ref("");
const heroFilterOptions = ref({
    categories: [],
    genders: {},
    languages: [],
    platforms: {},
});

/* Platform options from API, with fallback so dropdown works before API loads */
const heroPlatformOptions = computed(() => {
    const p = heroFilterOptions.value.platforms;
    if (p && typeof p === "object" && Object.keys(p).length > 0) return p;
    return {
        instagram: "Instagram",
        facebook: "Facebook",
        youtube: "YouTube",
        tiktok: "TikTok",
        twitter: "Twitter / X",
        linkedin: "LinkedIn",
        snapchat: "Snapchat",
        pinterest: "Pinterest",
        other: "Other",
    };
});

function isInternalHeroUrl(url) {
    if (!url || typeof url !== "string") return false;
    return !url.startsWith("#") && !url.startsWith("http");
}
function cascadeHasLink(img) {
    return img?.link && String(img.link).trim() !== "";
}
function submitHeroSearch() {
    const q = {};
    if (heroSearchQuery.value?.trim()) q.search = heroSearchQuery.value.trim();
    if (heroSearchPlatform.value) q.platform = heroSearchPlatform.value;
    if (heroSearchCategory.value) q.category = heroSearchCategory.value;
    if (heroSearchGender.value) q.gender = heroSearchGender.value;
    if (heroSearchLanguage.value) q.language = heroSearchLanguage.value;
    if (heroSearchMinRate.value !== "" && heroSearchMinRate.value != null)
        q.min_rate = heroSearchMinRate.value;
    router.push({ path: "/creators", query: q });
}
function clearHeroSearch() {
    heroSearchQuery.value = "";
    heroSearchPlatform.value = "";
    heroSearchCategory.value = "";
    heroSearchGender.value = "";
    heroSearchLanguage.value = "";
    heroSearchMinRate.value = "";
    router.push({ path: "/creators" });
}

const heroWallpaperScrollImagesComputed = computed(() => {
    const imgs = heroData.value?.wallpaper_images?.length
        ? heroData.value.wallpaper_images
        : heroWallpaperImages;
    return [...imgs, ...imgs];
});
const galleryCascadeImagesComputed = computed(() => {
    const imgs = heroData.value?.cascade_images?.length
        ? heroData.value.cascade_images
        : galleryCascadeImages;
    return imgs;
});

/* Portrait images for hero gallery cascade – creator/lifestyle theme */
const galleryCascadeImages = [
    {
        src: "https://later.com/static/1b915d30aa4b21839dc37f1d8cc31aad/4b779/inside-l.webp",
        alt: "Inside left",
    },
    {
        src: "https://later.com/static/9ab27d0ea1dad250c3b3139da588a4d3/102bb/middle.webp",
        alt: "Middle",
    },
    {
        src: "https://later.com/static/447307fa7254533db7b69f9261a8842e/435a7/inside-r.webp",
        alt: "Inside right",
    },
    {
        src: "https://later.com/static/f789766aa86aec8fde2c5f78ed0b4cf8/7980a/outer-r.webp",
        alt: "Outer right",
    },
    {
        src: "https://later.com/static/53dc907229ca6b860ea26841d00f39dc/9b018/outer-l.webp",
        alt: "Outer left",
    },
];

function getCascadeStyle(index) {
    const rotations = [-6, -2, 0, 2, 6];
    const isActive = activeCascadeIndex.value === index;
    const transform = isActive
        ? "rotate(0deg) scale(1.08)"
        : `rotate(${rotations[index]}deg) scale(1)`;
    return {
        zIndex: isActive ? 20 : 5 + index,
        transform,
        marginLeft: index === 0 ? 0 : "-48px",
        marginBottom: index === 5 ? "0" : "-15px",
    };
}

const heroCollageCards = [
    {
        src: "https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=300&h=300&fit=crop",
        alt: "Social media",
        label: "Brand",
        class: "hero-collage-card-1",
        delay: "0s",
    },
    {
        src: "https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=300&h=300&fit=crop",
        alt: "Content creation",
        label: "UGC",
        class: "hero-collage-card-2",
        delay: "0.2s",
    },
    {
        src: "https://images.unsplash.com/photo-1611262588024-d12430b98920?w=300&h=300&fit=crop",
        alt: "Instagram",
        label: "IG",
        class: "hero-collage-card-3",
        delay: "0.4s",
    },
    {
        src: "https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=300&h=300&fit=crop",
        alt: "Video content",
        label: "YT",
        class: "hero-collage-card-4",
        delay: "0.1s",
    },
    {
        src: "https://images.unsplash.com/photo-1611605698335-8b1569810432?w=300&h=300&fit=crop",
        alt: "Creator",
        label: "TikTok",
        class: "hero-collage-card-5",
        delay: "0.3s",
    },
];

const categories = ref([
    {
        name: "Fashion",
        count: "1.2k",
        image: "https://picsum.photos/seed/fashion/400/500",
    },
    {
        name: "Beauty",
        count: "890",
        image: "https://picsum.photos/seed/beauty/400/500",
    },
    {
        name: "Tech",
        count: "650",
        image: "https://picsum.photos/seed/tech/400/500",
    },
    {
        name: "Travel",
        count: "720",
        image: "https://picsum.photos/seed/travel/400/500",
    },
]);

const featuredCreators = ref([]);
const featuredStudios = ref([]);
const partners = ref([]);

const steps = ref([
    {
        title: "Search Creators",
        desc: "Browse thousands of vetted creators. Filter by niche, location, and budget.",
    },
    {
        title: "Chat & Book",
        desc: "Message creators directly and book packages. Payment is held securely until delivery.",
    },
    {
        title: "Get Content",
        desc: "Receive high-quality content on time. Approve and download from your dashboard.",
    },
]);

const gridCards = [
    {
        id: 1,
        title: "Discover Creators",
        desc: "Search by niche, platform, and budget. Find vetted creators that match your brand.",
        image: "https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=600&h=380&fit=crop",
        url: "#featured",
        cta: "Browse creators",
    },
    {
        id: 2,
        title: "Secure Payments",
        desc: "Payments held in escrow until you approve content. Creators get paid on delivery.",
        image: "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&h=380&fit=crop",
        url: "#how-it-works",
        cta: "How it works",
    },
    {
        id: 3,
        title: "Direct Messaging",
        desc: "Chat with creators, share briefs, and collaborate in one place. No back-and-forth emails.",
        image: "https://images.unsplash.com/photo-1557804506-669a67965ba0?w=600&h=380&fit=crop",
        url: "#join-brand",
        cta: "Get started",
    },
    {
        id: 4,
        title: "Campaign Tracking",
        desc: "Track projects, approvals, and content delivery from your dashboard.",
        image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=380&fit=crop",
        url: "#",
        cta: "Learn more",
    },
    {
        id: 5,
        title: "Creator Profiles",
        desc: "Set your rates, showcase your work, and get discovered by brands worldwide.",
        image: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=380&fit=crop",
        url: "#join-creator",
        cta: "Join as creator",
    },
    {
        id: 6,
        title: "Content Library",
        desc: "Download approved content and reuse across ads, social, and campaigns.",
        image: "https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=600&h=380&fit=crop",
        url: "#",
        cta: "See features",
    },
];

const youtubeVideos = ref([
    {
        videoId: "CaZxAzb7PPM",
        title: "Influencer Marketing Guide",
        embedUrl: "https://www.youtube.com/embed/CaZxAzb7PPM",
        watchUrl: "https://www.youtube.com/watch?v=CaZxAzb7PPM",
        desc: "Learn how to run effective influencer campaigns.",
    },
    {
        videoId: "K1ImD48UqNQ",
        title: "Creator Success Stories",
        embedUrl: "https://www.youtube.com/embed/K1ImD48UqNQ",
        watchUrl: "https://www.youtube.com/watch?v=K1ImD48UqNQ",
        desc: "Hear from brands and creators who found success.",
    },
    {
        videoId: "c64h-K74bFI",
        title: "UGC & Brand Collabs",
        embedUrl: "https://www.youtube.com/embed/c64h-K74bFI",
        watchUrl: "https://www.youtube.com/watch?v=c64h-K74bFI",
        desc: "How to create authentic UGC content with brands.",
    },
    {
        videoId: "5qap5aO4i9A",
        title: "How to Find the Right Creators",
        embedUrl: "https://www.youtube.com/embed/5qap5aO4i9A",
        watchUrl: "https://www.youtube.com/watch?v=5qap5aO4i9A",
        desc: "Tips for discovering creators that fit your brand.",
    },
    {
        videoId: "jNQXAC9IVRw",
        title: "Pricing & Negotiation Tips",
        embedUrl: "https://www.youtube.com/embed/jNQXAC9IVRw",
        watchUrl: "https://www.youtube.com/watch?v=jNQXAC9IVRw",
        desc: "Setting rates and working with budgets.",
    },
    {
        videoId: "9bZkp7q19f0",
        title: "Content Brief Best Practices",
        embedUrl: "https://www.youtube.com/embed/9bZkp7q19f0",
        watchUrl: "https://www.youtube.com/watch?v=9bZkp7q19f0",
        desc: "Writing briefs that get great content.",
    },
]);
const selectedVideoIndex = ref(0);

const articles = [
    {
        id: 1,
        title: "How to Pitch Brands as a Creator",
        excerpt:
            "Stand out with a strong media kit and clear rates. Learn what brands look for and how to land your first collab.",
        category: "For Creators",
        date: "Feb 12, 2025",
        image: "https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=600&h=380&fit=crop",
        url: "#",
    },
    {
        id: 2,
        title: "Influencer Marketing ROI: What to Measure",
        excerpt:
            "Track the metrics that matter—engagement, reach, conversions—and report results brands care about.",
        category: "For Brands",
        date: "Feb 10, 2025",
        image: "https://images.unsplash.com/photo-1557804506-669a67965ba0?w=600&h=380&fit=crop",
        url: "#",
    },
    {
        id: 3,
        title: "UGC Content That Converts",
        excerpt:
            "User-generated content drives trust. Here’s how to brief creators and get UGC that performs on social and ads.",
        category: "Strategy",
        date: "Feb 8, 2025",
        image: "https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=600&h=380&fit=crop",
        url: "#",
    },
    {
        id: 4,
        title: "Setting Your Rates as a Micro-Influencer",
        excerpt:
            "A practical guide to pricing your time, content, and reach without underselling or overpricing.",
        category: "For Creators",
        date: "Feb 5, 2025",
        image: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=380&fit=crop",
        url: "#",
    },
    {
        id: 5,
        title: "TikTok vs Instagram: Where to Invest in 2025",
        excerpt:
            "Compare reach, engagement, and brand fit so you can allocate budget where it pays off.",
        category: "Trends",
        date: "Feb 3, 2025",
        image: "https://images.unsplash.com/photo-1611262588024-d12430b98920?w=600&h=380&fit=crop",
        url: "#",
    },
    {
        id: 6,
        title: "Building Long-Term Creator Relationships",
        excerpt:
            "Why one-off campaigns fall short and how to turn creators into ongoing partners.",
        category: "For Brands",
        date: "Jan 28, 2025",
        image: "https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&h=380&fit=crop",
        url: "#",
    },
];

const testimonials = ref([
    {
        quote: "Super easy to find creators that fit our brand. Saved us so much time.",
        name: "Demo User",
        role: "Brand Manager",
        avatar: "https://picsum.photos/seed/t1/80/80",
    },
    {
        quote: "The platform is intuitive and the creators are professional. Highly recommend.",
        name: "Demo Client",
        role: "Marketing Lead",
        avatar: "https://picsum.photos/seed/t2/80/80",
    },
    {
        quote: "We scaled our influencer campaigns 3x in six months. Game changer.",
        name: "Demo Founder",
        role: "Founder",
        avatar: "https://picsum.photos/seed/t3/80/80",
    },
]);

const faqs = ref([
    {
        question: "What is influencer marketing?",
        answer: "Influencer marketing is a form of marketing where brands collaborate with social media influencers and content creators to promote products or services.",
    },
    {
        question: "How does StarJD work?",
        answer: "StarJD connects brands with vetted creators. Brands search by platform, category, and budget; message creators; and book packages. Payment is held securely until content is delivered.",
    },
    {
        question: "How much does it cost?",
        answer: "Costs vary by creator reach and platform. Many creators offer packages starting under ₹5,000. No upfront platform fee — you pay per project.",
    },
    {
        question: "Which platforms are supported?",
        answer: "We support Instagram, TikTok, YouTube, Twitter, and UGC creators. Filter by platform in the search bar to find the right fit.",
    },
]);

function toggleFaq(index) {
    openFaq.value = openFaq.value === index ? null : index;
}

onMounted(() => {
    axios
        .get("/api/me", { withCredentials: true })
        .then((r) => {
            navUser.value = r.data;
        })
        .catch(() => {
            navUser.value = null;
        });
    axios
        .get("/api/services")
        .then((r) => {
            navServices.value = r.data ?? [];
        })
        .catch(() => {
            navServices.value = [];
        });
    document.addEventListener("click", onNavUserMenuClickOutside);
    axios
        .get("/api/studios", { params: { featured: 1, per_page: 4 } })
        .then((r) => {
            const data = r.data?.data ?? r.data ?? [];
            featuredStudios.value = Array.isArray(data)
                ? data
                : (data?.data ?? []);
        })
        .catch(() => {
            featuredStudios.value = [];
        });
    axios
        .get("/api/creators", { params: { featured: 1, per_page: 8 } })
        .then((r) => {
            const data = r.data?.data ?? r.data ?? [];
            const list = Array.isArray(data)
                ? data
                : data && data.data
                  ? data.data
                  : [];
            featuredCreators.value = list.map((p) => ({
                id: p.id,
                slug: p.slug,
                name: p.user?.name,
                image: p.avatar_url,
                tagline: p.tagline,
                price: p.min_rate,
                location: p.location,
                category: p.category,
            }));
        })
        .catch(() => {});
    if (typeof window === "undefined") return;
    axios
        .get("/api/sections")
        .then((r) => {
            const d = r.data;
            if (d.categories?.length) categories.value = d.categories;
            if (d.steps?.length) steps.value = d.steps;
            if (d.testimonials?.length) testimonials.value = d.testimonials;
            if (d.faqs?.length) faqs.value = d.faqs;
            if (d.hero && Object.keys(d.hero).length) heroData.value = d.hero;
            if (d.partners?.length) partners.value = d.partners;
        })
        .catch(() => {});
    axios
        .get("/api/creators/options/filters")
        .then((r) => {
            heroFilterOptions.value.categories = r.data.categories ?? [];
            heroFilterOptions.value.genders = r.data.genders ?? {};
            heroFilterOptions.value.languages = r.data.languages ?? [];
            heroFilterOptions.value.platforms = r.data.platforms ?? {};
        })
        .catch(() => {});
    axios
        .get("/api/videos")
        .then((r) => {
            if (r.data?.videos?.length) youtubeVideos.value = r.data.videos;
        })
        .catch(() => {});
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) entry.target.classList.add("in-view");
            });
        },
        { threshold: 0.1, rootMargin: "0px 0px -40px 0px" },
    );
    document
        .querySelectorAll(".animate-on-scroll")
        .forEach((el) => observer.observe(el));
});

onBeforeUnmount(() => {
    document.removeEventListener("click", onNavUserMenuClickOutside);
});
</script>

<style scoped>
.partners-marquee-track {
    mask-image: linear-gradient(
        to right,
        transparent,
        black 8%,
        black 92%,
        transparent
    );
    -webkit-mask-image: linear-gradient(
        to right,
        transparent,
        black 8%,
        black 92%,
        transparent
    );
}
.partners-marquee-inner {
    display: flex;
    width: max-content;
    animation: partners-scroll 40s linear infinite;
}
.partners-marquee-inner:hover {
    animation-play-state: paused;
}
@keyframes partners-scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}
</style>
