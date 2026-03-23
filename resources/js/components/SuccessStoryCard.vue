<template>
  <div class="group relative overflow-hidden rounded-2xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl border border-[#e2e8f0]">
    <router-link :to="'/success-stories/' + story.slug" class="block h-full">
      <div class="aspect-[16/10] overflow-hidden bg-slate-100">
        <img
          v-if="story.image"
          :src="story.image"
          :alt="story.title"
          class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
        />
        <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#e63946]/5 to-[#e63946]/10 text-[#e63946]/20">
          <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
      </div>
      
      <div class="p-5">
        <div class="mb-3 flex items-center justify-between">
          <span v-if="story.role" class="rounded-full bg-[#e63946]/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#e63946]">
            {{ story.role.name }}
          </span>
          <span class="text-[10px] font-medium text-[#94a3b8]">{{ formatDate(story.created_at) }}</span>
        </div>
        
        <h3 class="mb-2 line-clamp-2 text-lg font-bold leading-tight text-[#1a1a1a] transition-colors group-hover:text-[#e63946]">
          {{ story.title }}
        </h3>
        
        <div v-if="story.author_name" class="mt-4 flex items-center gap-2 border-t border-[#f1f5f9] pt-4">
          <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#1a1a1a] text-[10px] font-bold text-white uppercase">
            {{ story.author_name.charAt(0) }}
          </div>
          <div class="min-w-0">
            <p class="truncate text-xs font-semibold text-[#1a1a1a]">{{ story.author_name }}</p>
            <p v-if="story.author_designation" class="truncate text-[10px] text-[#64748b]">{{ story.author_designation }}</p>
          </div>
        </div>
      </div>
    </router-link>
  </div>
</template>

<script setup>
defineProps({
  story: {
    type: Object,
    required: true,
  },
});

function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>
