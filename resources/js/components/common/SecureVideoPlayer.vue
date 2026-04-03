<template>
  <div
    class="relative bg-black"
    :class="wrapperClass"
    @contextmenu.prevent
  >
    <video
      ref="videoEl"
      class="block w-full max-w-full bg-black"
      :class="videoClass"
      playsinline
      webkit-playsinline
      disablePictureInPicture
      disableRemotePlayback
      :controls="false"
      preload="metadata"
      @click="togglePlay"
      @loadedmetadata="onMeta"
      @timeupdate="onTime"
      @play="playing = true"
      @pause="playing = false"
      @ended="playing = false"
    >
      <source :src="src" />
    </video>
    <div
      class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent pt-10 pb-3 px-3 sm:px-4 flex flex-col gap-2"
      @click.stop
    >
      <input
        type="range"
        min="0"
        max="100"
        step="0.05"
        :value="progress"
        class="w-full h-1 accent-[#fc4402] cursor-pointer opacity-90 hover:opacity-100"
        @input="onSeek"
      />
      <div class="flex items-center gap-2 text-white text-[11px] sm:text-xs font-bold">
        <button
          type="button"
          class="shrink-0 rounded-lg bg-white/15 hover:bg-white/25 p-2 transition"
          :aria-label="playing ? 'Pause' : 'Play'"
          @click="togglePlay"
        >
          <svg v-if="!playing" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
          <svg v-else class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" /></svg>
        </button>
        <span class="tabular-nums text-white/90">{{ formatTime(current) }} / {{ formatTime(duration) }}</span>
        <button
          type="button"
          class="ml-auto shrink-0 rounded-lg bg-white/15 hover:bg-white/25 p-2 transition"
          :aria-label="muted ? 'Unmute' : 'Mute'"
          @click="toggleMute"
        >
          <svg v-if="muted" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" /></svg>
          <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
        </button>
        <button
          type="button"
          class="shrink-0 rounded-lg bg-white/15 hover:bg-white/25 px-2 py-2 text-[10px] font-black uppercase tracking-wider transition"
          @click="toggleFullscreen"
        >
          Full
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

defineProps({
  src: { type: String, required: true },
  wrapperClass: { type: String, default: '' },
  videoClass: { type: String, default: 'max-h-[80vh]' },
});

const videoEl = ref(null);
const playing = ref(false);
const muted = ref(false);
const current = ref(0);
const duration = ref(0);

const progress = computed(() => {
  if (!duration.value) return 0;
  return (current.value / duration.value) * 100;
});

function formatTime(sec) {
  if (!Number.isFinite(sec) || sec < 0) return '0:00';
  const m = Math.floor(sec / 60);
  const s = Math.floor(sec % 60);
  return `${m}:${s.toString().padStart(2, '0')}`;
}

function onMeta() {
  const v = videoEl.value;
  if (!v) return;
  duration.value = v.duration || 0;
}

function onTime() {
  const v = videoEl.value;
  if (!v) return;
  current.value = v.currentTime;
}

function togglePlay() {
  const v = videoEl.value;
  if (!v) return;
  if (v.paused) {
    v.play().catch(() => {});
  } else {
    v.pause();
  }
}

function toggleMute() {
  const v = videoEl.value;
  if (!v) return;
  v.muted = !v.muted;
  muted.value = v.muted;
}

function onSeek(e) {
  const v = videoEl.value;
  if (!v || !duration.value) return;
  v.currentTime = (Number(e.target.value) / 100) * duration.value;
}

function toggleFullscreen() {
  const v = videoEl.value;
  if (!v) return;
  if (document.fullscreenElement) {
    document.exitFullscreen().catch(() => {});
    return;
  }
  v.requestFullscreen?.().catch(() => {});
}
</script>
