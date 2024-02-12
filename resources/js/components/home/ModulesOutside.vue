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
          :min="0"
          type="number" />
        <Input
          v-model="module.university"
          label="University" />
        <div class="flex flex-col">
          <button
            class="mt-auto flex min-h-[40px] items-center bg-red-500 px-4 text-center font-medium leading-4 text-white hover:bg-red-600 "
            type="button"
            @click="modulesOutsideArray.splice(index, 1)">
            Remove
          </button>
        </div>
      </div>
      <div>
        <button
          class="flex min-h-[50px] items-center justify-center bg-black px-4 text-center font-medium leading-4 text-white hover:bg-primary hover:text-black"
          type="button"
          @click="addNewModule">
          <span class="">New Module</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { IModuleOutside, IText } from '@/interfaces';

import { whenever } from '@vueuse/core';

type Props = {
  texts: IText[];
};
type Emits = {
  'updateModulesOutsideData': [ value: IModuleOutside[]];
};
const props = defineProps<Props>();
const emit = defineEmits<Emits>();
const description: IText | undefined = props.texts.find(text => text.name === 'modules_outside_description') || undefined;
const modulesOutsideArray: Ref<IModuleOutside[]> = ref([]);

function addNewModule() {
  modulesOutsideArray.value.push({ ects: 0, title: '', university: '' });
}
whenever(modulesOutsideArray, (value) => {
  emit('updateModulesOutsideData', value);
}, { deep: true });
</script>
