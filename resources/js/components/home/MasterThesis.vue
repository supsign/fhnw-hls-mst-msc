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
      :options="data.time_frames"
      option-labels="start.long_name" />
    <div
      v-if="text"
      v-html="text.content" />
    <div>
      <Select
        v-model="value.theses"
        label="Subject of the MSc Thesis"
        :options="data.theses"
        multiple
        :size="data.theses.length" />
    </div>
    <div>
      <label
        for="furtherDetails"
        class="px-1 text-black">Further Details on MSc Topic (optional)</label>
      <textarea
        id="furtherDetails"
        v-model="value.furtherDetails"
        class="w-full border border-light px-4 py-2 outline-light" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, type WritableComputedRef } from 'vue';
import type { IText } from '../../interfaces/text.interface';
import type { IThesisDataResponse, IThesisSelection } from '../../interfaces/theses.interface';
import Select from '../base/Select.vue';

type Props = {
  data: IThesisDataResponse;
  modelValue: IThesisSelection;
}
type Emits = {
  (e: 'update:modelValue', value: IThesisSelection): void;
}
const props = defineProps<Props>();

const emit = defineEmits<Emits>();

const text: IText | null = props.data.texts.find((text) => text.name === 'thesis_text') || null;

const value: WritableComputedRef<IThesisSelection> = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  }
});
</script>
