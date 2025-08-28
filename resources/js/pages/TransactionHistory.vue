<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Transaction History" />

    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Transaction History
          </h1>
          <p class="text-gray-500 dark:text-gray-400">
            View and track all financial transactions
          </p>
        </div>

        <!-- Export Buttons -->
        <div class="flex space-x-2">
          <button
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition focus:ring-2 focus:ring-blue-400"
          >
            Export CSV
          </button>
          <button
            class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white shadow-sm transition focus:ring-2 focus:ring-green-400"
          >
            Export PDF
          </button>
        </div>
      </div>

      <!-- Filters + Search -->
      <div
        class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 flex flex-wrap gap-4"
      >
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search transactions..."
          class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 
                 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex-1"
        />

        <select
          v-model="filterType"
          class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">All Types</option>
          <option value="payment">Payment</option>
          <option value="expense">Expense</option>
          <option value="membership">Membership</option>
        </select>

        <input
          type="date"
          v-model="filterDate"
          class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 
                 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Transactions Table -->
      <div class="overflow-x-auto rounded-2xl shadow border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-left">
          <thead>
            <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm uppercase">
              <th class="px-6 py-3">Date</th>
              <th class="px-6 py-3">Type</th>
              <th class="px-6 py-3">Description</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(tx, index) in filteredTransactions"
              :key="index"
              class="odd:bg-gray-50 even:bg-white dark:odd:bg-gray-900 dark:even:bg-gray-800
                     border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            >
              <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ tx.date }}</td>
              <td class="px-6 py-3 capitalize text-gray-900 dark:text-gray-100">{{ tx.type }}</td>
              <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ tx.description }}</td>
              <td class="px-6 py-3 font-semibold text-green-600 dark:text-green-400">
                ₱{{ tx.amount }}
              </td>
              <td class="px-6 py-3">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    tx.status === 'Completed' ? 'bg-green-500 text-white' :
                    tx.status === 'Pending' ? 'bg-yellow-500 text-white' : 
                    'bg-red-500 text-white'
                  ]"
                >
                  {{ tx.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-6 text-sm text-gray-600 dark:text-gray-400">
        <p>
          Showing {{ filteredTransactions.length }} of {{ transactions.length }} transactions
        </p>
        <div class="flex space-x-2">
          <button
            class="px-3 py-1 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
          >
            Prev
          </button>
          <button
            class="px-3 py-1 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>


<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue'

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


const searchQuery = ref("")
const filterType = ref("")
const filterDate = ref("")

// Sample Data
const transactions = ref([
  { date: "2025-08-01", type: "payment", description: "Membership Fee", amount: 500, status: "Completed" },
  { date: "2025-08-05", type: "expense", description: "Event Supplies", amount: 1200, status: "Completed" },
  { date: "2025-08-08", type: "payment", description: "Donation", amount: 800, status: "Pending" },
  { date: "2025-08-12", type: "membership", description: "New Member", amount: 450, status: "Completed" },
])

// Filtering Logic
const filteredTransactions = computed(() => {
  return transactions.value.filter((tx) => {
    const matchesSearch =
      tx.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      tx.type.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesType = filterType.value ? tx.type === filterType.value : true
    const matchesDate = filterDate.value ? tx.date === filterDate.value : true

    return matchesSearch && matchesType && matchesDate
  })
})
</script>
