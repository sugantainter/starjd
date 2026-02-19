<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-[#f0fdf4] px-4 py-20 md:py-28">
      <div class="mx-auto max-w-4xl text-center">
        <h1 class="text-3xl font-bold text-[#1a1a1a] md:text-4xl">I'm a Creator</h1>
        <p class="mt-4 text-lg text-[#6b7280]">Get discovered by brands. Set your rates. Get paid securely.</p>
        <router-link to="/register?type=creator" class="cursor-link mt-8 inline-block rounded-xl bg-[#10b981] px-8 py-3 font-semibold text-white transition hover:bg-[#059669]">Join as Creator</router-link>
      </div>
    </section>
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <h2 class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl">Why Creators Choose StarJD</h2>
        <div class="mt-12 grid gap-8 md:grid-cols-3">
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#1a1a1a]">Set Your Prices</h3>
            <p class="mt-2 text-sm text-[#6b7280]">You decide your rates. No middleman cuts.</p>
          </div>
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#1a1a1a]">Secure Payments</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Get paid when brands approve. Escrow protection for both sides.</p>
          </div>
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#1a1a1a]">Direct Messaging</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Chat with brands, receive briefs, and accept only what you want.</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>
