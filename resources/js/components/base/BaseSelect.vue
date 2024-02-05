<template>
  <div class="grid w-full">
    <label
      v-if="label"
      class="px-1 text-black"
      for="input"
      :title="tooltip">{{ label }}</label>
    <Multiselect
      v-model="modelValue"
      v-bind="$attrs"
      :can-clear="clearable"
      :can-deselect="false"
      class="multiselect-custom"
      :disabled="disabled"
      :hide-selected="hideSelected"
      :label="optionLabel"
      :mode="mode"
      :options="options"
      :required="required"
      :searchable="searchable"
      :track-by="searchKey"
      :value-prop="valueProp"
      @change="$emit('change', $event)"
      @deselect="$emit('deselect', $event)"
      @select="$emit('select', $event)" />
  </div>
</template>

<script setup lang="ts" generic="T">
import Multiselect from '@vueform/multiselect';

type Props = {
  clearable?: boolean;
  disabled?: boolean;
  error?: string;
  hideSelected?: boolean;
  label?: string;
  mode?: Multiselect['mode'];
  optionLabel?: string;
  options: T[];
  required?: boolean;
  rules?: string;
  searchable?: boolean;
  searchKey?: string;
  tooltip?: string;
  valueProp?: string;
};
type Emits = {
  change: [value: T];
  deselect: [value: T];
  select: [value: T];
};

withDefaults(defineProps<Props>(), {
  error: undefined,
  label: '',
  mode: 'single',
  modelValue: undefined,
  optionLabel: 'label',
  rules: '',
  searchKey: 'name',
  valueProp: 'id'
});
defineEmits<Emits>();

const modelValue = defineModel<number | T>();
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<style>
.multiselect-custom {
    --ms-border-color: #d1d5db;
    --ms-radius: 0rem;
    --ms-ring-width: 0px;
    --ms-option-py: 0.25rem;
    --ms-border-color-active: #d1d5db;
    --ms-dropdown-border-color: #d1d5db;
    --ms-option-color-pointed: #1f2937;
    --ms-option-bg-selected:#fde70e;
    --ms-option-color-selected: #000000;
    --ms-option-color-selected-pointed: #000000;
    --ms-option-bg-selected-pointed: #fde70e;
}
</style>
