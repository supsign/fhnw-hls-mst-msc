<!-- eslint-disable vue/no-v-html -->
<template>
  <h2 class="mt-10">
    Double Degree Option
  </h2>
  <div
    v-if="description"
    class="mb-7"
    v-html="description.content" />
  <div class="flex">
    <input
      type="checkbox"
      :value="modelValue"
      class="box-border h-7 w-7 rounded-lg border border-gray-200 shadow-md"
      @input="$emit('update:modelValue', !modelValue)">
    <label class="ml-2">I'm interested in the Double-Degree option</label>
  </div>
</template>

<script setup lang="ts">
import type { IText } from '../../interfaces/text.interface';

type Props = {
  texts: IText[];
  modelValue: boolean;
}
type Emits = {
  (e: 'update:modelValue', value: boolean): void;
}
const props = defineProps<Props>();
defineEmits<Emits>();

const description: IText | null = props.texts.find((text) => text.name === 'double_degree_description') || null;
</script>
