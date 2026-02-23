<template>
  <div class="min-h-screen bg-[#fafaf9]">
    <section class="border-b border-[#e5e7eb] bg-white px-4 py-20 md:py-28">
      <div class="mx-auto max-w-2xl">
        <h1 class="section-title text-3xl font-bold text-[#1a1a1a] md:text-4xl">Contact Us</h1>
        <p class="section-subtitle mt-4 text-[#6b7280]">Have a question or want to work with us? Send a message.</p>
        <p class="mt-2 text-sm text-[#94a3b8]">StarJD is powered by Suganta International.</p>

        <form @submit.prevent="submit" class="mt-10 space-y-6 rounded-2xl border border-[#e5e7eb] bg-white p-6 shadow-sm md:p-8">
          <div v-if="message" :class="['rounded-lg px-4 py-3 text-sm', success ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-800']">{{ message }}</div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Name</label>
            <input v-model="form.name" type="text" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="Your name" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Email</label>
            <input v-model="form.email" type="email" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="you@example.com" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Subject</label>
            <input v-model="form.subject" type="text" class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="Brief subject" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-[#1a1a1a]">Message</label>
            <textarea v-model="form.body" rows="5" required class="w-full rounded-xl border border-[#e5e7eb] px-4 py-3 text-[#1a1a1a] focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]" placeholder="Your message"></textarea>
          </div>
          <button type="submit" :disabled="loading" class="cursor-link w-full rounded-xl bg-[#e63946] px-6 py-3 font-semibold text-white transition hover:bg-[#c1121f] disabled:opacity-50">Send Message</button>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';

const form = reactive({ name: '', email: '', subject: '', body: '' });
const loading = ref(false);
const message = ref('');
const success = ref(false);

async function submit() {
  loading.value = true;
  message.value = '';
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    await axios.post('/api/contact', form, { headers: token ? { 'X-CSRF-TOKEN': token } : {} });
    success.value = true;
    message.value = 'Thanks! We\'ll get back to you soon.';
    form.name = form.email = form.subject = form.body = '';
  } catch (e) {
    success.value = false;
    message.value = e.response?.data?.message || 'Something went wrong. Please try again.';
  } finally {
    loading.value = false;
  }
}
</script>
