<template>
  <div class="pb-20">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-[#1a1a1a] tracking-tight">Collaborations</h1>
        <p class="mt-1 text-[#64748b] font-medium">Your collaboration requests and payments. Pay securely via PayU.</p>
      </div>
      <div v-if="activeTab === 'collaborations'" class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-2xl border border-slate-200">
        <div class="w-2 h-2 rounded-full bg-[#fc4402] animate-pulse"></div>
        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ list.length }} Projects Tracked</span>
      </div>
    </div>

    <!-- Settlement Header Tabs -->
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
        <ul v-if="list.length" class="space-y-8">
          <li v-for="c in list" :key="c.id" 
              class="group overflow-hidden rounded-[48px] border border-[#e2e8f0] bg-white transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1">
            
            <!-- Card Header -->
            <div class="border-b border-slate-50 bg-[#f8fafc]/50 p-8 sm:p-10">
              <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-5">
                  <div class="h-16 w-16 shrink-0 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center overflow-hidden">
                    <img v-if="c.creator?.profile?.avatar" :src="c.creator.profile.avatar" class="h-full w-full object-cover" />
                    <span v-else class="text-2xl font-black text-slate-300 uppercase">{{ c.creator?.name?.charAt(0) }}</span>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-[#1a1a1a] group-hover:text-[#fc4402] transition-colors">{{ c.creator?.name }}</h3>
                    <div class="mt-1 flex items-center gap-3">
                      <span class="text-[10px] font-black uppercase tracking-widest text-[#fc4402] bg-[#fc4402]/5 px-2 py-0.5 rounded-md border border-[#fc4402]/10">
                        {{ c.package?.name || 'Custom Project' }}
                      </span>
                      <span class="text-xs font-bold text-[#64748b]">₹{{ c.status === 'resolved' ? (c.amount - (c.resolved_refund_amount || 0)).toFixed(2) : c.amount }} <span class="text-[10px] text-slate-400 font-medium">(inc. fee: ₹{{ c.platform_fee }})</span></span>
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 rounded-2xl bg-white p-2 border border-slate-100 shadow-sm">
                    <div class="px-5 py-2 text-center rounded-2xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Revisions</p>
                        <p class="text-lg font-black" :class="c.revision_count >= c.max_revisions ? 'text-red-500' : 'text-slate-700'">
                            {{ c.revision_count }}<span class="text-xs text-slate-300">/{{ c.max_revisions === 20 ? '∞' : c.max_revisions }}</span>
                        </p>
                    </div>
                    <div v-if="c.status === 'completed'" class="flex items-center gap-2 text-green-600 bg-green-50 px-5 py-2 rounded-2xl border border-green-100 font-black uppercase text-[10px] tracking-widest">
                        Project Finished
                    </div>
                </div>
              </div>
            </div>

            <!-- Progress Stepper -->
            <div class="px-6 py-8 sm:px-12 bg-white border-b border-slate-50">
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

            <!-- Lower Section: Actions -->
            <div class="p-8 sm:p-10 bg-[#f8fafc]/30">
              <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1 max-w-2xl">
                    <!-- Project Preview for Delivered/Completed -->
                    <div v-if="c.deliverable_content" class="flex items-center gap-5 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm animate-in zoom-in-95 duration-500">
                        <div class="h-16 w-16 rounded-2xl bg-slate-50 flex items-center justify-center text-[#fc4402] border border-slate-100">
                          <svg v-if="isImage(c.deliverable_content)" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                          <svg v-else class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest line-clamp-1">Secure Project Deliverable</p>
                            <p class="text-sm font-bold text-slate-700 truncate max-w-xs">{{ c.deliverable_content.split('/').pop() }}</p>
                            <p v-if="c.status === 'delivered' && isVideoFile(c.deliverable_content) && c.deliverable_preview_status === 'processing'" class="text-[10px] font-bold text-amber-600 mt-1.5 uppercase tracking-wide">
                              Preparing lower-resolution watermarked preview (1–3 min)…
                            </p>
                            <p v-if="c.status === 'delivered' && isVideoFile(c.deliverable_content) && c.deliverable_preview_status === 'failed'" class="text-[10px] font-bold text-red-600 mt-1.5">
                              Preview encoding failed — contact support.
                            </p>
                            <div class="mt-2 flex items-center gap-4">
                                <button v-if="c.status === 'delivered' || c.status === 'completed' || c.status === 'resolved'" 
                                        @click="openPreview(c)" 
                                        class="text-[10px] font-black text-[#fc4402] uppercase tracking-widest hover:underline flex items-center gap-1.5">
                                   <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                   Secure Preview
                                </button>
                                <button v-if="c.status === 'completed' || c.status === 'resolved'" 
                                   type="button"
                                   @click="downloadDeliverable(c)"
                                   class="text-[10px] font-black text-emerald-600 uppercase tracking-widest hover:underline flex items-center gap-1.5 bg-transparent border-none cursor-pointer p-0 font-inherit">
                                   <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                   Download Final
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="c.status === 'accepted' && !c.paid_at" class="flex flex-col gap-4">
                      <div class="flex items-center gap-3">
                          <input type="checkbox" v-model="c.acceptedTerms" class="h-5 w-5 rounded-lg border-slate-300 text-[#fc4402] focus:ring-[#fc4402]" />
                          <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">I accept the <button @click="showTermsModal = true" class="text-[#fc4402] hover:underline">Working Agreement</button></label>
                      </div>
                      <button @click="pay(c)" :disabled="payingId === c.id || !c.acceptedTerms" class="h-16 px-12 rounded-2xl bg-[#fc4402] text-white font-black uppercase tracking-widest shadow-2xl shadow-[#fc4402]/30 hover:bg-[#e63d02] transition active:scale-95 disabled:opacity-50">
                        {{ payingId === c.id ? 'Starting Payment...' : 'Secure Pay ₹' + c.amount }}
                      </button>
                    </div>

                    <div v-else-if="c.status === 'disputed'" class="flex gap-4 rounded-3xl bg-red-50 p-6 border border-red-100/50">
                        <div class="h-12 w-12 shrink-0 rounded-2xl bg-red-600 flex items-center justify-center text-white shadow-xl shadow-red-600/20">
                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div>
                          <p class="text-[10px] font-black uppercase tracking-widest text-red-600">Dispute Under Review</p>
                          <p class="mt-1 text-sm text-red-900 font-bold">Admin mediation in progress.</p>
                          <router-link :to="'/brand/support?collab_id=' + c.id" class="mt-2 inline-flex items-center gap-2 text-[11px] font-black text-red-600 uppercase tracking-widest hover:underline">
                            Open Support Ticket
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                          </router-link>
                        </div>
                    </div>
                </div>

                <!-- Right Action Block -->
                <div class="shrink-0 flex items-center lg:justify-end gap-3">
                   <div v-if="c.status === 'delivered'" class="flex gap-3">
                     <button @click="complete(c)" class="h-14 px-8 rounded-2xl bg-[#10b981] text-white font-black uppercase tracking-widest hover:bg-[#059669] shadow-xl shadow-emerald-200 transition active:scale-95">Accept & Finish</button>
                     <div class="flex gap-2">
                        <button v-if="c.revision_count < c.max_revisions" @click="openRevision(c)" class="h-14 px-8 rounded-2xl border border-purple-200 bg-white text-purple-600 font-black uppercase tracking-widest hover:bg-purple-50 transition active:scale-95">Revision</button>
                        <button @click="rejectWork(c)" class="h-14 px-8 rounded-2xl border border-red-200 bg-white text-red-600 font-black uppercase tracking-widest hover:bg-red-50 transition active:scale-95">Reject</button>
                     </div>
                   </div>

                   <div v-else-if="c.status === 'resolved'" class="rounded-3xl bg-amber-50 p-6 border border-amber-200">
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-3 flex items-center gap-1.5 line-clamp-1 truncate">
                           <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                           Mediation Settled
                        </p>
                        <div class="flex items-center gap-8">
                            <div>
                                <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest leading-none mb-1.5">Settlement Refund</p>
                                <p class="text-2xl font-black text-amber-900 leading-none">₹{{ c.resolved_refund_amount || 0 }}</p>
                            </div>
                            <button 
                               v-if="c.resolved_refund_amount > 0"
                               @click="openClaimModal(c)" 
                               :disabled="c.brand_claimed"
                               class="h-14 px-8 rounded-2xl bg-amber-600 text-white font-black uppercase tracking-widest hover:bg-amber-700 transition shadow-xl shadow-amber-600/20 disabled:opacity-40"
                            >
                               {{ 
                                 c.brand_claimed 
                                   ? (c.payout_requests?.find(p => p.type === 'brand_refund')?.status === 'paid' ? 'Refunded' : 'Processing') 
                                   : 'Claim Refund' 
                               }}
                            </button>
                        </div>
                        <div v-if="c.payout_requests?.find(p => p.type === 'brand_refund')?.receipt_url" class="mt-4 pt-4 border-t border-amber-100">
                           <a :href="'/storage/' + c.payout_requests.find(p => p.type === 'brand_refund').receipt_url" target="_blank" class="text-[9px] font-black text-amber-700 uppercase tracking-widest hover:underline flex items-center gap-1.5 justify-center">
                              <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                              Download Receipt Notice
                           </a>
                        </div>
                   </div>

                   <div v-else-if="['paid', 'revision_requested'].includes(c.status)" class="px-6 py-3 bg-blue-50 text-blue-600 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-blue-100 flex items-center gap-2">
                       <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span>
                       Creator is Working
                   </div>
                </div>
              </div>
            </div>
          </li>
        </ul>

        <div v-else class="mt-12 rounded-[48px] border-2 border-dashed border-slate-200 bg-white p-24 text-center">
            <div class="w-24 h-24 bg-slate-50 rounded-[40px] flex items-center justify-center mx-auto mb-8 text-slate-300">
               <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
            <h3 class="text-3xl font-black text-slate-900 tracking-tight">No projects yet</h3>
            <p class="mt-3 text-slate-500 font-bold max-w-sm mx-auto uppercase text-[10px] tracking-[0.2em] leading-relaxed">Book creators to start collaborating. All your payments and progress will be tracked here.</p>
        </div>
    </div>

    <!-- Modals -->
    <div v-if="showTermsModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4" @click.self="showTermsModal = false">
      <div class="w-full max-w-2xl overflow-hidden rounded-[48px] bg-white shadow-2xl animate-in zoom-in-95 duration-500">
        <div class="border-b border-slate-100 bg-[#f8fafc] px-10 py-8">
          <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Collaboration Agreement</h2>
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">Professional Working Terms</p>
        </div>
        <div class="max-h-[60vh] overflow-y-auto p-10 space-y-8 text-sm leading-relaxed text-slate-600">
            <section v-for="(term, i) in ['Payment Protection', 'Deliverables & Revisions', 'Dispute Resolution', 'Platform Ethics']" :key="i">
                <h3 class="font-black text-slate-900 uppercase tracking-widest text-[10px] mb-3">{{ i+1 }}. {{ term }}</h3>
                <p class="font-medium text-slate-500">StarJD ensures high-quality outcomes. Payments are held in escrow and only released upon your formal approval of the delivered work.</p>
            </section>
        </div>
        <div class="border-t border-slate-100 p-8 flex justify-end bg-slate-50">
            <button @click="showTermsModal = false" class="h-16 px-12 rounded-2xl bg-slate-900 text-white font-black uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:bg-black transition active:scale-95">I Accept Terms</button>
        </div>
      </div>
    </div>

    <div v-if="showRevisionModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4" @click.self="showRevisionModal = false">
      <div class="w-full max-w-lg overflow-hidden rounded-[40px] bg-white shadow-2xl animate-in zoom-in-95 duration-500">
        <div class="px-10 py-8 border-b border-slate-50" :class="isRejectionMode ? 'bg-red-50' : 'bg-slate-50'">
          <h2 class="text-2xl font-black" :class="isRejectionMode ? 'text-red-900' : 'text-slate-900'">{{ isRejectionMode ? 'Dispute Delivery' : 'Request Revision' }}</h2>
          <p class="text-[10px] font-bold uppercase tracking-widest mt-2" :class="isRejectionMode ? 'text-red-600' : 'text-slate-400'">{{ isRejectionMode ? 'Escalate to Support Mediation' : 'Fine-tune the output' }}</p>
        </div>
        <div class="p-10 space-y-6">
          <div>
            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 ml-1">{{ isRejectionMode ? 'Dispute Reason' : 'Revision Details' }}</label>
            <textarea 
              v-model="revisionText"
              class="w-full min-h-[160px] rounded-[32px] border-none bg-slate-50 focus:ring-2 focus:ring-slate-400 p-6 font-medium text-slate-700 resize-none transition-shadow"
              placeholder="Be specific about what needs to be changed..."
            ></textarea>
          </div>
          <div class="flex gap-4">
            <button @click="submitRevision" :disabled="submittingRev || !revisionText.trim()" class="flex-1 h-16 rounded-2xl text-white font-black uppercase tracking-widest transition-all active:scale-95 disabled:opacity-40" :class="isRejectionMode ? 'bg-red-600 shadow-red-200' : 'bg-slate-900 shadow-slate-200'">
              {{ submittingRev ? 'Processing...' : (isRejectionMode ? 'Raise Dispute' : 'Send Request') }}
            </button>
            <button @click="showRevisionModal = false" class="px-10 h-16 rounded-2xl border border-slate-200 text-slate-600 font-black uppercase tracking-widest hover:bg-slate-50 transition active:scale-95">Cancel</button>
          </div>
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
              
              <!-- Watermark Overlay -->
              <div class="absolute inset-0 pointer-events-none flex items-center justify-center overflow-hidden opacity-10 select-none rotate-[-30deg]">
                 <div class="text-[12rem] font-black text-white whitespace-nowrap tracking-tighter">STARJD PREVIEW</div>
              </div>
           </div>
           
           <div class="mt-12 px-8 py-4 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 text-white/60 text-[10px] font-black uppercase tracking-[0.3em] flex items-center gap-4">
              <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> SECURE PREVIEW MODE</span>
              <span class="w-px h-4 bg-white/10"></span>
              <span>PROTECTED BY STARJD ESCROW</span>
           </div>
        </div>
    </div>

    <!-- Refund Claim Bank Selector Modal -->
    <div v-if="showClaimModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/40 backdrop-blur-md p-4">
       <div class="w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col bg-slate-50 rounded-[64px] shadow-2xl animate-in zoom-in-95 duration-500">
          <div class="p-12 pb-6 flex justify-between items-center bg-white border-b border-slate-100">
             <div>
               <h3 class="text-4xl font-black text-slate-900 leading-none tracking-tight">Claim Refund</h3>
               <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-3">Verified Payout Destination</p>
             </div>
             <button @click="showClaimModal = false" class="h-14 w-14 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-900 hover:bg-slate-100 transition shadow-sm border border-slate-100">
               <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
             </button>
          </div>
          <div class="flex-1 overflow-y-auto p-12">
             <div class="mb-12 bg-amber-50 border border-amber-200 rounded-[40px] p-8 flex items-center justify-between">
                <div>
                   <p class="text-[11px] font-black text-amber-600 uppercase tracking-widest mb-1.5 leading-none">Total Refund Amount</p>
                   <p class="text-5xl font-black text-amber-900 leading-none tracking-tighter">₹{{ claimingCollab?.resolved_refund_amount }}</p>
                </div>
                <div class="h-16 w-1 rounded-full bg-amber-200 mx-10"></div>
                <p class="text-sm font-bold text-amber-700 leading-relaxed max-w-sm italic italic">
                   "Funds will be credited back to your bank account within 5-7 working days after initiation."
                </p>
             </div>
             <BankManager @select="claimRefund" />
          </div>
       </div>
    </div>

    <!-- Hidden PayU Form -->
    <form ref="payuFormEl" method="post" :action="payuUrl" class="hidden">
      <input v-for="(val, key) in payuParams" :key="key" :name="key" :value="val" type="hidden" />
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick, onMounted } from 'vue';
import axios from 'axios';
import { notify } from '../../lib/notify.js';
import BankManager from '../../components/common/BankManager.vue';
import SecureVideoPlayer from '../../components/common/SecureVideoPlayer.vue';

const list = ref([]);
const error = ref('');
const payingId = ref(null);
const payuFormEl = ref(null);
const payuUrl = ref('');
const payuParams = ref({});

const activeTab = ref('collaborations');
const showClaimModal = ref(false);
const claimingCollab = ref(null);

const showRevisionModal = ref(false);
const isRejectionMode = ref(false);
const showTermsModal = ref(false);
const selectedCollab = ref(null);
const revisionText = ref('');
const submittingRev = ref(false);

const showPreviewModal = ref(false);
const previewUrl = ref('');
const previewType = ref('');

const steps = ['Request', 'Accepted', 'Paid', 'Delivered', 'Completed'];

const statusMap = {
  'pending': 0,
  'accepted': 1,
  'paid': 2,
  'revision_requested': 2,
  'disputed': 3,
  'delivered': 3,
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

function isImage(url) {
    if (!url) return false;
    return /\.(jpg|jpeg|png|webp|gif)$/i.test(url);
}

function isVideoFile(path) {
    if (!path) return false;
    return /\.(mp4|mov|m4v|avi|mkv)$/i.test(path);
}

async function openPreview(c) {
  try {
    const res = await axios.get(`/api/collaborations/${c.id}/file`, { withCredentials: true });
    if (res.data.ready === false) {
      notify.info(res.data.message || 'Preview is not ready yet. Please try again in a minute.');
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

async function downloadDeliverable(c) {
  try {
    const res = await axios.get(`/api/collaborations/${c.id}/file`, {
      params: { intent: 'download' },
      withCredentials: true,
    });
    const token = res.data.preview_token;
    if (!token) {
      notify.error('Unable to start download.');
      return;
    }
    const url = `${res.data.url}?preview_token=${encodeURIComponent(token)}&download=1`;
    window.location.assign(url);
  } catch (e) {
    notify.error(e.response?.data?.message || 'Download failed.');
  }
}

function openClaimModal(c) {
  claimingCollab.value = c;
  showClaimModal.value = true;
}

async function claimRefund(bank) {
  const c = claimingCollab.value;
  try {
    await axios.post(`/api/collaborations/${c.id}/claim-settlement`, { 
        type: 'brand',
        bank_account_id: bank.id
    }, { withCredentials: true });
    notify.success('Refund request successfully raised. Credits will reflect in 5-7 working days.');
    showClaimModal.value = false;
    await load();
  } catch (e) {
    notify.error(e.response?.data?.message || 'Failed to process refund claim.');
  }
}

async function load() {
  const res = await axios.get('/api/collaborations', { withCredentials: true });
  list.value = res.data.map(c => ({
    ...c,
    acceptedTerms: false
  }));
}

async function complete(c) {
  if(!confirm('Are you sure you want to accept this delivery? This will release the payment to the creator.')) return;
  try {
    await axios.post(`/api/collaborations/${c.id}/complete`, {}, { withCredentials: true });
    notify.success('Collaboration marked as completed. You can now access full deliverables.');
    await load();
  } catch (e) {
    notify.error('Failed to complete collaboration.');
  }
}

function openRevision(c) {
  selectedCollab.value = c;
  isRejectionMode.value = false;
  revisionText.value = '';
  showRevisionModal.value = true;
}

function rejectWork(c) {
  selectedCollab.value = c;
  isRejectionMode.value = true;
  revisionText.value = '';
  showRevisionModal.value = true;
}

async function submitRevision() {
  submittingRev.value = true;
  try {
    const endpoint = isRejectionMode.value ? `/api/collaborations/${selectedCollab.value.id}/reject-work` : `/api/collaborations/${selectedCollab.value.id}/revision`;
    await axios.post(endpoint, {
      [isRejectionMode.value ? 'reason' : 'notes']: revisionText.value
    }, { withCredentials: true });
    
    notify.success(isRejectionMode.value ? 'Dispute raised successfully.' : 'Revision requested successfully.');
    showRevisionModal.value = false;
    await load();
  } catch (e) {
    notify.error('Action failed.');
  } finally {
    submittingRev.value = false;
  }
}

async function pay(c) {
  payingId.value = c.id;
  try {
    const res = await axios.post('/api/payment/payu/create', {
      type: 'collaboration',
      collaboration_id: c.id,
      amount: Number(c.amount),
    }, { withCredentials: true });
    payuUrl.value = res.data.payment_url;
    payuParams.value = res.data.params || {};
    nextTick(() => {
      if (payuFormEl.value) payuFormEl.value.submit();
    });
  } catch (e) {
    payingId.value = null;
    notify.error(e.response?.data?.message || 'Failed to start payment.');
  }
}

onMounted(load);
</script>
