<template>
  <div class="flex min-h-screen flex-col bg-[#fafaf9] text-[#1a1a1a]">
    <Navbar />

    <main class="flex-1">
      <router-view />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
</script>
