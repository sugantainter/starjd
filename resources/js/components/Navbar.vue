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
                    class="h-14 w-auto object-contain sm:h-20"
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
                        v-for="link in desktopTopLinks"
                        :key="link.to"
                        :to="link.to"
                        class="text-sm font-semibold transition hover:text-[#fc4402]"
                    >
                        {{ link.label }}
                    </router-link>
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
                            Discover Creators
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
                                class="absolute left-1/2 top-full z-50 mt-2 w-[min(95vw,280px)] -translate-x-1/2 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-xl"
                            >
                                <div class="flex flex-col py-2">
                                    <div
                                        class="px-4 py-2 border-b border-[#e5e7eb] mb-1"
                                    >
                                        <span
                                            class="text-xs font-bold uppercase tracking-wider text-[#fc4402]"
                                            >For Creators</span
                                        >
                                        <p
                                            class="text-[10px] text-[#6b7280] leading-tight"
                                        >
                                            For influencers and content
                                            creators.
                                        </p>
                                    </div>
                                    <router-link
                                        v-for="(
                                            item, idx
                                        ) in creatorCategoriesExtended"
                                        :key="'cat-' + idx"
                                        :to="creatorCategoryTo(item)"
                                        class="block px-4 py-2 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                        @click="creatorsDropdownOpen = false"
                                    >
                                        {{ item.name }}
                                    </router-link>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Hire Professionals Dropdown -->
                    <div
                        ref="professionalsRef"
                        class="relative"
                        @mouseenter="professionalsDropdownOpen = true"
                        @mouseleave="professionalsDropdownOpen = false"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-sm font-semibold transition hover:text-[#fc4402]"
                            :class="{
                                'text-[#fc4402]': professionalsDropdownOpen,
                            }"
                            aria-haspopup="true"
                            :aria-expanded="professionalsDropdownOpen"
                            @click="
                                professionalsDropdownOpen =
                                    !professionalsDropdownOpen
                            "
                        >
                            Hire Professionals
                            <svg
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="{
                                    'rotate-180': professionalsDropdownOpen,
                                }"
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
                                v-show="professionalsDropdownOpen"
                                class="absolute left-1/2 top-full z-50 mt-2 w-[min(95vw,280px)] -translate-x-1/2 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-xl"
                            >
                                <div class="flex flex-col py-2">
                                    <div
                                        class="px-4 py-2 border-b border-[#e5e7eb] mb-1"
                                    >
                                        <span
                                            class="text-xs font-bold uppercase tracking-wider text-[#fc4402]"
                                            >Service Marketplace</span
                                        >
                                        <p
                                            class="text-[10px] text-[#6b7280] leading-tight"
                                        >
                                            Find professionals for your project.
                                        </p>
                                    </div>
                                    <router-link
                                        v-for="(
                                            item, idx
                                        ) in professionalCategories"
                                        :key="'prof-' + idx"
                                        :to="professionalCategoryTo(item)"
                                        class="block px-4 py-2 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                        @click="
                                            professionalsDropdownOpen = false
                                        "
                                    >
                                        {{ item.name }}
                                    </router-link>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Studios Dropdown -->
                    <div
                        ref="studiosRef"
                        class="relative"
                        @mouseenter="studiosDropdownOpen = true"
                        @mouseleave="studiosDropdownOpen = false"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-sm font-semibold transition hover:text-[#fc4402]"
                            :class="{ 'text-[#fc4402]': studiosDropdownOpen }"
                            aria-haspopup="true"
                            :aria-expanded="studiosDropdownOpen"
                            @click="studiosDropdownOpen = !studiosDropdownOpen"
                        >
                            Studios
                            <svg
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="{ 'rotate-180': studiosDropdownOpen }"
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
                                v-show="studiosDropdownOpen"
                                class="absolute left-1/2 top-full z-50 mt-2 w-[min(95vw,250px)] -translate-x-1/2 overflow-hidden rounded-xl border border-[#e5e7eb] bg-white shadow-xl"
                            >
                                <div class="flex flex-col py-2">
                                    <router-link
                                        v-for="(
                                            item, idx
                                        ) in studioCategoriesExtended"
                                        :key="'studio-' + idx"
                                        :to="studioCategoryTo(item)"
                                        class="block px-4 py-2 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                        @click="studiosDropdownOpen = false"
                                    >
                                        {{ item.name }}
                                    </router-link>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <router-link
                        v-for="link in desktopBottomLinks"
                        :key="link.to"
                        :to="link.to"
                        class="text-sm font-semibold transition hover:text-[#fc4402]"
                    >
                        {{ link.label }}
                    </router-link>

                    <template v-if="displayUser">
                        <div class="relative" ref="userMenuRef">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-lg border border-[#e5e7eb] bg-white px-4 py-2 text-sm font-medium text-[#1a1a1a] shadow-sm transition hover:border-[#fc4402] hover:bg-[#fafafa] focus:outline-none focus:ring-2 focus:ring-[#fc4402]/20"
                                :class="{
                                    'hover:border-[#10b981] focus:ring-[#10b981]/20':
                                        user.role === 'creator',
                                    'hover:border-[#1e293b] focus:ring-[#1e293b]/20':
                                        user.role === 'admin',
                                    'hover:border-[#7c3aed] focus:ring-[#7c3aed]/20':
                                        user.role === 'agency',
                                    'hover:border-[#f59e0b] focus:ring-[#f59e0b]/20':
                                        user.role === 'professional',
                                    'hover:border-[#e63946] focus:ring-[#e63946]/20':
                                        user.role === 'studio_owner',
                                }"
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
                                            :to="ROUTE_PATHS.creatorDashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                            @click="userMenuOpen = false"
                                            >Creator Dashboard</router-link
                                        >
                                        <router-link
                                            :to="ROUTE_PATHS.creatorProfile"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                            @click="userMenuOpen = false"
                                            >My Profile</router-link
                                        >
                                    </template>
                                    <template v-else-if="user.role === 'brand'">
                                        <router-link
                                            :to="ROUTE_PATHS.brandDashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                            @click="userMenuOpen = false"
                                            >Brand Dashboard</router-link
                                        >
                                        <router-link
                                            :to="ROUTE_PATHS.brandCreators"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                            @click="userMenuOpen = false"
                                            >Discover Creators</router-link
                                        >
                                    </template>
                                    <template v-else-if="user.role === 'admin'">
                                        <router-link
                                            :to="ROUTE_PATHS.admin"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]"
                                            @click="userMenuOpen = false"
                                            >Admin Panel</router-link
                                        >
                                    </template>
                                    <template
                                        v-else-if="user.role === 'agency'"
                                    >
                                        <router-link
                                            :to="ROUTE_PATHS.agencyDashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#7c3aed]/5 hover:text-[#7c3aed]"
                                            @click="userMenuOpen = false"
                                            >Agency Dashboard</router-link
                                        >
                                    </template>
                                    <template
                                        v-else-if="user.role === 'professional'"
                                    >
                                        <router-link
                                            :to="
                                                ROUTE_PATHS.professionalDashboard
                                            "
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#f59e0b]/5 hover:text-[#f59e0b]"
                                            @click="userMenuOpen = false"
                                            >Professional Dashboard</router-link
                                        >
                                        <router-link
                                            :to="
                                                ROUTE_PATHS.professionalProfile
                                            "
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#f59e0b]/5 hover:text-[#f59e0b]"
                                            @click="userMenuOpen = false"
                                            >My Profile</router-link
                                        >
                                    </template>
                                    <template
                                        v-else-if="user.role === 'studio_owner'"
                                    >
                                        <router-link
                                            :to="ROUTE_PATHS.studioDashboard"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                            @click="userMenuOpen = false"
                                            >Studio Dashboard</router-link
                                        >
                                        <router-link
                                            :to="ROUTE_PATHS.studioMyStudios"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                            @click="userMenuOpen = false"
                                            >My Studios</router-link
                                        >
                                        <router-link
                                            :to="ROUTE_PATHS.studioBookings"
                                            class="block px-4 py-2.5 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                            @click="userMenuOpen = false"
                                            >Bookings</router-link
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
                            :to="ROUTE_PATHS.login"
                            class="text-sm font-semibold transition hover:text-[#fc4402]"
                            >Login</router-link
                        >
                        <router-link
                            :to="ROUTE_PATHS.register"
                            class="rounded-lg bg-[#fc4402] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#e63d02]"
                            >Join Free</router-link
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
                            v-for="link in mobileMainLinks"
                            :key="link.to"
                            :to="link.to"
                            class="rounded-lg px-4 py-3 text-sm font-medium text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                            @click="navMobileOpen = false"
                        >
                            {{ link.label }}
                        </router-link>
                        <div class="py-1">
                            <p
                                class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#64748b]"
                            >
                                Discover Creators
                            </p>
                            <router-link
                                v-for="(item, idx) in creatorCategoriesExtended"
                                :key="'mob-cre-' + idx"
                                :to="creatorCategoryTo(item)"
                                class="block rounded-lg px-4 py-2.5 pl-6 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                @click="navMobileOpen = false"
                                >{{ item.name }}</router-link
                            >
                        </div>
                        <div class="py-1">
                            <p
                                class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#64748b]"
                            >
                                Hire Professionals
                            </p>
                            <router-link
                                v-for="(item, idx) in professionalCategories"
                                :key="'mob-prof-' + idx"
                                :to="professionalCategoryTo(item)"
                                class="block rounded-lg px-4 py-2.5 pl-6 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                @click="navMobileOpen = false"
                                >{{ item.name }}</router-link
                            >
                        </div>
                        <div class="py-1">
                            <p
                                class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#64748b]"
                            >
                                Studios
                            </p>
                            <router-link
                                v-for="(item, idx) in studioCategoriesExtended"
                                :key="'mob-stu-' + idx"
                                :to="studioCategoryTo(item)"
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
                                    :to="ROUTE_PATHS.creatorDashboard"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                    @click="navMobileOpen = false"
                                    >Creator Dashboard</router-link
                                >
                                <router-link
                                    :to="ROUTE_PATHS.creatorProfile"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#10b981]/5 hover:text-[#10b981]"
                                    @click="navMobileOpen = false"
                                    >My Profile</router-link
                                >
                            </template>
                            <template v-else-if="user.role === 'brand'">
                                <router-link
                                    :to="ROUTE_PATHS.brandDashboard"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                    @click="navMobileOpen = false"
                                    >Brand Dashboard</router-link
                                >
                                <router-link
                                    :to="ROUTE_PATHS.brandCreators"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#fc4402]/5 hover:text-[#fc4402]"
                                    @click="navMobileOpen = false"
                                    >Discover Creators</router-link
                                >
                            </template>
                            <template v-else-if="user.role === 'admin'">
                                <router-link
                                    :to="ROUTE_PATHS.admin"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#1e293b]/5 hover:text-[#1e293b]"
                                    @click="navMobileOpen = false"
                                    >Admin Panel</router-link
                                >
                            </template>
                            <template v-else-if="user.role === 'agency'">
                                <router-link
                                    :to="ROUTE_PATHS.agencyDashboard"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#7c3aed]/5 hover:text-[#7c3aed]"
                                    @click="navMobileOpen = false"
                                    >Agency Dashboard</router-link
                                >
                            </template>
                            <template v-else-if="user.role === 'professional'">
                                <router-link
                                    :to="ROUTE_PATHS.professionalDashboard"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#f59e0b]/5 hover:text-[#f59e0b]"
                                    @click="navMobileOpen = false"
                                    >Professional Dashboard</router-link
                                >
                                <router-link
                                    :to="ROUTE_PATHS.professionalProfile"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#f59e0b]/5 hover:text-[#f59e0b]"
                                    @click="navMobileOpen = false"
                                    >My Profile</router-link
                                >
                            </template>
                            <template v-else-if="user.role === 'studio_owner'">
                                <router-link
                                    :to="ROUTE_PATHS.studioDashboard"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                    @click="navMobileOpen = false"
                                    >Studio Dashboard</router-link
                                >
                                <router-link
                                    :to="ROUTE_PATHS.studioMyStudios"
                                    class="rounded-lg px-4 py-3 text-sm text-[#1a1a1a] transition hover:bg-[#e63946]/5 hover:text-[#e63946]"
                                    @click="navMobileOpen = false"
                                    >My Studios</router-link
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
                                    :to="ROUTE_PATHS.login"
                                    class="rounded-lg border border-[#e5e7eb] px-4 py-3 text-center text-sm font-medium transition hover:border-[#fc4402] hover:text-[#fc4402]"
                                    @click="navMobileOpen = false"
                                    >Login</router-link
                                >
                                <router-link
                                    :to="ROUTE_PATHS.register"
                                    class="rounded-lg bg-[#fc4402] px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-[#e63d02]"
                                    @click="navMobileOpen = false"
                                    >Join Free</router-link
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
const creatorsDropdownOpen = ref(false);
const creatorsRef = ref(null);
const professionalsDropdownOpen = ref(false);
const professionalsRef = ref(null);
const studiosDropdownOpen = ref(false);
const studiosRef = ref(null);
const servicesDropdownOpen = ref(false);
const servicesRef = ref(null);
const ourWorkDropdownOpen = ref(false);
const ourWorkRef = ref(null);
const creatorsDropdownImage = ref(
    "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=400&h=500&fit=crop",
);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);
const navMobileOpen = ref(false);
const headerRef = ref(null);
const user = ref(null);

