<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { BarChart, LineChart, PieChart } from 'lucide-vue-next'
import { ref } from 'vue'

// ✅ Chart.js + vue-chartjs imports
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  BarElement,
  CategoryScale,
  LinearScale,
  PointElement,
  ChartOptions
} from 'chart.js'
import { Line, Bar } from 'vue-chartjs'

// register needed chart components
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  BarElement,
  CategoryScale,
  LinearScale,
  PointElement
)

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Financial Management', href: '/financial-management' },
]

// Example data
const incomeData = [5000, 7000, 6000, 8000, 7500]
const expenseData = [3000, 4000, 3500, 4500, 4200]
const budgetProgress = 65
const dateRange = ref({ start: '2025-01-01', end: '2025-08-01' })

// ✅ Chart Data
const lineData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
  datasets: [
    {
      label: 'Income',
      data: incomeData,
      borderColor: '#22c55e',
      backgroundColor: 'rgba(34,197,94,0.2)',
      tension: 0.3,
      fill: true,
    },
  ],
}

const barData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
  datasets: [
    {
      label: 'Expenses',
      data: expenseData,
      backgroundColor: '#ef4444',
    },
  ],
}

// ✅ Chart Options (typed to avoid TS errors)
const lineOptions: ChartOptions<'line'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true },
    tooltip: {
      mode: 'index',
      intersect: false,
    },
  },
  scales: {
    x: { ticks: { color: '#888' }, grid: { display: false } },
    y: { beginAtZero: true, ticks: { color: '#888' }, grid: { display: true } },
  },
}

const barOptions: ChartOptions<'bar'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true },
    tooltip: {
      mode: 'index',
      intersect: false,
    },
  },
  scales: {
    x: { ticks: { color: '#888' }, grid: { display: false } },
    y: { beginAtZero: true, ticks: { color: '#888' }, grid: { display: true } },
  },
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Financial Reports" />  

    <div class="p-6 space-y-6">
      <!-- Page Title -->
      <div class="space-y-1">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Financial Reports</h1>
        <p class="text-gray-600 dark:text-gray-400">View and analyze all financial reports</p>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Income Summary (Line Chart) -->
        <Card class="rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition">
          <CardHeader>
            <CardTitle class="flex items-center gap-2 text-gray-900 dark:text-gray-100">
              <BarChart class="w-5 h-5 text-green-500 dark:text-green-400" /> Income Summary
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Monthly Income Trend</p>
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg h-40">
              <Line :data="lineData" :options="lineOptions" />
            </div>
          </CardContent>
        </Card>

        <!-- Expense Tracking (Bar Chart) -->
        <Card class="rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition">
          <CardHeader>
            <CardTitle class="flex items-center gap-2 text-gray-900 dark:text-gray-100">
              <LineChart class="w-5 h-5 text-red-500 dark:text-red-400" /> Expense Tracking
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Monthly Expenses Overview</p>
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg h-40">
              <Bar :data="barData" :options="barOptions" />
            </div>
          </CardContent>
        </Card>

        <!-- Budget Allocation -->
        <Card class="rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition">
          <CardHeader>
            <CardTitle class="flex items-center gap-2 text-gray-900 dark:text-gray-100">
              <PieChart class="w-5 h-5 text-blue-500 dark:text-blue-400" /> Budget Allocation
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Budget Utilization</p>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
              <div
                class="bg-blue-600 dark:bg-blue-500 h-4 rounded-full shadow-sm transition-all"
                :style="{ width: budgetProgress + '%' }"
              ></div>
            </div>
            <p class="text-sm mt-2 font-medium text-gray-900 dark:text-gray-200">
              {{ budgetProgress }}% used
            </p>
          </CardContent>
        </Card>
      </div>

      <!-- Date Range Selector -->
      <Card class="rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <CardHeader>
          <CardTitle class="text-gray-900 dark:text-gray-100">Custom Date Range</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex flex-wrap items-end gap-4">
            <!-- Start Date -->
            <div class="flex flex-col">
              <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Start Date</label>
              <input
                type="date"
                v-model="dateRange.start"
                class="px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <!-- End Date -->
            <div class="flex flex-col">
              <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">End Date</label>
              <input
                type="date"
                v-model="dateRange.end"
                class="px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <!-- Apply Button -->
            <button
              class="bg-blue-600 text-white px-5 py-2 rounded-xl shadow-sm
                     hover:bg-blue-700 dark:hover:bg-blue-500 transition focus:ring-2 focus:ring-blue-400"
            >
              Apply
            </button>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
