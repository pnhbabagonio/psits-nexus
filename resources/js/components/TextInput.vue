<template>
  <input
    :id="id"
    :type="type"
    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :required="required"
    :autofocus="autofocus"
  >
</template>

<script setup>
defineProps({
  id: String,
  type: {
    type: String,
    default: 'text'
  },
  modelValue: [String, Number],
  required: Boolean,
  autofocus: Boolean
});

defineEmits(['update:modelValue']);
</script>
