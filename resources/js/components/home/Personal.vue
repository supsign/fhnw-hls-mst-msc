<template>
  <template v-if="data">
    <Introduction :texts="data.texts" />
    <h2 class="mt-10">
      Personal Data
    </h2>
    <div class="flex flex-col gap-5">
      <Input
        v-model="value.surname"
        label="Surname" />
      <Input
        v-model="value.givenName"
        label="Given Name" />
      <Select
        v-model="value.semester"
        label="Start of Studies"
        :options="semesters"
        option-labels="long_name_with_short"
        @change="$emit('getCourseData')" />
      <Select
        v-model="value.studyMode"
        label="Study Mode"
        :options="data.studyMode.studyModes"
        option-labels="label"
        :tooltip="data.studyMode.tooltip"
        @change="$emit('getCourseData')" />
      <Select
        v-model="value.specialization"
        label="Specialization"
        :options="data.specializations"
        placeholder="-- Choose Specialization --"
        @change="$emit('getCourseData')" />
    </div>
  </template>
</template>

<script setup lang="ts">
import { onBeforeMount, ref, type WritableComputedRef, computed } from 'vue';
import axios from 'axios';
import type { IPersonalDataResponse, IPersonalData } from '../../interfaces/personal.interface';
import Input from '../base/Input.vue';
import Select from '../base/Select.vue';
import Introduction from './Introduction.vue';
import dayjs from 'dayjs';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';
import type { ISemester } from '@/interfaces/semester.interface';

type Props = {
  modelValue: IPersonalData;
}
type Emits = {
  (e: 'getCourseData'): void;
  (e: 'update:modelValue', value: IPersonalData): void;
}

const props = defineProps<Props>();

const emit = defineEmits<Emits>();

dayjs.extend(isSameOrAfter);

const data = ref<IPersonalDataResponse>();

const value: WritableComputedRef<IPersonalData> = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  }
});

onBeforeMount(async() => {
  data.value = await getPersonalData();
  prefillValues(data.value);
});

async function getPersonalData() {
  const response = await axios.get<IPersonalDataResponse>('/personaldata');
  return response.data;
}

function prefillValues(data: IPersonalDataResponse) {
  const currentSemester = data.semesters.find((semester) => semester.is_current);
  const springSwitchDate = dayjs(dayjs().get('year') + '-04-30');
  const autumSwitchDate = dayjs(dayjs().get('year') + '-11-30');
  console.log(dayjs());
  console.log(autumSwitchDate);
  console.log(dayjs().isSameOrAfter(autumSwitchDate, 'day'));
  if (currentSemester?.type === 1 && dayjs().isSameOrAfter(autumSwitchDate, 'day')) {
    const index = data.semesters.findIndex((semester) => semester.is_current);
    value.value.semester = data.semesters[index + 1];
  } else if (currentSemester?.type === 2 && dayjs().isSameOrAfter(springSwitchDate, 'day')) {
    const index = data.semesters.findIndex((semester) => semester.is_current);
    value.value.semester = data.semesters[index + 1];
  } else {
    value.value.semester = data.semesters.find((semester) => semester.is_current);
  }
  value.value.studyMode = data.studyMode.studyModes[0];
}

const semesters = computed(() => {
  if (!data.value) return [];
  const current = data.value.semesters.find((semester: ISemester) => semester.is_current);
  return data.value.semesters.map((semester: ISemester) => {
    if (dayjs(semester.start_date).isBefore(dayjs(current?.start_date))) {
      semester.long_name_with_short += ' (replanning of already started studies)';
    }
    return semester;
  });
});
</script>
