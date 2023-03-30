<!-- eslint-disable vue/no-v-html -->
<template>
  <div>
    <h2 class="mt-10">
      Modules outside the Curriculum
    </h2>
    <div
      v-if="description"
      class="mb-5"
      v-html="description.content" />
    <div class="flex flex-col gap-5">
      <div
        v-for="(module, index) in modulesOutsideArray"
        :key="index"
        class="flex gap-10">
        <Input
          v-model="module.title"
          label="Module Title" />
        <NumberInput
          v-model="module.ects"
          label="ECTS"
          type="number"
          :min="0" />
        <Input
          v-model="module.university"
          label="University" />
        <div
          class="mt-6 flex h-10 cursor-pointer items-center rounded-md bg-red-600 py-1 px-4 text-white shadow-sm transition duration-300 ease-in-out hover:bg-red-700 hover:shadow-xl"
          @click="modulesOutsideArray.splice(index, 1)">
          Remove
        </div>
      </div>
      <div>
        <button
          type="button"
          class="flex min-h-[50px] items-center justify-center bg-black px-4 text-center font-medium leading-4 text-white hover:bg-primary hover:text-black"
          @click="addNewModule">
          <span class="">New Module</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { whenever } from '@vueuse/core';
import { type Ref, ref } from 'vue';
import type { IModuleOutside } from '../../interfaces/moduleOutside.interface';
import type { IText } from '../../interfaces/text.interface';
import Input from '../base/Input.vue';
import NumberInput from '../base/NumberInput.vue';

type Props = {
  texts: IText[];
}
type Emits = {
  (e: 'updateModulesOutsideData', value: IModuleOutside[]): void;
}
const props = defineProps<Props>();
const emit = defineEmits<Emits>();
const description: IText | null = props.texts.find((text) => text.name === 'modules_outside_description') || null;
const modulesOutsideArray: Ref<IModuleOutside[]> = ref([]);

function addNewModule() {
  modulesOutsideArray.value.push({ title: '', ects: 0, university: '' });
}
whenever(modulesOutsideArray, (value) => {
  emit('updateModulesOutsideData', value);
});
</script>
