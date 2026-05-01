<template>
  <div>
    <AdminPageHeader
      title="Location CMS"
      description="Manage SEO-friendly pages for Areas, Hospitals, Markets, Metros, and Schools. Use Bulk Import to convert existing data."
      :breadcrumbs="[{ label: 'Content & CMS', path: '/admin' }, { label: 'Location CMS', path: '/admin/seo-pages' }]"
    >
      <template #actions>
        <button 
          type="button" 
          class="rounded-lg bg-white border border-[#e2e8f0] px-4 py-2 text-sm font-medium text-[#64748b] hover:bg-[#f8fafc] flex items-center gap-2" 
          @click="generateSitemap"
          :disabled="generatingSitemap"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          {{ generatingSitemap ? 'Generating...' : 'Update Sitemap' }}
        </button>
        <div v-if="showImportProcessing" class="flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-amber-700 border border-amber-200 animate-pulse">
           <svg class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
           <span class="text-xs font-bold uppercase tracking-tight">Importing in background...</span>
           <button @click="showImportProcessing = false; load()" class="ml-1 text-amber-900 underline font-bold">Refresh List</button>
        </div>
        <button 
          v-if="!showImportProcessing"
          type="button" 
          class="rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f] flex items-center gap-2" 
          @click="openImportModal"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
          Bulk Import
        </button>
      </template>
    </AdminPageHeader>

    <div class="mb-4 flex flex-col gap-3 rounded-xl border border-[#e2e8f0] bg-white px-4 py-3 shadow-sm">
      <div class="flex flex-wrap items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8]">Filter by Type</span>
        <button
          v-for="t in types"
          :key="t.value"
          type="button"
          class="rounded-full px-3 py-1.5 text-sm font-medium transition"
          :class="filterType === t.value ? 'bg-[#e63946] text-white' : 'bg-[#f1f5f9] text-[#64748b] hover:bg-[#e2e8f0]'"
          @click="filterType = t.value"
        >
          {{ t.label }}
        </button>
        
        <div class="ml-auto flex items-center gap-2">
          <div class="relative">
            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-[#94a3b8] uppercase">Slug</span>
            <input 
              v-model="filterSlug" 
              type="text" 
              placeholder="contains..." 
              class="rounded-lg border border-[#e2e8f0] bg-white pl-10 pr-8 py-1.5 text-sm focus:border-[#e63946] focus:outline-none w-40"
              @keyup.enter="load"
            />
            <button v-if="filterSlug" @click="filterSlug = ''; load()" class="absolute right-2 top-1/2 -translate-y-1/2 text-[#94a3b8] hover:text-[#e63946]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search title..." 
            class="rounded-lg border border-[#e2e8f0] bg-white px-3 py-1.5 text-sm focus:border-[#e63946] focus:outline-none w-40"
            @keyup.enter="load"
          />
          <button 
            class="rounded-lg bg-[#f1f5f9] px-3 py-1.5 text-sm font-medium text-[#64748b] hover:bg-[#e2e8f0]"
            @click="load"
          >
            Search
          </button>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-slate-100">
        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8]">State</span>
          <select v-model="filterState" class="rounded-lg border border-[#e2e8f0] px-3 py-1.5 text-sm focus:border-[#e63946] focus:outline-none bg-[#f8fafc] min-w-[140px]">
            <option value="">All States</option>
            <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8]">City</span>
          <select v-model="filterCity" :disabled="!filterState" class="rounded-lg border border-[#e2e8f0] px-3 py-1.5 text-sm focus:border-[#e63946] focus:outline-none bg-[#f8fafc] min-w-[140px] disabled:opacity-50">
            <option value="">All Cities</option>
            <option v-for="c in listFilteredCities" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="flex items-center gap-2 ml-auto">
          <span class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8]">Show</span>
          <select v-model="perPage" class="rounded-lg border border-[#e2e8f0] px-3 py-1.5 text-sm focus:border-[#e63946] focus:outline-none bg-[#f8fafc]">
            <option value="20">20 per page</option>
            <option value="50">50 per page</option>
            <option value="100">100 per page</option>
            <option value="500">500 per page</option>
            <option value="-1">All Items</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div v-if="selectedIds.length" class="mb-4 overflow-hidden rounded-xl bg-[#1e293b] text-white shadow-lg animate-in slide-in-from-top-4">
      <div v-if="selectedIds.length === items.length && pagination.total > items.length && !selectAllMatching" class="bg-white/10 px-4 py-2 text-center text-xs border-b border-white/5">
        All {{ items.length }} items on this page are selected. 
        <button @click="selectAllMatching = true" class="ml-1 font-bold text-emerald-400 hover:underline">Select all {{ pagination.total }} items matching this filter</button>
      </div>
      <div v-else-if="selectAllMatching" class="bg-emerald-500/20 px-4 py-2 text-center text-xs border-b border-emerald-500/20 font-medium text-emerald-400">
        All {{ pagination.total }} matching items are selected.
        <button @click="selectedIds = []; selectAllMatching = false" class="ml-2 text-white hover:underline">Clear selection</button>
      </div>

      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-4">
          <span class="text-sm font-medium">
            <template v-if="selectAllMatching">All {{ pagination.total }} matching items selected</template>
            <template v-else>{{ selectedIds.length }} items selected</template>
          </span>
          <div class="h-4 w-px bg-white/20"></div>
          <div class="flex gap-2">
            <button @click="showTemplateModal = true" class="rounded-lg bg-emerald-500 px-3 py-1 text-xs font-bold hover:bg-emerald-600 flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.477 2.387a2 2 0 00.547 1.022l1.428 1.428a2 2 0 002.828 0l1.428-1.428a2 2 0 00.547-1.022l.477-2.387a2 2 0 00-1.414-1.96l-2.387-.477a2 2 0 00-1.022.547l-1.428 1.428a2 2 0 000 2.828l1.428 1.428" /></svg>
              Apply Common Content
            </button>
            <button @click="bulkAction('status', 'published')" class="rounded-lg bg-white/10 px-3 py-1 text-xs font-medium hover:bg-white/20">Publish</button>
            <button @click="bulkAction('status', 'draft')" class="rounded-lg bg-white/10 px-3 py-1 text-xs font-medium hover:bg-white/20">Draft</button>
            <button @click="bulkAction('delete')" class="rounded-lg bg-red-500/20 px-3 py-1 text-xs font-medium text-red-400 hover:bg-red-500/30">Delete</button>
          </div>
          
          <div v-if="!selectAllMatching && items.length > 20" class="h-4 w-px bg-white/20 ml-2"></div>
          <div v-if="!selectAllMatching && items.length > 20" class="flex gap-1 items-center">
            <span class="text-[10px] uppercase text-white/40 font-bold mr-1">Select:</span>
            <button @click="selectedIds = items.slice(0, 20).map(i => i.id)" class="px-2 py-0.5 rounded bg-white/5 text-[10px] hover:bg-white/10 transition">Top 20</button>
            <button @click="selectedIds = items.slice(0, 50).map(i => i.id)" class="px-2 py-0.5 rounded bg-white/5 text-[10px] hover:bg-white/10 transition">Top 50</button>
            <button @click="selectedIds = items.slice(0, 100).map(i => i.id)" class="px-2 py-0.5 rounded bg-white/5 text-[10px] hover:bg-white/10 transition">Top 100</button>
          </div>
        </div>
        <button @click="selectedIds = []; selectAllMatching = false" class="text-xs text-white/60 hover:text-white">Clear selection</button>
      </div>
    </div>

    <div v-if="loading" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white">
      <AdminTableSkeleton :columns="6" :rows="8" />
    </div>
    <div v-else-if="items.length" class="overflow-hidden rounded-xl border border-[#e2e8f0] bg-white shadow-sm">
      <table class="min-w-full divide-y divide-[#e2e8f0]">
        <thead class="bg-[#f8fafc]">
          <tr>
            <th class="w-10 px-4 py-3">
              <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]" />
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Type</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Title / Slug</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Content Status</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#64748b]">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#64748b]">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#e2e8f0]">
          <tr v-for="item in items" :key="item.id" class="hover:bg-[#f8fafc]">
            <td class="px-4 py-3">
              <input type="checkbox" :value="item.id" v-model="selectedIds" class="rounded border-[#e2e8f0] text-[#e63946] focus:ring-[#e63946]" />
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex rounded-full bg-[#f1f5f9] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#64748b]">
                {{ item.type }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="font-medium text-[#1a1a1a]">{{ item.title }}</div>
              <div class="text-xs text-[#94a3b8] font-mono">{{ item.slug }}</div>
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-col gap-1">
                <span class="flex items-center gap-1.5 text-xs" :class="item.intro_text ? 'text-emerald-600' : 'text-amber-500'">
                  <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                  Intro
                </span>
                <span class="flex items-center gap-1.5 text-xs" :class="item.faqs && item.faqs.length ? 'text-emerald-600' : 'text-amber-500'">
                  <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                  FAQs ({{ item.faqs?.length || 0 }})
                </span>
              </div>
            </td>
            <td class="px-4 py-3">
              <span 
                :class="item.status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'" 
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
              >
                {{ item.status }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex justify-end gap-3 items-center">
                <a :href="'/' + item.slug" target="_blank" class="text-emerald-600 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition" title="View Public Page">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </a>
                <button type="button" class="text-blue-600 hover:underline text-sm font-bold" @click="editPage(item)">Edit</button>
                <button type="button" class="text-red-600 hover:underline text-sm" @click="confirmDelete(item)">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > pagination.perPage" class="flex flex-col items-center justify-between gap-4 border-t border-[#e2e8f0] bg-[#f8fafc] px-4 py-4 sm:flex-row">
        <p class="text-xs text-[#64748b]">
          Showing <span class="font-medium text-[#1a1a1a]">{{ pagination.from }}</span> to <span class="font-medium text-[#1a1a1a]">{{ pagination.to }}</span> of <span class="font-medium text-[#1a1a1a]">{{ pagination.total }}</span> pages
        </p>
        <div class="flex items-center gap-1">
          <button @click="listPage--" :disabled="listPage <= 1" class="rounded-lg border border-[#e2e8f0] bg-white p-1.5 text-[#64748b] disabled:opacity-50 hover:bg-[#f1f5f9]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
          </button>
          <span class="px-3 text-sm font-medium text-[#1a1a1a]">{{ listPage }} / {{ pagination.lastPage }}</span>
          <button @click="listPage++" :disabled="listPage >= pagination.lastPage" class="rounded-lg border border-[#e2e8f0] bg-white p-1.5 text-[#64748b] disabled:opacity-50 hover:bg-[#f1f5f9]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </button>
        </div>
      </div>
    </div>
    
    <AdminEmptyState
      v-else
      title="No SEO Pages yet"
      description="You haven't created or imported any location-based SEO pages yet. Use the Bulk Import tool to start."
    >
      <button type="button" class="mt-4 rounded-lg bg-[#e63946] px-4 py-2 text-sm font-medium text-white hover:bg-[#c1121f]" @click="openImportModal">
        Start Bulk Import
      </button>
    </AdminEmptyState>

    <!-- Import Modal (Simplified for Large Data) -->
    <div v-if="showImportModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4" @click.self="showImportModal = false">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl animate-in zoom-in-95 duration-200">
        <h3 class="text-xl font-bold text-[#1a1a1a]">Bulk Import Data</h3>
        <p class="mt-2 text-sm text-[#64748b]">Select source and optional filters to import large datasets efficiently.</p>
        
        <div class="mt-6 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-[#1a1a1a] mb-2">Data Source Table</label>
            <div class="grid grid-cols-2 gap-2">
              <button 
                v-for="t in types.filter(x => x.value !== '')" 
                :key="t.value"
                @click="importForm.type = t.value"
                class="flex items-center justify-between rounded-xl border px-4 py-3 text-left transition"
                :class="importForm.type === t.value ? 'border-[#e63946] bg-red-50 text-[#e63946]' : 'border-[#e2e8f0] hover:border-[#cbd5e1] text-[#1a1a1a]'"
              >
                <span class="text-sm font-medium">{{ t.label }}</span>
                <div v-if="importForm.type === t.value" class="h-2 w-2 rounded-full bg-[#e63946]"></div>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-[#1a1a1a]">Filter by State</label>
              <select v-model="importForm.state_id" class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none">
                <option value="">All States</option>
                <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-[#1a1a1a]">Filter by City</label>
              <select v-model="importForm.city_id" :disabled="!importForm.state_id" class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none disabled:opacity-50">
                <option value="">All Cities</option>
                <option v-for="c in filteredCities" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
          </div>

          <div class="grid gap-4">
            <div>
              <label class="block text-sm font-semibold text-[#1a1a1a]">Slug Pattern</label>
              <input v-model="importForm.slug_pattern" type="text" placeholder="e.g. influencer-in-{name}" class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none" />
              <p class="mt-1 text-[10px] text-[#94a3b8]">Placeholders: <code class="text-[#64748b]">{name}, {city}</code></p>
            </div>
          </div>
        </div>

        <div class="mt-8 flex gap-3">
          <button 
            @click="runImport" 
            :disabled="importing || !importForm.type"
            class="flex-1 rounded-xl bg-[#e63946] py-3 text-sm font-bold text-white transition hover:bg-[#c1121f] disabled:opacity-50"
          >
            <span v-if="importing">Importing...</span>
            <span v-else>Start Import</span>
          </button>
          <button 
            @click="showImportModal = false" 
            class="flex-1 rounded-xl bg-[#f1f5f9] py-3 text-sm font-bold text-[#64748b] hover:bg-[#e2e8f0]"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Template Modal (Common Content) -->
    <div v-if="showTemplateModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4" @click.self="showTemplateModal = false">
      <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-8 shadow-2xl animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-2xl font-bold text-[#1a1a1a]">Apply Common Content</h3>
            <p class="mt-2 text-sm text-[#64748b]">
              Apply this template to the 
              <span class="font-bold text-[#e63946]">
                {{ selectAllMatching ? pagination.total : selectedIds.length }}
              </span> 
              selected pages.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <input 
              v-model="aiFocusTopic" 
              type="text" 
              placeholder="Provide a topic (e.g. Expert Tutors)" 
              class="rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none w-64"
            />
            <button 
              @click="generateAiForTemplate" 
              :disabled="generatingAi"
              class="rounded-xl border-2 border-emerald-500 px-4 py-2 text-sm font-bold text-emerald-600 hover:bg-emerald-50 transition flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
              {{ generatingAi ? 'Generating...' : 'Magic AI Suggest' }}
            </button>
          </div>
        </div>
        
        <div class="mt-8 grid gap-8 lg:grid-cols-2">
           <div class="space-y-6">
              <div class="rounded-xl border border-[#e2e8f0] p-5 bg-white mb-6">
                <h4 class="mb-4 font-bold text-[#1a1a1a] flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#e63946]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                  SEO Meta Templates
                </h4>
                <div class="space-y-4">
                  <div>
                    <label class="block text-xs font-bold text-[#64748b] mb-1">Meta Title Template</label>
                    <input v-model="templateForm.meta_title" type="text" placeholder="e.g. Best {type} in {city} | StarJD" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-[#64748b] mb-1">Meta Description Template</label>
                    <textarea v-model="templateForm.meta_description" rows="2" placeholder="e.g. Find the top rated {name} in {city}. Read reviews and book..." class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none"></textarea>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-[#64748b] mb-1">Meta Keywords Template</label>
                    <input v-model="templateForm.meta_keywords" type="text" placeholder="e.g. {type} in {city}, best {name}, top {type}" class="w-full rounded-lg border border-[#e2e8f0] px-3 py-2 text-sm focus:border-[#e63946] focus:outline-none" />
                  </div>
                  <p class="text-[10px] text-[#94a3b8]">Placeholders: <code class="text-[#e63946] font-bold">{name}</code>, <code class="text-[#e63946] font-bold">{city}</code>, <code class="text-[#e63946] font-bold">{type}</code></p>
                </div>
              </div>

              <div>
                <label class="block text-sm font-bold text-[#1a1a1a] mb-2">Intro Template</label>
                <RichTextEditor v-model="templateForm.intro_text" placeholder="e.g. Discover the best {type} in {name}..." />
              </div>
              <div>
                <label class="block text-sm font-bold text-[#1a1a1a] mb-4">FAQ Template</label>
                <div class="space-y-4">
                  <div v-for="(faq, idx) in templateForm.faqs" :key="idx" class="relative rounded-xl border bg-[#f8fafc] p-4">
                    <button @click="templateForm.faqs.splice(idx, 1)" class="absolute -right-2 -top-2 rounded-full bg-white p-1 text-red-500 shadow-sm border">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <input v-model="faq.q" type="text" placeholder="Question template" class="w-full bg-transparent font-bold text-sm focus:outline-none" />
                    <textarea v-model="faq.a" rows="2" placeholder="Answer template" class="mt-2 w-full bg-transparent text-sm focus:outline-none"></textarea>
                  </div>
                  <button @click="templateForm.faqs.push({q:'', a:''})" class="w-full rounded-xl border-2 border-dashed border-[#e2e8f0] py-2 text-sm font-medium text-[#64748b] hover:border-[#e63946] hover:text-[#e63946]">
                    + Add FAQ Template
                  </button>
                </div>
              </div>
           </div>

           <div class="space-y-6">
              <div>
                <label class="block text-sm font-bold text-[#1a1a1a] mb-4">Guide Sections Template</label>
                <div class="space-y-6">
                  <div v-for="(section, idx) in templateForm.guide_content" :key="idx" class="rounded-xl border p-4 bg-white shadow-sm relative">
                    <button @click="templateForm.guide_content.splice(idx, 1)" class="absolute -right-2 -top-2 rounded-full bg-white p-1 text-red-500 shadow-sm border">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <input v-model="section.title" type="text" placeholder="Section Title Template" class="font-bold text-[#1a1a1a] focus:outline-none border-b border-[#f1f5f9] mb-3 w-full" />
                    <RichTextEditor v-model="section.content" placeholder="Content template..." />
                  </div>
                  <button @click="templateForm.guide_content.push({title:'', content:''})" class="w-full rounded-xl border-2 border-dashed border-[#e2e8f0] py-3 text-sm font-bold text-[#e63946] bg-red-50/20 hover:bg-red-50 transition">
                    + Add Guide Template
                  </button>
                </div>
              </div>
           </div>
        </div>

        <div class="mt-10 flex gap-4 border-t pt-6">
          <button @click="applyTemplate" :disabled="applyingTemplate" class="flex-1 rounded-xl bg-emerald-600 py-4 text-sm font-bold text-white shadow-lg hover:bg-emerald-700 transition active:scale-95 disabled:opacity-50">
            {{ applyingTemplate ? 'Applying...' : 'Apply to Selected Pages' }}
          </button>
          <button @click="showTemplateModal = false" class="flex-1 rounded-xl bg-[#f1f5f9] py-4 text-sm font-bold text-[#64748b] hover:bg-[#e2e8f0] transition">
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Modal (Premium Content Hub) -->
    <div v-if="showEditModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4" @click.self="showEditModal = false">
      <div class="w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-8 shadow-2xl">
        <div class="mb-6 flex items-center justify-between border-b pb-4">
          <div>
            <h3 class="text-2xl font-bold text-[#1a1a1a]">Edit StarJD SEO Page</h3>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-xs font-bold bg-[#f1f5f9] text-[#64748b] px-2 py-0.5 rounded">SLUG</span>
              <input v-model="editForm.slug" type="text" class="text-sm text-[#e63946] font-mono bg-transparent border-none p-0 focus:ring-0 w-full" />
            </div>
          </div>
          <div class="flex items-center gap-3">
             <input 
              v-model="aiFocusTopic" 
              type="text" 
              placeholder="AI Topic / Keyword..." 
              class="rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-sm focus:border-emerald-500 focus:outline-none w-48"
            />
             <button 
              @click="generateAiForEdit" 
              :disabled="generatingAi"
              class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-600 transition flex items-center gap-2 shadow-md"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
              {{ generatingAi ? 'Generating...' : 'Magic AI' }}
            </button>
            <button @click="showEditModal = false" class="rounded-full p-2 hover:bg-[#f1f5f9]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#64748b]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Left Column: Core SEO & Intro -->
          <div class="space-y-6">
            <div class="rounded-2xl border border-[#e2e8f0] p-6">
              <h4 class="mb-4 font-bold text-[#1a1a1a] flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-[10px] text-[#e63946]">1</span>
                Core Content
              </h4>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-[#64748b]">Page Title (H1)</label>
                  <input v-model="editForm.title" type="text" class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 focus:border-[#e63946] focus:outline-none" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-[#64748b]">Meta Title</label>
                  <input v-model="editForm.meta_title" type="text" class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 focus:border-[#e63946] focus:outline-none" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-[#64748b]">Meta Description</label>
                  <textarea v-model="editForm.meta_description" rows="2" class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 focus:border-[#e63946] focus:outline-none"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-[#64748b]">Meta Keywords</label>
                  <input v-model="editForm.meta_keywords" type="text" placeholder="keyword1, keyword2" class="mt-1 w-full rounded-lg border border-[#e2e8f0] px-3 py-2 focus:border-[#e63946] focus:outline-none" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-[#64748b] mb-2">Introduction Paragraph</label>
                  <RichTextEditor 
                    v-model="editForm.intro_text" 
                    placeholder="Write a professional introduction..."
                    upload-image-url="/api/admin/posts/upload"
                  />
                </div>
              </div>
            </div>

            <div class="rounded-2xl border border-[#e2e8f0] p-6">
              <h4 class="mb-4 font-bold text-[#1a1a1a] flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-[10px] text-[#e63946]">2</span>
                Frequently Asked Questions
              </h4>
              <div class="space-y-4">
                <div v-for="(faq, idx) in editForm.faqs" :key="idx" class="relative rounded-xl border bg-[#f8fafc] p-4">
                  <button @click="editForm.faqs.splice(idx, 1)" class="absolute -right-2 -top-2 rounded-full bg-white p-1 text-red-500 shadow-sm border">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                  <input v-model="faq.q" type="text" placeholder="Question" class="w-full bg-transparent font-bold text-sm focus:outline-none" />
                  <textarea v-model="faq.a" rows="2" placeholder="Answer" class="mt-2 w-full bg-transparent text-sm focus:outline-none"></textarea>
                </div>
                <button @click="editForm.faqs.push({q:'', a:''})" class="w-full rounded-xl border-2 border-dashed border-[#e2e8f0] py-3 text-sm font-medium text-[#64748b] hover:border-[#e63946] hover:text-[#e63946]">
                  + Add FAQ
                </button>
              </div>
            </div>
          </div>

          <!-- Right Column: Guide Sections -->
          <div class="space-y-6">
            <div class="rounded-2xl border border-[#e2e8f0] p-6">
              <h4 class="mb-4 font-bold text-[#1a1a1a] flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-[10px] text-[#e63946]">3</span>
                Detailed Service Guide
              </h4>
              <div class="space-y-6">
                <div v-for="(section, idx) in editForm.guide_content" :key="idx" class="rounded-xl border p-5 bg-white shadow-sm">
                  <div class="flex items-center justify-between mb-4">
                    <input v-model="section.title" type="text" placeholder="Section Title" class="font-bold text-[#1a1a1a] focus:outline-none border-b border-transparent focus:border-[#e63946]" />
                    <button @click="editForm.guide_content.splice(idx, 1)" class="text-red-400 hover:text-red-500">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                  </div>
                  <RichTextEditor 
                    v-model="section.content" 
                    placeholder="Detailed content..."
                    upload-image-url="/api/admin/posts/upload"
                  />
                </div>
                <button @click="editForm.guide_content.push({title:'', content:''})" class="w-full rounded-xl border-2 border-dashed border-[#e2e8f0] py-4 text-sm font-bold text-[#e63946] bg-red-50/30 hover:bg-red-50 transition">
                  + Add New Guide Section
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-10 flex gap-4 border-t pt-6">
          <button @click="savePage" :disabled="saving" class="flex-1 rounded-xl bg-[#e63946] py-4 text-sm font-bold text-white shadow-lg hover:bg-[#c1121f] transition active:scale-95 disabled:opacity-50">
            {{ saving ? 'Saving Changes...' : 'Save & Publish' }}
          </button>
          <button @click="showEditModal = false" class="flex-1 rounded-xl bg-[#f1f5f9] py-4 text-sm font-bold text-[#64748b] hover:bg-[#e2e8f0] transition">
            Close Editor
          </button>
        </div>
      </div>
    </div>
    
    <AdminConfirmModal
      :open="showDeleteModal"
      title="Delete SEO Page"
      message="This will permanently delete this SEO page. Original location data will remain safe."
      confirm-label="Delete Forever"
      @close="showDeleteModal = false; itemToDelete = null"
      @confirm="doDelete"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, reactive } from 'vue';
import axios from 'axios';
import AdminPageHeader from '../../components/admin/AdminPageHeader.vue';
import AdminEmptyState from '../../components/admin/AdminEmptyState.vue';
import AdminTableSkeleton from '../../components/admin/AdminTableSkeleton.vue';
import AdminConfirmModal from '../../components/admin/AdminConfirmModal.vue';
import RichTextEditor from '../../components/admin/RichTextEditor.vue';

const items = ref([]);
const states = ref([]);
const cities = ref([]);
const loading = ref(true);
const importing = ref(false);
const saving = ref(false);
const generatingAi = ref(false);
const generatingSitemap = ref(false);
const applyingTemplate = ref(false);
const showImportModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showTemplateModal = ref(false);
const showImportProcessing = ref(false);
const itemToDelete = ref(null);
const selectAllMatching = ref(false);
const aiFocusTopic = ref('');

const filterType = ref('');
const filterState = ref('');
const filterCity = ref('');
const filterSlug = ref('');
const perPage = ref('20');
const search = ref('');
const listPage = ref(1);
const pagination = reactive({
  total: 0, perPage: 20, lastPage: 1, from: 0, to: 0
});

const types = [
  { label: 'All Types', value: '' },
  { label: 'Areas', value: 'area' },
  { label: 'Hospitals', value: 'hospital' },
  { label: 'Markets', value: 'market' },
  { label: 'Metros', value: 'metro' },
  { label: 'Schools', value: 'school' },
];

const selectedIds = ref([]);
const allSelected = computed(() => items.value.length > 0 && selectedIds.value.length === items.value.length);

const importForm = reactive({
  type: '', state_id: '', city_id: '', slug_pattern: '', title_pattern: ''
});

const templateForm = reactive({
  meta_title: '', meta_description: '', meta_keywords: '', intro_text: '', guide_content: [], faqs: []
});

const editForm = reactive({
  id: null, title: '', slug: '', meta_title: '', meta_description: '', meta_keywords: '', intro_text: '', guide_content: [], faqs: [], status: 'published'
});

const filteredCities = computed(() => {
  if (!importForm.state_id) return [];
  return cities.value.filter(c => c.state_id == importForm.state_id);
});

const listFilteredCities = computed(() => {
  if (!filterState.value) return [];
  return cities.value.filter(c => c.state_id == filterState.value);
});

async function load() {
  loading.value = true;
  try {
    const r = await axios.get('/api/admin/seo-pages', {
      params: { 
        page: listPage.value, 
        type: filterType.value, 
        search: search.value,
        slug: filterSlug.value,
        state_id: filterState.value,
        city_id: filterCity.value,
        per_page: perPage.value
      }
    });
    items.value = r.data.data;
    pagination.total = r.data.total;
    pagination.perPage = r.data.per_page;
    pagination.lastPage = r.data.last_page;
    pagination.from = r.data.from;
    pagination.to = r.data.to;
  } finally {
    loading.value = false;
  }
}

async function loadLocations() {
  try {
    const [s, c] = await Promise.all([
      axios.get('/api/admin/states'),
      axios.get('/api/admin/cities')
    ]);
    states.value = s.data;
    cities.value = c.data;
  } catch (e) {}
}

function openImportModal() {
  showImportModal.value = true;
}

async function runImport() {
  if (!importForm.type) return;
  importing.value = true;
  showImportModal.value = false;
  
  try {
    const r = await axios.post('/api/admin/seo-pages/bulk-import', importForm, { timeout: 120000 });
    alert(r.data.message);
    load();
  } catch (e) { 
    // Handle timeouts or server overloads gracefully
    if (e.code === 'ECONNABORTED' || e.response?.status === 504 || e.response?.status === 502) {
      showImportProcessing.value = true;
      // The server is likely still processing in background
    } else {
      alert(e.response?.data?.message || 'Import failed. Please check filters.'); 
    }
  }
  finally { importing.value = false; }
}

async function generateAiForEdit() {
  if (!currentItem.value) return;
  generatingAi.value = true;
  try {
    const r = await axios.post('/api/admin/seo-pages/generate-ai', {
      name: currentItem.value.entity?.name,
      city: currentItem.value.entity?.city,
      type: currentItem.value.type,
      topic: aiFocusTopic.value
    });
    editForm.intro_text = r.data.intro_text;
    editForm.guide_content = r.data.guide_content;
    editForm.faqs = r.data.faqs;
    aiFocusTopic.value = '';
  } catch (e) { alert(e.response?.data?.error || 'AI Generation failed'); }
  finally { generatingAi.value = false; }
}

async function generateAiForTemplate() {
  generatingAi.value = true;
  try {
    const r = await axios.post('/api/admin/seo-pages/generate-ai', {
      name: '{name}', city: '{city}', type: '{type}',
      is_template: true,
      topic: aiFocusTopic.value
    });
    templateForm.intro_text = r.data.intro_text;
    templateForm.guide_content = r.data.guide_content;
    templateForm.faqs = r.data.faqs;
    aiFocusTopic.value = '';
  } catch (e) { alert('AI Generation failed'); }
  finally { generatingAi.value = false; }
}

async function applyTemplate() {
  if (!selectedIds.value.length) return;
  applyingTemplate.value = true;
  try {
    await axios.post('/api/admin/seo-pages/bulk-action', {
      ids: selectAllMatching.value ? [] : selectedIds.value, 
      all_matching: selectAllMatching.value,
      filters: selectAllMatching.value ? { 
        type: filterType.value, 
        search: search.value, 
        slug: filterSlug.value, 
        state_id: filterState.value, 
        city_id: filterCity.value 
      } : null,
      action: 'template', 
      template_data: templateForm
    });
    showTemplateModal.value = false;
    selectedIds.value = [];
    selectAllMatching.value = false;
    alert('Template applied successfully!');
    load();
  } catch (e) { alert('Failed to apply template'); }
  finally { applyingTemplate.value = false; }
}

function editPage(item) {
  currentItem.value = item;
  editForm.id = item.id;
  editForm.title = item.title || '';
  editForm.slug = item.slug || '';
  editForm.meta_title = item.meta_title || '';
  editForm.meta_description = item.meta_description || '';
  editForm.meta_keywords = item.meta_keywords || '';
  editForm.intro_text = item.intro_text || '';
  editForm.guide_content = Array.isArray(item.guide_content) ? [...item.guide_content] : [];
  editForm.faqs = Array.isArray(item.faqs) ? [...item.faqs] : [];
  editForm.status = item.status || 'published';
  showEditModal.value = true;
}

async function savePage() {
  saving.value = true;
  try {
    await axios.put(`/api/admin/seo-pages/${editForm.id}`, editForm);
    showEditModal.value = false;
    load();
  } catch (e) { alert('Failed to save changes'); }
  finally { saving.value = false; }
}

function toggleSelectAll() {
  if (allSelected.value) { 
    selectedIds.value = []; 
    selectAllMatching.value = false;
  } 
  else { 
    selectedIds.value = items.value.map(i => i.id); 
  }
}

async function bulkAction(action, value = null) {
  if (!selectedIds.value.length && !selectAllMatching.value) return;
  if (action === 'delete' && !confirm('Are you sure?')) return;
  try {
    await axios.post('/api/admin/seo-pages/bulk-action', { 
      ids: selectAllMatching.value ? [] : selectedIds.value, 
      all_matching: selectAllMatching.value,
      filters: selectAllMatching.value ? { 
        type: filterType.value, 
        search: search.value, 
        slug: filterSlug.value, 
        state_id: filterState.value, 
        city_id: filterCity.value 
      } : null,
      action, 
      value 
    });
    selectedIds.value = [];
    selectAllMatching.value = false;
    load();
  } catch (e) { alert('Action failed'); }
}

function confirmDelete(item) {
  itemToDelete.value = item;
  showDeleteModal.value = true;
}

async function doDelete() {
  if (!itemToDelete.value) return;
  try {
    await axios.delete(`/api/admin/seo-pages/${itemToDelete.value.id}`);
    showDeleteModal.value = false;
    load();
  } catch (e) { alert('Delete failed'); }
}

async function generateSitemap() {
  generatingSitemap.value = true;
  try {
    const r = await axios.post('/api/admin/sitemap/generate');
    alert(r.data.message);
  } catch (e) { alert('Sitemap generation failed'); }
  finally { generatingSitemap.value = false; }
}

watch([filterType, listPage, filterState, filterCity, perPage], () => {
  if (filterState.value === '' && filterCity.value !== '') {
    filterCity.value = '';
  }
  load();
});

onMounted(() => {
  loadLocations().then(load);
});
</script>

<style scoped>
.animate-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
