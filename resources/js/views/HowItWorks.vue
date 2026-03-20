<template>
  <div class="min-h-screen bg-[#fafaf9] overflow-x-hidden pt-0 selection:bg-[#fc4402]/20 selection:text-[#fc4402]">
    <!-- Hero Section -->
    <section class="relative px-4 pt-12 pb-16 lg:pt-20 lg:pb-20">
      <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] bg-[#fc4402]/5 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute top-[20%] -left-[10%] w-[30%] h-[30%] bg-[#10b981]/5 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 right-1/4 w-[20%] h-[20%] bg-blue-500/5 rounded-full blur-[80px]"></div>
      </div>
      
      <div class="mx-auto max-w-7xl relative z-10 text-center">
        <span class="inline-block px-4 py-1.5 mb-4 text-xs font-bold uppercase tracking-wider text-[#fc4402] bg-[#fc4402]/10 rounded-full animate-fade-in-down border border-[#fc4402]/20">
          India's Premier Studio & Creative Network
        </span>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-[#1a1a1a] leading-tight mb-4 tracking-tight">
          How <span class="text-[#fc4402]">StarJD</span> Connects <br class="hidden md:block" /> Studios & Creators
        </h1>
        <p class="mx-auto max-w-3xl text-lg md:text-xl text-[#4b5563] leading-relaxed mb-6 opacity-0 animate-fade-in animation-delay-300">
          Discover a seamless digital platform connecting studio owners, content creators, brands, and professionals in Delhi NCR and beyond.
        </p>
        <div class="flex flex-wrap justify-center gap-4 opacity-0 animate-fade-in animation-delay-500">
          <router-link to="/register" class="px-8 py-4 bg-[#fc4402] text-white rounded-2xl font-bold text-lg transition-all hover:bg-[#e63d02] hover:scale-105 shadow-xl shadow-[#fc4402]/30 active:scale-95">
            Book a Studio Today
          </router-link>
          <a href="#workflow" class="group px-8 py-4 bg-white text-[#1a1a1a] border border-[#e5e7eb] rounded-2xl font-bold text-lg transition-all hover:bg-[#fafafc] hover:border-[#fc4402]/40 shadow-sm flex items-center gap-2">
            See the Process
            <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </a>
        </div>
      </div>
      
      <div class="mt-12 mx-auto max-w-6xl relative animate-up animation-delay-700 px-4">
        <div class="rounded-[2.5rem] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-white/50 backdrop-blur-sm group p-2">
           <img src="/images/how-it-works/hero.png" alt="StarJD Platform Showcase" class="w-full h-[300px] md:h-[450px] object-cover rounded-[2rem] transition-transform duration-1000 group-hover:scale-105" />
        </div>
        <!-- Floaters -->
        <div class="absolute -top-6 -right-2 lg:-right-8 bg-white/90 backdrop-blur p-4 rounded-3xl shadow-2xl border border-white hidden md:block animate-bounce-slow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                </div>
                <span class="font-bold text-sm tracking-tight">Verified in Delhi NCR</span>
            </div>
        </div>
      </div>
    </section>

    <!-- Intro Text -->
    <section class="py-12 bg-white/50 backdrop-blur-sm border-y border-[#e5e7eb]">
        <div class="mx-auto max-w-4xl px-4 text-center">
            <p class="text-xl md:text-2xl text-[#1a1a1a] leading-relaxed font-medium">
              Whether you're looking to <span class="text-[#fc4402] font-bold">book a studio in Delhi</span>, collaborate on a project, or find creative talent, StarJD makes the process fast, and efficient.
            </p>
        </div>
    </section>

    <!-- Roles Section -->
    <section class="py-32 bg-[#fafaf9] relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4">
            <div class="mb-20 text-center max-w-3xl mx-auto animate-on-scroll fade-in">
                <h2 class="text-4xl md:text-5xl font-black text-[#1a1a1a] mb-6 tracking-tight">How StarJD Works for Everyone</h2>
                <p class="text-[#64748b] text-lg">Our platform bridges the gap between creative demand and supply, helping you grow effortlessly.</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="space-y-4 animate-on-scroll fade-in">
                    <div 
                        v-for="(role, index) in roles" 
                        :key="index"
                        @click="activeRole = index"
                        class="p-8 rounded-3xl border transition-all duration-500 cursor-pointer group hover:-translate-y-1"
                        :class="activeRole === index ? 'bg-white border-[#fc4402] shadow-[0_20px_40px_rgba(252,68,2,0.08)] ring-1 ring-[#fc4402]/10' : 'bg-white/40 border-[#e5e7eb] hover:bg-white'"
                    >
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-xl transition-colors duration-500" 
                                :class="activeRole === index ? 'bg-[#fc4402] text-white' : 'bg-[#e5e7eb] text-[#6b7280] group-hover:bg-[#fc4402]/10 group-hover:text-[#fc4402]'">
                                {{ index + 1 }}
                            </div>
                            <div>
                                <h3 class="text-2xl font-black transition-colors" :class="activeRole === index ? 'text-[#1a1a1a]' : 'text-[#6b7280]'">{{ role.name }}</h3>
                                <p class="text-sm font-bold text-[#fc4402] uppercase tracking-wider mt-0.5" v-if="activeRole === index">{{ role.tagline }}</p>
                            </div>
                        </div>
                        <div v-if="activeRole === index" class="mt-6 text-[#64748b] animate-fade-in space-y-4">
                            <p class="text-lg leading-relaxed">{{ role.description }}</p>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div v-for="(benefit, bi) in role.benefits" :key="bi" class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-[#10b981] mt-1 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    <span class="text-sm font-medium text-[#4b5563]">{{ benefit }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative animate-on-scroll scale-up lg:sticky lg:top-32 h-fit">
                    <div class="aspect-[4/5] rounded-[3rem] overflow-hidden shadow-[0_30px_60px_rgba(0,0,0,0.12)] border-8 border-white bg-white group">
                        <img 
                            :src="roles[activeRole].image" 
                            :alt="roles[activeRole].name" 
                            class="w-full h-full object-cover transition-all duration-1000 ease-out scale-100 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-10">
                            <div class="bg-[#fc4402] text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest w-fit mb-4">Elite StarJD Choice</div>
                            <h4 class="text-white text-3xl font-black mb-3">{{ roles[activeRole].name }}</h4>
                            <p class="text-white/80 text-lg leading-snug">{{ roles[activeRole].highlight }}</p>
                        </div>
                    </div>
                    <!-- Decor UI elements -->
                    <div class="absolute -bottom-10 -left-10 w-48 p-6 bg-white rounded-[2rem] shadow-2xl border border-emerald-100 hidden xl:block animate-float">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg></div>
                            <span class="text-xs font-bold text-slate-800">Booking Insight</span>
                        </div>
                        <p class="text-[10px] text-slate-500 font-medium">98% customer satisfaction on recent bookings in Delhi NCR.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- The 5 Steps Workflow -->
    <section id="workflow" class="py-32 bg-white relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4">
            <div class="text-center mb-24 animate-on-scroll fade-in">
                <span class="text-[#fc4402] font-black uppercase tracking-widest text-sm mb-4 block">Proven Ecosystem</span>
                <h2 class="text-4xl md:text-6xl font-black text-[#1a1a1a] mb-6 tracking-tight italic">Our Process – Simple, Fast & Effective</h2>
                <p class="text-xl text-[#6b7280] max-w-2xl mx-auto">StarJD streamlines your journey from initial signup to large-scale production execution.</p>
            </div>

            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-1/2 top-0 bottom-1/4 w-[2px] bg-gradient-to-b from-[#fc4402] via-[#fc4402]/20 to-transparent hidden lg:block opacity-20"></div>

                <div class="space-y-40">
                    <!-- Step 1 -->
                    <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-32 group animate-on-scroll fade-in">
                        <div class="flex-1 text-right lg:order-1 order-2">
                             <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-[#fc4402] text-white text-3xl font-black mb-8 shadow-2xl shadow-[#fc4402]/30 lg:hidden transform rotate-3">1</div>
                             <h3 class="text-4xl font-black text-[#1a1a1a] mb-6">1. Sign Up – Create Your Free Account</h3>
                             <p class="text-[#64748b] text-xl leading-relaxed">
                                Register on StarJD in just a few clicks. No hidden fees—just a simple onboarding process designed for quick access to <span class="text-[#fc4402] font-bold">studio booking in Delhi NCR</span>.
                             </p>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-20 h-20 rounded-[2rem] bg-[#fc4402] text-white text-3xl font-black shadow-2xl shadow-[#fc4402]/30 z-20 order-2 transform hover:rotate-12 transition-transform duration-500">1</div>
                        <div class="flex-1 order-3 lg:order-3">
                             <div class="bg-[#fafafc] p-10 rounded-[3rem] border border-[#e5e7eb] shadow-xl group-hover:shadow-[#fc4402]/5 transition-all duration-700">
                                 <div class="flex flex-col gap-6">
                                     <div class="h-14 bg-white rounded-2xl border border-[#e5e7eb] flex items-center px-4 gap-3">
                                         <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V5h2v4z"></path></svg></div>
                                         <span class="text-sm font-bold text-slate-700">Name: StarJD</span>
                                     </div>
                                     <div class="h-14 bg-white rounded-2xl border border-[#e5e7eb] flex items-center px-4 gap-3">
                                         <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg></div>
                                         <span class="text-sm font-bold text-slate-700">Email: shine@starjd.com</span>
                                     </div>
                                     <div class="h-14 bg-[#fc4402] rounded-2xl flex items-center justify-center text-white font-black shadow-lg">CONTINUE</div>
                                 </div>
                             </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-32 group animate-on-scroll fade-in">
                        <div class="flex-1 order-2 lg:order-1">
                             <div class="bg-[#fafafc] p-10 rounded-[3rem] border border-[#e5e7eb] shadow-xl group-hover:shadow-[0_20px_40px_rgba(0,0,0,0.05)] transition-all duration-700">
                                 <div class="grid grid-cols-2 gap-4">
                                     <div class="p-6 bg-white rounded-3xl border-2 border-[#fc4402] text-center scale-105 shadow-xl">
                                         <div class="w-10 h-10 bg-[#fc4402]/10 rounded-xl flex items-center justify-center text-[#fc4402] mx-auto mb-3"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg></div>
                                         <span class="block font-black text-slate-800 tracking-tight">Studio Owner</span>
                                     </div>
                                     <div class="p-6 bg-white rounded-3xl border border-[#e5e7eb] text-center hover:border-[#fc4402]/50 transition-colors">
                                         <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 mx-auto mb-3"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg></div>
                                         <span class="block font-bold text-slate-400">Creator</span>
                                     </div>
                                 </div>
                             </div>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-20 h-20 rounded-[2rem] bg-[#10b981] text-white text-3xl font-black shadow-2xl shadow-[#10b981]/30 z-20 order-2 transform hover:-rotate-12 transition-transform duration-500">2</div>
                        <div class="flex-1 lg:order-3 order-3">
                             <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-[#10b981] text-white text-3xl font-black mb-8 shadow-2xl shadow-[#10b981]/30 lg:hidden transform -rotate-3">2</div>
                             <h3 class="text-4xl font-black text-[#1a1a1a] mb-6">2. Choose Your Role</h3>
                             <p class="text-[#64748b] text-xl leading-relaxed">
                                This helps us tailor your dashboard and connect you with relevant opportunities such as studio rentals, freelance projects, or production collaborations in Delhi.
                             </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-32 group animate-on-scroll fade-in">
                        <div class="flex-1 text-right lg:order-1 order-2">
                             <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-[#fc4402] text-white text-3xl font-black mb-8 shadow-2xl shadow-[#fc4402]/30 lg:hidden transform rotate-2">3</div>
                             <h3 class="text-4xl font-black text-[#1a1a1a] mb-6">3. Explore or List Services</h3>
                             <p class="text-[#64748b] text-xl leading-relaxed">
                                Creators can explore massive lists of <span class="text-[#fc4402] font-bold">photo studios in Delhi NCR</span>, while owners list spaces to attract high-intent clients actively searching for rentals.
                             </p>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-20 h-20 rounded-[2rem] bg-[#fc4402] text-white text-3xl font-black shadow-2xl shadow-[#fc4402]/30 z-20 order-2 transform hover:rotate-12 transition-transform duration-500">3</div>
                        <div class="flex-1 order-3 lg:order-3">
                             <div class="bg-[#fafafc] p-6 rounded-[3rem] border border-[#e5e7eb] shadow-xl overflow-hidden relative group-hover:translate-x-4 transition-transform duration-700">
                                 <div class="flex gap-4">
                                     <div class="w-full h-48 bg-white rounded-3xl shadow-lg p-4">
                                         <div class="h-32 bg-slate-100 rounded-2xl mb-3 relative overflow-hidden">
                                             <img src="/images/how-it-works/studio.png" class="w-full h-full object-cover" />
                                             <div class="absolute top-2 right-2 bg-emerald-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">AVAILBLE</div>
                                         </div>
                                         <div class="flex justify-between items-center">
                                             <span class="text-xs font-black text-slate-800 italic">Okhla Phase III Studio</span>
                                             <span class="text-xs font-bold text-[#fc4402]">₹1200/hr</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-32 group animate-on-scroll fade-in">
                        <div class="flex-1 order-2 lg:order-1">
                             <div class="bg-[#1a1a1a] p-10 rounded-[3rem] shadow-2xl transform group-hover:scale-95 transition-all duration-700 text-white">
                                 <div class="flex items-center gap-4 mb-8">
                                     <div class="w-12 h-12 rounded-2xl bg-[#fc4402] flex items-center justify-center font-black">SJ</div>
                                     <div>
                                         <div class="text-sm font-black">Secure Messenger</div>
                                         <div class="text-[10px] text-white/50">End-to-End Encrypted</div>
                                     </div>
                                 </div>
                                 <div class="space-y-4">
                                     <div class="bg-white/5 p-4 rounded-2xl rounded-bl-none max-w-[80%] text-xs border border-white/10">Hi! I want to book for tomorrow.</div>
                                     <div class="bg-[#fc4402] p-4 rounded-2xl rounded-br-none max-w-[80%] ml-auto text-xs font-medium">Sure! We are available in Okhla.</div>
                                 </div>
                             </div>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-20 h-20 rounded-[2rem] bg-[#10b981] text-white text-3xl font-black shadow-2xl shadow-[#10b981]/30 z-20 order-2 transform hover:-rotate-12 transition-transform duration-500">4</div>
                        <div class="flex-1 lg:order-3 order-3">
                             <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-[#10b981] text-white text-3xl font-black mb-8 shadow-2xl shadow-[#10b981]/30 lg:hidden transform -rotate-3">4</div>
                             <h3 class="text-4xl font-black text-[#1a1a1a] mb-6">4. Connect & Collaborate</h3>
                             <p class="text-[#64748b] text-xl leading-relaxed">
                                Communicate <span class="text-slate-900 border-b-2 border-[#10b981]/20 font-bold">directly</span> with studio owners, creators, and professionals. Discuss project requirements and budgets without any intermediaries.
                             </p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-32 group animate-on-scroll fade-in">
                        <div class="flex-1 text-right lg:order-1 order-2">
                             <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-[#fc4402] text-white text-3xl font-black mb-8 shadow-2xl shadow-[#fc4402]/30 lg:hidden transform rotate-2">5</div>
                             <h3 class="text-4xl font-black text-[#1a1a1a] mb-6">5. Execute Projects Seamlessly</h3>
                             <p class="text-[#64748b] text-xl leading-relaxed">
                                From booking a <span class="text-slate-900 font-bold">creative space in Delhi</span> to completing a full-scale production, StarJD ensures a smooth workflow with reliable support.
                             </p>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-20 h-20 rounded-[2rem] bg-[#fc4402] text-white text-3xl font-black shadow-2xl shadow-[#fc4402]/30 z-20 order-2 transform hover:rotate-12 transition-transform duration-500">5</div>
                        <div class="flex-1 order-3 lg:order-3">
                             <div class="bg-gradient-to-br from-[#10b981]/20 to-[#fc4402]/20 p-1.5 rounded-[3.5rem] group-hover:scale-105 transition-transform duration-700">
                                 <div class="bg-white p-12 rounded-[2.9rem] text-center border border-white/30 backdrop-blur-3xl">
                                     <div class="w-24 h-24 bg-emerald-500 rounded-full flex items-center justify-center text-white mx-auto mb-8 animate-bounce-slow shadow-2xl shadow-emerald-500/40">
                                         <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                     </div>
                                     <h5 class="text-3xl font-black text-slate-800 mb-2 italic">Project Complete!</h5>
                                     <p class="text-slate-500 font-bold tracking-widest text-xs uppercase">Unlock Your Full Potential</p>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose StarJD -->
    <section class="py-32 bg-slate-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="grid grid-cols-12 gap-1 h-full">
                <div v-for="i in 120" :key="i" class="border border-white/5 h-20"></div>
            </div>
        </div>
        <div class="mx-auto max-w-7xl px-4 relative z-10 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-8 animate-on-scroll fade-in">Why Thousands Choose <span class="text-[#fc4402]">StarJD</span></h2>
            <p class="text-white/60 mb-20 max-w-2xl mx-auto italic">The ultimate studio booking and creative collaboration platform in Delhi NCR.</p>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div v-for="(feature, idx) in features" :key="idx" class="p-10 rounded-[3rem] bg-white/5 border border-white/10 hover:bg-white transition-all duration-700 group text-left animate-on-scroll scale-up" :style="{ transitionDelay: (idx * 150) + 'ms' }">
                    <div class="w-20 h-20 mb-10 rounded-3xl flex items-center justify-center bg-white/10 group-hover:bg-[#fc4402] transition-all duration-700 transform group-hover:rotate-[360deg] shadow-2xl">
                        <svg class="w-10 h-10 group-hover:text-white text-[#fc4402] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="feature.icon"></svg>
                    </div>
                    <h3 class="text-2xl font-black text-white group-hover:text-slate-900 mb-4 tracking-tight">{{ feature.title }}</h3>
                    <p class="text-white/40 group-hover:text-slate-600 font-medium leading-relaxed">{{ feature.description }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-32 bg-white">
        <div class="mx-auto max-w-4xl px-4">
            <div class="text-center mb-24 animate-on-scroll fade-in">
                <h2 class="text-4xl md:text-5xl font-black text-slate-800 mb-6 italic">Got Questions? We Have Answers.</h2>
                <p class="text-lg text-slate-500 font-bold tracking-widest uppercase">Frequently Asked Questions</p>
            </div>
            
            <div class="space-y-6 animate-on-scroll fade-in">
                <div v-for="(faq, i) in faqs" :key="i" class="group border-2 border-slate-50 rounded-[2.5rem] overflow-hidden bg-slate-50/50 hover:bg-white hover:border-[#fc4402]/20 transition-all duration-500">
                    <button 
                        @click="toggleFaq(i)"
                        class="w-full px-10 py-8 text-left flex items-center justify-between group"
                    >
                        <span class="text-xl font-black text-slate-800 group-hover:text-[#fc4402] transition-colors">{{ faq.q }}</span>
                        <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm group-hover:bg-[#fc4402] group-hover:border-[#fc4402] transition-all">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition-transform" :class="{ 'rotate-180': openFaq === i }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </button>
                    <div v-show="openFaq === i" class="px-10 pb-8 text-[#64748b] text-lg font-medium animate-fade-in-down border-t border-slate-100 mt-2 pt-6">
                        {{ faq.a }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-32 px-4 animate-on-scroll fade-in">
        <div class="mx-auto max-w-6xl rounded-[4rem] bg-gradient-to-br from-[#fc4402] to-[#e63d02] p-16 md:p-24 text-center relative overflow-hidden shadow-[0_40px_100px_rgba(252,68,2,0.3)]">
            <!-- Background effects -->
            <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-20">
              <svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
              </svg>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-4xl md:text-7xl font-black text-white mb-8 tracking-tighter">Start Your Journey with <br /> StarJD Today</h2>
                <p class="text-xl text-white/90 mb-12 max-w-2xl mx-auto font-bold tracking-tight">List your studio, book a creative space, or hire professionals in Delhi NCR in seconds.</p>
                <router-link to="/register" class="inline-block px-14 py-6 bg-white text-[#fc4402] rounded-[2rem] font-black text-2xl hover:bg-slate-900 hover:text-white transition-all transform hover:scale-110 shadow-3xl active:scale-95">
                    Join StarJD Now
                </router-link>
            </div>
        </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const activeRole = ref(0);
const openFaq = ref(null);

const toggleFaq = (i) => {
    openFaq.value = openFaq.value === i ? null : i;
};

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
});

const roles = [
    {
        name: "Studio Owners",
        tagline: "List & Get More Bookings",
        description: "Grow your studio rental business in Delhi NCR. StarJD helps you connect with high-intent clients searching for photo rentals, video shoot studios, and creative spaces.",
        benefits: ["Direct booking inquiries", "SEO-optimized listings", "Zero commission barriers", "Reach verified brands"],
        image: "/images/how-it-works/studio.png",
        highlight: "Average revenue growth of 40% for Delhi studios."
    },
    {
        name: "Creators",
        tagline: "Find & Book Effortlessly",
        description: "Whether you're a YouTuber, podcaster, or filmmaker, explore a wide range of affordable rentals in Delhi tailored to your creative needs.",
        benefits: ["Search by budget & type", "Real-time availability", "Photography & podcast spaces", "Collaborator discovery"],
        image: "/images/how-it-works/creator.png",
        highlight: "Book premium Delhi studios in under 2 minutes."
    },
    {
        name: "Brands",
        tagline: "Find Studio & Talent",
        description: "Your all-in-one platform to discover production teams and creative professionals in Delhi NCR. Scale your content campaigns with ease.",
        benefits: ["Vetted talent pool", "Faster turnaround time", "Direct collaboration", "Complete project execution"],
        image: "/images/how-it-works/brand.png",
        highlight: "Used by 500+ Indian brands for high-impact ads."
    },
    {
        name: "Creative Professionals",
        tagline: "Showcase & Get Discovered",
        description: "Photographers, editors, and videographers connect with clients actively searching for creative experts in the Delhi creative industry.",
        benefits: ["Build a personal brand", "Direct client connections", "No middleman interference", "Freelance opportunities"],
        image: "/images/how-it-works/collaboration.png",
        highlight: "Top professionals earn 3x more via our direct network."
    }
];

const features = [
    {
        title: "Delhi's Top Studios",
        description: "Access a curated network of photography, video shoot, and podcast studios across Delhi NCR.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
    },
    {
        title: "Verified Experts",
        description: "Every professional and studio on StarJD goes through a strict verification process for reliability.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-7.618 3.033A11.955 11.955 0 012.248 14.39a11.934 11.934 0 0010.133 7.596 11.934 11.934 0 0010.133-7.596 11.955 11.955 0 01-2.903-11.422z" />',
    },
    {
        title: "Zero Intermediaries",
        description: "Communicate and finalize bookings directly with owners and talent. No hidden middleman fees.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
    },
    {
        title: "Scalable Creative",
        description: "Ideal for startups and large enterprises looking for scalable creative studio solutions.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />',
    }
];

const faqs = [
    { q: "What is StarJD and how does it work?", a: "StarJD is a digital platform that connects studio owners, creators, brands, and professionals, enabling seamless booking, collaboration, and project execution." },
    { q: "How can I list my studio on StarJD?", a: "You can sign up, create a studio profile, add details like pricing and availability, and start receiving booking requests instantly." },
    { q: "Is StarJD suitable for beginners in content creation?", a: "Yes, StarJD is ideal for beginners and professionals alike, offering easy access to studios and collaboration opportunities." },
    { q: "Can brands hire professionals directly on StarJD?", a: "Yes, brands can explore professional profiles, connect directly, and collaborate without intermediaries." }
];
</script>

<style scoped>
@keyframes fade-in-down {
  0% { opacity: 0; transform: translateY(-20px); }
  100% { opacity: 1; transform: translateY(0); }
}
@keyframes fade-in {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@keyframes up {
    0% { opacity: 0; transform: translateY(60px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down { animation: fade-in-down 1s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
.animate-fade-in { animation: fade-in 1s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
.animate-up { animation: up 1.2s cubic-bezier(0.22, 1, 0.36, 1) forwards; }

.animation-delay-300 { animation-delay: 300ms; }
.animation-delay-500 { animation-delay: 500ms; }
.animation-delay-700 { animation-delay: 700ms; }

/* Scroll Animations */
.animate-on-scroll {
  opacity: 0;
  transition: all 1s cubic-bezier(0.22, 1, 0.36, 1);
}
.animate-on-scroll.fade-in {
  transform: translateY(40px);
}
.animate-on-scroll.scale-up {
  transform: scale(0.95) translateY(20px);
}
.animate-on-scroll.is-visible {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.animate-bounce-slow {
  animation: bounce-slow 4s ease-in-out infinite;
}
@keyframes bounce-slow {
  0%, 100% { transform: translateY(-8%); }
  50% { transform: translateY(0); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}
@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0); }
  50% { transform: translateY(-20px) rotate(2deg); }
}

/* Custom transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
