<template>
  <img v-if="displaySrc" v-bind="$attrs" :src="displaySrc" :alt="alt" />
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

defineOptions({ inheritAttrs: false });

const props = defineProps({
  pathOrUrl: { type: String, default: '' },
  alt: { type: String, default: '' },
});

const displaySrc = ref('');
let abortCtrl = null;

function resolvableWithoutFetch(s) {
  if (!s) return false;
  return /^https?:\/\//i.test(s) || s.startsWith('//') || s.startsWith('blob:') || s.startsWith('data:');
}

watch(
  () => props.pathOrUrl,
  async (v) => {
    displaySrc.value = '';
    if (abortCtrl) {
      abortCtrl.abort();
      abortCtrl = null;
    }
    if (v == null || String(v).trim() === '') return;
    const t = String(v).trim();
    if (resolvableWithoutFetch(t)) {
      displaySrc.value = t.startsWith('//') ? `https:${t}` : t;
      return;
    }
    let path = t.replace(/^\/+/, '');
    while (path.startsWith('storage/')) {
      path = path.slice('storage/'.length);
    }
    abortCtrl = new AbortController();
    const signal = abortCtrl.signal;
    try {
      const { data } = await axios.get('/api/admin/storage-url', {
        params: { path },
        withCredentials: true,
        headers: { Accept: 'application/json' },
        signal,
      });
      if (!signal.aborted && data?.url) {
        displaySrc.value = data.url;
      }
    } catch (e) {
      if (!axios.isCancel(e) && e.code !== 'ERR_CANCELED' && e.name !== 'CanceledError') {
        displaySrc.value = '';
      }
    }
  },
  { immediate: true }
);

onBeforeUnmount(() => {
  if (abortCtrl) abortCtrl.abort();
});
</script>
