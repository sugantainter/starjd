<template>
    <div class="min-h-screen bg-white">
        <!-- Hero: sirf yahan #f0f7f4; contact form thoda upar, card white; baki page white -->
        <section
            class="relative overflow-hidden border-b border-[#e5e7eb] bg-[#feece5] px-4 pt-12 pb-16 sm:pt-16 sm:pb-20 md:pt-20 md:pb-24 lg:pt-24 lg:pb-28"
        >
            <div class="relative mx-auto max-w-6xl">
                <div
                    class="grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14"
                >
                    <!-- Left: headline, subheadline, popular, trusted -->
                    <div class="order-2 lg:order-1">
                        <h1
                            class="text-3xl font-bold leading-tight text-[#1a1a1a] sm:text-4xl md:text-[2.75rem] lg:text-5xl"
                        >
                            <span class="block">Where Ideas Become</span>
                            <span class="block">Powerful Visual Stories</span>
                        </h1>
                        <p class="mt-4 text-base text-[#64748b] sm:text-lg">
                            Transform your vision with professional video production, photography, studio rental, and live streaming services. We help brands, startups, and creators produce high-quality content that drives engagement and builds impact.
                        </p>

                        <p
                            class="mt-8 text-xs font-medium uppercase tracking-wider text-[#94a3b8]"
                        >
                            Our Services:
                        </p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="rounded-lg px-4 py-2 text-sm text-[#64748b] transition hover:bg-white hover:text-[#fc4402] hover:shadow-sm"
                                @click="scrollToForm"
                            >
                                Video Production
                            </button>
                            <button
                                type="button"
                                class="rounded-lg px-4 py-2 text-sm text-[#64748b] transition hover:bg-white hover:text-[#fc4402] hover:shadow-sm"
                                @click="scrollToForm"
                            >
                                Photography
                            </button>
                            <button
                                type="button"
                                class="rounded-lg px-4 py-2 text-sm text-[#64748b] transition hover:bg-white hover:text-[#fc4402] hover:shadow-sm"
                                @click="scrollToForm"
                            >
                                Podcast Studio Rental
                            </button>
                            <button
                                type="button"
                                class="rounded-lg px-4 py-2 text-sm text-[#64748b] transition hover:bg-white hover:text-[#fc4402] hover:shadow-sm"
                                @click="scrollToForm"
                            >
                                Live Streaming
                            </button>
                            <button
                                type="button"
                                class="rounded-lg px-4 py-2 text-sm text-[#64748b] transition hover:bg-white hover:text-[#fc4402] hover:shadow-sm"
                                @click="scrollToForm"
                            >
                                Social Media Content
                            </button>
                        </div>

                        <p
                            class="mt-8 text-xs font-medium uppercase tracking-wider text-[#94a3b8]"
                        >
                            Fully equipped studio. Experienced team. Fast turnaround.
                        </p>
                        <div
                            class="mt-3 flex flex-wrap items-center gap-6 opacity-80"
                        >
                            <span class="text-lg font-bold text-[#1a1a1a]"
                                >StarJD</span
                            >
                            <span class="text-sm font-semibold text-[#64748b]"
                                >Suganta International</span
                            >
                        </div>
                    </div>

                    <!-- Right: illustration + floating cards -->
                    <div
                        class="relative order-1 flex justify-center lg:order-2 lg:min-h-[380px] lg:justify-end"
                    >
                        <div
                            class="relative w-full max-w-[320px] sm:max-w-[360px] md:max-w-[400px]"
                        >
                            <!-- Decorative circle behind -->
                            <div
                                class="absolute left-1/2 top-1/2 h-[280px] w-[280px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#e8f0ec] sm:h-[320px] sm:w-[320px] md:h-[360px] md:w-[360px]"
                                aria-hidden="true"
                            />
                            <!-- Main image -->
                            <div
                                class="relative z-10 aspect-square overflow-hidden rounded-2xl bg-[#e2e8f0] shadow-lg md:rounded-3xl"
                            >
                                <img
                                    src="/images/contact-us.png"
                                    alt="Creative collaboration"
                                    class="h-full w-full object-cover object-center"
                                />
                            </div>
                            <!-- Floating card 1: Quick response -->
                            <div
                                class="absolute left-0 top-[18%] z-20 w-[140px] rounded-xl border border-[#e5e7eb] bg-white p-3 shadow-lg sm:left-[-8px] sm:w-[160px] md:left-[-12px]"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-600"
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
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                            />
                                        </svg>
                                    </span>
                                    <span
                                        class="text-xs font-semibold text-[#1a1a1a] sm:text-sm"
                                        >Quick response</span
                                    >
                                </div>
                                <p
                                    class="mt-1 text-[10px] text-[#64748b] sm:text-xs"
                                >
                                    We reply within 24 hrs
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact form: hero ke bahar, sirf white background; #f0f7f4 sirf hero me -->
        <div
            class="relative z-10 px-4 pb-12 sm:pb-16 md:pb-20 -mt-8 sm:-mt-10 md:-mt-12"
        >
            <div class="relative mx-auto max-w-6xl">
                <div id="contact-form" class="w-full">
                    <div
                        class="overflow-hidden rounded-2xl border border-[#e5e7eb] bg-white shadow-xl md:rounded-3xl"
                    >
                        <div class="grid gap-0 md:grid-cols-2">
                            <!-- Left: Get In Touch -->
                            <div
                                class="relative overflow-hidden border-b border-[#e5e7eb] bg-cover bg-center bg-no-repeat p-6 md:border-b-0 md:border-r md:border-[#e5e7eb] md:p-8 lg:p-10"
                                style="background-image: url('/images/contact-2.jpg')"
                            >
                                <div class="absolute inset-0 bg-white/75" aria-hidden="true" />
                                <div class="relative z-10">
                                <h2
                                    class="text-2xl font-bold text-[#1a1a1a] md:text-3xl"
                                >
                                    Get In Touch
                                </h2>
                                <div class="mt-6 space-y-5">
                                    <div class="flex gap-4">
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[#fc4402]"
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
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                        </span>
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-[#1a1a1a]"
                                            >
                                                Locations
                                            </p>
                                            <p
                                                class="mt-0.5 text-sm text-[#000000]"
                                            >
                                                SuGanta International, 4th
                                                Floor, 96A, Block- B, Pocket-10,
                                                Dwarka Sector -13, New Delhi
                                                110075, Delhi, India
                                            </p>
                                        </div>
                                    </div>
                                    <hr class="border-[#e5e7eb]" />
                                    <div class="flex gap-4">
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[#fc4402]"
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
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </span>
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-[#1a1a1a]"
                                            >
                                                Email Address
                                            </p>
                                            <a
                                                href="mailto:shine@starjd.com"
                                                class="mt-0.5 block text-sm text-[#000000] hover:text-[#fc4402]"
                                                >shine@starjd.com</a
                                            >
                                        </div>
                                    </div>
                                    <hr class="border-[#e5e7eb]" />
                                    <div class="flex gap-4">
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[#fc4402]"
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
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                                />
                                            </svg>
                                        </span>
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-[#1a1a1a]"
                                            >
                                                Call us
                                            </p>
                                            <a
                                                href="tel:+919540463434"
                                                class="mt-0.5 block text-sm text-[#000000] hover:text-[#fc4402]"
                                                >+91 95404 63434</a
                                            >
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Right: Drop Us Your Query (contact form) -->
                            <div class="p-6 md:p-8 lg:p-10">
                                <div class="flex items-start gap-3">
                                    <span
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#fc4402]/10 text-[#fc4402]"
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
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                    </span>
                                    <div>
                                        <h2
                                            class="text-xl font-bold text-[#1a1a1a] md:text-2xl"
                                        >
                                            Have a project in mind?
                                        </h2>
                                        <p class="mt-2 text-sm text-[#64748b]">
                                            Send us your query — we’ll respond within 24 hours. Let’s create something impactful together.
                                        </p>
                                    </div>
                                </div>

                                <form
                                    @submit.prevent="submit"
                                    class="mt-8 space-y-6"
                                >
                                    <div
                                        v-if="message"
                                        :class="[
                                            'rounded-lg px-4 py-3 text-sm',
                                            success
                                                ? 'bg-emerald-50 text-emerald-800'
                                                : 'bg-red-50 text-red-800',
                                        ]"
                                    >
                                        {{ message }}
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1 block text-sm font-medium text-[#1a1a1a]"
                                            >Name</label
                                        >
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            required
                                            class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                                            placeholder="Your name"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1 block text-sm font-medium text-[#1a1a1a]"
                                            >Email</label
                                        >
                                        <input
                                            v-model="form.email"
                                            type="email"
                                            required
                                            class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                                            placeholder="you@example.com"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1 block text-sm font-medium text-[#1a1a1a]"
                                            >Subject</label
                                        >
                                        <input
                                            v-model="form.subject"
                                            type="text"
                                            class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                                            placeholder="Brief subject"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1 block text-sm font-medium text-[#1a1a1a]"
                                            >Message</label
                                        >
                                        <textarea
                                            v-model="form.body"
                                            rows="5"
                                            required
                                            class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
                                            placeholder="Your message"
                                        ></textarea>
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="loading"
                                        class="cursor-link w-auto min-w-[120px] rounded-xl bg-[#fc4402] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e63d02] disabled:opacity-50 sm:min-w-[140px] sm:px-6 sm:py-2.5 sm:text-base md:min-w-[160px]"
                                    >
                                        Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import axios from "axios";

const form = reactive({ name: "", email: "", subject: "", body: "" });
const loading = ref(false);
const message = ref("");
const success = ref(false);

function scrollToForm() {
    const el = document.getElementById("contact-form");
    if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
}

async function submit() {
    loading.value = true;
    message.value = "";
    try {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        await axios.post("/api/contact", form, {
            headers: token ? { "X-CSRF-TOKEN": token } : {},
        });
        success.value = true;
        message.value = "Thanks! We'll get back to you soon.";
        form.name = "";
        form.email = "";
        form.subject = "";
        form.body = "";
    } catch (e) {
        success.value = false;
        message.value =
            e.response?.data?.message ||
            "Something went wrong. Please try again.";
    } finally {
        loading.value = false;
    }
}
</script>
