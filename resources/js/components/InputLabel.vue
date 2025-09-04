<!-- resources/js/components/InputLabel.vue -->
<template>
  <label :for="for" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>

<script setup>
defineProps({
  for: String,
  value: String
});
</script>