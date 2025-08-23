<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { BarChart, LineChart, PieChart } from 'lucide-vue-next'
import { ref } from 'vue'

// Example reactive data (replace later with backend props)
const incomeData = [5000, 7000, 6000, 8000, 7500]
const expenseData = [3000, 4000, 3500, 4500, 4200]
const budgetProgress = 65
const dateRange = ref({ start: '2025-01-01', end: '2025-08-01' })
</script>

<template>
  <AppLayout title="Financial Reports">
    <div class="p-6 space-y-6">
      <!-- Page Title -->
      <h1 class="text-2xl font-semibold">Financial Reports</h1>

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
      <Card>
        <CardHeader>
          <CardTitle>Custom Date Range</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex items-center gap-4">
            <div>
              <label class="text-sm font-medium">Start Date</label>
              <input type="date" v-model="dateRange.start" class="border rounded-lg px-2 py-1" />
            </div>
            <div>
              <label class="text-sm font-medium">End Date</label>
              <input type="date" v-model="dateRange.end" class="border rounded-lg px-2 py-1" />
            </div>
            <button
              class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
            >
              Apply
            </button>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>