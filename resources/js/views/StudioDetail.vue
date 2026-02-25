<template>
  <div v-if="loading" class="mx-auto max-w-6xl px-4 py-12 text-center text-[#64748b]">Loading…</div>
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
      <div class="lg:col-span-2">
        <h1 class="text-3xl font-bold text-[#1a1a1a]">{{ studio.name }}</h1>
        <p v-if="studio.city" class="mt-2 text-[#64748b]">{{ studio.city }}{{ studio.state ? ', ' + studio.state : '' }}</p>
        <div v-if="studio.rating_avg != null" class="mt-2 flex items-center gap-2 text-amber-600">
          <span>★ {{ studio.rating_avg }}</span>
          <span class="text-sm text-[#64748b]">({{ studio.reviews_count }} reviews)</span>
        </div>
        <div v-if="studio.category" class="mt-2">
          <span class="rounded-full bg-[#e2e8f0] px-3 py-1 text-sm text-[#64748b]">{{ studio.category.name }}</span>
        </div>

        <div v-if="studio.description" class="mt-6">
          <h2 class="text-lg font-semibold text-[#1a1a1a]">About</h2>
          <div
            class="studio-description prose prose-sm mt-2 max-w-none text-[#64748b] prose-headings:text-[#1a1a1a] prose-p:my-2 prose-ul:my-2 prose-ol:my-2 prose-li:my-0.5"
            v-html="studio.description"
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
  <div v-else class="mx-auto max-w-6xl px-4 py-12 text-center text-[#64748b]">Studio not found.</div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import StudioCard from '../components/studio/StudioCard.vue';
import BookingWidget from '../components/studio/BookingWidget.vue';
import ReviewList from '../components/studio/ReviewList.vue';
import AvailabilityCalendar from '../components/studio/AvailabilityCalendar.vue';

const route = useRoute();
const studio = ref(null);
const loading = ref(true);
const currentIndex = ref(0);
const bookingDate = ref('');
const bookingStart = ref('09:00');
const bookingEnd = ref('10:00');
const priceBreakdown = ref(null);
const bookingInProgress = ref(false);

const similarStudios = computed(() => studio.value?.similar_studios ?? []);

const currentImage = computed(() => {
  const g = studio.value?.gallery;
  if (!g?.length) return studio.value?.main_image || 'https://images.unsplash.com/photo-1595846519845-68e298c2f195?w=800&h=500&fit=crop';
  return g[currentIndex.value]?.image || g[0]?.image;
});

async function fetchStudio() {
  loading.value = true;
  try {
    const res = await axios.get('/api/studios/' + route.params.slug);
    studio.value = res.data;
  } catch {
    studio.value = null;
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
    const res = await axios.get('/api/bookings/calculate', {
      params: {
        studio_id: studio.value.id,
        date: bookingDate.value,
        start_time: bookingStart.value,
        end_time: bookingEnd.value,
      },
    });
    priceBreakdown.value = res.data;
  } catch {
    priceBreakdown.value = null;
  }
}

watch([bookingDate, bookingStart, bookingEnd], () => fetchPrice());

async function onBook() {
  if (!bookingDate.value || !bookingStart.value || !bookingEnd.value) return;
  bookingInProgress.value = true;
  try {
    await axios.post('/api/bookings', {
      studio_id: studio.value.id,
      date: bookingDate.value,
      start_time: bookingStart.value,
      end_time: bookingEnd.value,
      cancellation_policy: 'moderate',
    });
    alert('Booking request created. Complete payment to confirm.');
  } catch (e) {
    alert(e.response?.data?.message || 'Booking failed. Are you logged in?');
  } finally {
    bookingInProgress.value = false;
  }
}

onMounted(() => fetchStudio());
watch(() => route.params.slug, () => fetchStudio());
</script>
