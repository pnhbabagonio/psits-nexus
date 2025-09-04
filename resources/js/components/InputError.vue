<template>
  <div v-show="message" class="text-sm text-red-600 dark:text-red-400">
    {{ message }}
  </div>
</template>

<script setup>
defineProps({
  message: String
});
</script>