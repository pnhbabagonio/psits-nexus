<template>
  <AppLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Transaction History</h1>
        
        <!-- Export Buttons -->
        <div class="flex space-x-2">
          <button class="px-4 py-2 bg-blue-600 rounded-xl hover:bg-blue-700 text-white">Export CSV</button>
          <button class="px-4 py-2 bg-green-600 rounded-xl hover:bg-green-700 text-white">Export PDF</button>
        </div>
      </div>

      <!-- Filters + Search -->
      <div class="flex flex-wrap gap-4 mb-6">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search transactions..."
          class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1"
        />

        <select v-model="filterType" class="px-4 py-2 rounded-xl border border-gray-300">
          <option value="">All Types</option>
          <option value="payment">Payment</option>
          <option value="expense">Expense</option>
          <option value="membership">Membership</option>
        </select>

        <input
          type="date"
          v-model="filterDate"
          class="px-4 py-2 rounded-xl border border-gray-300"
        />
      </div>

      <!-- Transactions Table -->
      <div class="overflow-x-auto rounded-2xl shadow">
        <table class="min-w-full text-left">
          <thead>
            <tr class="bg-gray-800 text-gray-300">  
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
              class="border-b border-gray-300 hover:bg-gray-700 transition text-white"
>

              <td class="px-6 py-3">{{ tx.date }}</td>
              <td class="px-6 py-3 capitalize">{{ tx.type }}</td>
              <td class="px-6 py-3">{{ tx.description }}</td>
              <td class="px-6 py-3 font-semibold text-green-700">₱{{ tx.amount }}</td>
              <td class="px-6 py-3">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-sm text-gray',
                    tx.status === 'Completed' ? 'bg-green-500' :
                    tx.status === 'Pending' ? 'bg-yellow-500' : 'bg-red-500'
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
      <div class="flex justify-between items-center mt-6 text-sm text-gray-600">
        <p>
          Showing {{ filteredTransactions.length }} of {{ transactions.length }} transactions
        </p>
        <div class="flex space-x-2">
          <button class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Prev</button>
          <button class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'

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