// On login/register pages always show logged-out UI so logout redirect looks correct
const displayUser = computed(() => {
    if (route.path === "/login" || route.path === "/register") return null;
    return user.value;
});

watch(
    () => route.path,
    () => {
        servicesDropdownOpen.value = false;
        ourWorkDropdownOpen.value = false;
        creatorsDropdownOpen.value = false;
        professionalsDropdownOpen.value = false;
        studiosDropdownOpen.value = false;
        navMobileOpen.value = false;
    },
);

// Shared nav link lists to keep the navbar markup lean (desktop + mobile)
const desktopTopLinks = [
    { label: "Home", to: "/" },
    { label: "Campaigns", to: "/campaigns" },
];
const desktopBottomLinks = [
    { label: "How It Works", to: "/how-it-works" },
    { label: "Pricing", to: "/pricing" },
    { label: "For Brands", to: "/brand" },
    { label: "For Creators", to: "/creator" },
];
const mobileMainLinks = [
    { label: "Home", to: "/" },
    { label: "Campaigns", to: "/campaigns" },
    ...desktopBottomLinks
];

// Keep all frequently used routes centralized (used in template directly).
const ROUTE_PATHS = {
    home: "/",
    login: "/login",
    register: "/register",
    creatorDashboard: "/creator/dashboard",
    creatorProfile: "/creator/profile",
    brandDashboard: "/brand/dashboard",
    brandCreators: "/brand/creators",
    admin: "/admin",
    agencyDashboard: "/agency/dashboard",
    professionalDashboard: "/professional/dashboard",
    professionalProfile: "/professional/profile",
    studioDashboard: "/studio/dashboard",
    studioMyStudios: "/studio/studios",
    studioBookings: "/studio/bookings",
};

