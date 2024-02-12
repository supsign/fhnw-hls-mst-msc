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
      v-model="modelValue"
      class="box-border size-7 rounded-lg border border-gray-200 shadow-md"
      type="checkbox">
    <label class="ml-2">I'm interested in the Double-Degree option</label>
  </div>
</template>

<script setup lang="ts">
import type { IText } from '@/interfaces';

type Props = {
  texts: IText[];
};

const props = defineProps<Props>();

const modelValue = defineModel<boolean>();

const description: IText | undefined = props.texts.find(text => text.name === 'double_degree_description');
</script>
