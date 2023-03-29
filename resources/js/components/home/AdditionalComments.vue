<template>
  <div class="flex flex-col gap-5">
    <div class="text-lg font-bold">
      Additional Comments on the Module Choice
    </div>
    <textarea
      :value="modelValue"
      class="box-border block w-full rounded-lg border border-gray-200 py-2 px-4 text-gray-900 shadow-md"
      @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)" />
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  modelValue: String
});
const emits = defineEmits(['update:modelValue']);
</script>
