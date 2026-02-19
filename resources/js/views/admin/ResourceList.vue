<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">{{ title }}</h1>
      <button v-if="showAdd" type="button" class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="$emit('add')">Add</button>
    </div>
    <div v-if="loading" class="text-[#64748b]">Loading…</div>
    <div v-else-if="!items.length" class="rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]">No items yet.</div>
    <div v-else class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th v-for="col in columns" :key="col.key" class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]">{{ col.label }}</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td v-for="col in columns" :key="col.key" class="px-4 py-3 text-sm text-[#1a1a1a]">{{ formatCell(item[col.key], col) }}</td>
            <td class="px-4 py-3 text-right">
              <button v-if="showEdit" type="button" class="text-[#e63946] hover:underline" @click="$emit('edit', item)">Edit</button>
              <button type="button" class="ml-3 text-red-600 hover:underline" @click="$emit('delete', item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  items: { type: Array, default: () => [] },
  columns: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  showAdd: { type: Boolean, default: true },
  showEdit: { type: Boolean, default: true },
});
defineEmits(['add', 'edit', 'delete']);

function formatCell(val, col) {
  if (val == null) return '—';
  if (col.key === 'body' || col.key === 'quote' || col.key === 'answer') return String(val).slice(0, 60) + (String(val).length > 60 ? '…' : '');
  return String(val);
}
</script>