function creatorCategoryTo(item) {
    return item.name
        ? { path: "/creators", query: { category: item.name } }
        : "/creators";
}

function professionalCategoryTo(item) {
    // Keep desktop & mobile consistent (Service Marketplace)
    return item.slug
        ? { name: "marketplace", query: { category: item.slug } }
        : "/marketplace";
}

function studioCategoryTo(item) {
    return item.slug
        ? { path: "/studios", query: { category: item.slug } }
        : "/studios";
}

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

const creatorCategoriesExtended = ref([
    { name: "Influencers", slug: "influencers" },
    { name: "UGC Creators", slug: "ugc-creators" },
    { name: "Podcast Hosts", slug: "podcast-hosts" },
    { name: "Voiceover Artists", slug: "voiceover-artists" },
    { name: "Short-Form Creators", slug: "short-form-creators" },
    { name: "YouTube Creators", slug: "youtube-creators" },
    { name: "Regional Creators", slug: "regional-creators" },
]);
const professionalCategories = [
    { name: "Graphic & Video Editors", slug: "graphic-video-editors" },
    {
        name: "Photographers & Videographers",
        slug: "photographers-videographers",
    },
    { name: "Social Media Managers", slug: "social-media-managers" },
    { name: "Script/ Content writers", slug: "content-writers" },
    { name: "Marketing/ Advertising Agencies", slug: "marketing-agencies" },
    { name: "Anchors", slug: "anchors" },
    { name: "Makeup Artists", slug: "makeup-artists" },
    { name: "Wedding Planners", slug: "wedding-planners" },
];
const studioCategoriesExtended = [
    { name: "Podcast Recording Studios", slug: "podcast-studios" },
    { name: "Product Photography Studios", slug: "photography-studios" },
    { name: "Video Production Studios", slug: "video-studios" },
    { name: "Green Screen Studios", slug: "green-screen-studios" },
    { name: "Content Creation Studios", slug: "content-studios" },
];
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
    if (professionalsRef.value && !professionalsRef.value.contains(e.target))
        professionalsDropdownOpen.value = false;
    if (studiosRef.value && !studiosRef.value.contains(e.target))
        studiosDropdownOpen.value = false;
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
    axios
        .get("/api/creators/options/filters")
        .then((r) => {
            const cats = r.data.categories || [];
            const navCats = cats.filter((c) => c.show_on_navbar);
            if (navCats.length > 0) {
                creatorCategoriesExtended.value = navCats;
            }
        })
        .catch(() => {});
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
