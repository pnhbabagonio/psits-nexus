<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';
import { ref } from 'vue';
import { Wallet } from 'lucide-vue-next'; // optional icons

const paymentMethod = ref<'gcash' | 'paymaya' | null>(null);

// Replace with your actual extracted QR strings
const gcashQR = '00020101021126650014com.gcash0111+639171234567...';
const paymayaQR = '00020101021126660014com.paymaya0111+639191234567...';

const methods = [
  {
    id: 'gcash',
    name: 'GCash',
    color: 'bg-blue-500',
    hover: 'hover:bg-blue-600',
    description: 'Scan to pay via GCash',
  },
  {
    id: 'paymaya',
    name: 'PayMaya',
    color: 'bg-green-500',
    hover: 'hover:bg-green-600',
    description: 'Scan to pay via PayMaya',
  },
];
</script>

<template>
  <Head title="Payment QR Codes" />

  <AppLayout>
    <div class="p-6 space-y-8">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Choose Payment Method</h1>
        <p class="text-muted-foreground">Scan the QR code to send your payment directly.</p>
      </div>

      <!-- Payment Options -->
      <div class="grid gap-6 sm:grid-cols-2">
        <div
          v-for="method in methods"
          :key="method.id"
          class="cursor-pointer rounded-2xl border border-border p-6 transition-all shadow-sm hover:shadow-md"
          :class="paymentMethod === method.id ? method.color + ' text-white' : 'bg-card'"
          @click="paymentMethod = method.id as 'gcash' | 'paymaya'"
        >
          <div class="flex items-center gap-3">
            <Wallet class="h-6 w-6" />
            <h2 class="text-lg font-semibold">{{ method.name }}</h2>
          </div>
          <p class="mt-2 text-sm opacity-80">{{ method.description }}</p>
        </div>
      </div>

      <!-- QR Code Display -->
      <div
        v-if="paymentMethod"
        class="rounded-2xl border border-border bg-card p-8 text-center shadow-md"
      >
        <h2 class="text-xl font-semibold mb-4">
          {{ paymentMethod === 'gcash' ? 'GCash QR Code' : 'PayMaya QR Code' }}
        </h2>

        <div class="flex justify-center">
          <QrcodeVue
            :value="paymentMethod === 'gcash' ? gcashQR : paymayaQR"
            :size="220"
          />
        </div>

        <p class="mt-4 text-sm text-muted-foreground">
          Scan this code with your {{ paymentMethod === 'gcash' ? 'GCash' : 'PayMaya' }} app to complete the payment.
        </p>
      </div>
    </div>
  </AppLayout>
</template>
