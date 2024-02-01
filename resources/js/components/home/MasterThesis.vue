<!-- eslint-disable vue/no-v-html -->
<template>
  <div
    v-if="data"
    class="flex flex-col gap-5">
    <h2 class="mt-10">
      Master Thesis
    </h2>
    <BaseSelect
      v-model="modelValue.start"
      label="Start of MSc Thesis"
      :options="timeFrames"
      value-prop="start" />
    <div
      v-if="text"
      v-html="text.content" />
    <div class="grid grid-cols-3 gap-5">
      <BaseSelect
        v-model="modelValue.theses1_id"
        label="Subject of the MSc Thesis (1st choice)"
        option-label="name"
        :options="getThesisData(data, 1)" />
      <BaseSelect
        v-model="modelValue.theses2_id"
        label="Subject of the MSc Thesis (2nd choice)"
        option-label="name"
        :options="getThesisData(data, 2)" />
      <BaseSelect
        v-model="modelValue.theses3_id"
        clearable
        label="Subject of the MSc Thess (3rd choice, optional)"
        option-label="name"
        :options="getThesisData(data, 3)" />
    </div>
    <div>
      <label
        class="px-1 text-black"
        for="furtherDetails">Further Details on MSc Topic (optional)</label>
      <textarea
        id="furtherDetails"
        v-model="modelValue.furtherDetails"
        class="w-full border border-light px-4 py-2 outline-light" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { IText, IThesisDataResponse, IThesisSelection } from '@/interfaces';

type Props = {
  data: IThesisDataResponse;
};

const props = defineProps<Props>();

const modelValue = defineModel<IThesisSelection>({
  default: {
    furtherDetails: ''
  }
});

const text: IText | undefined = props.data.texts.find(text => text.name === 'thesis_text');

function getThesisData(data: IThesisDataResponse, select: 1 | 2 | 3) {
  if (select === 1) return data.theses.filter(thesis => thesis.id !== modelValue.value.theses2_id && thesis.id !== modelValue.value.theses3_id);
  if (select === 2) return data.theses.filter(thesis => thesis.id !== modelValue.value.theses1_id && thesis.id !== modelValue.value.theses3_id);
  if (select === 3) return data.theses.filter(thesis => thesis.id !== modelValue.value.theses1_id && thesis.id !== modelValue.value.theses2_id);
  return data.theses;
}

const timeFrames = computed(() => {
  return props.data.time_frames.map((timeFrame) => {
    return {
      ...timeFrame,
      label: timeFrame.start?.long_name
    };
  });
});
</script>
