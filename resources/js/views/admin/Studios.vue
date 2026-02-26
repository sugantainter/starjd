<template>
    <div>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="mb-2 text-2xl font-bold text-[#1a1a1a]">Studio listings</h1>
                <p class="text-sm text-[#64748b]">
                    Approve studios to make them visible on the marketplace. Only
                    <strong>active</strong> studios appear to customers.
                </p>
            </div>
            <router-link
                to="/admin/studios/new"
                class="shrink-0 rounded-xl bg-[#e63946] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#c1121f]"
            >
                + Add studio
            </router-link>
        </div>

        <div class="mb-4 flex flex-wrap items-center gap-2">
            <span class="text-sm text-[#64748b]">Filter:</span>
            <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                :class="
                    filterStatus === ''
                        ? 'bg-[#e63946] text-white'
                        : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'
                "
                @click="
                    filterStatus = '';
                    load();
                "
            >
                All
            </button>
            <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                :class="
                    filterStatus === 'draft'
                        ? 'bg-[#e63946] text-white'
                        : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'
                "
                @click="
                    filterStatus = 'draft';
                    load();
                "
            >
                Draft
            </button>
            <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                :class="
                    filterStatus === 'active'
                        ? 'bg-[#e63946] text-white'
                        : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'
                "
                @click="
                    filterStatus = 'active';
                    load();
                "
            >
                Active
            </button>
            <button
                type="button"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                :class="
                    filterStatus === 'inactive'
                        ? 'bg-[#e63946] text-white'
                        : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'
                "
                @click="
                    filterStatus = 'inactive';
                    load();
                "
            >
                Inactive
            </button>
        </div>

        <div v-if="loading" class="text-[#64748b]">Loading…</div>
        <div
            v-else-if="error"
            class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
        >
            {{ error }}
        </div>
        <div
            v-else-if="!items.length"
            class="rounded-xl border border-[#e2e8f0] bg-white p-8 text-center text-[#64748b]"
        >
            No studios found. Studios are added by studio owners; they start as
            <strong>draft</strong> until you approve them here.
        </div>
        <div
            v-else
            class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm"
        >
            <table class="min-w-full divide-y divide-[#e2e8f0]">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]"
                        >
                            Studio
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]"
                        >
                            Owner
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]"
                        >
                            Category
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]"
                        >
                            City
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]"
                        >
                            Status
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium uppercase text-[#64748b]"
                        >
                            Featured
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium uppercase text-[#64748b]"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e2e8f0]">
                    <tr
                        v-for="item in items"
                        :key="item.id"
                        class="hover:bg-[#f8fafc]"
                    >
                        <td class="px-4 py-3">
                            <span class="font-medium text-[#1a1a1a]">{{
                                item.name
                            }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#64748b]">
                            {{ item.owner?.name || "—" }}
                            <span
                                v-if="item.owner?.email"
                                class="block text-xs text-[#94a3b8]"
                                >{{ item.owner.email }}</span
                            >
                        </td>
                        <td class="px-4 py-3 text-sm text-[#64748b]">
                            {{ item.category?.name || "—" }}
                        </td>
                        <td class="px-4 py-3 text-sm text-[#64748b]">
                            {{ item.city || "—" }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="{
                                    'bg-amber-100 text-amber-800':
                                        item.status === 'draft',
                                    'bg-green-100 text-green-800':
                                        item.status === 'active',
                                    'bg-slate-100 text-slate-700':
                                        item.status === 'inactive' ||
                                        item.status === 'suspended',
                                }"
                            >
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ item.is_featured ? "Yes" : "—" }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex flex-wrap justify-end gap-1">
                                <button
                                    v-if="item.status !== 'active'"
                                    type="button"
                                    class="rounded bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-700"
                                    @click="setStatus(item, 'active')"
                                >
                                    Approve
                                </button>
                                <button
                                    v-if="item.status !== 'inactive'"
                                    type="button"
                                    class="rounded bg-slate-200 px-2 py-1 text-xs font-medium text-[#64748b] hover:bg-slate-300"
                                    @click="setStatus(item, 'inactive')"
                                >
                                    Inactive
                                </button>
                                <button
                                    type="button"
                                    class="rounded px-2 py-1 text-xs font-medium"
                                    :class="
                                        item.is_featured
                                            ? 'bg-amber-200 text-amber-900 hover:bg-amber-300'
                                            : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'
                                    "
                                    @click="toggleFeatured(item)"
                                >
                                    {{
                                        item.is_featured
                                            ? "Unfeature"
                                            : "Feature"
                                    }}
                                </button>
                                <router-link
                                    :to="'/admin/studios/' + item.id + '/edit'"
                                    class="rounded bg-[#e2e8f0] px-2 py-1 text-xs font-medium text-[#475569] hover:bg-[#cbd5e1]"
                                >
                                    Edit
                                </router-link>
                                <a
                                    :href="'/studios/' + (item.slug || item.id)"
                                    target="_blank"
                                    class="rounded bg-[#f1f5f9] px-2 py-1 text-xs text-[#64748b] hover:bg-[#e2e8f0]"
                                    >View</a
                                >
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div
                v-if="total > items.length"
                class="border-t border-[#e2e8f0] px-4 py-2 text-sm text-[#64748b]"
            >
                Showing {{ items.length }} of {{ total }} studios.
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const items = ref([]);
const total = ref(0);
const loading = ref(true);
const error = ref("");
const filterStatus = ref("");

async function load() {
    loading.value = true;
    error.value = "";
    try {
        const params = filterStatus.value ? { status: filterStatus.value } : {};
        const r = await axios.get("/api/admin/studios", {
            params,
            withCredentials: true,
        });
        const d = r.data;
        items.value = Array.isArray(d.data) ? d.data : [];
        total.value = d.total ?? items.value.length;
    } catch (e) {
        items.value = [];
        error.value = e.response?.data?.message || "Failed to load studios.";
    } finally {
        loading.value = false;
    }
}

async function setStatus(item, status) {
    try {
        await axios.put(
            "/api/admin/studios/" + item.id,
            { status },
            { withCredentials: true },
        );
        item.status = status;
    } catch (e) {
        alert(e.response?.data?.message || "Failed to update.");
    }
}

async function toggleFeatured(item) {
    try {
        await axios.put(
            "/api/admin/studios/" + item.id,
            { is_featured: !item.is_featured },
            { withCredentials: true },
        );
        item.is_featured = !item.is_featured;
    } catch (e) {
        alert(e.response?.data?.message || "Failed to update.");
    }
}

onMounted(load);
</script>
