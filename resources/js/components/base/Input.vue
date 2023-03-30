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
