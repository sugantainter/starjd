<template>
  <div class="h-[300px] w-full">
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Tooltip,
  Legend
} from 'chart.js';
import { Bar } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip, Legend);

const props = defineProps({
  dataRows: {
    type: Array, // [ ['age13-17', 20], ['age18-24', 40], ... ]
    required: true
  },
  color: {
    type: String,
    default: '#6366f1'
  }
});

const chartData = computed(() => {
  return {
    labels: props.dataRows.map(row => row[0].replace('age', '').replace('-', ' to ') + ' years'),
    datasets: [
      {
        label: 'Percentage (%)',
        backgroundColor: props.color,
        borderRadius: 8,
        data: props.dataRows.map(row => row[1]),
        barThickness: 24,
      }
    ]
  };
});

const chartOptions = {
  indexAxis: 'y', // Horizontal bars
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: '#1e293b',
      padding: 12,
      cornerRadius: 12,
      titleFont: { size: 12 },
      bodyFont: { size: 14, weight: 'bold' }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        font: { size: 11, color: '#64748b' },
        callback: (value) => value + '%'
      }
    },
    y: {
      grid: {
        display: false
      },
      ticks: {
        font: { size: 11, color: '#64748b' }
      }
    }
  }
};
</script>
