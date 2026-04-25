<template>
  <div v-if="loading" class="mx-auto max-w-6xl px-4 py-12 text-center text-[#64748b]">
    <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#e63946] border-r-transparent align-[-0.125em]" role="status"></div>
    <p class="mt-4">Loading studio details...</p>
  </div>
  <div v-else-if="studio" class="mx-auto max-w-6xl px-4 py-8">
    <!-- Gallery -->
    <div class="relative overflow-hidden rounded-2xl bg-[#f1f5f9]">
      <img
        :src="currentImage"
        :alt="studio.name"
        class="h-[320px] w-full object-cover sm:h-[400px]"
      />
      <div v-if="studio.gallery?.length > 1" class="absolute bottom-4 left-0 right-0 flex justify-center gap-2">
        <button
          v-for="(img, i) in studio.gallery"
          :key="img.id"
          type="button"
          class="h-2 w-2 rounded-full transition"
          :class="currentIndex === i ? 'bg-[#e63946] scale-125' : 'bg-white/70 hover:bg-white'"
          @click="currentIndex = i"
        />
      </div>
      <span v-if="studio.featured" class="absolute left-4 top-4 rounded-full bg-[#f59e0b] px-3 py-1 text-sm font-medium text-white">Featured</span>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
      <!-- Main Content -->
      <div v-if="studio" class="lg:col-span-2">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h1 class="text-3xl font-bold text-[#1a1a1a]">{{ studio.name || 'Studio' }}</h1>
            <p v-if="studio.city" class="mt-1 flex items-center gap-1 text-[#64748b]">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              {{ studio.city }}{{ studio.state ? ', ' + studio.state : '' }}
            </p>
            <div v-if="studio.category" class="mt-2">
              <span class="rounded-full bg-[#e2e8f0] px-3 py-1 text-sm text-[#64748b]">
                {{ typeof studio.category === 'object' ? studio.category.name : studio.category }}
              </span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-2xl font-bold text-amber-500">★ {{ studio.rating_avg || 'New' }}</span>
            <span class="text-sm text-[#64748b]">({{ studio.reviews_count || 0 }} reviews)</span>
          </div>
        </div>

        <div v-if="studio.description" class="mt-6">
          <h2 class="text-lg font-semibold text-[#1a1a1a]">About</h2>
          <RichTextContent
            class="mt-2"
            :content="studio.description"
          />
        </div>

        <div v-if="studio.amenities?.length" class="mt-6">
          <h2 class="text-lg font-semibold text-[#1a1a1a]">Amenities</h2>
          <div class="mt-2 flex flex-wrap gap-2">
            <span
              v-for="a in studio.amenities"
              :key="a.id"
              class="rounded-lg border border-[#e2e8f0] bg-white px-3 py-1.5 text-sm text-[#64748b]"
            >{{ a.name }}</span>
          </div>
        </div>

        <div class="mt-6">
          <h2 class="text-lg font-semibold text-[#1a1a1a]">Cancellation policy</h2>
          <p class="mt-2 text-sm text-[#64748b]">
            <template v-if="(studio.cancellation_policy || 'moderate') === 'flexible'">Full refund if cancelled at least 24 hours before start.</template>
            <template v-else-if="(studio.cancellation_policy || 'moderate') === 'strict'">Full refund if cancelled at least 7 days before start.</template>
            <template v-else>Full refund if cancelled at least 72 hours before start.</template>
          </p>
        </div>

        <div class="mt-8">
          <ReviewList :reviews="studio.reviews || []" />
        </div>

        <div class="mt-8">
          <AvailabilityCalendar :slots="studio.availability_slots || []" />
        </div>

        <div v-if="similarStudios?.length" class="mt-10">
          <h2 class="text-xl font-semibold text-[#1a1a1a]">Similar studios</h2>
          <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <StudioCard v-for="s in similarStudios" :key="s.id" :studio="s" />
          </div>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div class="sticky top-24">
          <BookingWidget
            :studio="studio"
            :breakdown="priceBreakdown"
            :can-book="true"
            :booking-in-progress="bookingInProgress"
            v-model:date="bookingDate"
            v-model:start-time="bookingStart"
            v-model:end-time="bookingEnd"
            @book="onBook"
          />
        </div>
      </div>
    </div>
  </div>
  <div v-else class="mx-auto max-w-6xl px-4 py-24 text-center">
    <div class="mb-4 text-center text-5xl opacity-20">🔍</div>
    <h2 class="text-2xl font-bold text-[#1a1a1a]">Studio not found</h2>
    <p class="mt-2 text-[#64748b]">{{ errorMessage || 'The studio you are looking for does not exist or has been moved.' }}</p>
    <router-link to="/studios" class="mt-6 inline-block rounded-xl bg-[#e63946] px-6 py-3 font-medium text-white hover:bg-[#c1121f]">Browse All Studios</router-link>
  </div>
  <form ref="payuForm" method="post" :action="payuUrl" class="hidden">
    <input v-for="(val, key) in payuParams" :key="key" :name="key" :value="val" type="hidden" />
  </form>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import StudioCard from '../components/studio/StudioCard.vue';
