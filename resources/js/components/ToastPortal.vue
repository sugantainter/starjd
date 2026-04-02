<template>
  <div class="fixed right-0 top-0 z-[100] flex w-full max-w-sm flex-col gap-3 p-6 pointer-events-none">
    <TransitionGroup 
      name="toast" 
      enter-active-class="transition duration-300 ease-out" 
      enter-from-class="translate-x-full opacity-0 scale-90" 
      enter-to-class="translate-x-0 opacity-100 scale-100" 
      leave-active-class="transition duration-300 ease-in" 
      leave-from-class="translate-x-0 opacity-100 scale-100" 
      leave-to-class="translate-x-full opacity-0 scale-90"
    >
      <div 
        v-for="n in notifications" 
        :key="n.id" 
        class="pointer-events-auto relative overflow-hidden rounded-2xl border shadow-2xl backdrop-blur-md"
        :class="{
          'bg-white/90 border-slate-100': n.type === 'success' || n.type === 'info',
          'bg-red-50/90 border-red-100': n.type === 'error'
        }"
      >
        <div class="flex items-center gap-4 p-4 pr-10">
          <div 
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
            :class="{
              'bg-[#10b981]/10 text-[#10b981]': n.type === 'success',
              'bg-blue-100 text-blue-600': n.type === 'info',
              'bg-red-100 text-red-600': n.type === 'error'
            }"
          >
            <svg v-if="n.type === 'success'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-if="n.type === 'info'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-if="n.type === 'error'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
             <p class="text-sm font-semibold text-[#0f172a] capitalize">{{ n.type }}</p>
             <p class="mt-0.5 text-sm text-slate-500 leading-tight">{{ n.message }}</p>
          </div>
          <button @click="remove(n.id)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <!-- Progress bar -->
        <div class="absolute bottom-0 left-0 h-1 bg-current opacity-20 transition-all duration-[4000ms] w-0 animate-progress" :class="{'text-[#10b981]': n.type === 'success', 'text-blue-600': n.type === 'info', 'text-red-600': n.type === 'error'}"></div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { globalNotifications as notifications, useNotify } from '../lib/notify';
const { remove } = useNotify();
</script>

<style scoped>
@keyframes progress {
  from { width: 100%; }
  to { width: 0%; }
}
.animate-progress {
  animation: progress 4s linear forwards;
}
</style>
