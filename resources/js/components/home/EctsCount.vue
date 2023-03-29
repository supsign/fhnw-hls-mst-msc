<template>
  <div class="sticky top-0 z-10 self-start border bg-hls p-2 shadow-xl">
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
import type { PropType } from 'vue';
import type { IStatistics } from '../../interfaces/statistics.interface';
import type { IText } from '../../interfaces/text.interface';

const props = defineProps({
  texts: { type: Array as PropType<IText[]>, required: true },
  statistics: { type: Object as PropType<IStatistics>, required: true }
});
const description: IText | null = props.texts.find((text) => text.name === 'description_before_further') || null;
</script>
