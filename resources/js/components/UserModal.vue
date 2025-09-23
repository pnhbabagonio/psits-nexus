<!-- components/UserModal.vue -->
<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm">
    <div class="mx-4 w-full" :class="maxWidthClass">
      <div class="rounded-xl border border-border bg-card p-6 shadow-lg">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-foreground">{{ title }}</h2>
          <Button variant="ghost" size="sm" @click="emit('close')" class="h-8 w-8 p-0">
            <span class="sr-only">Close</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </Button>
        </div>

        <!-- Error Display -->
        <div v-if="errors && Object.keys(errors).length" class="mb-4 rounded-lg border border-destructive/50 bg-destructive/10 p-3">
          <p class="font-semibold text-destructive">Please fix the following errors:</p>
          <ul class="mt-1 list-disc list-inside">
            <li v-for="(error, field) in errors" :key="field" class="text-destructive">{{ error }}</li>
          </ul>
        </div>

        <!-- Content -->
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'

interface Props {
  isOpen: boolean
  title: string
  errors?: Record<string, string>
  maxWidth?: 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  maxWidth: 'lg',
  errors: () => ({})
})

const emit = defineEmits<{
  close: []
}>()

const maxWidthClass = computed(() => {
  const classes = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg'
  }
  return classes[props.maxWidth]
})
</script>
