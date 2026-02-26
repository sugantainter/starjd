<template>
  <aside class="w-full shrink-0 rounded-xl border border-[#e2e8f0] bg-white p-4 lg:w-64">
    <h3 class="font-semibold text-[#1a1a1a]">Filters</h3>

    <div class="mt-4">
      <label class="block text-sm font-medium text-[#64748b]">Category</label>
      <select
        :value="model.category"
        class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
        @change="emit('update:category', $event.target.value)"
      >
        <option value="">All</option>
        <option v-for="c in categories" :key="c.id" :value="c.slug">{{ c.name }}</option>
      </select>
    </div>

    <div class="mt-4">
      <label class="block text-sm font-medium text-[#64748b]">City</label>
      <input
        :value="model.city"
        type="text"
        placeholder="e.g. Mumbai"
        class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none focus:ring-1 focus:ring-[#e63946]"
        @input="emit('update:city', $event.target.value)"
      />
    </div>

    <div class="mt-4">
      <label class="block text-sm font-medium text-[#64748b]">Price range (₹/hr)</label>
      <div class="mt-1 flex gap-2">
        <input
          :value="model.min_price"
          type="number"
          min="0"
          placeholder="Min"
          class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none"
          @input="emit('update:min_price', $event.target.value ? Number($event.target.value) : '')"
        />
        <input
          :value="model.max_price"
          type="number"
          min="0"
          placeholder="Max"
          class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none"
          @input="emit('update:max_price', $event.target.value ? Number($event.target.value) : '')"
        />
      </div>
    </div>

    <div class="mt-4">
      <label class="block text-sm font-medium text-[#64748b]">Amenities</label>
      <div class="mt-2 space-y-1">
        <label v-for="a in amenities" :key="a.id" class="flex cursor-pointer items-center gap-2">
          <input
            type="checkbox"
            :checked="model.amenities && model.amenities.includes(a.id)"
            class="rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]"
            @change="toggleAmenity(a.id)"
          />
          <span class="text-sm text-[#1a1a1a]">{{ a.name }}</span>
        </label>
      </div>
    </div>

    <div class="mt-4">
      <label class="flex cursor-pointer items-center gap-2">
        <input
          type="checkbox"
          :checked="model.featured"
          class="rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]"
          @change="emit('update:featured', $event.target.checked)"
        />
        <span class="text-sm text-[#1a1a1a]">Featured only</span>
      </label>
    </div>

    <div class="mt-4">
      <label class="block text-sm font-medium text-[#64748b]">Min. rating</label>
      <select
        :value="model.rating"
        class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none"
        @change="emit('update:rating', $event.target.value ? Number($event.target.value) : '')"
      >
        <option value="">Any</option>
        <option value="3">3+</option>
        <option value="4">4+</option>
        <option value="4.5">4.5+</option>
      </select>
    </div>

    <div class="mt-6 flex gap-2">
      <button
        type="button"
        class="flex-1 rounded-lg bg-[#e63946] px-3 py-2 text-sm font-medium text-white hover:bg-[#c1121f]"
        @click="emit('apply')"
      >
        Apply
      </button>
      <button
        type="button"
        class="rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm hover:bg-[#f1f5f9]"
        @click="emit('clear')"
      >
        Clear
      </button>
    </div>
  </aside>
</template>

<script setup>
const props = defineProps({
  categories: { type: Array, default: () => [] },
  amenities: { type: Array, default: () => [] },
  model: { type: Object, required: true },
});

const emit = defineEmits(['update:category', 'update:city', 'update:min_price', 'update:max_price', 'update:featured', 'update:rating', 'update:amenities', 'apply', 'clear']);

function toggleAmenity(id) {
  const current = props.model?.amenities || [];
  const next = current.includes(id) ? current.filter((x) => x !== id) : [...current, id];
  emit('update:amenities', next);
}
</script>
