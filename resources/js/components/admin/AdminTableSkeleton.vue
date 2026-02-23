<template>
  <div class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white">
    <table class="min-w-full divide-y divide-[#e2e8f0]">
      <thead class="bg-[#f8fafc]">
        <tr>
          <th v-for="i in columns" :key="i" class="px-4 py-3 text-left">
            <div class="h-3 w-24 animate-pulse rounded bg-[#e2e8f0]" />
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#e2e8f0]">
        <tr v-for="r in rows" :key="r" class="hover:bg-[#f8fafc]">
          <td v-for="c in columns" :key="c" class="px-4 py-3">
            <div :class="c === columns ? 'ml-auto h-3 w-20' : 'h-3 w-32'" class="animate-pulse rounded bg-[#e2e8f0]" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  columns: { type: Number, default: 4 },
  rows: { type: Number, default: 6 },
});
</script>
