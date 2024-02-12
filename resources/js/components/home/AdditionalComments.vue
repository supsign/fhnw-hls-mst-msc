<template>
  <h2 class="mt-10">
    Additional Comments on the Module Choice
  </h2>
  <textarea
    v-model="modelValue"
    class="w-full border border-light px-4 py-2 outline-light" />
</template>

<script setup lang="ts">
const modelValue = defineModel<string>();
</script>
