<template>
  <div class="relative">
    <input
      v-model="searchQuery"
      type="text"
      :placeholder="placeholder"
      :disabled="disabled"
      class="w-full rounded-xl border border-[#e2e8f0] px-4 py-3 focus:outline-none focus:ring-1 disabled:cursor-not-allowed disabled:bg-[#f8fafc] disabled:text-[#94a3b8]"
      @focus="open = true"
      @blur="closeWithDelay"
    />

    <div
      v-if="open && !disabled"
      class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-[#e2e8f0] bg-white shadow-lg"
    >
      <button
        v-for="city in filteredOptions"
        :key="city.id"
        type="button"
        class="block w-full px-3 py-2 text-left text-sm text-[#1a1a1a] hover:bg-[#f8fafc]"
        @mousedown.prevent
        @click="selectCity(city)"
      >
        {{ city.name }}
      </button>
      <div v-if="!filteredOptions.length" class="px-3 py-2 text-sm text-[#64748b]">
        No city found
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [Number, String, null],
    default: null,
  },
  options: {
    type: Array,
    default: () => [],
  },
  placeholder: {
    type: String,
    default: 'Search and select city',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const searchQuery = ref('');

const filteredOptions = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) return props.options;
  return props.options.filter((city) => String(city.name || '').toLowerCase().includes(q));
});

function selectCity(city) {
  emit('update:modelValue', city.id);
  searchQuery.value = city.name;
  open.value = false;
}

function closeWithDelay() {
  setTimeout(() => {
    open.value = false;
  }, 120);
}

watch(
  () => [props.modelValue, props.options],
  () => {
    const selected = props.options.find((city) => String(city.id) === String(props.modelValue));
    searchQuery.value = selected ? selected.name : '';
  },
  { immediate: true, deep: true }
);
</script>
