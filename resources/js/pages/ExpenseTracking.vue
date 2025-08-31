<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Financial Management', href: '/financial-management' },
]

const expenses = ref([
  {
    id: 1,
    date: '2025-08-10',
    category: 'Travel',
    description: 'Taxi fare to client meeting',
    amount: 250,
    status: 'Pending',
  },
  {
    id: 2,
    date: '2025-08-12',
    category: 'Office Supplies',
    description: 'Printer ink',
    amount: 1200,
    status: 'Approved',
  },
  {
    id: 3,
    date: '2025-08-14',
    category: 'Meals',
    description: 'Team lunch',
    amount: 800,
    status: 'Rejected',
  },
])

const categories = ['Travel', 'Meals', 'Office Supplies', 'Utilities', 'Other']
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Expense Tracking" />

    <div class="p-6 space-y-8">
      <!-- Page Title -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          Expense Tracking
        </h1>
        <p class="mt-1 text-gray-500 dark:text-gray-400">
          Track and monitor all expenses and financial activities
        </p>
      </div>

      <!-- Expense Entry Form -->
      <div class="rounded-2xl bg-white p-6 shadow-md dark:bg-gray-900/80 border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
          Add Expense
        </h2>

        <form class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <!-- Category -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Category
            </label>
            <select
              class="mt-1 w-full rounded-xl border border-gray-300 bg-white p-2.5 text-gray-800 shadow-sm
                     focus:ring-2 focus:ring-blue-500 focus:outline-none
                     dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
            >
              <option disabled selected>Select a category</option>
              <option v-for="c in categories" :key="c">{{ c }}</option>
            </select>
          </div>

          <!-- Amount -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Amount
            </label>
            <input
              type="number"
              placeholder="₱0.00"
              class="mt-1 w-full rounded-xl border border-gray-300 bg-white p-2.5 text-gray-800 shadow-sm
                     focus:ring-2 focus:ring-blue-500 focus:outline-none
                     dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
            />
          </div>

          <!-- Description -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Description
            </label>
            <input
              type="text"
              placeholder="Enter description"
              class="mt-1 w-full rounded-xl border border-gray-300 bg-white p-2.5 text-gray-800 shadow-sm
                     focus:ring-2 focus:ring-blue-500 focus:outline-none
                     dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
            />
          </div>

          <!-- Receipt Upload -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Receipt Upload
            </label>
            <input
              type="file"
              class="mt-1 w-full rounded-xl border border-gray-300 bg-white p-2.5 text-gray-800 shadow-sm file:cursor-pointer
                     focus:ring-2 focus:ring-blue-500 focus:outline-none
                     dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
            />
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end md:col-span-2">
            <button
              type="submit"
              class="rounded-xl bg-blue-600 px-6 py-2.5 text-white font-medium shadow hover:bg-blue-700
                     focus:ring-2 focus:ring-blue-500 transition"
            >
              Submit Expense
            </button>
          </div>
        </form>
      </div>

      <!-- Expense Table -->
      <div class="rounded-2xl bg-white p-6 shadow-md dark:bg-gray-900/80 border border-gray-200 dark:border-gray-700">
        <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">
          Submitted Expenses
        </h2>
        <div class="overflow-x-auto rounded-2xl">
          <table class="w-full border-collapse text-sm">
            <!-- Table Header -->
            <thead>
              <tr class="bg-gray-100 text-gray-700 uppercase dark:bg-gray-800 dark:text-gray-300">
                <th class="px-6 py-3 text-left">Date</th>
                <th class="px-6 py-3 text-left">Category</th>
                <th class="px-6 py-3 text-left">Description</th>
                <th class="px-6 py-3 text-left">Amount</th>
                <th class="px-6 py-3 text-left">Status</th>
              </tr>
            </thead>

            <!-- Table Body -->
            <tbody>
              <tr
                v-for="e in expenses"
                :key="e.id"
                class="border-b border-gray-200 even:bg-gray-50
                       dark:border-gray-700 dark:even:bg-gray-800/40
                       hover:bg-gray-50 dark:hover:bg-gray-800/70 transition"
              >
                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ e.date }}</td>
                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ e.category }}</td>
                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ e.description }}</td>
                <td class="px-6 py-4 font-semibold text-green-600 dark:text-green-400">₱{{ e.amount }}</td>
                <td class="px-6 py-4">
                  <!-- Status Badges -->
                  <span
                    v-if="e.status === 'Approved'"
                    class="inline-flex items-center rounded-full bg-green-500/90 px-3 py-1 text-xs font-semibold text-white shadow-sm"
                  >Approved</span>
                  <span
                    v-else-if="e.status === 'Pending'"
                    class="inline-flex items-center rounded-full bg-yellow-500/90 px-3 py-1 text-xs font-semibold text-white shadow-sm"
                  >Pending</span>
                  <span
                    v-else
                    class="inline-flex items-center rounded-full bg-red-500/90 px-3 py-1 text-xs font-semibold text-white shadow-sm"
                  >Rejected</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
