<template>
  <template v-if="personalData">
    <Introduction :texts="personalData.texts" />
    <h2 class="mt-10">
      Personal Data
    </h2>
    <div class="flex flex-col gap-5">
      <Input
        v-model="modelValue.surname"
        label="Surname" />
      <Input
        v-model="modelValue.givenName"
        label="Given Name" />
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
import type { IPersonalData, IPersonalDataResponse, ISemester } from '@/interfaces';

import { useAxios } from '@vueuse/integrations/useAxios';
import dayjs from 'dayjs';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';

import Introduction from './Introduction.vue';

type Emits = {
  'filledOrChanged': [value: Required<IPersonalData>];
};

const emit = defineEmits<Emits>();

const modelValue = defineModel<IPersonalData>({ default: {
  givenName: '',
  surname: ''
} });

dayjs.extend(isSameOrAfter);

const personalData = ref<IPersonalDataResponse>();

onMounted(async () => {
  await useAxios<IPersonalDataResponse>('/personaldata', undefined, {
    immediate: true,
    onError(error) {
      console.log(error);
    },
    onSuccess(data: IPersonalDataResponse) {
      prefillValues(data);
      personalData.value = data;
    }
  });
});

function prefillValues(data: IPersonalDataResponse) {
  modelValue.value.studyMode_id = data.studyMode.studyModes[0].id;
  const nextSemester = data.semesters.find(semester => !semester.is_replanning);
  if (nextSemester) modelValue.value.semester_id = nextSemester.id;

  /* const currentSemester = data.semesters.find(semester => semester.is_replanning);
  const springSwitchDate = dayjs(dayjs().get('year') + '-04-30');
  const autumSwitchDate = dayjs(dayjs().get('year') + '-11-30');
  console.log(dayjs());
  console.log(autumSwitchDate);
  console.log(dayjs().isSameOrAfter(autumSwitchDate, 'day'));
  if (currentSemester?.type === 1 && dayjs().isSameOrAfter(autumSwitchDate, 'day')) {
    const index = data.semesters.findIndex(semester => semester.is_replanning);
    value.value.semester_id = data.semesters[index + 1].id;
  }
  else if (currentSemester?.type === 2 && dayjs().isSameOrAfter(springSwitchDate, 'day')) {
    const index = data.semesters.findIndex(semester => semester.is_replanning);
    value.value.semester_id = data.semesters[index + 1].id;
  }
  else {
    value.value.semester_id = data.semesters.find(semester => semester.is_replanning).id;
  }
  */
}

const semesters = computed(() => {
  return personalData.value?.semesters.map(semester => ({
    ...semester,
    long_name_with_short: `${semester.long_name_with_short}${semester.is_replanning ? ' (replanning of already started studies)' : ''}`
  })) || [];
});

function selectUpdated() {
  if (modelValue.value.semester_id && modelValue.value.studyMode_id && modelValue.value.specialization_id) {
    emit('filledOrChanged', modelValue.value as Required<IPersonalData>);
  }
}
</script>
