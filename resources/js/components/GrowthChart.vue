<template>
  <div class="h-[300px] w-full">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

const props = defineProps({
  history: {
    type: Array,
    required: true
  },
  metricName: {
    type: String,
    default: 'Views'
  },
  metricIndex: {
    type: Number,
    default: 3 // 0:date, 1:gained, 2:lost, 3:views, 4:likes
  },
  color: {
    type: String,
    default: '#10b981'
  }
});

const chartData = computed(() => {
  return {
    labels: props.history.map(row => {
      const date = new Date(row[0]);
      return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
    }),
    datasets: [
      {
        label: props.metricName,
        backgroundColor: props.color + '20',
        borderColor: props.color,
        borderWidth: 2,
        pointRadius: 3,
        pointBackgroundColor: props.color,
        fill: true,
        data: props.history.map(row => row[props.metricIndex]),
        tension: 0.4
      }
    ]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      mode: 'index',
      intersect: false,
      backgroundColor: '#1e293b',
      padding: 12,
      cornerRadius: 12,
      titleFont: { size: 12, weight: 'bold' },
      bodyFont: { size: 14 }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        maxRotation: 0,
        autoSkip: true,
        maxTicksLimit: 7,
        font: { size: 11, color: '#64748b' }
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: '#f1f5f9'
      },
      ticks: {
        font: { size: 11, color: '#64748b' }
      }
    }
  }
};
</script>
