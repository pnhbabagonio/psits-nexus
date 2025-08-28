<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';
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

    <div class="p-6 space-y-6">
      <!-- Page Title -->
      <div>
        <h1 class="text-2xl font-semibold">Expense Tracking</h1>
        <p class="text-gray-400">Track and monitor all expenses and financial activities</p>
      </div>

      <!-- Expense Entry Form -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
          Add Expense
        </h2>    

        <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Category -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Category
            </label>
            <select
              class="mt-1 w-full p-2 rounded-lg border border-gray-300 dark:border-gray-600 
                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                     focus:ring-2 focus:ring-blue-500 focus:outline-none"
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
              class="mt-1 w-full p-2 rounded-lg border border-gray-300 dark:border-gray-600
                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                     focus:ring-2 focus:ring-blue-500 focus:outline-none"
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
              class="mt-1 w-full p-2 rounded-lg border border-gray-300 dark:border-gray-600
                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                     focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
          </div>

          <!-- Receipt Upload -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Receipt Upload
            </label>
            <input
              type="file"
              class="mt-1 w-full p-2 rounded-lg border border-gray-300 dark:border-gray-600
                     bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                     focus:ring-2 focus:ring-blue-500 focus:outline-none file:cursor-pointer"
            />
          </div>

          <!-- Submit Button -->
          <div class="md:col-span-2 flex justify-end">
            <button
              type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow
                     transition duration-200"
            >
              Submit Expense
            </button>
          </div>
        </form>
      </div>

      <!-- Expense Table -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
  <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
    Submitted Expenses
  </h2>
  <div class="overflow-x-auto rounded-2xl">
    <table class="w-full border-collapse">
      <!-- Table Header -->
      <thead>
        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-left text-sm uppercase">
          <th class="px-6 py-3">Date</th>
          <th class="px-6 py-3">Category</th>
          <th class="px-6 py-3">Description</th>
          <th class="px-6 py-3">Amount</th>
          <th class="px-6 py-3">Status</th>
        </tr>
      </thead>

      <!-- Table Body -->
      <tbody>
        <tr
          v-for="e in expenses"
          :key="e.id"
          class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
        >
          <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ e.date }}</td>
          <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ e.category }}</td>
          <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ e.description }}</td>
          <td class="px-6 py-4 text-green-600 dark:text-green-400 font-semibold">₱{{ e.amount }}</td>
          <td class="px-6 py-4">
            <!-- Status Badges -->
            <span
              v-if="e.status === 'Approved'"
              class="px-3 py-1 text-xs font-semibold bg-green-500 text-white rounded-full shadow-sm"
            >
              Approved
            </span>
            <span
              v-else-if="e.status === 'Pending'"
              class="px-3 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full shadow-sm"
            >
              Pending
            </span>
            <span
              v-else
              class="px-3 py-1 text-xs font-semibold bg-red-500 text-white rounded-full shadow-sm"
            >
              Rejected
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
</div>
  </AppLayout>
</template>
