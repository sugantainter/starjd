<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#1a1a1a]">Post Campaign</h1>
      <p class="mt-1 text-[#64748b]">Create a new campaign and reach creators for your brand.</p>
    </div>
    <PostCampaignFlow @created="onCampaignCreated" />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import PostCampaignFlow from '../../components/brand/PostCampaignFlow.vue';

const router = useRouter();

function onCampaignCreated() {
  router.push({ name: 'brand-dashboard' });
}
</script>
