<!-- resources/js/components/RegistrationModal.vue -->
<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog'

interface Props {
  open: boolean
  title: string
  description?: string
}

const props = defineProps<Props>()
const emit = defineEmits<{ close: [] }>()
</script>

<template>
  <Dialog :open="props.open" @update:open="val => !val && emit('close')">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle>{{ props.title }}</DialogTitle>
        <DialogDescription v-if="props.description">
          {{ props.description }}
        </DialogDescription>
      </DialogHeader>

      <!-- Default slot for modal content -->
      <div class="py-4">
        <slot />
      </div>

      <DialogFooter>
        <slot name="footer" />
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
