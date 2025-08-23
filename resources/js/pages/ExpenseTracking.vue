<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'

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
  <AppLayout title="Expense Tracking">
    <div class="p-6 space-y-6">
      <!-- Page Title -->
      <h1 class="text-2xl font-bold text-gray-800">Expense Tracking</h1>

      <!-- Expense Entry Form -->
      <div class="bg-gray-800 p-6 rounded-2xl shadow-md space-y-4">
        <h2 class="text-lg font-semibold text-gray-500">Add Expense</h2>    
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-300">Category</label>
            <select class="mt-1 w-full p-2 border rounded-lg">
              <option disabled selected>Select a category</option>
              <option v-for="c in categories" :key="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300">Amount</label>
            <input type="number" class="mt-1 w-full p-2 border rounded-lg" placeholder="₱0.00" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300">Description</label>
            <input type="text" class="mt-1 w-full p-2 border rounded-lg" placeholder="Enter description" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300">Receipt Upload</label>
            <input type="file" class="mt-1 w-full p-2 border rounded-lg" />
          </div>
          <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
              Submit Expense
            </button>
          </div>
        </form>
      </div>

      <!-- Expense Table -->
      <div class="bg-white p-6 rounded-2xl shadow-md">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Submitted Expenses</h2>
        <table class="w-full border-collapse rounded-2xl overflow-hidden shadow-sm">
          <thead>
            <tr class="bg-gray-100 text-gray-700 text-left text-sm uppercase">
              <th class="px-6 py-3">Date</th>
              <th class="px-6 py-3">Category</th>
              <th class="px-6 py-3">Description</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in expenses" :key="e.id" class="border-b hover:bg-gray-50">
              <td class="px-6 py-4">{{ e.date }}</td>
              <td class="px-6 py-4">{{ e.category }}</td>
              <td class="px-6 py-4">{{ e.description }}</td>
              <td class="px-6 py-4 text-green-600 font-semibold">₱{{ e.amount }}</td>
              <td class="px-6 py-4">
                <span
                  v-if="e.status === 'Approved'"
                  class="px-3 py-1 text-xs font-semibold bg-green-500 text-white rounded-full"
                >
                  Approved
                </span>
                <span
                  v-else-if="e.status === 'Pending'"
                  class="px-3 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full"
                >
                  Pending
                </span>
                <span
                  v-else
                  class="px-3 py-1 text-xs font-semibold bg-red-500 text-white rounded-full"
                >
                  Rejected
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
