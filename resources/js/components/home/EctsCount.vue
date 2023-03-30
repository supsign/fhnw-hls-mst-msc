<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="sticky top-[141px] z-10 self-start border-t-4 border-white  bg-primary p-2 shadow-xl">
    <div
      v-if="description"
      v-html="description.content" />
    <div class="flex gap-5 divide-x-2 divide-black font-bold">
      <div>Current ECTS: {{ statistics.ects }}/50</div>
      <div class="pl-5">
        {{ statistics.specialization }} S
      </div>
      <div class="pl-5">
        {{ statistics.cluster }} CS
      </div>
      <div class="pl-5">
        {{ statistics.core }} CC
      </div>
      <div class="pl-5">
        {{ statistics.outside }} Modules Outside
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { IStatistics } from '../../interfaces/statistics.interface';
import type { IText } from '../../interfaces/text.interface';

type Props = {
  texts: IText[];
  statistics: IStatistics;
}
const props = defineProps<Props>();
const description: IText | null = props.texts.find((text) => text.name === 'description_before_further') || null;
</script>
