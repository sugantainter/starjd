<template>
  <div class="min-h-screen bg-[#fafaf9] overflow-x-hidden pt-12">
    <!-- Hero Section -->
    <section class="relative px-4 pt-20 pb-24 lg:pt-32 lg:pb-40">
      <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] bg-[#fc4402]/5 rounded-full blur-[100px]"></div>
        <div class="absolute top-[20%] -left-[10%] w-[30%] h-[30%] bg-[#10b981]/5 rounded-full blur-[100px]"></div>
      </div>
      
      <div class="mx-auto max-w-7xl relative z-10 text-center">
        <span class="inline-block px-4 py-1.5 mb-6 text-xs font-bold uppercase tracking-wider text-[#fc4402] bg-[#fc4402]/10 rounded-full animate-fade-in-down">
          India's Leading Creator Marketplace
        </span>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-[#1a1a1a] leading-tight mb-8">
          How <span class="text-[#fc4402]">StarJD</span> Works
        </h1>
        <p class="mx-auto max-w-3xl text-lg md:text-xl text-[#4b5563] leading-relaxed mb-10">
          StarJD is a comprehensive creator marketplace and video production platform that bridges the gap between brands, influencers, and creative professionals.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
          <router-link to="/register" class="px-8 py-4 bg-[#fc4402] text-white rounded-2xl font-bold text-lg transition-all hover:bg-[#e63d02] hover:scale-105 shadow-xl shadow-[#fc4402]/20">
            Get Started for Free
          </router-link>
          <a href="#workflow" class="px-8 py-4 bg-white text-[#1a1a1a] border border-[#e5e7eb] rounded-2xl font-bold text-lg transition-all hover:bg-[#f9fafb] hover:border-[#fc4402]/30">
            See the Process
          </a>
        </div>
      </div>
      
      <div class="mt-16 mx-auto max-w-6xl relative animate-up">
        <div class="rounded-3xl overflow-hidden shadow-2xl border border-[#e5e7eb] transform -rotate-1 hover:rotate-0 transition-transform duration-700">
           <img src="/images/how-it-works/hero.png" alt="StarJD Platform" class="w-full h-auto object-cover" />
        </div>
        <div class="absolute -bottom-6 -right-6 lg:-right-12 bg-white p-6 rounded-2xl shadow-xl border border-[#e5e7eb] hidden md:block animate-bounce-slow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#10b981]/10 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-[#6b7280] uppercase">Verified Professionals</p>
                    <p class="text-xl font-bold text-[#1a1a1a]">5,000+</p>
                </div>
            </div>
        </div>
      </div>
    </section>

    <!-- The 5 Steps Workflow -->
    <section id="workflow" class="py-24 bg-white relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-[#1a1a1a] mb-4">Step-by-Step Guide</h2>
                <p class="text-lg text-[#6b7280]">Our streamlined process from signing up to project delivery.</p>
            </div>

            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-[#fc4402]/20 via-[#10b981]/20 to-transparent hidden lg:block"></div>

                <div class="space-y-24">
                    <!-- Step 1 -->
                    <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-24 group animate-on-scroll fade-in">
                        <div class="flex-1 text-right lg:order-1 order-2">
                             <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#fc4402] text-white text-2xl font-black mb-6 shadow-lg shadow-[#fc4402]/30 lg:hidden">1</div>
                             <h3 class="text-2xl font-bold text-[#1a1a1a] mb-4">1. Sign Up and Choose Your Role</h3>
                             <p class="text-[#64748b] text-lg leading-relaxed">
                                Create an account and select your role: Brand, Creator, Model, or Professional. This defines your experience and customizes your dashboard to match your specific needs.
                             </p>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-12 h-12 rounded-full bg-[#fc4402] text-white text-xl font-black shadow-lg shadow-[#fc4402]/30 z-20 order-2">1</div>
                        <div class="flex-1 order-3 lg:order-3">
                             <div class="bg-[#fafaf9] p-8 rounded-3xl border border-[#e5e7eb] shadow-sm transform group-hover:scale-105 transition-transform duration-500">
                                 <div class="grid grid-cols-2 gap-4">
                                     <div class="p-4 bg-white rounded-xl border border-[#e5e7eb] text-center">
                                         <span class="block text-[#fc4402] font-bold mb-1">Brand</span>
                                         <span class="text-xs text-[#6b7280]">Hire talent</span>
                                     </div>
                                     <div class="p-4 bg-white rounded-xl border border-[#e5e7eb] text-center">
                                         <span class="block text-[#10b981] font-bold mb-1">Creator</span>
                                         <span class="text-xs text-[#6b7280]">Monetize</span>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-24 group animate-on-scroll fade-in">
                        <div class="flex-1 order-2 lg:order-1">
                             <div class="bg-[#fafaf9] p-8 rounded-3xl border border-[#e5e7eb] shadow-sm transform group-hover:scale-105 transition-transform duration-500">
                                 <div class="flex items-center gap-4 mb-4">
                                     <div class="w-12 h-12 rounded-full bg-[#e5e7eb] animate-pulse"></div>
                                     <div class="flex-1 space-y-2">
                                         <div class="h-4 bg-[#e5e7eb] rounded w-3/4"></div>
                                         <div class="h-3 bg-[#e5e7eb] rounded w-1/2"></div>
                                     </div>
                                 </div>
                                 <div class="space-y-2">
                                     <div class="h-10 bg-white rounded-xl border border-[#e5e7eb] flex items-center px-4"><div class="h-2 bg-[#fc4402]/20 rounded w-full"></div></div>
                                     <div class="h-10 bg-white rounded-xl border border-[#e5e7eb] flex items-center px-4"><div class="h-2 bg-[#10b981]/20 rounded w-full"></div></div>
                                 </div>
                             </div>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-12 h-12 rounded-full bg-[#10b981] text-white text-xl font-black shadow-lg shadow-[#10b981]/30 z-20 order-2">2</div>
                        <div class="flex-1 lg:order-3 order-3">
                             <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#10b981] text-white text-2xl font-black mb-6 shadow-lg shadow-[#10b981]/30 lg:hidden">2</div>
                             <h3 class="text-2xl font-bold text-[#1a1a1a] mb-4">2. Build a High-Performance Profile</h3>
                             <p class="text-[#64748b] text-lg leading-relaxed">
                                Your profile is your digital identity. Add a professional bio, portfolio samples, skills, and pricing. A well-optimized profile increases your visibility on search engines by 10x.
                             </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-24 group animate-on-scroll fade-in">
                        <div class="flex-1 text-right lg:order-1 order-2">
                             <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#fc4402] text-white text-2xl font-black mb-6 shadow-lg shadow-[#fc4402]/30 lg:hidden">3</div>
                             <h3 class="text-2xl font-bold text-[#1a1a1a] mb-4">3. Discover Talent or Opportunities</h3>
                             <p class="text-[#64748b] text-lg leading-relaxed">
                                Brands can browse a curated list of professionals. Creators can explore available projects and receive direct inquiries. Our smart matching system connects you with the most relevant opportunities.
                             </p>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-12 h-12 rounded-full bg-[#fc4402] text-white text-xl font-black shadow-lg shadow-[#fc4402]/30 z-20 order-2">3</div>
                        <div class="flex-1 order-3 lg:order-3">
                             <div class="bg-[#fafaf9] p-8 rounded-3xl border border-[#e5e7eb] shadow-sm transform group-hover:scale-105 transition-transform duration-500">
                                 <div class="relative">
                                     <div class="h-40 bg-white rounded-2xl border border-[#e5e7eb] overflow-hidden">
                                         <div class="p-4 flex items-center gap-3 border-b border-[#e2e8f0]">
                                             <div class="w-8 h-8 rounded-full bg-[#fc4402]/10"></div>
                                             <div class="h-2 bg-[#fc4402]/20 rounded w-24"></div>
                                         </div>
                                         <div class="p-4 space-y-3">
                                             <div class="h-2 bg-[#e2e8f0] rounded w-full"></div>
                                             <div class="h-2 bg-[#e2e8f0] rounded w-3/4"></div>
                                             <div class="h-6 bg-[#fc4402] rounded-lg w-20 ml-auto"></div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-24 group animate-on-scroll fade-in">
                        <div class="flex-1 order-2 lg:order-1">
                             <div class="bg-[#fafaf9] p-8 rounded-3xl border border-[#e5e7eb] shadow-sm transform group-hover:scale-105 transition-transform duration-500">
                                 <div class="flex flex-col gap-2">
                                     <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-[#e5e7eb]">
                                         <span class="text-sm font-medium">Finalize Brief</span>
                                         <span class="w-4 h-4 rounded-full bg-[#10b981] flex items-center justify-center"><svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-1.414-1.414L8 11.172 4.707 7.879 3.293 9.293l4.707 4.707 8.707-8.707z"></path></svg></span>
                                     </div>
                                     <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-[#e5e7eb]">
                                         <span class="text-sm font-medium">Agree on Budget</span>
                                         <span class="w-4 h-4 rounded-full bg-[#10b981] flex items-center justify-center"><svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293l-1.414-1.414L8 11.172 4.707 7.879 3.293 9.293l4.707 4.707 8.707-8.707z"></path></svg></span>
                                     </div>
                                     <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-[#fc4402] border-dashed">
                                         <span class="text-sm font-medium">Production Phase</span>
                                         <span class="w-2 h-2 rounded-full bg-[#fc4402] animate-ping"></span>
                                     </div>
                                 </div>
                             </div>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-12 h-12 rounded-full bg-[#10b981] text-white text-xl font-black shadow-lg shadow-[#10b981]/30 z-20 order-2">4</div>
                        <div class="flex-1 lg:order-3 order-3">
                             <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#10b981] text-white text-2xl font-black mb-6 shadow-lg shadow-[#10b981]/30 lg:hidden">4</div>
                             <h3 class="text-2xl font-bold text-[#1a1a1a] mb-4">4. Collaborate and Execute Projects</h3>
                             <p class="text-[#64748b] text-lg leading-relaxed">
                                Use our unified platform to chat, finalize briefs, set budgets, and execute projects. Whether it's a social media reel or a full production commercial, our structured workflow keeps everyone aligned.
                             </p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-24 group animate-on-scroll fade-in">
                        <div class="flex-1 text-right lg:order-1 order-2">
                             <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#fc4402] text-white text-2xl font-black mb-6 shadow-lg shadow-[#fc4402]/30 lg:hidden">5</div>
                             <h3 class="text-2xl font-bold text-[#1a1a1a] mb-4">5. Deliver and Build Long-Term Partnerships</h3>
                             <p class="text-[#64748b] text-lg leading-relaxed">
                                Deliver final assets securely through the platform. Receive reviews and ratings to build your reputation. Positive feedback leads to more opportunities and higher earnings in the long run.
                             </p>
                        </div>
                        <div class="hidden lg:flex items-center justify-center w-12 h-12 rounded-full bg-[#fc4402] text-white text-xl font-black shadow-lg shadow-[#fc4402]/30 z-20 order-2">5</div>
                        <div class="flex-1 order-3 lg:order-3">
                             <div class="bg-gradient-to-br from-[#10b981]/10 to-[#fc4402]/10 p-1 rounded-3xl group-hover:scale-105 transition-transform duration-500">
                                 <div class="bg-white p-8 rounded-[22px] border border-white/50 text-center">
                                     <div class="flex justify-center mb-4">
                                         <div class="flex gap-1 text-yellow-400">
                                             <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                             <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                             <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                             <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                             <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                         </div>
                                     </div>
                                     <p class="font-bold text-[#1a1a1a]">Highly Recommended</p>
                                     <p class="text-sm text-[#6b7280]">"The best platform for creators!"</p>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Role Selection Section -->
    <section class="py-24 bg-[#fafaf9] relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="animate-on-scroll fade-in">
                    <h2 class="text-3xl md:text-5xl font-bold text-[#1a1a1a] mb-8 leading-tight">
                        Tailored Experience for <span class="text-[#fc4402]">Every Professional</span>
                    </h2>
                    <div class="space-y-6">
                        <div 
                            v-for="(role, index) in roles" 
                            :key="index"
                            @mouseenter="activeRole = index"
                            class="p-6 rounded-2xl border transition-all cursor-pointer"
                            :class="activeRole === index ? 'bg-white border-[#fc4402] shadow-xl translate-x-4' : 'bg-transparent border-[#e5e7eb] grayscale opacity-60'"
                        >
                            <div class="flex items-center gap-4">
                                <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold" :class="activeRole === index ? 'bg-[#fc4402] text-white' : 'bg-[#e5e7eb] text-[#6b7280]'">
                                    {{ index + 1 }}
                                </span>
                                <h3 class="text-xl font-bold" :class="activeRole === index ? 'text-[#1a1a1a]' : 'text-[#6b7280]'">{{ role.name }}</h3>
                            </div>
                            <p v-if="activeRole === index" class="mt-4 text-[#64748b] animate-fade-in">
                                {{ role.description }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="relative animate-on-scroll scale-up">
                    <div class="aspect-[4/5] rounded-[2rem] overflow-hidden shadow-2xl transition-all duration-700 bg-white border border-[#e5e7eb]">
                        <img 
                            :src="roles[activeRole].image" 
                            :alt="roles[activeRole].name" 
                            class="w-full h-full object-cover transition-opacity duration-300"
                        />
                        <div class="absolute inset-x-0 bottom-0 p-8 bg-gradient-to-t from-black/80 to-transparent">
                            <h4 class="text-white text-2xl font-bold">{{ roles[activeRole].name }} Benefit:</h4>
                            <p class="text-white/80 mt-2">{{ roles[activeRole].benefit }}</p>
                        </div>
                    </div>
                    <!-- Absolute Decors -->
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-[#fc4402]/10 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-[#10b981]/10 rounded-full blur-2xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose StarJD Section -->
    <section class="py-24 bg-white">
        <div class="mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-[#1a1a1a] mb-16 animate-on-scroll fade-in">Why Choose StarJD?</h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="(feature, idx) in features" :key="idx" class="p-8 rounded-[2rem] bg-[#fafaf9] border border-[#e5e7eb] hover:shadow-2xl hover:bg-white hover:-translate-y-2 transition-all duration-500 group animate-on-scroll scale-up" :style="{ transitionDelay: (idx * 100) + 'ms' }">
                    <div class="w-16 h-16 mx-auto mb-6 rounded-2xl flex items-center justify-center bg-white shadow-sm group-hover:bg-[#fc4402] transition-colors duration-500">
                        <svg class="w-8 h-8 group-hover:text-white transition-colors" :class="feature.color" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="feature.icon"></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#1a1a1a] mb-4">{{ feature.title }}</h3>
                    <p class="text-sm text-[#64748b] leading-relaxed">{{ feature.description }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-24 bg-white border-t border-[#e5e7eb]">
        <div class="mx-auto max-w-4xl px-4">
            <div class="text-center mb-16 animate-on-scroll fade-in">
                <h2 class="text-3xl md:text-5xl font-bold text-[#1a1a1a] mb-4">Frequently Asked Questions</h2>
                <p class="text-lg text-[#6b7280]">Common questions about getting started on StarJD.</p>
            </div>
            
            <div class="space-y-4 animate-on-scroll fade-in">
                <div v-for="(faq, i) in faqs" :key="i" class="border border-[#e5e7eb] rounded-2xl overflow-hidden bg-white hover:border-[#fc4402]/30 transition-colors">
                    <button 
                        @click="toggleFaq(i)"
                        class="w-full px-6 py-5 text-left flex items-center justify-between"
                    >
                        <span class="font-bold text-[#1a1a1a]">{{ faq.q }}</span>
                        <svg class="w-5 h-5 text-[#fc4402] transition-transform" :class="{ 'rotate-180': openFaq === i }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div v-show="openFaq === i" class="px-6 pb-6 text-[#64748b] animate-fade-in-down">
                        {{ faq.a }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 px-4 animate-on-scroll fade-in">
        <div class="mx-auto max-w-6xl rounded-[3rem] bg-gradient-to-br from-[#1a1a1a] to-[#2d2d2d] p-12 lg:p-24 text-center relative overflow-hidden">
            <!-- Background effects -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#fc4402]/20 rounded-full blur-[80px]"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#10b981]/10 rounded-full blur-[80px]"></div>
            
            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl lg:text-6xl font-black text-white mb-8">Ready to Scale Your Content?</h2>
                <p class="text-lg text-white/70 mb-12 max-w-2xl mx-auto">Join thousands of creators, brands, and professionals who use StarJD to revolutionize their production workflow.</p>
                <router-link to="/register" class="inline-block px-12 py-5 bg-[#fc4402] text-white rounded-[2rem] font-black text-xl hover:bg-white hover:text-[#fc4402] transition-all transform hover:scale-110 shadow-2xl hover:shadow-[#fc4402]/40">
                    Get Started Now
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
        name: "Brands & Businesses",
        description: "Post your project requirements, browse verified talent, and manage full-scale marketing campaigns in one place. Streamline your production and save costs.",
        benefit: "Access to verified talent & faster turnaround times",
        image: "https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2070&auto=format&fit=crop"
    },
    {
        name: "Influencers & Creators",
        description: "Monetize your audience, connect with premium brands, and access professional production services to grow your personal brand.",
        benefit: "Paid brand collaborations & growth tools",
        image: "https://images.unsplash.com/photo-1611162617474-5b21e879e113?q=80&w=1974&auto=format&fit=crop"
    },
    {
        name: "Models & Artists",
        description: "Showcase your portfolio, apply for casting calls, and get discovered by top agencies and photographers in the industry.",
        benefit: "Increased exposure & direct access to jobs",
        image: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=1974&auto=format&fit=crop"
    },
    {
        name: "Creative Professionals",
        description: "For editors, photographers, and writers. Find consistent freelance work and collaborate with India's top brands and creators.",
        benefit: "Regular freelance opportunities & network growth",
        image: "/images/how-it-works/collaboration.png"
    }
];

const features = [
    {
        title: "All-in-One Platform",
        description: "From talent discovery to final content delivery, we handle every stage of creation.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
        color: "text-[#fc4402]"
    },
    {
        title: "Verified Talent",
        description: "Access a network of trusted, vetted creative professionals across India.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-7.618 3.033A11.955 11.955 0 012.248 14.39a11.934 11.934 0 0010.133 7.596 11.934 11.934 0 0010.133-7.596 11.955 11.955 0 01-2.903-11.422z" />',
        color: "text-[#10b981]"
    },
    {
        title: "Efficient Workflow",
        description: "Direct communication and structured project flows reduce delays and miscommunication.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
        color: "text-blue-500"
    },
    {
        title: "Scalable Growth",
        description: "Ideal for startups, eCommerce brands, and large enterprises looking for scalable solutions.",
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />',
        color: "text-purple-500"
    }
];

const faqs = [
    { q: "How do I create a profile on StarJD?", a: "To create a profile on StarJD, simply sign up, choose your role (creator, brand, or professional), and fill in your details such as portfolio, skills, and experience. A complete profile helps you get discovered by brands and clients faster." },
    { q: "Can I update my profile and portfolio anytime?", a: "Yes, StarJD allows you to update your profile, portfolio, and services anytime. Regular updates improve your visibility and increase your chances of getting more projects and collaborations." },
    { q: "How does my profile help me get projects on StarJD?", a: "Your profile acts as your digital portfolio. Brands and clients review your work, skills, and experience before hiring. A well-optimized profile with quality content increases your chances of getting hired." },
    { q: "Is it free to create a profile on StarJD?", a: "Yes, creating a profile on StarJD is free. You can join the platform, showcase your work, and start connecting with brands, creators, and professionals without any upfront cost." }
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
@keyframes slide-right {
  0% { opacity: 0; transform: translateX(-50px); }
  100% { opacity: 1; transform: translateX(0); }
}
@keyframes slide-left {
  0% { opacity: 0; transform: translateX(50px); }
  100% { opacity: 1; transform: translateX(0); }
}
@keyframes up {
    0% { opacity: 0; transform: translateY(50px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down { animation: fade-in-down 0.8s ease-out forwards; }
.animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
.animate-slide-right { animation: slide-right 1s ease-out forwards; }
.animate-slide-left { animation: slide-left 1s ease-out forwards; }
.animate-up { animation: up 1s cubic-bezier(0.22, 1, 0.36, 1) forwards; }

/* Scroll Animations */
.animate-on-scroll {
  opacity: 0;
  transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}
.animate-on-scroll.fade-in {
  transform: translateY(30px);
}
.animate-on-scroll.scale-up {
  transform: scale(0.9);
}
.animate-on-scroll.is-visible {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.animate-bounce-slow {
  animation: bounce-slow 4s infinite;
}
@keyframes bounce-slow {
  0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
  50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
}
.glass {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}
</style>
