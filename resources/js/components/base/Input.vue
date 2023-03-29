<template>
  <div>
    <label
      v-if="label"
      for="input"
      class="bg-white px-1 text-gray-400">{{ label }}</label>
    <input
      id="input"
      class="box-border block w-full rounded-lg border border-gray-200 py-2 px-4 text-gray-900 shadow-md"
      :value="modelValue"
      v-bind="$attrs"
      @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)">
  </div>
</template>

<script setup lang="ts">
type Props = {
  label?: string;
  modelValue?: string;
};
type Emits = {
  (e: 'update:modelValue', value: string): void;
}
defineProps<Props>();
defineEmits<Emits>();
</script>
