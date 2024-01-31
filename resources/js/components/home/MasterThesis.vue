<!-- eslint-disable vue/no-v-html -->
<template>
  <div
    v-if="data"
    class="flex flex-col gap-5">
    <h2 class="mt-10">
      Master Thesis
    </h2>
    <Select
      v-model="value.start"
      label="Start of MSc Thesis"
      option-labels="start.long_name"
      :options="data.time_frames" />
    <div
      v-if="text"
      v-html="text.content" />
    <div class="grid grid-cols-3 gap-5">
      <Select
        v-model="value.theses1"
        label="Subject of the MSc Thesis (1st choice)"
        :options="getThesisData(data, 1)" />
      <Select
        v-model="value.theses2"
        label="Subject of the MSc Thesis (2nd choice)"
        :options="getThesisData(data, 2)" />
      <Select
        v-model="value.theses3"
        label="Subject of the MSc Thess (3rd choice, optional)"
        :options="getThesisData(data, 3)" />
    </div>
    <div>
      <label
        class="px-1 text-black"
        for="furtherDetails">Further Details on MSc Topic (optional)</label>
      <textarea
        id="furtherDetails"
        v-model="value.furtherDetails"
        class="w-full border border-light px-4 py-2 outline-light" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { IText, IThesisDataResponse, IThesisSelection } from '@/interfaces';

type Props = {
  data: IThesisDataResponse;
  modelValue: IThesisSelection;
};
type Emits = {
  (e: 'update:modelValue', value: IThesisSelection): void;
};
const props = defineProps<Props>();

const emit = defineEmits<Emits>();

const text: IText | null = props.data.texts.find(text => text.name === 'thesis_text') || null;

const value: WritableComputedRef<IThesisSelection> = computed({
  get: () => props.modelValue,
  set(value) {
    emit('update:modelValue', value);
  }
});

function getThesisData(data: IThesisDataResponse, select: 1 | 2 | 3) {
  if (select === 1) return props.data.theses.filter(thesis => thesis.id !== props.modelValue.theses2?.id && thesis.id !== props.modelValue.theses3?.id);
  if (select === 2) return props.data.theses.filter(thesis => thesis.id !== props.modelValue.theses1?.id && thesis.id !== props.modelValue.theses3?.id);
  if (select === 3) return props.data.theses.filter(thesis => thesis.id !== props.modelValue.theses1?.id && thesis.id !== props.modelValue.theses2?.id);
  return props.data.theses;
}
</script>