import BookingWidget from '../components/studio/BookingWidget.vue';
import ReviewList from '../components/studio/ReviewList.vue';
import AvailabilityCalendar from '../components/studio/AvailabilityCalendar.vue';
import RichTextContent from '../components/RichTextContent.vue';
import { notify } from '../lib/notify.js';

const route = useRoute();
const studio = ref(null);
const loading = ref(true);
const currentIndex = ref(0);
const bookingDate = ref('');
const bookingStart = ref('09:00');
const bookingEnd = ref('10:00');
const priceBreakdown = ref(null);
const bookingInProgress = ref(false);
const errorMessage = ref('');

const similarStudios = computed(() => studio.value?.similar_studios ?? []);

const currentImage = computed(() => {
  const g = studio.value?.gallery;
  if (!g?.length) return studio.value?.main_image || 'https://images.unsplash.com/photo-1595846519845-68e298c2f195?w=800&h=500&fit=crop';
  return g[currentIndex.value]?.image || g[0]?.image;
});

// useHead must be called in setup synchronously
useHead({
  title: computed(() => {
    if (!studio.value) return 'Studio | StarJD';
    const name = studio.value.name || 'Studio';
    const city = studio.value.city || 'India';
    return `${name} | StarJD`;
  }),
  meta: [
    {
      name: 'description',
      content: computed(() => {
        if (!studio.value) return 'Book professional studios on StarJD.';
        return studio.value.description?.replace(/<[^>]*>/g, '').substring(0, 160) || 'Book this professional studio on StarJD.';
      })
    }
  ]
});

async function fetchStudio() {
  const slug = route.params.slug;
  if (!slug) return;
  
  loading.value = true;
  errorMessage.value = '';
  try {
    const res = await axios.get('/api/studios/' + slug);
    const data = res.data?.data ?? res.data;
    if (data && typeof data === 'object' && data.id) {
      studio.value = data;
    } else {
      studio.value = null;
      errorMessage.value = 'Studio data is empty or invalid.';
    }
  } catch (err) {
    console.error('Failed to fetch studio:', err);
    studio.value = null;
    errorMessage.value = err.response?.data?.message || err.message || 'Could not connect to the server.';
  } finally {
    loading.value = false;
  }
}

async function fetchPrice() {
  if (!studio.value?.id || !bookingDate.value || !bookingStart.value || !bookingEnd.value) {
    priceBreakdown.value = null;
    return;
  }
  try {
    const params = {
      studio_id: studio.value.id,
      date: bookingDate.value,
      start_time: bookingStart.value,
      end_time: bookingEnd.value,
    };
    const res = await axios.get('/api/bookings/calculate', { params });
    priceBreakdown.value = res.data;
  } catch {
    priceBreakdown.value = null;
  }
}

watch([bookingDate, bookingStart, bookingEnd], () => fetchPrice());

const payuForm = ref(null);
const payuUrl = ref('');
const payuParams = ref({});

async function onBook() {
  if (!bookingDate.value || !bookingStart.value || !bookingEnd.value) return;
  bookingInProgress.value = true;
  try {
    const bookPayload = {
      studio_id: studio.value.id,
      date: bookingDate.value,
      start_time: bookingStart.value,
      end_time: bookingEnd.value,
      cancellation_policy: 'moderate',
    };
    const bookRes = await axios.post('/api/bookings', bookPayload, { withCredentials: true });
    const booking = bookRes.data?.booking;
    if (!booking?.id || booking.amount == null) {
      notify.success('Booking created. Complete payment from your account.');
      return;
    }

    if (booking.status === 'confirmed') {
      notify.success('Success! Your booking is confirmed with 100% discount.');
      location.href = '/dashboard/bookings';
      return;
    }

    const payRes = await axios.post('/api/payment/payu/create', {
      type: 'booking',
      booking_id: booking.id,
      amount: Number(booking.amount),
    }, { withCredentials: true });
    payuUrl.value = payRes.data.payment_url;
    payuParams.value = payRes.data.params || {};
    nextTick(() => {
      if (payuForm.value) payuForm.value.submit();
    });
  } catch (e) {
    notify.error(e.response?.data?.message || 'Booking failed. Are you logged in?');
    bookingInProgress.value = false;
  }
}

onMounted(() => fetchStudio());
watch(() => route.params.slug, () => fetchStudio());
</script>
