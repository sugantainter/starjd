<template>
  <div class="pb-20">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-[#1a1a1a] tracking-tight">Collaborations</h1>
        <p class="mt-1 text-[#64748b] font-medium">Manage your brand partnerships and track project scaling.</p>
      </div>
      <div v-if="activeTab === 'collaborations'" class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-2xl border border-slate-200">
        <div class="w-2 h-2 rounded-full bg-[#10b981] animate-pulse"></div>
        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ list.length }} Active Projects</span>
      </div>
    </div>

    <!-- Payout Dashboard Header Tabs -->
    <header class="mb-10 animate-in fade-in slide-in-from-top-4 duration-700">
      <div class="border-b border-slate-100">
        <nav class="-mb-px flex space-x-12">
          <button @click="activeTab = 'collaborations'" :class="activeTab === 'collaborations' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="border-b-2 py-4 px-1 text-sm font-black uppercase tracking-widest transition-all">Collaborations</button>
          <button @click="activeTab = 'bank'" :class="activeTab === 'bank' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="border-b-2 py-4 px-1 text-sm font-black uppercase tracking-widest transition-all">Bank Accounts</button>
        </nav>
      </div>
    </header>

    <div v-if="error" class="mb-6 rounded-2xl bg-red-50 px-6 py-4 text-sm text-red-800 border border-red-100 flex items-center gap-3">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      <span class="font-semibold">{{ error }}</span>
    </div>

    <div v-if="activeTab === 'bank'" class="animate-in fade-in slide-in-from-bottom-4 duration-700">
        <BankManager />
    </div>

    <div v-else>
        <ul v-if="list.length" class="space-y-6">
          <li v-for="c in list" :key="c.id" 
              class="group overflow-hidden rounded-[40px] border border-[#e2e8f0] bg-white transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1">
            
            <!-- Card Header -->
            <div class="border-b border-slate-50 bg-[#f8fafc]/50 p-8 sm:p-10">
              <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-5">
                  <div class="h-16 w-16 shrink-0 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center overflow-hidden">
                    <img v-if="c.brand?.brand_profile?.logo_url || c.brand?.brandProfile?.logo_url" :src="c.brand.brand_profile?.logo_url || c.brand.brandProfile?.logo_url" class="h-full w-full object-cover" />
                    <span v-else class="text-2xl font-black text-slate-300 uppercase">{{ c.brand?.name?.charAt(0) }}</span>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-[#1a1a1a] group-hover:text-[#fc4402] transition-colors">{{ c.brand?.name }}</h3>
                    <div class="mt-1 flex items-center gap-3">
                      <span class="text-[10px] font-black uppercase tracking-widest text-[#10b981] bg-[#10b981]/5 px-2 py-0.5 rounded-md border border-[#10b981]/10">
                        {{ c.package?.name || 'Custom Project' }}
                      </span>
                      <span class="text-xs font-bold text-[#64748b]">₹{{ c.amount }}</span>
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 rounded-2xl bg-white p-2 border border-slate-100 shadow-sm">
                    <div class="px-4 text-center border-r border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Your Earnings</p>
                        <p class="text-lg font-black text-[#1a1a1a]">₹{{ c.status === 'resolved' ? (c.resolved_creator_amount || 0) : c.creator_amount }}</p>
                    </div>
                    <div class="px-4 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Revisions</p>
                        <p class="text-lg font-black" :class="c.revision_count >= c.max_revisions ? 'text-red-500' : 'text-slate-700'">
                            {{ c.revision_count }}<span class="text-xs text-slate-300">/{{ c.max_revisions === 20 ? '∞' : c.max_revisions }}</span>
                        </p>
                    </div>
                </div>
              </div>
            </div>

            <!-- Progress Stepper -->
            <div class="px-6 py-8 sm:px-12 bg-white">
              <div class="relative flex items-center justify-between">
                <div class="absolute left-0 top-1/2 h-0.5 w-full -translate-y-1/2 bg-slate-100"></div>
                <div class="absolute left-0 top-1/2 h-1 -translate-y-1/2 bg-[#fc4402] transition-all duration-1000 rounded-full" :style="{ width: getProgressWidth(c.status) + '%' }"></div>

                <div v-for="(step, idx) in steps" :key="idx" class="relative z-10 flex flex-col items-center">
                  <div 
                    :class="[
                      'flex h-10 w-10 items-center justify-center rounded-full border-4 transition-all duration-500',
                      isStepCompleted(c.status, idx) ? 'border-[#fc4402] bg-[#fc4402] text-white' : isStepActive(c.status, idx) ? 'border-[#fc4402] bg-white text-[#fc4402] ring-4 ring-[#fc4402]/10' : 'border-white bg-slate-100 text-slate-400'
                    ]"
                  >
                    <div v-if="isStepCompleted(c.status, idx)" class="h-5 w-5">
                      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span v-else class="text-xs font-black">{{ idx + 1 }}</span>
                  </div>
                  <span :class="['absolute top-12 whitespace-nowrap text-[10px] font-black uppercase tracking-widest', isStepActive(c.status, idx) ? 'text-[#1a1a1a]' : 'text-slate-400']">{{ step }}</span>
                </div>
              </div>
            </div>

            <!-- Lower Section: Conditional Content & Actions -->
            <div class="border-t border-slate-50 bg-[#f8fafc]/30 p-8 sm:p-10">
              <div class="flex flex-col gap-8 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1 max-w-2xl">
                  <div v-if="c.status === 'revision_requested'" class="flex gap-4 rounded-3xl bg-purple-50 p-6 border border-purple-100">
                    <div class="h-12 w-12 shrink-0 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/20">
                      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <div>
                      <p class="text-xs font-black uppercase tracking-widest text-purple-600">Revision Requested</p>
                      <p class="mt-1 text-sm text-purple-900 italic font-medium leading-relaxed">"{{ c.revision_notes }}"</p>
                    </div>
                  </div>

                  <div v-else-if="c.status === 'disputed'" class="flex gap-4 rounded-3xl bg-red-50 p-6 border border-red-100/50">
                    <div class="h-12 w-12 shrink-0 rounded-2xl bg-red-600 flex items-center justify-center text-white shadow-xl shadow-red-600/20">
                      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                      <div class="flex items-center gap-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-red-600">Dispute Under Review</p>
                        <span class="h-1.5 w-1.5 rounded-full bg-red-600 animate-ping"></span>
                      </div>
                      <p class="mt-1 text-sm text-red-900 font-bold leading-tight">Admin mediation in progress offline.</p>
                      <router-link :to="'/creator/support?collab_id=' + c.id" class="mt-2 inline-flex items-center gap-2 text-[11px] font-black text-red-600 hover:text-red-800 transition-colors uppercase tracking-widest">
                        Join Mediation Chat
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                      </router-link>
                    </div>
                  </div>

                  <div v-else-if="c.deliverable_content && (c.status === 'delivered' || c.status === 'completed' || c.status === 'resolved')" class="flex items-center gap-5">
                    <div @click="openPreview(c)" class="h-14 w-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200 cursor-pointer hover:bg-slate-200 transition">
                      <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Project Deliverable</p>
                        <p @click="openPreview(c)" class="text-base font-bold text-slate-700 truncate max-w-[300px] cursor-pointer hover:text-[#fc4402] transition">{{ c.deliverable_content.split('/').pop() }}</p>
                        <p v-if="c.status === 'delivered'" class="text-[10px] font-black text-amber-600 uppercase tracking-widest mt-0.5">Awaiting Brand Approval</p>
                    </div>
                  </div>

                  <div v-else-if="c.brand_notes" class="flex gap-4">
                     <svg class="h-6 w-6 text-slate-300 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
                     <p class="text-sm font-medium text-[#64748b] leading-relaxed">{{ c.brand_notes }}</p>
                  </div>
                </div>

                <!-- Action Group -->
                <div class="shrink-0">
                  <div v-if="c.status === 'pending'" class="flex gap-3">
                    <button @click="accept(c)" class="rounded-2xl bg-[#10b981] px-8 py-4 text-sm font-black text-white hover:bg-[#059669] shadow-lg shadow-emerald-200 transition active:scale-95">Accept</button>
                    <button @click="reject(c)" class="rounded-2xl border border-red-200 px-8 py-4 text-sm font-bold text-red-600 hover:bg-red-50 transition active:scale-95">Reject</button>
                  </div>

                  <div v-else-if="c.status === 'accepted'" class="flex items-center gap-3 px-6 py-4 bg-amber-50 rounded-2xl border border-amber-100">
                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-xs font-black text-amber-700 uppercase tracking-widest">Waiting for Brand Payment</span>
                  </div>

                  <div v-else-if="['paid', 'revision_requested', 'delivered'].includes(c.status)">
                    <button @click="openDeliver(c)" class="rounded-2xl bg-[#fc4402] px-10 py-4 text-sm font-black text-white shadow-xl shadow-[#fc4402]/20 hover:bg-[#e63d02] transition hover:-translate-y-0.5 active:scale-95">
                      {{ c.status === 'delivered' ? 'Update Work' : 'Submit Deliverable' }}
                    </button>
                  </div>

                  <div v-else-if="c.status === 'completed' || c.status === 'resolved'" class="flex flex-col items-end gap-3">
                     <div class="rounded-3xl bg-emerald-50 p-6 border border-emerald-200">
                         <div class="flex items-center gap-2 mb-3">
                            <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                            <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">
                                {{ c.status === 'resolved' ? 'Mediation Settled' : 'Project Finished' }}
                            </p>
                         </div>
                         <div class="flex items-center gap-8">
                             <div>
                                 <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none mb-1">Your Payout</p>
                                 <p class="text-2xl font-black text-emerald-900">₹{{ c.status === 'resolved' ? (c.resolved_creator_amount || 0) : c.creator_amount }}</p>
                             </div>
                             <button 
                               @click="openClaimModal(c)" 
                               :disabled="c.creator_claimed"
                               class="rounded-2xl bg-emerald-600 px-8 py-3 text-[11px] font-black text-white hover:bg-emerald-700 transition shadow-xl shadow-emerald-600/20 disabled:opacity-40 uppercase tracking-widest"
                             >
                               {{ 
                                 c.creator_claimed 
                                   ? (c.payout_requests?.find(p => p.type === 'creator_payout')?.status === 'paid' ? 'Paid' : 'Payout Processing') 
                                   : 'Claim Payout' 
                               }}
                             </button>
                             <div v-if="creatorPayoutReceiptUrl(c)" class="mt-2">
                                <a :href="creatorPayoutReceiptUrl(c)" target="_blank" rel="noopener noreferrer" class="text-[9px] font-black text-indigo-600 uppercase tracking-widest hover:underline flex items-center gap-1">
                                   <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                   View Receipt
                                </a>
                             </div>
                         </div>
                     </div>
                  </div>

                   <div v-else-if="c.status === 'rejected'" class="flex flex-col items-end gap-2">
                      <div class="px-6 py-3 bg-red-50 text-red-600 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-red-100 flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                        Rejected
                      </div>
                      <p v-if="c.rejection_reason" class="text-[9px] font-medium text-slate-400 italic">Reason: {{ c.rejection_reason }}</p>
                   </div>
                </div>
              </div>
            </div>
          </li>
        </ul>

        <div v-else class="mt-12 rounded-[48px] border-2 border-dashed border-slate-200 bg-white p-24 text-center">
          <div class="w-24 h-24 bg-slate-50 rounded-[32px] flex items-center justify-center mx-auto mb-8 text-slate-300">
             <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
          </div>
          <h3 class="text-3xl font-black text-slate-900 tracking-tight">No active projects</h3>
          <p class="mt-3 text-slate-500 font-bold max-w-sm mx-auto uppercase text-[10px] tracking-widest leading-relaxed">Incoming collaboration requests will be listed here with end-to-end tracking.</p>
        </div>
    </div>

    <!-- Modals -->
    <div v-if="showDeliverModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4" @click.self="showDeliverModal = false">
      <div class="w-full max-w-xl overflow-hidden rounded-[48px] bg-white shadow-2xl animate-in zoom-in-95 duration-500">
        <div class="border-b border-slate-50 bg-[#f8fafc] px-10 py-8">
          <h2 class="text-3xl font-black text-slate-900 tracking-tight">Submit Work</h2>
          <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Send your deliverables to the brand</p>
        </div>
        <form @submit.prevent="submitDelivery" class="p-10 space-y-8">
          <div class="rounded-[32px] bg-amber-50 p-6 border border-amber-100 flex gap-5">
            <div class="h-12 w-12 shrink-0 rounded-2xl bg-amber-500 flex items-center justify-center text-white shadow-xl shadow-amber-500/20">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
              <p class="text-[11px] font-black text-amber-800 uppercase tracking-widest mb-1.5 pt-1">Payment Security</p>
              <p class="text-xs leading-relaxed text-amber-700 font-bold italic">StarJD Escrow holds the brand's payment. Uploading files here ensures you are paid upon brand approval.</p>
            </div>
          </div>

          <div>
            <label class="mb-3 block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Deliverable Files</label>
            <div class="relative group">
              <input type="file" required @change="handleFileChange" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
              <div class="w-full rounded-[32px] border-2 border-dashed border-slate-200 p-12 text-center flex flex-col items-center justify-center bg-slate-50 group-hover:bg-slate-100 group-hover:border-[#fc4402]/30 transition-all duration-300">
                <div class="h-16 w-16 rounded-[24px] bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-[#fc4402] transition-colors mb-4">
                   <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                </div>
                <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ deliveryFile ? deliveryFile.name : 'Click to Upload Deliverable' }}</p>
                <p class="mt-1 text-[10px] text-slate-400 font-bold uppercase tracking-widest">Max 100MB • MP4, PDF, ZIP, PNG, JPG</p>
              </div>
            </div>
            
            <div v-if="submittingDelivery" class="mt-8 space-y-3">
              <div class="flex justify-between items-center text-[10px] font-black text-[#fc4402] uppercase tracking-widest">
                <span>Uploading to Secure Storage...</span>
                <span>{{ uploadPercentage }}%</span>
              </div>
              <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden border border-slate-50">
                <div class="h-full bg-gradient-to-r from-[#fc4402] to-[#ff6b3d] transition-all duration-500 ease-out shadow-[0_0_15px_rgba(252,68,2,0.4)]" :style="{ width: uploadPercentage + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="flex gap-4 pt-6">
            <button type="submit" :disabled="submittingDelivery || !deliveryFile" class="flex-1 h-16 rounded-2xl bg-[#fc4402] text-white font-black uppercase tracking-widest shadow-2xl shadow-[#fc4402]/30 hover:bg-[#e63d02] hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-40">
              {{ submittingDelivery ? 'Processing...' : 'Confirm Delivery' }}
            </button>
            <button @click="showDeliverModal = false" type="button" class="px-10 h-16 rounded-2xl border border-slate-200 text-slate-600 font-black uppercase tracking-widest hover:bg-slate-50 transition active:scale-95">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Payout Claim Bank Selector Modal -->
    <div v-if="showClaimModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/40 backdrop-blur-md p-4">
       <div class="w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col bg-slate-50 rounded-[64px] shadow-2xl animate-in zoom-in-95 duration-500">
          <div class="p-12 pb-6 flex justify-between items-center bg-white border-b border-slate-100">
             <div>
               <h3 class="text-4xl font-black text-slate-900 leading-none tracking-tight">Claim Payout</h3>
               <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-3">Verified Settlement Destination</p>
             </div>
             <button @click="showClaimModal = false" class="h-14 w-14 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-900 hover:bg-slate-100 transition shadow-sm border border-slate-100">
               <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
             </button>
          </div>
          <div class="flex-1 overflow-y-auto p-12">
             <div class="mb-12 bg-emerald-50 border border-emerald-200 rounded-[40px] p-8 flex items-center justify-between">
                <div>
                   <p class="text-[11px] font-black text-emerald-600 uppercase tracking-widest mb-1.5 leading-none">Net Payout to Account</p>
                   <p class="text-5xl font-black text-emerald-900 leading-none tracking-tighter">₹{{ claimingCollab?.status === 'resolved' ? claimingCollab?.resolved_creator_amount : claimingCollab?.amount }}</p>
                </div>
                <div class="h-16 w-1 rounded-full bg-emerald-200 mx-10"></div>
                <p class="text-sm font-bold text-emerald-700 leading-relaxed max-w-sm italic">
                   "Funds are processed securely. Please select a verified bank account for immediate transfer initiation."
                </p>
             </div>
             <BankManager @select="claimPayout" />
          </div>
       </div>
    </div>

    <!-- Preview Modal -->
    <div v-if="showPreviewModal" class="fixed inset-0 z-[150] flex items-center justify-center bg-slate-900/95 backdrop-blur-xl p-4 sm:p-12 overflow-hidden" @contextmenu.prevent>
        <button @click="showPreviewModal = false" class="absolute top-8 right-8 z-[160] h-16 w-16 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-all hover:scale-110 active:scale-90 border border-white/10">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <div class="relative w-full h-full flex flex-col items-center justify-center transition-all duration-700 animate-in fade-in zoom-in-95">
           <div class="relative max-w-full max-h-full rounded-[40px] overflow-hidden shadow-[0_64px_128px_-32px_rgba(0,0,0,0.8)] border border-white/10">
              <img v-if="previewType === 'image'" :src="previewUrl" class="max-w-full max-h-[80vh] object-contain" @contextmenu.prevent />
              <SecureVideoPlayer v-else-if="previewType === 'video'" :src="previewUrl" />
              <iframe v-else-if="previewType === 'pdf'" :src="previewUrl + '#toolbar=0'" class="w-[80vw] h-[80vh] border-none bg-slate-800"></iframe>
           </div>
           <div class="mt-12 px-8 py-4 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 text-white/60 text-[10px] font-black uppercase tracking-[0.3em] flex items-center gap-4">
              <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> SECURE PREVIEW MODE</span>
              <span class="w-px h-4 bg-white/10"></span>
              <span>PROTECTED BY STARJD ESCROW</span>
           </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4" @click.self="showRejectModal = false">
      <div class="w-full max-w-lg overflow-hidden rounded-[40px] bg-white shadow-2xl animate-in zoom-in-95 duration-500">
        <div class="px-10 py-8 border-b border-slate-50 bg-red-50 text-red-900">
          <h2 class="text-2xl font-black uppercase tracking-tighter">Decline Project</h2>
          <p class="text-[10px] font-bold uppercase tracking-widest mt-2 text-red-600">Please provide a reason for declining</p>
        </div>
        <div class="p-10 space-y-6">
          <div>
            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 ml-1">Select Reason</label>
            <div class="grid grid-cols-1 gap-2">
               <button v-for="r in commonReasons" :key="r" @click="rejectionReason = r" :class="rejectionReason === r ? 'bg-red-600 text-white border-red-600' : 'bg-slate-50 text-slate-600 border-slate-100 hover:bg-slate-100'" class="px-6 py-3 rounded-2xl border text-[11px] font-black uppercase tracking-wider transition-all text-left">
                  {{ r }}
               </button>
            </div>
          </div>
          <div v-if="rejectionReason === 'Other'">
            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 ml-1">Custom Reason</label>
            <textarea v-model="customReason" class="w-full min-h-[100px] rounded-3xl border-none bg-slate-50 focus:ring-2 focus:ring-red-100 p-6 text-sm font-medium text-slate-700 resize-none" placeholder="Explain why you are declining..."></textarea>
          </div>
          <div class="flex gap-4 pt-4">
            <button @click="confirmReject" :disabled="submittingReject || !rejectionReason || (rejectionReason === 'Other' && !customReason.trim())" class="flex-1 h-16 rounded-2xl bg-red-600 text-white font-black uppercase tracking-widest shadow-xl shadow-red-200 transition-all active:scale-95 disabled:opacity-40">
              {{ submittingReject ? 'Declining...' : 'Confirm Decline' }}
            </button>
            <button @click="showRejectModal = false" class="px-10 h-16 rounded-2xl border border-slate-200 text-slate-600 font-black uppercase tracking-widest hover:bg-slate-50 transition active:scale-95">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { notify } from '../../lib/notify.js';
