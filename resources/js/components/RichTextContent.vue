<template>
  <div
    class="rich-text-content"
    v-html="decodedContent"
  ></div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  content: {
    type: String,
    default: ''
  }
});

/**
 * Decodes HTML entities and handles plain text conversion to HTML if needed.
 */
const decodedContent = computed(() => {
  if (!props.content) return '';

  // 1. Decode entities first (e.g. &lt;p&gt; -> <p>)
  // This handles cases where the content might have been double-encoded.
  const decoded = decodeEntities(String(props.content).trim());

  if (!decoded) return '';

  // 2. If it doesn't look like HTML, treat it as plain text and add basic formatting
  if (!decoded.startsWith('<')) {
    return decoded
      .split(/\r?\n\r?\n+/)
      .map((p) => '<p>' + p.replace(/\r?\n/g, '<br/>') + '</p>')
      .join('');
  }

  return decoded;
});

/**
 * Helper to decode HTML entities using the browser's DOM.
 * @param {string} str 
 */
function decodeEntities(str) {
  if (!str || !str.includes('&')) return str;
  const txt = document.createElement('textarea');
  txt.innerHTML = str;
  return txt.value;
}
</script>

<style>
/* 
  Styles are defined globally in app.css under .rich-text-content 
  to ensure they apply to the v-html content.
*/
</style>
