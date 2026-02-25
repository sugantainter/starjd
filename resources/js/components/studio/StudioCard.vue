<template>
  <router-link
    :to="'/studios/' + (studio.slug || studio.id)"
    class="group flex flex-col overflow-hidden rounded-2xl border border-[#e2e8f0] bg-white shadow-sm transition hover:border-[#e63946]/30 hover:shadow-md"
  >
    <div class="relative aspect-[4/3] shrink-0 overflow-hidden bg-[#f1f5f9]">
      <img
        :src="mainImage || placeholderImage"
        :alt="studio.name"
        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
      />
      <span v-if="studio.featured" class="absolute left-2 top-2 rounded-full bg-[#f59e0b] px-2 py-0.5 text-xs font-medium text-white">Featured</span>
      <span v-if="studio.category?.name" class="absolute bottom-2 left-2 rounded bg-gray-800/80 px-2 py-0.5 text-xs font-medium text-white">{{ studio.category.name }}</span>
      <div v-if="studio.rating_avg != null" class="absolute bottom-2 right-2 flex items-center gap-1 rounded bg-black/60 px-2 py-1 text-xs font-medium text-white">
        <span>★</span> {{ studio.rating_avg }} ({{ studio.reviews_count }})
      </div>
    </div>
    <div class="flex flex-1 flex-col p-4">
      <h3 class="font-semibold text-[#1a1a1a]">{{ studio.name }}</h3>
      <p v-if="studio.city" class="mt-1 text-sm text-[#64748b]">{{ studio.city }}{{ studio.state ? ', ' + studio.state : '' }}</p>
      <p v-if="studio.description" class="mt-2 line-clamp-2 text-sm text-[#64748b]">{{ studio.description }}</p>
      <p v-if="priceLabel" class="mt-3 text-base font-semibold text-[#e63946]">{{ priceLabel }}</p>
      <span class="mt-4 inline-block rounded-lg bg-[#e63946] px-4 py-2 text-center text-sm font-medium text-white transition group-hover:bg-[#c1121f]">View details</span>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  studio: { type: Object, required: true },
});

const placeholderImage = 'https://images.unsplash.com/photo-1595846519845-68e298c2f195?w=400&h=300&fit=crop';

const mainImage = computed(() => props.studio.main_image || null);

const priceLabel = computed(() => {
  const s = props.studio;
  if (s.price_per_hour != null) return `₹${s.price_per_hour}/hr`;
  if (s.price_per_day != null) return `₹${s.price_per_day}/day`;
  return null;
});
</script>
