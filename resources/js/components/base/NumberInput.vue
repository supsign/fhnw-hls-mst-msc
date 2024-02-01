<template>
  <div>
    <label
      v-if="label"
      class="px-1 text-black"
      for="input">{{ label }}</label>
    <input
      id="input"
      v-model="modelValue"
      class="w-full border border-light px-4 py-2 outline-light"
      type="number"
      v-bind="$attrs">
  </div>
</template>

<script setup lang="ts">
type Props = {
  label?: string;

};

defineProps<Props>();

const modelValue = defineModel<number>();
</script>
