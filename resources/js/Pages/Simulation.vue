<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">データシミュレーション</h1>

    <!-- Spreadsheet -->
    <div ref="hotElement" />

    <!-- Chart -->
    <div class="mt-8 w-full max-w-4xl h-[1000px] mx-auto">
        <canvas ref="chartCanvas" class="w-full h-full"></canvas>
    </div>

    <div class="mt-4 flex gap-2">
      <button @click="downloadExcel" class="px-4 py-2 bg-green-600 text-white rounded">
        ダウンロード
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Handsontable from 'handsontable'
import 'handsontable/dist/handsontable.full.min.css'
import axios from 'axios'
import {
  Chart,
  LineController,
  LineElement,
  PointElement,
  LinearScale,
  Title,
  CategoryScale,
  Legend
} from 'chart.js'

Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend)

const hotElement = ref(null)
const hotInstance = ref(null)
const chartCanvas = ref(null)
let chartInstance = null

onMounted(() => {
  hotInstance.value = new Handsontable(hotElement.value, {
    data: [
      ['1月', 10000, 12000],
      ['2月', 15000, 16000],
      ['3月', 15000, 16000],
      ['4月', 15000, 16000],
      ['5月', 15000, 16000],
      ['6月', 15000, 16000],
      ['7月', 15000, 16000],
      ['8月', 15000, 16000],
      ['9月', 15000, 16000],
      ['10月', 15000, 16000],
      ['11月', 15000, 16000],
      ['12月', 17000, 18000]
    ],
    colHeaders: ['月', 'プランA', 'プランB'],
    rowHeaders: true,
    licenseKey: 'non-commercial-and-evaluation',
    width: '100%',
    height: 'auto',
    stretchH: 'all',

    afterChange: (changes, source) => {
      if (source !== 'loadData') {
        drawChart()
      }
    }
  })

  drawChart()
})

const drawChart = () => {
  const rawData = hotInstance.value.getData()
  const labels = rawData.map(row => row[0])
  const planA = rawData.map(row => Number(row[1]) || 0)
  const planB = rawData.map(row => Number(row[2]) || 0)

  if (chartInstance) {
    chartInstance.destroy()
  }

  chartInstance = new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'プランA',
          data: planA,
          borderColor: 'blue',
          backgroundColor: 'rgba(0, 0, 255, 0.1)',
          tension: 0.3,
        },
        {
          label: 'プランB',
          data: planB,
          borderColor: 'green',
          backgroundColor: 'rgba(0, 255, 0, 0.1)',
          tension: 0.3,
        }
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top' },
        title: {
          display: true,
          text: '貯蓄プランの比較',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => `¥${value.toLocaleString()}`,
          }
        }
      }
    }
  })
}

const downloadExcel = async () => {
  const headers = ['月', 'プランA', 'プランB']
  const rawData = hotInstance.value.getData()
  const finalData = [headers, ...rawData]

  try {
    const response = await axios.post('/export', { data: finalData }, { responseType: 'blob' })
    const blob = new Blob([response.data], { type: response.headers['content-type'] })
    const link = document.createElement('a')
    link.href = URL.createObjectURL(blob)
    link.download = 'simulation_with_chart.xlsx'
    link.click()
    URL.revokeObjectURL(link.href)
  } catch (err) {
    console.error('Export failed:', err)
    alert('Failed to export Excel.')
  }
}
</script>
