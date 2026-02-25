<template>
  <div class="min-h-screen bg-[#fafaf9] text-[#1a1a1a]">
    <Navbar />

    <main>
      <router-view />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
</script>
