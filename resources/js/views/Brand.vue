<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-[#fff5f5] px-4 py-20 md:py-28">
      <div class="mx-auto max-w-4xl text-center">
        <h1 class="text-3xl font-bold text-[#1a1a1a] md:text-4xl">I'm a Brand</h1>
        <p class="mt-4 text-lg text-[#6b7280]">Find vetted creators. Brief, book, and get content delivered — all in one place.</p>
        <router-link to="/register?type=brand" class="cursor-link mt-8 inline-block rounded-xl bg-[#e63946] px-8 py-3 font-semibold text-white transition hover:bg-[#c1121f]">Join as Brand</router-link>
      </div>
    </section>
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-16 md:py-24">
      <div class="mx-auto max-w-6xl">
        <h2 class="section-title text-2xl font-bold text-[#1a1a1a] md:text-3xl">Why Brands Choose StarJD</h2>
        <div class="mt-12 grid gap-8 md:grid-cols-3">
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#1a1a1a]">Vetted Creators</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Search by niche, platform, and budget. Every creator is verified.</p>
          </div>
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#1a1a1a]">Escrow Payments</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Payments held securely until you approve content. No risk.</p>
          </div>
          <div class="rounded-2xl border border-[#e5e7eb] bg-[#fafaf9] p-6">
            <h3 class="font-semibold text-[#1a1a1a]">Campaign Tracking</h3>
            <p class="mt-2 text-sm text-[#6b7280]">Track briefs, approvals, and delivery from your dashboard.</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>
