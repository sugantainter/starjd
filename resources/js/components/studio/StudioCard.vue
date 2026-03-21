<template>
  <router-link
    :to="'/studios/' + (studio.slug || studio.id)"
    class="group flex flex-col md:flex-row overflow-hidden rounded-3xl border border-[#e2e8f0] bg-white hover:border-[#e63946]/40 hover:shadow-2xl transition-all duration-500 w-full"
  >
    <!-- Studio Image -->
    <div class="relative w-full md:w-80 h-64 md:h-auto shrink-0 overflow-hidden bg-[#f1f5f9]">
      <img
        :src="mainImage || placeholderImage"
        :alt="studio.name"
        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
      />
      <div v-if="studio.featured" class="absolute top-4 left-4">
        <span class="bg-[#f59e0b] text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-tighter shadow-lg shadow-[#f59e0b]/20">Premium</span>
      </div>
      <div v-if="studio.rating_avg != null" class="absolute bottom-4 right-4 flex items-center gap-1.5 rounded-xl bg-black/60 backdrop-blur-md px-3 py-1.5 text-xs font-bold text-white border border-white/20">
        <span class="text-[#f59e0b]">★</span> {{ studio.rating_avg }}
      </div>
    </div>

    <!-- Studio Content -->
    <div class="flex-1 p-8 md:p-10 flex flex-col">
      <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-6">
        <div>
          <div class="flex items-center gap-2 mb-2">
            <span v-if="studio.category?.name" class="text-[10px] font-black text-[#e63946] uppercase tracking-widest">{{ studio.category.name }}</span>
            <span class="w-1 h-1 bg-[#cbd5e1] rounded-full"></span>
            <span v-if="studio.city" class="text-[10px] font-bold text-[#64748b] uppercase tracking-widest">{{ studio.city }}</span>
          </div>
          <h3 class="text-3xl font-black text-[#1a1a1a] group-hover:text-[#e63946] transition-colors leading-tight mb-4">{{ studio.name }}</h3>
          <p v-if="studio.description" class="text-[#64748b] text-lg line-clamp-2 leading-relaxed max-w-2xl">{{ studio.description }}</p>
        </div>

        <div class="flex flex-col items-start md:items-end shrink-0">
           <div class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest mb-1">Starting from</div>
           <div class="text-4xl font-black text-[#1a1a1a]">{{ priceLabel || 'Contact' }}</div>
        </div>
      </div>

      <div class="mt-auto pt-8 border-t border-[#f1f5f9] flex flex-wrap items-center justify-between gap-6">
         <div class="flex items-center gap-6">
            <div v-if="studio.capacity" class="flex flex-col">
               <span class="text-[10px] font-bold text-[#94a3b8] uppercase mb-1">Capacity</span>
               <span class="text-sm font-black text-[#1a1a1a]">{{ studio.capacity }} People</span>
            </div>
            <div v-if="studio.sqft" class="flex flex-col">
               <span class="text-[10px] font-bold text-[#94a3b8] uppercase mb-1">Size</span>
               <span class="text-sm font-black text-[#1a1a1a]">{{ studio.sqft }} sqft</span>
            </div>
            <div class="flex flex-col">
               <span class="text-[10px] font-bold text-[#94a3b8] uppercase mb-1">Availability</span>
               <span class="text-sm font-black text-green-600">Open for Booking</span>
            </div>
         </div>

         <div class="flex items-center gap-3">
            <button class="px-8 py-4 bg-black text-white rounded-2xl font-black text-sm hover:bg-[#e63946] transition-all group-hover:scale-105 shadow-xl shadow-black/10">
               Book Studio
            </button>
         </div>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  studio: { type: Object, required: true },
});

const placeholderImage = 'https://images.unsplash.com/photo-1595846519845-68e298c2f195?w=800&h=600&fit=crop';

const mainImage = computed(() => props.studio.main_image || null);

const priceLabel = computed(() => {
  const s = props.studio;
  if (s.price_per_hour != null) return `₹${s.price_per_hour}/hr`;
  if (s.price_per_day != null) return `₹${s.price_per_day}/day`;
  return null;
});
</script>
