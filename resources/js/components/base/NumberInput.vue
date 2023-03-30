<template>
  <div>
    <label
      v-if="label"
      for="input"
      class="px-1 text-black">{{ label }}</label>
    <input
      id="input"
      class="w-full border border-light px-4 py-2 outline-light"
      :value="modelValue"
      type="number"
      v-bind="$attrs"
      @input="$emit('update:modelValue', Number(($event.target as HTMLTextAreaElement).value))">
  </div>
</template>

<script setup lang="ts">
type Props = {
  label?: string;
  modelValue?: number;
};
type Emits = {
  (e: 'update:modelValue', value: number): void;
}
defineProps<Props>();
defineEmits<Emits>();
</script>
