<template>
  <div>
    <label
      v-if="label"
      for="input"
      class="bg-white px-1 text-gray-400"
      :title="tooltip">{{ label }}</label>
    <select
      id="select"
      v-model="value"
      class="box-border block w-full rounded-lg border border-gray-200 py-2 px-4 text-gray-900 shadow-md"
      v-bind="$attrs"
      @change="$emit('change')">
      <template v-if="options.length > 0">
        <option
          v-if="placeholder"
          value="null"
          disabled
          selected>
          {{ placeholder }}
        </option>
        <option
          v-for="(option, index) in options"
          :key="index"
          :value="option"
          class="appearance-none border-t border-gray-50"
          :label="getLabel(option)" />
      </template>
      <template v-else>
        <option
          class="border-t border-gray-50"
          disabled>
          No Options available
        </option>
      </template>
    </select>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

type Props = {
  label?: string;
  options: any[];
  optionLabels?: string;
  placeholder?: string;
  modelValue?: any;
  tooltip?: string;
};
type Emits = {
  (e: 'change'): void;
  (e: 'update:modelValue', value: any): void;
}
const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const value = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  }
});

function getLabel(option: any) {
  if (!props.optionLabels) {
    return option.name;
  }
  const strings = props.optionLabels.split('.').map((s) => [s]);
  if (strings.length === 1) {
    // @ts-expect-error: ???
    return option[strings[0]];
  }
  // @ts-expect-error: ???
  return option[strings[0]][strings[1]];
}
</script>
