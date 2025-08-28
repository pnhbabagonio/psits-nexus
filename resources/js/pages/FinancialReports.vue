<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { BarChart, LineChart, PieChart } from 'lucide-vue-next'
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    // NAVBAR TITLE
    {
        title: 'Financial Management',
        href: '/financial-management',
    },
];

// Example reactive data (replace later with backend props)
const incomeData = [5000, 7000, 6000, 8000, 7500]
const expenseData = [3000, 4000, 3500, 4500, 4200]
const budgetProgress = 65
const dateRange = ref({ start: '2025-01-01', end: '2025-08-01' })
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Financial Reports" />

    <div class="p-6 space-y-6">
      <!-- Page Title -->
      <h1 class="text-2xl font-semibold">Financial Reports</h1>
      <!-- Add your financial reports content here -->

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Income Summary -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <BarChart class="w-5 h-5 text-green-600" /> Income Summary
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-500 text-sm mb-2">Monthly Income Trend</p>
            <!-- Placeholder for chart -->
            <div class="bg-gray-100 rounded-lg h-40 flex items-center justify-center text-gray-400">
              [Income Chart]
            </div>
          </CardContent>
        </Card>

        <!-- Expense Tracking -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <LineChart class="w-5 h-5 text-red-600" /> Expense Tracking
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-500 text-sm mb-2">Monthly Expenses Overview</p>
            <div class="bg-gray-100 rounded-lg h-40 flex items-center justify-center text-gray-400">
              [Expense Chart]
            </div>
          </CardContent>
        </Card>

        <!-- Budget Progress -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <PieChart class="w-5 h-5 text-blue-600" /> Budget Allocation
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-gray-500 text-sm mb-2">Budget Utilization</p>
            <div class="w-full bg-gray-200 rounded-full h-4">
              <div
                class="bg-blue-600 h-4 rounded-full"
                :style="{ width: budgetProgress + '%' }"
              ></div>
            </div>
            <p class="text-sm mt-2 font-medium">{{ budgetProgress }}% used</p>
          </CardContent>
        </Card>
      </div>

      <!-- Date Range Selector -->
<Card class="rounded-2xl shadow-md">
  <CardHeader>
    <CardTitle class="text-gray-800 dark:text-gray-200">Custom Date Range</CardTitle>
  </CardHeader>
  <CardContent>
    <div class="flex flex-wrap items-end gap-4">
      <!-- Start Date -->
      <div class="flex flex-col">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
          Start Date
        </label>
        <input
          type="date"
          v-model="dateRange.start"
          class="px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200
                 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- End Date -->
      <div class="flex flex-col">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
          End Date
        </label>
        <input
          type="date"
          v-model="dateRange.end"
          class="px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200
                 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Apply Button -->
      <button
        class="bg-blue-600 text-white px-5 py-2 rounded-xl shadow-sm
               hover:bg-blue-700 dark:hover:bg-blue-500 transition"
      >
        Apply
      </button>
    </div>
  </CardContent>
</Card>
    </div>
  </AppLayout>
</template>