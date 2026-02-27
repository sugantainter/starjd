<template>
    <header
        ref="headerRef"
        class="sticky top-0 z-50 w-full max-w-[calc(100%-3rem)] mx-auto"
    >
        <nav
            class="nav-bar nav-bar-bg flex items-center w-full h-auto rounded-[10rem] border-b-0 justify-between px-3 py-2 sm:px-8 sm:py-2"
        >
            <!-- Logo – left -->
            <router-link
                to="/"
                class="flex shrink-0 items-center"
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
                    class="hidden text-xl font-bold tracking-tight text-[#1a1a1a]"
                    >StarJD</span
                >
            </router-link>

            <!-- Right: menu toggle (mobile) + links & buttons -->
            <div class="flex items-center gap-6 md:gap-8 ml-auto">
                <!-- Mobile / tablet menu toggle -->
                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-lg border border-[#e5e7eb] bg-white text-[#1a1a1a] transition hover:border-[#fc4402] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#fc4402]/20 md:hidden"
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

                <!-- Desktop nav -->
                <div class="hidden items-center gap-4 md:flex">
                    <router-link
                        to="/"
                        class="text-sm font-semibold transition hover:text-[#fc4402]"
                        >Home</router-link
                    >
                    <router-link
                        to="/videos"
                        class="text-sm font-semibold transition hover:text-[#fc4402]"
                        >Brands</router-link
                    >
                    <div
                        ref="creatorsRef"
                        class="relative"
                        @mouseenter="creatorsDropdownOpen = true"
                        @mouseleave="creatorsDropdownOpen = false"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-sm font-semibold transition hover:text-[#fc4402]"
                            :class="{ 'text-[#fc4402]': creatorsDropdownOpen }"
                            aria-haspopup="true"
                            :aria-expanded="creatorsDropdownOpen"
                            @click="
                                creatorsDropdownOpen = !creatorsDropdownOpen
                            "
                        >
                            Creators
                            <svg
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="{ 'rotate-180': creatorsDropdownOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
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
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="opacity-0 translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 translate-y-1"
                        >
                            <div
                                v-show="creatorsDropdownOpen"
                                class="absolute left-1/2 top-full z-50 mt-2 w-[min(95vw,520px)] -translate-x-1/2 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-xl"
                            >
                                <div class="flex min-h-[260px]">
                                    <!-- Left: creator categories -->
                                    <div
                                        class="flex flex-1 flex-col border-r border-[#e5e7eb] py-4"
                                    >
                                        <span
                                            class="mb-2 inline-block px-4 text-xs font-semibold uppercase tracking-wide text-[#fc4402]"
                                            >Discover Creators</span
                                        >
                                        <div class="flex flex-col gap-0.5 px-4">
                                            <router-link
                                                v-for="(
                                                    item, idx
                                                ) in creatorCategories"
                                                :key="'cat-' + idx"
                                                :to="
                                                    item.slug
                                                        ? {
                                                              path: '/creators',
                                                              query: {
                                                                  category:
                                                                      item.slug,
                                                              },
                                                          }
                                                        : '/creators'
                                                "
                                                class="block py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                                @click="
                                                    creatorsDropdownOpen = false
                                                "
                                            >
                                                {{ item.name }}
                                            </router-link>
                                        </div>
                                    </div>
                                    <!-- Right: image -->
                                    <router-link
                                        to="/creators"
                                        class="flex w-[48%] min-w-[180px] shrink-0 overflow-hidden bg-[#f5f5f4] transition hover:bg-[#eee]"
                                        @click="creatorsDropdownOpen = false"
                                    >
                                        <div
                                            class="aspect-[4/5] w-full overflow-hidden bg-[#e5e7eb]"
                                        >
                                            <img
                                                v-if="creatorsDropdownImage"
                                                :src="creatorsDropdownImage"
                                                alt="Creators"
                                                class="h-full w-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#fc4402]/15 to-[#1a1a1a]/10"
                                            >
                                                <span
                                                    class="text-2xl font-bold text-[#fc4402]/40"
                                                    >StarJD</span
                                                >
                                            </div>
                                        </div>
                                    </router-link>
                                </div>
                            </div>
                        </Transition>
                    </div>
                    <router-link
                        to="/creators"
                        class="text-sm font-semibold transition hover:text-[#fc4402]"
                        >Campaign</router-link
                    >

                    <div
                        ref="servicesRef"
                        class="relative"
                        @mouseenter="servicesDropdownOpen = true"
                        @mouseleave="servicesDropdownOpen = false"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-sm font-semibold transition hover:text-[#fc4402]"
                            :class="{ 'text-[#fc4402]': servicesDropdownOpen }"
                            aria-haspopup="true"
                            :aria-expanded="servicesDropdownOpen"
                            @click="
                                servicesDropdownOpen = !servicesDropdownOpen
                            "
                        >
                            Services
                            <svg
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="{ 'rotate-180': servicesDropdownOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
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
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="opacity-0 translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 translate-y-1"
                        >
                            <div
                                v-show="servicesDropdownOpen"
                                class="absolute left-1/2 top-full z-50 mt-2 w-[min(95vw,600px)] -translate-x-1/2 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-xl"
                            >
                                <div class="flex min-h-[280px]">
                                    <!-- Left column: All Services heading + 5 options -->
                                    <div
                                        class="flex flex-1 flex-col border-r border-[#e5e7eb] py-4"
                                    >
                                        <span
                                            class="mb-2 inline-block px-4 text-xs font-semibold uppercase tracking-wide text-[#fc4402]"
                                            >All Services</span
                                        >
                                        <div class="flex flex-col gap-0.5 px-4">
                                            <router-link
                                                v-for="(
                                                    item, idx
                                                ) in servicesColumn1"
                                                :key="'c1-' + idx"
                                                :to="'/services/' + item.slug"
                                                class="block py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                                @click="
                                                    servicesDropdownOpen = false
                                                "
                                            >
                                                {{ item.name }}
                                            </router-link>
                                        </div>
                                    </div>
                                    <!-- Middle column: 4 options -->
                                    <div
                                        class="flex flex-1 flex-col border-r border-[#e5e7eb] py-4"
                                    >
                                        <div class="flex flex-col gap-0.5 px-4">
                                            <router-link
                                                v-for="(
                                                    item, idx
                                                ) in servicesColumn2"
                                                :key="'c2-' + idx"
                                                :to="'/services/' + item.slug"
                                                class="block py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                                @click="
                                                    servicesDropdownOpen = false
                                                "
                                            >
                                                {{ item.name }}
                                            </router-link>
                                        </div>
                                    </div>
                                    <!-- Right: case study card with image -->
                                    <router-link
                                        to="/services"
                                        class="services-dropdown-card flex w-[52%] min-w-[200px] shrink-0 flex-col overflow-hidden bg-[#f5f5f4] p-4 transition hover:bg-[#eee]"
                                        @click="servicesDropdownOpen = false"
                                    >
                                        <span
                                            class="mb-2 inline-block text-xs font-semibold uppercase tracking-wide text-[#fc4402]"
                                            >Case study</span
                                        >
                                        <div class="flex flex-1 flex-col">
                                            <p
                                                class="text-lg font-bold text-[#1a1a1a]"
                                            >
                                                Driving brand love and sales
                                            </p>
                                            <p
                                                class="mt-1.5 text-sm leading-snug text-[#64748b]"
                                            >
                                                Learn how we helped brands
                                                launch creator campaigns with
                                                millions of impressions.
                                            </p>
                                        </div>
                                        <div
                                            class="mt-3 aspect-[16/10] overflow-hidden rounded-lg bg-[#e5e7eb]"
                                        >
                                            <img
                                                v-if="servicesDropdownImage"
                                                :src="servicesDropdownImage"
                                                alt="Case study"
                                                class="h-full w-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#fc4402]/15 to-[#1a1a1a]/10"
                                            >
                                                <span
                                                    class="text-3xl font-bold text-[#fc4402]/40"
                                                    >StarJD</span
                                                >
                                            </div>
                                        </div>
                                    </router-link>
                                </div>
                            </div>
                        </Transition>
                    </div>
                    <div
                        ref="ourWorkRef"
                        class="relative"
                        @mouseenter="ourWorkDropdownOpen = true"
                        @mouseleave="ourWorkDropdownOpen = false"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-sm font-semibold transition hover:text-[#fc4402]"
                            :class="{ 'text-[#fc4402]': ourWorkDropdownOpen }"
                            aria-haspopup="true"
                            :aria-expanded="ourWorkDropdownOpen"
                            @click="ourWorkDropdownOpen = !ourWorkDropdownOpen"
                        >
                            Our Work
                            <svg
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="{ 'rotate-180': ourWorkDropdownOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
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
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="opacity-0 translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 translate-y-1"
                        >
                            <div
                                v-show="ourWorkDropdownOpen"
                                class="absolute left-1/2 top-full z-50 mt-2 w-48 -translate-x-1/2 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white py-2 shadow-xl"
                            >
                                <router-link
                                    to="/blog"
                                    class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                    @click="ourWorkDropdownOpen = false"
                                    >Blog</router-link
                                >
                                <router-link
                                    to="/guide"
                                    class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                    @click="ourWorkDropdownOpen = false"
                                    >Guide</router-link
                                >
                                <router-link
                                    to="/videos"
                                    class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                    @click="ourWorkDropdownOpen = false"
                                    >Projects</router-link
                                >
                            </div>
                        </Transition>
                    </div>
                    <router-link
                        to="/about"
                        class="text-sm font-semibold transition hover:text-[#fc4402]"
                        >About</router-link
                    >
                    <router-link
                        to="/contact"
                        class="text-sm font-semibold transition hover:text-[#fc4402]"
                        >Contact</router-link
                    >

                    <template v-if="displayUser">
                        <div class="relative" ref="userMenuRef">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-lg border border-[#e5e7eb] bg-white px-4 py-2 text-sm font-medium text-[#1a1a1a] shadow-sm transition hover:border-[#fc4402] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#fc4402]/20"
                                :class="
                                    user.role === 'creator'
                                        ? 'hover:border-[#10b981] focus:ring-[#10b981]/20'
                                        : user.role === 'admin'
                                          ? 'hover:border-[#1e293b] focus:ring-[#1e293b]/20'
                                          : ''
                                "
                                @click="userMenuOpen = !userMenuOpen"
                            >
                                <span class="max-w-[120px] truncate">{{
                                    user.name
                                }}</span>
                                <svg
                                    class="h-4 w-4 shrink-0 text-[#64748b] transition-transform"
                                    :class="{ 'rotate-180': userMenuOpen }"
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
                                    v-show="userMenuOpen"
                                    class="absolute right-0 top-full z-50 mt-1 min-w-[200px] rounded-xl border border-[#e2e8f0] bg-white py-1 shadow-lg"
                                >
                                    <div
                                        class="border-b border-[#e2e8f0] px-4 py-2.5"
                                    >
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-[#64748b]"
                                        >
                                            {{ user.role }}
                                        </p>
                                        <p
                                            class="truncate text-sm font-medium text-[#1a1a1a]"
                                        >
                                            {{ user.name }}
                                        </p>
                                    </div>
                                    <template
                                        v-if="
                                            displayUser &&
                                            displayUser.role === 'creator'
                                        "
                                    >
                                        <router-link
                                            to="/creator/dashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                            @click="userMenuOpen = false"
                                            >Creator Dashboard</router-link
                                        >
                                        <router-link
                                            to="/creator/profile"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                            @click="userMenuOpen = false"
                                            >My Profile</router-link
                                        >
                                    </template>
                                    <template v-else-if="user.role === 'brand'">
                                        <router-link
                                            to="/brand/dashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                            @click="userMenuOpen = false"
                                            >Brand Dashboard</router-link
                                        >
                                        <router-link
                                            to="/brand/creators"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                            @click="userMenuOpen = false"
                                            >Discover Creators</router-link
                                        >
                                    </template>
                                    <template v-else-if="user.role === 'admin'">
                                        <router-link
                                            to="/admin"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]"
                                            @click="userMenuOpen = false"
                                            >Admin Panel</router-link
                                        >
                                    </template>
                                    <template
                                        v-else-if="user.role === 'agency'"
                                    >
                                        <router-link
                                            to="/agency/dashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#7c3aed]/5 hover:text-[#7c3aed]"
                                            @click="userMenuOpen = false"
                                            >Agency Dashboard</router-link
                                        >
                                    </template>
                                    <div class="border-t border-[#e2e8f0] pt-1">
                                        <button
                                            type="button"
                                            class="block w-full px-4 py-2.5 text-left text-sm text-[#64748b] transition hover:bg-red-50 hover:text-red-600"
                                            @click="logout"
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
                            class="rounded-lg bg-[#fc4402] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#e63d02]"
                            >Login</router-link
                        >
                    </template>
                </div>
            </div>
        </nav>

        <!-- Mobile / tablet dropdown -->
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
                class="absolute left-0 right-0 top-full z-40 border-t border-[#e5e7eb] bg-white shadow-lg md:hidden"
            >
                <div class="mx-auto max-w-6xl px-4 py-4">
                    <div class="flex flex-col gap-1">
                        <router-link
                            to="/"
                            class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                            @click="navMobileOpen = false"
                            >Home</router-link
                        >
                        <router-link
                            to="/about"
                            class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                            @click="navMobileOpen = false"
                            >About</router-link
                        >
                        <router-link
                            to="/contact"
                            class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                            @click="navMobileOpen = false"
                            >Contact</router-link
                        >
                        <div class="py-1">
                            <p
                                class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#64748b]"
                            >
                                Our Work
                            </p>
                            <router-link
                                to="/blog"
                                class="block rounded-lg px-4 py-2.5 pl-6 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                @click="navMobileOpen = false"
                                >Blog</router-link
                            >
                            <router-link
                                to="/guide"
                                class="block rounded-lg px-4 py-2.5 pl-6 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                @click="navMobileOpen = false"
                                >Guide</router-link
                            >
                            <router-link
                                to="/products"
                                class="block rounded-lg px-4 py-2.5 pl-6 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                @click="navMobileOpen = false"
                                >Products</router-link
                            >
                        </div>
                        <router-link
                            to="/services"
                            class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                            @click="navMobileOpen = false"
                            >Services</router-link
                        >
                        <router-link
                            to="/videos"
                            class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                            @click="navMobileOpen = false"
                            >Videos</router-link
                        >
                        <div class="py-1">
                            <p
                                class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#64748b]"
                            >
                                Creators
                            </p>
                            <router-link
                                v-for="(item, idx) in creatorCategories"
                                :key="'mob-cat-' + idx"
                                :to="
                                    item.slug
                                        ? {
                                              path: '/creators',
                                              query: { category: item.slug },
                                          }
                                        : '/creators'
                                "
                                class="block rounded-lg px-4 py-2.5 pl-6 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                @click="navMobileOpen = false"
                                >{{ item.name }}</router-link
                            >
                        </div>
                        <template v-if="user">
                            <div class="my-2 border-t border-[#e5e7eb] pt-2">
                                <p
                                    class="px-4 py-1 text-xs font-medium uppercase text-[#64748b]"
                                >
                                    {{ user.role }}
                                </p>
                                <p
                                    class="truncate px-4 py-1 text-sm font-medium text-[#1a1a1a]"
                                >
                                    {{ user.name }}
                                </p>
                            </div>
                            <template
                                v-if="
                                    displayUser &&
                                    displayUser.role === 'creator'
                                "
                            >
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
                            <template v-else-if="user.role === 'brand'">
                                <router-link
                                    to="/brand/dashboard"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                    @click="navMobileOpen = false"
                                    >Brand Dashboard</router-link
                                >
                                <router-link
                                    to="/brand/creators"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                    @click="navMobileOpen = false"
                                    >Discover Creators</router-link
                                >
                            </template>
                            <template v-else-if="user.role === 'admin'">
                                <router-link
                                    to="/admin"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]"
                                    @click="navMobileOpen = false"
                                    >Admin Panel</router-link
                                >
                            </template>
                            <template v-else-if="user.role === 'agency'">
                                <router-link
                                    to="/agency/dashboard"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#7c3aed]/5 hover:text-[#7c3aed]"
                                    @click="navMobileOpen = false"
                                    >Agency Dashboard</router-link
                                >
                            </template>
                            <button
                                type="button"
                                class="rounded-lg px-4 py-3 text-left text-sm text-red-600 transition hover:bg-red-50"
                                @click="
                                    logout();
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
                                    class="rounded-lg border border-[#e5e7eb] px-4 py-3 text-center text-sm font-medium transition hover:border-[#fc4402] hover:text-[#fc4402]"
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
                                    class="rounded-lg bg-[#fc4402] px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-[#e63d02]"
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
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
watch(
    () => route.path,
    () => {
        servicesDropdownOpen.value = false;
        ourWorkDropdownOpen.value = false;
        creatorsDropdownOpen.value = false;
        navMobileOpen.value = false;
    },
);

const fallbackServicesColumn1 = [
    { name: "Video Production", slug: "video-production" },
    { name: "Podcast & Interview", slug: "podcast-interview" },
    {
        name: "Professional Model Portfolio",
        slug: "professional-model-portfolio",
    },
    { name: "Live Streaming", slug: "live-streaming" },
    { name: "Advertisement", slug: "advertisement" },
];
const fallbackServicesColumn2 = [
    { name: "Professional Photography", slug: "professional-photography" },
    { name: "Video Editing", slug: "video-editing" },
    { name: "Studio for Rentals", slug: "studio-for-rentals" },
    { name: "Kid Portfolio Shoot", slug: "kid-portfolio-shoot" },
];

const servicesColumn1 = computed(() => {
    if (services.value && services.value.length > 0) {
        const half = Math.ceil(services.value.length / 2);
        return services.value.slice(0, half);
    }
    return fallbackServicesColumn1;
});
const servicesColumn2 = computed(() => {
    if (services.value && services.value.length > 0) {
        const half = Math.ceil(services.value.length / 2);
        return services.value.slice(half);
    }
    return fallbackServicesColumn2;
});

const user = ref(null);
// On login/register pages always show logged-out UI so logout redirect looks correct
const displayUser = computed(() => {
    if (route.path === "/login" || route.path === "/register") return null;
    return user.value;
});
const userMenuOpen = ref(false);
const userMenuRef = ref(null);
const navMobileOpen = ref(false);
const headerRef = ref(null);
const servicesDropdownOpen = ref(false);
const servicesRef = ref(null);
const ourWorkDropdownOpen = ref(false);
const ourWorkRef = ref(null);
const creatorsDropdownOpen = ref(false);
const creatorsRef = ref(null);
const creatorsDropdownImage = ref(
    "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=400&h=500&fit=crop",
);
const creatorCategories = [
    { name: "Top Creators", slug: "" },
    { name: "Beauty Influencers", slug: "beauty-influencers" },
    { name: "Parenting Influencers", slug: "parenting-influencers" },
    { name: "Travel Influencers", slug: "travel-influencers" },
    {
        name: "Fashion/Lifestyle Influencers",
        slug: "fashion-lifestyle-influencers",
    },
];
const services = ref([]);
const servicesLoading = ref(false);
const servicesDropdownImage = ref("");

function logout() {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    axios
        .post(
            "/api/logout",
            {},
            {
                withCredentials: true,
                headers: token ? { "X-CSRF-TOKEN": token } : {},
            },
        )
        .then(() => {
            user.value = null;
            window.location.href = "/login";
        })
        .catch(() => {
            user.value = null;
            window.location.href = "/login";
        });
}

function onClickOutside(e) {
    if (userMenuRef.value && !userMenuRef.value.contains(e.target))
        userMenuOpen.value = false;
    if (servicesRef.value && !servicesRef.value.contains(e.target))
        servicesDropdownOpen.value = false;
    if (ourWorkRef.value && !ourWorkRef.value.contains(e.target))
        ourWorkDropdownOpen.value = false;
    if (creatorsRef.value && !creatorsRef.value.contains(e.target))
        creatorsDropdownOpen.value = false;
    if (
        navMobileOpen.value &&
        headerRef.value &&
        !headerRef.value.contains(e.target)
    )
        navMobileOpen.value = false;
}

onMounted(() => {
    axios
        .get("/api/me", { withCredentials: true })
        .then((r) => {
            user.value = r.data;
        })
        .catch(() => {
            user.value = null;
        });
    servicesLoading.value = true;
    axios
        .get("/api/services")
        .then((r) => {
            services.value = r.data || [];
            const first = (r.data || [])[0];
            if (first && first.image) servicesDropdownImage.value = first.image;
        })
        .catch(() => {
            services.value = [];
        })
        .finally(() => {
            servicesLoading.value = false;
        });
    document.addEventListener("click", onClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", onClickOutside);
});
</script>

<style scoped>
.nav-bar {
    z-index: 1;
    position: relative;
    width: 100%;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.nav-bar-bg {
    background: linear-gradient(
        135deg,
        rgba(230, 229, 229, 0.55) 0%,
        rgba(255, 255, 255, 0.25) 50%,
        rgba(230, 229, 229, 0.4) 100%
    );
    backdrop-filter: blur(8px);
}
</style>
