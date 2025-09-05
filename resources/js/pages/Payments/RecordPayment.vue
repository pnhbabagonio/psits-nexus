<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Record Payment', href: '/payments/record' },
];

const form = useForm({
  date: new Date().toISOString().substr(0, 10), // auto today
  type: 'Membership', // default value
  description: '',
  amount: 0,
});

const submitPayment = () => {
  form.post('/payments/record', {
    onSuccess: () => {
      alert('Payment recorded successfully!');
      form.reset();
      form.date = new Date().toISOString().substr(0, 10);
      form.type = 'Membership'; // reset to default
    },
  });
};
</script>

<template>
  <Head title="Record Payment" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-md mx-auto mt-10 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
      <h2 class="text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
        Record Payment
      </h2>

      <form @submit.prevent="submitPayment" class="space-y-5">
        <!-- Category -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
          <select
            v-model="form.type"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
          >
            <option>Membership</option>
            <option>Event Fee</option>
            <option>Merchandise</option>
            <option>Penalties</option>
          </select>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
          <input
            v-model="form.description"
            type="text"
            placeholder="Payment description"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
            required
          />
        </div>

        <!-- Amount -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
            <input
            v-model.number="form.amount"
            type="number"
            placeholder="₱0.00"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition no-spinner"
             required
            />
        </div>

        <!-- Hidden date field -->
        <input type="hidden" v-model="form.date" />

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md transition"
        >
          Record Payment
        </button>
      </form>
    </div>
  </AppLayout>
</template>
