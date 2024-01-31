<template>
  <h2 class="mt-10">
    Additional Comments on the Module Choice
  </h2>
  <textarea
    class="w-full border border-light px-4 py-2 outline-light"
    :value="modelValue"
    @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)" />
</template>

<script setup lang="ts">
type Props = {
  modelValue: string;
};
type Emits = {
  (e: 'update:modelValue', value: string): void;
};
defineProps<Props>();
defineEmits<Emits>();
</script>