import BankManager from '../../components/common/BankManager.vue';
import SecureVideoPlayer from '../../components/common/SecureVideoPlayer.vue';

const list = ref([]);
const error = ref('');
const activeTab = ref('collaborations');
const showClaimModal = ref(false);
const claimingCollab = ref(null);

const showDeliverModal = ref(false);
const selectedCollab = ref(null);
const deliveryFile = ref(null);
const submittingDelivery = ref(false);
const uploadPercentage = ref(0);

const showPreviewModal = ref(false);
const previewUrl = ref('');
const previewType = ref('');

const showRejectModal = ref(false);
const rejectingCollab = ref(null);
const rejectionReason = ref('');
const customReason = ref('');
const submittingReject = ref(false);

const commonReasons = [
  "Not enough budget for this project",
  "Timeline doesn't match my availability",
  "Brand mismatch with my content style",
  "Requirement is unclear",
  "Other"
];

const steps = ['Request', 'Accepted', 'Paid', 'Delivered', 'Completed'];

const statusMap = {
  'pending': 0,
  'accepted': 1,
  'paid': 2,
  'revision_requested': 2,
  'delivered': 3,
  'disputed': 3,
  'resolved': 4,
  'completed': 4,
  'rejected': -1
};

function getProgressWidth(status) {
  const currentStep = statusMap[status] || 0;
  if (currentStep < 0) return 0;
  return (currentStep / (steps.length - 1)) * 100;
}

