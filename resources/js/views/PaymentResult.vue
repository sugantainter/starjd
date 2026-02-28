<template>
    <div class="mx-auto max-w-md px-4 py-16 text-center">
        <div
            v-if="status === 'success'"
            class="rounded-2xl border border-[#e2e8f0] bg-white p-8 shadow-sm"
        >
            <div
                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#10b981]/10 text-[#10b981]"
            >
                <svg
                    class="h-10 w-10"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-[#1a1a1a]">Payment successful</h1>
            <p class="mt-2 text-[#64748b]">Your payment has been completed.</p>
            <p class="mt-4 text-sm text-[#94a3b8]">
                Redirecting to your dashboard in 3 seconds...
            </p>
            <div class="mt-6 flex flex-col gap-3">
                <router-link
                    v-if="isBooking"
                    to="/studio/bookings"
                    class="rounded-xl bg-[#e63946] px-4 py-3 text-center font-medium text-white hover:bg-[#c1121f]"
                    >View my bookings</router-link
                >
                <router-link
                    to="/brand/dashboard"
                    class="rounded-xl border border-[#e2e8f0] px-4 py-3 text-center font-medium hover:bg-[#f8fafc]"
                    >Go to dashboard</router-link
                >
            </div>
        </div>
        <div
            v-else
            class="rounded-2xl border border-[#e2e8f0] bg-white p-8 shadow-sm"
        >
            <div
                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-600"
            >
                <svg
                    class="h-10 w-10"
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
            </div>
            <h1 class="text-xl font-bold text-[#1a1a1a]">Payment failed</h1>
            <p class="mt-2 text-[#64748b]">
                Your payment could not be completed. Try again from your
                dashboard.
            </p>
            <router-link
                to="/"
                class="mt-6 inline-block rounded-xl bg-[#e63946] px-4 py-3 font-medium text-white hover:bg-[#c1121f]"
                >Back to home</router-link
            >
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const status = computed(() => route.query.status || "failed");
const isBooking = computed(() => route.query.booking === "1");

onMounted(() => {
    document.title =
        status.value === "success"
            ? "Payment successful - StarJD"
            : "Payment failed - StarJD";

    // Auto-redirect to dashboard after 3 seconds on success
    if (status.value === "success") {
        setTimeout(() => {
            const targetRoute = isBooking.value
                ? "/studio/bookings"
                : "/brand/dashboard";
            router.push(targetRoute);
        }, 3000);
    }
});
</script>
