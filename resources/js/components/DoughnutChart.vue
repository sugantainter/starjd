<template>
  <div class="h-[250px] w-full relative">
    <Doughnut :data="chartData" :options="chartOptions" />
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
       <div class="text-center">
          <div class="text-[10px] font-bold uppercase tracking-wider text-[#94a3b8]">Gender</div>
          <div class="text-xs font-semibold text-[#1a1a1a]">Split</div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
  counts: {
    type: Object, // { male: 60, female: 40 }
    required: true
  }
});

const chartData = computed(() => {
  return {
    labels: ['Male', 'Female'],
    datasets: [
      {
        backgroundColor: ['#3b82f6', '#ec4899'],
        hoverBackgroundColor: ['#2563eb', '#db2777'],
        borderWidth: 0,
        data: [props.counts.male || 0, props.counts.female || 0],
        cutout: '70%'
      }
    ]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        usePointStyle: true,
        pointStyle: 'circle',
        padding: 20,
        font: { size: 11, weight: 'medium' }
      }
    },
    tooltip: {
      backgroundColor: '#1e293b',
      padding: 12,
      cornerRadius: 12,
      bodyFont: { size: 13, weight: 'bold' }
    }
  }
};
</script>