function isStepCompleted(status, idx) {
  const currentStep = statusMap[status] || 0;
  return currentStep > idx;
}

function isStepActive(status, idx) {
  const currentStep = statusMap[status] || 0;
  return currentStep === idx;
}

function creatorPayoutReceiptUrl(c) {
  const pr = c?.payout_requests?.find((p) => p.type === 'creator_payout');
  if (!pr?.receipt_full_url && !pr?.receipt_url) return '';
  if (pr.receipt_full_url) return pr.receipt_full_url;
  return pr.receipt_url.startsWith('http') ? pr.receipt_url : '';
}

async function openPreview(c) {
  try {
    const res = await axios.get(`/api/collaborations/${c.id}/file`, { withCredentials: true });
    if (res.data.ready === false) {
      notify.info(res.data.message || 'Preview is not ready yet.');
      return;
    }
    const token = res.data.preview_token;
    if (!token) {
      notify.error('Unable to open secure preview.');
      return;
    }
    previewUrl.value = `${res.data.url}?preview_token=${encodeURIComponent(token)}`;
    const ext = c.deliverable_content.split('.').pop().toLowerCase();
    previewType.value = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext) ? 'image' : (ext === 'pdf' ? 'pdf' : 'video');
    showPreviewModal.value = true;
  } catch (e) {
    notify.error(e.response?.data?.message || 'Unable to open secure preview.');
  }
}

