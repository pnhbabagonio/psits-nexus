<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';
import { ref } from 'vue';
import { Wallet } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';

// Selected payment method
const paymentMethod = ref<'gcash' | 'paymaya' | null>(null);
const amount = ref(0);

// QR strings
const gcashQR = '00020101021126650014com.gcash0111+639171234567...';
const paymayaQR = '00020101021126660014com.paymaya0111+639191234567...';

// Payment methods data
const methods = [
  { id: 'gcash', name: 'GCash', color: 'bg-blue-500', hover: 'hover:bg-blue-600', description: 'Scan to pay via GCash' },
  { id: 'paymaya', name: 'PayMaya', color: 'bg-green-500', hover: 'hover:bg-green-600', description: 'Scan to pay via PayMaya' },
];

// Form for submitting payment
const form = useForm({
  date: new Date().toISOString().substr(0, 10),
  type: '',
  description: '',
  amount: 0,
});

// Function to record payment
const recordPayment = () => {
  if (!paymentMethod.value) return alert('Please select a payment method');
  if (!amount.value || amount.value <= 0) return alert('Enter a valid amount');

  form.type = paymentMethod.value === 'gcash' ? 'GCash' : 'PayMaya';
  form.amount = amount.value;
  form.description = `Payment via ${form.type}`;

  form.post('/payments/record', {
    onSuccess: () => {
      alert('Payment recorded successfully!');
      amount.value = 0;
      paymentMethod.value = null;
      form.reset();
    },
  });
};
</script>

<template>

  <Head title="Payment QR Codes" />
  <AppLayout>
    <div class="p-6 space-y-8">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Choose Payment Method</h1>
        <p class="text-muted-foreground">Scan the QR code and record your payment.</p>
      </div>

      <!-- Payment Options -->
      <div class="grid gap-6 sm:grid-cols-2">
        <div v-for="method in methods" :key="method.id"
          class="cursor-pointer rounded-2xl border border-border p-6 transition-all shadow-sm hover:shadow-md"
          :class="paymentMethod === method.id ? method.color + ' text-white' : 'bg-card'"
          @click="paymentMethod = method.id as 'gcash' | 'paymaya'">
          <div class="flex items-center gap-3">
            <Wallet class="h-6 w-6" />
            <h2 class="text-lg font-semibold">{{ method.name }}</h2>
          </div>
          <p class="mt-2 text-sm opacity-80">{{ method.description }}</p>
        </div>
      </div>

      <!-- QR Code Display + Amount -->
      <div v-if="paymentMethod" class="rounded-2xl border border-border bg-card p-8 text-center shadow-md space-y-4">
        <h2 class="text-xl font-semibold mb-4">
          {{ paymentMethod === 'gcash' ? 'GCash QR Code' : 'PayMaya QR Code' }}
        </h2>

        <QrcodeVue :value="paymentMethod === 'gcash' ? gcashQR : paymayaQR" :size="180" class="mx-auto" />

        <p class="mt-2 text-sm text-muted-foreground">
          Scan this code with your {{ paymentMethod === 'gcash' ? 'GCash' : 'PayMaya' }} app to complete the payment.
        </p>

        <!-- Amount Input -->
        <div class="mt-4 flex justify-center gap-2">
          <input type="number" v-model.number="amount" placeholder="Enter amount"
            class="rounded-xl border border-gray-300 px-4 py-2 w-40 focus:outline-none focus:ring-2 focus:ring-blue-400" />
          <button @click="recordPayment"
            class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition">
            Record Payment
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
