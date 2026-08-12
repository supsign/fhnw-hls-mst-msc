<template>
  <template v-if="personalData">
    <Introduction :texts="personalData.texts" />
    <h2 class="mt-10">Personal Data</h2>
    <div class="flex flex-col gap-5">
      <Input v-model="modelValue.surname" label="Surname" />
      <Input v-model="modelValue.givenName" label="Given Name" />
      <BaseSelect
        v-model="modelValue.semester_id"
        label="Start of Studies"
        option-label="long_name_with_short"
        :options="semesters"
        @update:model-value="selectUpdated" />
      <BaseSelect
        v-model="modelValue.studyMode_id"
        label="Study Mode"
        :options="personalData.studyMode.studyModes"
        :tooltip="personalData.studyMode.tooltip"
        @update:model-value="selectUpdated" />
      <BaseSelect
        v-model="modelValue.specialization_id"
        label="Specialization"
        option-label="name"
        extend
        :options="personalData.specializations"
        placeholder="-- Choose Specialization --"
        @update:model-value="selectUpdated" />
    </div>
  </template>
</template>

<script setup lang="ts">
import type { IPersonalData, IPersonalDataResponse } from '@/interfaces';
import { useAxios } from '@vueuse/integrations/useAxios';

import Introduction from './Introduction.vue';

interface Emits {
  filledOrChanged: [value: Required<IPersonalData>];
}

const emit = defineEmits<Emits>();
const modelValue = defineModel<IPersonalData>({ default: { givenName: '', surname: '' } });
const personalData = ref<IPersonalDataResponse>();

function prefillValues(data: IPersonalDataResponse) {
  modelValue.value.studyMode_id = data.studyMode.studyModes[0].id;
  const nextSemester = data.semesters.find((semester) => !semester.is_replanning);
  if (nextSemester) {
    modelValue.value.semester_id = nextSemester.id;
  }
}

onMounted(async () => {
  await useAxios<IPersonalDataResponse>('/personaldata', undefined, {
    immediate: true,
    onSuccess(data: IPersonalDataResponse) {
      prefillValues(data);
      personalData.value = data;
    },
  });
});

const semesters = computed(
  () =>
    personalData.value?.semesters.map((semester) => ({
      ...semester,
      long_name_with_short: `${semester.long_name_with_short}${semester.is_replanning ? ' (replanning of already started studies)' : ''}`,
    })) || []
);

function selectUpdated() {
  if (
    modelValue.value.semester_id &&
    modelValue.value.studyMode_id &&
    modelValue.value.specialization_id
  ) {
    emit('filledOrChanged', modelValue.value as Required<IPersonalData>);
  }
}
</script>
