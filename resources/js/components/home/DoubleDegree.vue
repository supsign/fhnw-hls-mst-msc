<template>
  <div>
    <div class="mb-5 text-lg font-bold">
      Double Degree Option
    </div>
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
  </div>
</template>

<script setup lang="ts">
import type { PropType } from 'vue';
import type { IText } from '../../interfaces/text.interface';

const props = defineProps({
  texts: { type: Array as PropType<IText[]>, required: true },
  modelValue: Boolean
});

defineEmits(['update:modelValue']);

const description: IText | null = props.texts.find((text) => text.name === 'double_degree_description') || null;
</script>
