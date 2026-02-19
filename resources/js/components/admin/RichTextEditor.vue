<template>
  <div class="rich-text-editor rounded-lg border border-[#e2e8f0] bg-white">
    <div v-if="editor" class="flex flex-wrap items-center gap-1 border-b border-[#e2e8f0] bg-[#f8fafc] px-2 py-1.5">
      <button
        type="button"
        class="rounded p-1.5 text-[#64748b] hover:bg-[#e2e8f0] hover:text-[#1a1a1a]"
        :class="{ 'bg-[#e2e8f0] text-[#1a1a1a]': editor.isActive('bold') }"
        @click="editor.chain().focus().toggleBold().run()"
        title="Bold"
      >
        <span class="font-bold text-sm">B</span>
      </button>
      <button
        type="button"
        class="rounded p-1.5 text-[#64748b] hover:bg-[#e2e8f0] hover:text-[#1a1a1a]"
        :class="{ 'bg-[#e2e8f0] text-[#1a1a1a]': editor.isActive('italic') }"
        @click="editor.chain().focus().toggleItalic().run()"
        title="Italic"
      >
        <span class="italic text-sm">I</span>
      </button>
      <button
        type="button"
        class="rounded p-1.5 text-[#64748b] hover:bg-[#e2e8f0] hover:text-[#1a1a1a]"
        :class="{ 'bg-[#e2e8f0] text-[#1a1a1a]': editor.isActive('link') }"
        @click="setLink"
        title="Link"
      >
        <span class="text-sm">Link</span>
      </button>
      <span class="mx-0.5 h-4 w-px bg-[#e2e8f0]"></span>
      <button
        type="button"
        class="rounded p-1.5 text-[#64748b] hover:bg-[#e2e8f0] hover:text-[#1a1a1a]"
        @click="triggerImageUpload"
        title="Upload image"
      >
        <span class="text-sm">Image</span>
      </button>
      <input
        ref="imageInputRef"
        type="file"
        accept="image/*"
        class="hidden"
        @change="onImageFileSelect"
      />
      <button
        type="button"
        class="rounded p-1.5 text-[#64748b] hover:bg-[#e2e8f0] hover:text-[#1a1a1a]"
        @click="insertYouTube"
        title="Embed YouTube video"
      >
        <span class="text-sm">YouTube</span>
      </button>
    </div>
    <EditorContent :editor="editor" class="prose-editor" />
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import { Node } from '@tiptap/core';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const YouTube = Node.create({
  name: 'youtube',
  group: 'block',
  atom: true,
  addAttributes() {
    return {
      src: { default: null },
      width: { default: 560 },
      height: { default: 315 },
    };
  },
  parseHTML() {
    return [
      {
        tag: 'div[data-youtube]',
        getAttrs: (dom) => ({ src: dom.getAttribute('data-src') }),
      },
      {
        tag: 'div.youtube-embed',
        getAttrs: (dom) => {
          const iframe = dom.querySelector('iframe');
          return iframe ? { src: iframe.getAttribute('src') } : false;
        },
      },
      {
        tag: 'iframe[src*="youtube"]',
        getAttrs: (dom) => ({ src: dom.getAttribute('src') }),
      },
    ];
  },
  renderHTML({ node }) {
    if (!node.attrs.src) return ['div', { class: 'youtube-embed' }];
    return [
      'div',
      { class: 'youtube-embed my-4', 'data-youtube': '' },
      [
        'iframe',
        {
          src: node.attrs.src,
          width: node.attrs.width,
          height: node.attrs.height,
          frameborder: 0,
          allowfullscreen: 'true',
          allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture',
        },
      ],
    ];
  },
});

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Write your content…' },
  uploadImageUrl: { type: String, default: '/api/admin/posts/upload' },
});

const emit = defineEmits(['update:modelValue']);

const imageInputRef = ref(null);

const editor = useEditor({
  content: props.modelValue || '',
  extensions: [
    StarterKit,
    Image,
    Link.configure({ openOnClick: false, HTMLAttributes: { target: '_blank', rel: 'noopener' } }),
    Placeholder.configure({ placeholder: props.placeholder }),
    YouTube,
  ],
  editorProps: {
    attributes: {
      class: 'min-h-[200px] max-h-[400px] overflow-y-auto px-3 py-2 text-[#1a1a1a] focus:outline-none',
    },
  },
  onUpdate: ({ editor: e }) => {
    emit('update:modelValue', e.getHTML());
  },
});

watch(
  () => props.modelValue,
  (val) => {
    if (editor.value && val !== editor.value.getHTML()) {
      editor.value.commands.setContent(val || '', false);
    }
  }
);

onBeforeUnmount(() => {
  editor.value?.destroy();
});

function setLink() {
  if (!editor.value) return;
  const previousUrl = editor.value.getAttributes('link').href;
  const url = window.prompt('URL', previousUrl || 'https://');
  if (url === null) return;
  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
    return;
  }
  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
}

function triggerImageUpload() {
  imageInputRef.value?.click();
}

function getYouTubeEmbedUrl(url) {
  if (!url || !url.trim()) return null;
  const trimmed = url.trim();
  const m = trimmed.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
  if (m) return `https://www.youtube.com/embed/${m[1]}`;
  if (trimmed.includes('youtube.com/embed/')) return trimmed;
  return null;
}

async function onImageFileSelect(ev) {
  const file = ev.target.files?.[0];
  if (!file || !editor.value) return;
  const form = new FormData();
  form.append('image', file);
  try {
    const { data } = await axios.post(props.uploadImageUrl, form, {
      headers: { 'Content-Type': 'multipart/form-data', 'X-Requested-With': 'XMLHttpRequest' },
    });
    const url = data.url;
    if (url) editor.value.chain().focus().setImage({ src: url }).run();
  } catch (e) {
    alert(e.response?.data?.message || 'Image upload failed');
  }
  ev.target.value = '';
}

function insertYouTube() {
  if (!editor.value) return;
  const url = window.prompt('Paste YouTube video URL (e.g. https://www.youtube.com/watch?v=...)', '');
  const embedUrl = getYouTubeEmbedUrl(url);
  if (!embedUrl) {
    if (url) alert('Invalid YouTube URL');
    return;
  }
  editor.value
    .chain()
    .focus()
    .insertContent({
      type: 'youtube',
      attrs: { src: embedUrl },
    })
    .run();
}
</script>

<style>
.rich-text-editor .ProseMirror p.is-editor-empty:first-child::before {
  color: #94a3b8;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}
.rich-text-editor .ProseMirror img {
  max-width: 100%;
  height: auto;
}
.rich-text-editor .youtube-embed iframe {
  max-width: 100%;
}
</style>
