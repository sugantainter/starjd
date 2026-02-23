<template>
  <header class="mb-6">
    <nav v-if="breadcrumbs.length" class="mb-3 flex items-center gap-1.5 text-sm text-[#64748b]">
      <router-link to="/admin" class="hover:text-[#1a1a1a]">Dashboard</router-link>
      <template v-for="(crumb, i) in breadcrumbs" :key="crumb.path">
        <span class="text-[#cbd5e1]">/</span>
        <router-link v-if="i < breadcrumbs.length - 1" :to="crumb.path" class="hover:text-[#1a1a1a]">{{ crumb.label }}</router-link>
        <span v-else class="font-medium text-[#1a1a1a]">{{ crumb.label }}</span>
      </template>
    </nav>
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-[#1a1a1a]">{{ title }}</h1>
        <p v-if="description" class="mt-1 text-sm text-[#64748b]">{{ description }}</p>
      </div>
      <div v-if="$slots.actions" class="flex shrink-0 items-center gap-2">
        <slot name="actions" />
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  breadcrumbs: {
    type: Array,
    default: () => [],
    // [{ label: 'Pages', path: '/admin/pages' }]
  },
});
</script>