function handleFileChange(e) {
  deliveryFile.value = e.target.files[0];
}

function openDeliver(c) {
  selectedCollab.value = c;
  deliveryFile.value = null;
  uploadPercentage.value = 0;
  showDeliverModal.value = true;
}

function openClaimModal(c) {
  claimingCollab.value = c;
  showClaimModal.value = true;
}

async function claimPayout(bank) {
  const c = claimingCollab.value;
  try {
    await axios.post(`/api/collaborations/${c.id}/claim-settlement`, { 
        type: 'creator',
        bank_account_id: bank.id
    }, { withCredentials: true });
    notify.success('Payout claim successfully raised. Amount will reflect in 5-7 working days.');
    showClaimModal.value = false;
    await load();
  } catch (e) {
    notify.error(e.response?.data?.message || 'Failed to process payout claim.');
  }
}

async function submitDelivery() {
  if (!deliveryFile.value) {
    notify.error('Please select a file first.');
    return;
  }
  if (deliveryFile.value.size > 100 * 1024 * 1024) {
    notify.error('File size exceeds 100MB limit.');
    return;
  }
  submittingDelivery.value = true;
  uploadPercentage.value = 0;
  try {
    const formData = new FormData();
    formData.append('deliverable_file', deliveryFile.value);
    
    await axios.post(`/api/collaborations/${selectedCollab.value.id}/deliver`, formData, { 
      headers: { 'Content-Type': 'multipart/form-data' },
      withCredentials: true,
      onUploadProgress: (progressEvent) => {
        const total = progressEvent.total || deliveryFile.value.size;
        uploadPercentage.value = Math.round((progressEvent.loaded * 100) / total);
      }
    });
    
    const fname = deliveryFile.value?.name || '';
    const isVid = /\.(mp4|mov|m4v|avi|mkv)$/i.test(fname);
    notify.success(
      isVid
        ? 'Delivered. A watermarked, lower-resolution preview is generating for the brand (usually 1–3 minutes).'
        : 'Project delivered successfully.'
    );
    showDeliverModal.value = false;
    await load();
  } catch (e) {
    const msg = String(e.response?.data?.message || '');
    const payloadTooLarge =
      e.response?.status === 413 ||
      /post data is too large/i.test(msg);
    notify.error(
      payloadTooLarge
        ? 'Upload was blocked by the server (limit below 100MB). If this persists after retrying, contact support.'
        : (msg || 'Failed to submit delivery.')
    );
  } finally {
    submittingDelivery.value = false;
  }
}

async function load() {
  const res = await axios.get('/api/collaborations', { withCredentials: true });
  list.value = res.data;
}

async function accept(c) {
  error.value = '';
  try {
    await axios.post('/api/collaborations/' + c.id + '/accept', {}, { withCredentials: true });
    notify.success('Project accepted. Waiting for brand payment.');
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to accept.';
  }
}

async function reject(c) {
  rejectingCollab.value = c;
  rejectionReason.value = '';
  customReason.value = '';
  showRejectModal.value = true;
}

async function confirmReject() {
  if (!rejectingCollab.value) return;
  const c = rejectingCollab.value;
  const reason = rejectionReason.value === 'Other' ? customReason.value : rejectionReason.value;
  
  submittingReject.value = true;
  error.value = '';
  try {
    await axios.post('/api/collaborations/' + c.id + '/reject', { reason }, { withCredentials: true });
    notify.success('Project declined.');
    showRejectModal.value = false;
    await load();
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to reject.';
    notify.error(error.value);
  } finally {
    submittingReject.value = false;
  }
}

onMounted(load);
</script>
