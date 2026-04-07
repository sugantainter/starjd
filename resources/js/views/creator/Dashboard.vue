<template>
    <div class="relative">
        <!-- Notifications -->
        <div
            v-if="notification"
            class="fixed top-6 right-6 z-[100] animate-in fade-in slide-in-from-top-4 duration-300"
        >
            <div
                :class="[
                    'rounded-2xl px-6 py-4 shadow-2xl flex items-center gap-3 border transition-all',
                    notification.type === 'success'
                        ? 'bg-green-50 border-green-200 text-green-900'
                        : 'bg-red-50 border-red-200 text-red-900',
                ]"
            >
                <div
                    v-if="notification.type === 'success'"
                    class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="3"
                            d="M5 13l4 4L19 7"
                        ></path>
                    </svg>
                </div>
                <div
                    v-else
                    class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center text-white shrink-0"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="3"
                            d="M6 18L18 6M6 6l12 12"
                        ></path>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">{{ notification.title }}</p>
                    <p class="text-sm opacity-90">{{ notification.message }}</p>
                </div>
            </div>
        </div>
        <h1 class="text-2xl font-bold text-[#1a1a1a]">Dashboard</h1>
        <p class="mt-1 text-[#64748b]">Welcome back, {{ data?.user?.name }}.</p>

        <div
            v-if="data?.profile"
            class="mt-6 rounded-xl border-2 border-[#f59e0b]/30 bg-[#fffbeb] p-4 sm:p-5"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-lg font-semibold text-[#1a1a1a]">
                        Featured creator
                    </h2>
                    <p
                        v-if="
                            data.profile.is_featured &&
                            data.profile.featured_until
                        "
                        class="mt-1 text-sm text-[#64748b]"
                    >
                        Your profile is featured until
                        <strong>{{
                            formatDate(data.profile.featured_until)
                        }}</strong
                        >. You appear first in Discover.
                    </p>
                    <p v-else class="mt-1 text-sm text-[#64748b]">
                        Get more visibility: featured creators appear at the top
                        of Discover and on the homepage.
                    </p>
                </div>
                <router-link
                    v-if="!data.profile.is_featured"
                    to="/creator/featured"
                    class="shrink-0 rounded-xl bg-[#f59e0b] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#d97706]"
                    >Get Featured</router-link
                >
                <router-link
                    v-else
                    to="/creator/featured"
                    class="shrink-0 rounded-xl border border-[#f59e0b] bg-white px-5 py-2.5 text-sm font-semibold text-[#d97706] transition hover:bg-[#fffbeb]"
                    >Extend featured</router-link
                >
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
                <div class="text-sm text-[#64748b]">Packages</div>
                <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">
                    {{ data?.packages?.length ?? 0 }}
                </div>
            </div>
            <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
                <div class="text-sm text-[#64748b]">Collaborations</div>
                <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">
                    {{ data?.collaborations?.length ?? 0 }}
                </div>
            </div>
            <div class="rounded-xl border border-[#e2e8f0] bg-white p-4">
                <div class="text-sm text-[#64748b]">Social connected</div>
                <div class="mt-1 text-2xl font-bold text-[#1a1a1a]">
                    {{ connectedSocialCount }}
                </div>
            </div>
        </div>

        <!-- Social Reach Section -->
        <div v-if="connectedSocialCount > 0" class="mt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">
                Social Audience Reach
            </h2>
            <p class="mt-1 text-sm text-[#64748b]">
                Total combined reach across your connected platforms.
            </p>
            <div
                class="mt-4 grid gap-4 xs:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4"
            >
                <div
                    v-for="acc in data.social_accounts.filter(
                        (a) => a.is_connected && a.followers_count,
                    )"
                    :key="acc.id"
                    class="flex items-center gap-3 rounded-xl border border-[#e2e8f0] bg-white p-4 shadow-sm transition hover:shadow"
                >
                    <SocialPlatformIcon
                        :platform="acc.platform"
                        :size="40"
                        class="shrink-0"
                    />
                    <div>
                        <div
                            class="text-xs font-semibold uppercase tracking-wider text-[#64748b]"
                        >
                            {{ acc.platform }}
                        </div>
                        <div class="mt-0.5 text-xl font-bold text-[#1a1a1a]">
                            {{ formatFollowers(acc.followers_count) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Insights section (Graphs) -->
        <div v-if="selectedAccount && analyticsHistory.length > 0" class="mt-8">
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4"
            >
                <div>
                    <h2 class="text-lg font-semibold text-[#1a1a1a]">
                        Performance Insights
                    </h2>
                    <p class="mt-1 text-sm text-[#64748b]">
                        Select a platform to view your analytics history.
                    </p>
                </div>

                <!-- Platform Switcher Tabs -->
                <div
                    class="flex items-center gap-1 p-1 bg-slate-100/50 rounded-2xl border border-[#e2e8f0]"
                >
                    <button
                        v-for="acc in data.social_accounts.filter(
                            (a) => a.is_connected && a.analytics_data,
                        )"
                        :key="acc.platform"
                        @click="
                            selectedPlatform = acc.platform;
                            activeTab = platformTabs[acc.platform][0].id;
                        "
                        class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition-all"
                        :class="
                            selectedPlatform === acc.platform
                                ? 'bg-white shadow text-[#1a1a1a]'
                                : 'text-[#64748b] hover:text-[#1a1a1a] hover:bg-white/50'
                        "
                    >
                        <SocialPlatformIcon
                            :platform="acc.platform"
                            :size="20"
                        />
                        <span class="capitalize">{{ acc.platform }}</span>
                    </button>
                </div>
            </div>

            <!-- Metric Selector and Graph Title -->
            <div class="flex items-center justify-between mb-4">
                <h3
                    class="text-xs font-bold uppercase tracking-wider text-[#94a3b8]"
                >
                    {{
                        platformTabs[selectedPlatform]?.find(
                            (t) => t.id === activeTab,
                        )?.label ?? "Metrics"
                    }}
                    History
                </h3>
                <div class="flex gap-2">
                    <button
                        v-for="tab in platformTabs[selectedPlatform]"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="px-4 py-2 text-sm font-medium rounded-xl transition-all"
                        :class="
                            activeTab === tab.id
                                ? 'bg-[#1a1a1a] text-white'
                                : 'bg-white border border-[#e2e8f0] text-[#64748b] hover:bg-slate-50'
                        "
                    >
                        {{ tab.name }}
                    </button>
                </div>
            </div>

            <div
                class="rounded-2xl border border-[#e2e8f0] bg-white p-6 shadow-sm"
            >
                <GrowthChart
                    :history="analyticsHistory"
                    :metricIndex="activeMetricIndex"
                    :metricName="activeTabLabel"
                    :color="
                        selectedPlatform === 'linkedin'
                            ? '#0077b5'
                            : selectedPlatform === 'youtube'
                              ? '#ef4444'
                              : '#3b82f6'
                    "
                />
            </div>
        </div>

        <!-- Additional Insights Grid -->
        <div
            v-if="
                selectedAccount &&
                (topContent.length > 0 || demographics.age.length > 0)
            "
            class="mt-8 grid gap-8 lg:grid-cols-2"
        >
            <!-- Top Content -->
            <div v-if="topContent.length > 0">
                <h2 class="text-lg font-semibold text-[#1a1a1a]">
                    Top Content
                </h2>
                <p class="mt-1 text-sm text-[#64748b]">
                    Your best performing
                    {{
                        selectedPlatform === "youtube" ? "videos" : "posts"
                    }}
                    recently.
                </p>
                <div class="mt-4 space-y-3">
                    <div
                        v-for="item in topContent"
                        :key="item.id"
                        class="flex gap-4 rounded-xl border border-[#e2e8f0] bg-white p-3 transition hover:shadow-md"
                    >
                        <div v-if="item.thumbnail" class="shrink-0">
                            <img
                                :src="item.thumbnail"
                                alt=""
                                class="h-16 w-28 rounded-lg object-cover bg-slate-100 shadow-inner"
                            />
                        </div>
                        <div
                            v-else
                            class="h-16 w-28 rounded-lg bg-slate-100 flex items-center justify-center shrink-0"
                        >
                            <SocialPlatformIcon
                                :platform="selectedPlatform"
                                :size="32"
                                class="opacity-30"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4
                                class="line-clamp-2 font-medium text-[#1a1a1a]"
                                :title="item.title"
                            >
                                {{ item.title }}
                            </h4>
                            <div
                                class="mt-1 flex items-center gap-3 text-xs text-[#64748b]"
                            >
                                <span class="flex items-center gap-1">
                                    <svg
                                        class="h-3 w-3"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"
                                        />
                                    </svg>
                                    {{
                                        formatFollowers(
                                            item.views || item.engagement,
                                        )
                                    }}
                                    {{ item.views ? "views" : "engagement" }}
                                </span>
                                <a
                                    v-if="item.url"
                                    :href="item.url"
                                    target="_blank"
                                    class="text-[#3b82f6] hover:underline"
                                    >View</a
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audience Demographics -->
            <div v-if="demographics.age && demographics.age.length > 0">
                <h2 class="text-lg font-semibold text-[#1a1a1a]">
                    Audience Demographics
                </h2>
                <p class="mt-1 text-sm text-[#64748b]">
                    Deep insights into who is watching your content.
                </p>
                <div
                    class="mt-4 rounded-xl border border-[#e2e8f0] bg-white p-6 grid gap-8 sm:grid-cols-2"
                >
                    <!-- Gender Donut -->
                    <div>
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-4"
                        >
                            Gender Distribution
                        </h4>
                        <div class="h-[250px]">
                            <DoughnutChart :counts="demographics.gender" />
                        </div>
                    </div>

                    <!-- Age Bars -->
                    <div>
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-[#94a3b8] mb-4"
                        >
                            Top Age Groups
                        </h4>
                        <div class="h-[250px]">
                            <BarChart
                                :dataRows="demographics.age"
                                color="#6366f1"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaigns Section -->
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-[#1a1a1a]">
                        Campaign Applications
                    </h2>
                    <p class="mt-1 text-sm text-[#64748b]">
                        Track your progress with brands and upcoming
                        opportunities.
                    </p>
                </div>
                <router-link
                    to="/campaigns"
                    class="text-sm font-semibold text-[#10b981] hover:text-[#059669] flex items-center gap-1 transition-colors"
                >
                    Browse All
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        ></path>
                    </svg>
                </router-link>
            </div>

            <div
                v-if="!data?.campaign_applications?.length"
                class="bg-white border border-dashed border-slate-200 rounded-2xl p-12 text-center"
            >
                <div
                    class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                    <svg
                        class="w-8 h-8 text-slate-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                        ></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#1a1a1a] mb-1">
                    No applications yet
                </h3>
                <p class="text-[#64748b] mb-6">
                    Start applying to campaigns to grow your influence.
                </p>
                <router-link
                    to="/campaigns"
                    class="inline-flex items-center gap-2 bg-[#10b981] text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-[#059669] transition-all shadow-sm active:scale-95"
                >
                    Find Campaigns
                </router-link>
            </div>

            <div
                v-else
                class="overflow-hidden bg-white border border-[#e2e8f0] rounded-2xl shadow-sm"
            >
                <div class="overflow-x-auto text-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50/50 border-b border-[#e2e8f0]"
                            >
                                <th
                                    class="px-6 py-4 font-bold text-[#475569] uppercase tracking-wider text-[10px]"
                                >
                                    Campaign Details
                                </th>
                                <th
                                    class="px-6 py-4 font-bold text-[#475569] uppercase tracking-wider text-[10px]"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-6 py-4 font-bold text-[#475569] uppercase tracking-wider text-[10px]"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 font-bold text-[#475569] uppercase tracking-wider text-[10px]"
                                >
                                    Applied Date
                                </th>
                                <th
                                    class="px-6 py-4 font-bold text-[#475569] uppercase tracking-wider text-[10px] text-right"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f1f5f9]">
                            <tr
                                v-for="app in data.campaign_applications"
                                :key="app.id"
                                class="group hover:bg-[#fafafa] transition-all duration-200"
                            >
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100/50"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p
                                                class="font-bold text-[#1a1a1a] line-clamp-1 truncate max-w-[200px]"
                                                :title="app.campaign?.title"
                                            >
                                                {{
                                                    app.campaign?.title ||
                                                    "Unknown Campaign"
                                                }}
                                            </p>
                                            <p
                                                class="text-[11px] text-[#64748b] mt-0.5"
                                            >
                                                by
                                                {{
                                                    app.campaign?.brand?.name ||
                                                    "Partner Brand"
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <SocialPlatformIcon
                                            :platform="
                                                app.campaign?.campaign_type
                                            "
                                            :size="16"
                                        />
                                        <span
                                            class="font-medium text-[#475569] capitalize"
                                            >{{
                                                typeLabel(
                                                    app.campaign?.campaign_type,
                                                )
                                            }}</span
                                        >
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider"
                                        :class="
                                            applicationStatusClass(app.status)
                                        "
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full"
                                            :class="
                                                app.status === 'approved'
                                                    ? 'bg-emerald-500'
                                                    : app.status === 'pending'
                                                      ? 'bg-amber-500'
                                                      : 'bg-red-500'
                                            "
                                        ></span>
                                        {{ applicationStatusLabel(app.status) }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-5 text-[#64748b] font-medium"
                                >
                                    {{ formatDate(app.created_at) }}
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <router-link
                                        v-if="app.campaign?.slug"
                                        :to="'/campaigns/' + app.campaign.slug"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-[#10b981] hover:text-[#059669] px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-all border border-transparent hover:border-emerald-100"
                                    >
                                        View
                                        <svg
                                            class="w-3.5 h-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"
                                            ></path>
                                        </svg>
                                    </router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Collaborations Section -->
        <div class="mt-12 mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-[#1a1a1a]">
                        Recent Collaborations
                    </h2>
                    <p class="mt-1 text-sm text-[#64748b]">
                        A history of your successful partnerships and earnings.
                    </p>
                </div>
                <router-link
                    to="/creator/collaborations"
                    class="text-sm font-semibold text-[#10b981] hover:text-[#059669] flex items-center gap-1 transition-colors"
                >
                    View All
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        ></path>
                    </svg>
                </router-link>
            </div>

            <div
                v-if="!data?.collaborations?.length"
                class="bg-white border border-dashed border-slate-200 rounded-2xl p-12 text-center text-[#64748b]"
            >
                <div
                    class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300"
                >
                    <svg
                        class="w-8 h-8"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        ></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#1a1a1a] mb-1">
                    No collaborations yet
                </h3>
                <p>Your finished projects will appear here.</p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="c in data.collaborations.slice(0, 4)"
                    :key="c.id"
                    class="flex items-center justify-between p-5 bg-white border border-[#e2e8f0] rounded-2xl hover:shadow-lg transition-all duration-300 group"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center border border-slate-100 overflow-hidden shrink-0 group-hover:scale-105 transition-transform"
                        >
                            <img
                                v-if="c.brand?.logo"
                                :src="c.brand.logo"
                                class="w-full h-full object-cover"
                            />
                            <span
                                v-else
                                class="text-xl font-bold text-slate-300 capitalize"
                                >{{ c.brand?.name?.charAt(0) }}</span
                            >
                        </div>
                        <div>
                            <h4
                                class="font-bold text-[#1a1a1a] group-hover:text-[#10b981] transition-colors leading-tight"
                            >
                                {{ c.brand?.name || "Partner Brand" }}
                            </h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    :class="collaborationStatusClass(c.status)"
                                    class="text-[9px] uppercase tracking-widest font-black px-2 py-0.5 rounded-md border"
                                >
                                    {{ c.status }}
                                </span>
                                <span
                                    class="text-[11px] text-[#94a3b8] font-medium"
                                    >{{ formatDate(c.created_at) }}</span
                                >
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div
                            class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-0.5"
                        >
                            Earnings
                        </div>
                        <div class="text-xl font-black text-[#1a1a1a]">
                            ₹{{ c.amount }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support & Help Desk -->
        <div class="mt-12 mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-[#1a1a1a]">Dispute Mediation & Support</h2>
                    <p class="mt-1 text-sm text-[#64748b]">Communicate with admins regarding disputes or technical help.</p>
                </div>
                <router-link to="/creator/support" class="text-sm font-semibold text-[#3b82f6] hover:text-blue-700 flex items-center gap-1">
                    Help Center
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </router-link>
            </div>
            
            <div v-if="!data?.tickets?.length" class="bg-white border border-dashed border-slate-200 rounded-2xl p-8 text-center text-[#94a3b8]">
                No active support tickets or active disputes.
            </div>
            <div v-else class="grid gap-4 sm:grid-cols-2">
                <div v-for="t in data.tickets.slice(0, 4)" :key="t.id" @click="$router.push('/creator/support')" class="cursor-pointer group bg-white border border-[#e2e8f0] rounded-2xl p-5 hover:shadow-lg transition-all border-l-4" :class="t.status === 'open' ? 'border-l-blue-500' : 'border-l-slate-200'">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black tracking-widest text-[#94a3b8] uppercase">{{ t.ticket_id }}</span>
                        <span :class="t.status === 'open' ? 'bg-blue-50 text-blue-600' : 'bg-slate-50 text-slate-500'" class="text-[9px] uppercase font-black px-2 py-0.5 rounded border">{{ t.status }}</span>
                    </div>
                    <h4 class="font-bold text-[#1a1a1a] group-hover:text-blue-600 transition-colors">{{ t.subject }}</h4>
                    <p class="mt-1 text-xs text-[#64748b] line-clamp-1 italic">"{{ t.messages_recent?.message || 'Awaiting platform moderation...' }}"</p>
                    <div class="mt-3 text-[10px] text-[#94a3b8]">Updated {{ formatDate(t.updated_at) }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import SocialPlatformIcon from "../../components/SocialPlatformIcon.vue";
import GrowthChart from "../../components/GrowthChart.vue";
import DoughnutChart from "../../components/DoughnutChart.vue";
import BarChart from "../../components/BarChart.vue";

const router = useRouter();
const data = ref(null);
const selectedPlatform = ref("youtube");
const activeTab = ref("views");

const platformTabs = {
    youtube: [
        { id: "views", name: "Views", label: "Daily Views", index: 3 },
        {
            id: "subscribers",
            name: "Subscribers",
            label: "Net Subscriber growth",
            index: 1,
        },
        { id: "likes", name: "Likes", label: "Daily Likes", index: 4 },
    ],
    facebook: [
        { id: "reach", name: "Reach", label: "Daily Reach", index: 3 },
        {
            id: "engagement",
            name: "Engagement",
            label: "Daily Engagement",
            index: 1,
        },
    ],
    linkedin: [
        {
            id: "engagement",
            name: "Engagement",
            label: "Total Engagement",
            index: 3,
        },
        { id: "likes", name: "Likes", label: "Post Likes", index: 1 },
        { id: "comments", name: "Comments", label: "Post Comments", index: 2 },
    ],
    instagram: [
        { id: "reach", name: "Reach", label: "Account Reach", index: 3 },
        {
            id: "impressions",
            name: "Impressions",
            label: "Total Impressions",
            index: 2,
        },
    ],
    pinterest: [
        {
            id: "impressions",
            name: "Impressions",
            label: "Daily Impressions",
            index: 1,
        },
        { id: "saves", name: "Saves", label: "Daily Saves", index: 2 },
        { id: "clicks", name: "Clicks", label: "Outbound Clicks", index: 3 },
    ],
};

const notification = ref(null);

onMounted(async () => {
    // Professional Feedback: Check for success/error handshake from URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("success") || window.location.hash.includes("_=_")) {
        notification.value = {
            type: "success",
            title: "Success!",
            message: "Social account connected successfully.",
        };
        // Professional Cleanup: URL Wash
        window.history.replaceState(
            {},
            document.title,
            window.location.pathname,
        );
        setTimeout(() => (notification.value = null), 5000);
    }

    if (urlParams.has("error")) {
        notification.value = {
            type: "error",
            title: "Connection Failed",
            message: urlParams.get("msg")
                ? decodeURIComponent(urlParams.get("msg"))
                : "Could not link your social account.",
        };
        window.history.replaceState(
            {},
            document.title,
            window.location.pathname,
        );
        setTimeout(() => (notification.value = null), 5000);
    }

    try {
        const res = await axios.get("/api/creator/dashboard", {
            withCredentials: true,
        });
        data.value = res.data;

        // Set default platform based on what's connected
        if (data.value?.social_accounts) {
            const connected = data.value.social_accounts.find(
                (a) => a.is_connected && a.analytics_data,
            );
            if (connected) {
                selectedPlatform.value = connected.platform;
                activeTab.value =
                    platformTabs[connected.platform]?.[0]?.id || "views";
            }
        }
    } catch (err) {
        if (err.response?.status === 402 || err.response?.data?.requires_payment) {
            router.replace("/creator/choose-plan");
            return;
        }
        console.error("Failed to load dashboard:", err);
    }
});

const selectedAccount = computed(() => {
    return data.value?.social_accounts?.find(
        (a) =>
            a.platform === selectedPlatform.value &&
            a.is_connected &&
            a.analytics_data,
    );
});

const analyticsHistory = computed(() => {
    return selectedAccount.value?.analytics_data?.history ?? [];
});

const topContent = computed(() => {
    const ad = selectedAccount.value?.analytics_data;
    return ad?.top_videos ?? [];
});

const activeMetricIndex = computed(() => {
    const tabs = platformTabs[selectedPlatform.value] || [];
    const tab = tabs.find((t) => t.id === activeTab.value);
    return tab ? tab.index : 3;
});

const activeTabLabel = computed(() => {
    const tabs = platformTabs[selectedPlatform.value] || [];
    const tab = tabs.find((t) => t.id === activeTab.value);
    return tab ? tab.name : "Metric";
});

const demographics = computed(() => {
    const ad = selectedAccount.value?.analytics_data;
    const demoData = ad?.demographics ?? [];
    if (!demoData.length) return { gender: {}, age: [] };

    const genderMap = { male: 0, female: 0 };
    const ageGroups = {};

    demoData.forEach((row) => {
        // Standardizing demographic data parsing (YouTube: [age, gender, %])
        if (row.length >= 3) {
            const age = row[0];
            const gender = row[1].toLowerCase();
            const value = parseFloat(row[2]);
            genderMap[gender] = (genderMap[gender] || 0) + value;
            ageGroups[age] = (ageGroups[age] || 0) + value;
        }
    });

    return {
        gender: genderMap,
        age: Object.entries(ageGroups).sort((a, b) => b[1] - a[1]),
    };
});

const connectedSocialCount = computed(() => {
    const accounts = data.value?.social_accounts ?? [];
    return accounts.filter((a) => a.is_connected).length;
});

function formatFollowers(n) {
    if (n == null || n === "") return "0";
    const num = Number(n);
    if (num >= 1e6) return (num / 1e6).toFixed(1) + "M";
    if (num >= 1e3) return (num / 1e3).toFixed(1) + "K";
    return num.toLocaleString();
}

function typeLabel(type) {
    const map = {
        instagram: "Instagram",
        tiktok: "TikTok",
        ugc: "UGC",
        youtube: "YouTube",
    };
    return type ? map[type] || type : "—";
}

function applicationStatusLabel(status) {
    const map = {
        pending: "Pending",
        approved: "Approved",
        rejected: "Rejected",
    };
    return status ? map[status] || status : "—";
}

function applicationStatusClass(status) {
    const map = {
        pending: "bg-amber-50 text-amber-700 border-amber-100",
        approved: "bg-emerald-50 text-emerald-700 border-emerald-100",
        rejected: "bg-red-50 text-red-700 border-red-100",
    };
    return map[status] || "bg-slate-50 text-slate-600 border-slate-100";
}

function collaborationStatusClass(status) {
    const map = {
        completed: "bg-emerald-50 text-emerald-600 border-emerald-100",
        active: "bg-blue-50 text-blue-600 border-blue-100",
        pending: "bg-amber-50 text-amber-600 border-amber-100",
        accepted: "bg-emerald-50 text-emerald-600 border-emerald-100",
    };
    return map[status] || "bg-slate-50 text-slate-600 border-slate-100";
}

function formatDate(iso) {
    if (!iso) return "";
    const d = new Date(iso);
    return d.toLocaleDateString("en-IN", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
}
</script>
