<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Progress } from '@/components/ui/progress'
import { BarChart } from "lucide-vue-next"
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Financial Management', href: '/financial-management' },
]

const budgetProgress = ref(65)
const dateRange = ref({ start: '2025-01-01', end: '2025-08-01' })

// Sample data for charts
const studentData = [
  { name: "Jan", payment: 2200 },
  { name: "Feb", payment: 1800 },
  { name: "Mar", payment: 2000 },
  { name: "Apr", payment: 2500 },
  { name: "May", payment: 1900 }
]

// Largest payment value
const maxValue = Math.max(...studentData.map(m => m.payment))

// Steps for Y axis (every 500 pesos)
const stepSize = 500
const ySteps = Array.from(
  { length: Math.ceil(maxValue / stepSize) + 1 },
  (_, i) => maxValue - i * stepSize
)

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Financial Reports" />

    <div class="p-6 space-y-6">
      <!-- Page Title -->
      <div class="space-y-1">
        <h1 class="text-2xl font-bold text-foreground">Financial Reports</h1>
        <p class="text-muted-foreground">View and analyze all financial reports</p>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Student Payments -->
<Card class="rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition">
  <CardHeader>
    <CardTitle class="flex items-center gap-2 text-gray-900 dark:text-gray-100">
      <BarChart class="w-5 h-5 text-green-500 dark:text-green-400" /> Student Payments
    </CardTitle>
  </CardHeader>

  <CardContent>
    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
      Total amount collected per month
    </p>

    <div class="relative w-full h-64 grid grid-cols-[40px_1fr]">
      <!-- Y Axis -->
      <div class="flex flex-col justify-between text-xs text-gray-500 dark:text-gray-400 pr-2">
        <span v-for="step in ySteps" :key="step">
          ₱{{ step.toLocaleString() }}
        </span>
      </div>

      <!-- Bars -->
      <div class="relative flex items-end space-x-6">
        <div
          v-for="month in studentData"
          :key="month.name"
          class="flex-1 flex flex-col items-center"
        >
          <!-- Bar -->
          <div
            class="bg-green-500 dark:bg-green-400 w-8 rounded shadow-sm transition-all"
            :style="{ height: ((month.payment / maxValue) * 100) + '%' }"
          ></div>

          <!-- Month Label -->
          <span class="text-xs mt-2 text-gray-600 dark:text-gray-400 font-medium">
            {{ month.name }}
          </span>
        </div>
      </div>
    </div>
  </CardContent>
</Card>


        <!-- Expense Tracking -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <LineChart class="w-5 h-5 text-destructive" /> Expense Tracking
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-muted-foreground mb-2">Monthly Expenses Overview</p>
            <div class="rounded-lg h-40 bg-muted flex items-center justify-center text-muted-foreground">
              Bar Chart Placeholder
            </div>
          </CardContent>
        </Card>

        <!-- Budget Allocation -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <PieChart class="w-5 h-5 text-primary" /> Budget Allocation
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-muted-foreground mb-2">Budget Utilization</p>
            <Progress :model-value="budgetProgress" class="h-4" />
            <p class="text-sm mt-2 font-medium text-foreground">
              {{ budgetProgress }}% used
            </p>
          </CardContent>
        </Card>
      </div>

      <!-- Date Range Selector -->
      <Card>
        <CardHeader>
          <CardTitle>Custom Date Range</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex flex-wrap items-end gap-4">
            <!-- Start Date -->
            <div class="flex flex-col space-y-1">
              <Label for="start">Start Date</Label>
              <Input id="start" type="date" v-model="dateRange.start" />
            </div>

            <!-- End Date -->
            <div class="flex flex-col space-y-1">
              <Label for="end">End Date</Label>
              <Input id="end" type="date" v-model="dateRange.end" />
            </div>

            <!-- Apply Button -->
            <Button>Apply</Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
