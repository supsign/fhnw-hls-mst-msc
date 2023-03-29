<template>
  <div
    v-if="data"
    class="flex flex-col justify-center gap-5">
    <Introduction :texts="data.texts" />
    <div class="text-lg font-bold">
      Personal Data
    </div>
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
      option_labels="long_name_with_short"
      @change="emits('getCourseData')" />
    <Select
      v-model="value.studyMode"
      label="Study Mode"
      :options="data.studyMode.studyModes"
      option_labels="label"
      :tooltip="data.studyMode.tooltip"
      @change="emits('getCourseData')" />
    <Select
      v-model="value.specialization"
      label="Specialization"
      :options="data.specializations"
      placeholder="-- Choose Specialization --"
      @change="emits('getCourseData')" />
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, type PropType, ref, type WritableComputedRef, computed } from 'vue';
import axios from 'axios';
import type { IPersonalDataResponse, IPersonalData } from '../../interfaces/personal.interface';
import Input from '../base/Input.vue';
import Select from '../base/Select.vue';
import Introduction from './Introduction.vue';
import dayjs from 'dayjs';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';

const props = defineProps({
  modelValue: { type: Object as PropType<IPersonalData>, required: true }
});

const emits = defineEmits(['getCourseData', 'update:modelValue']);

dayjs.extend(isSameOrAfter);

const data = ref();

const value: WritableComputedRef<IPersonalData> = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emits('update:modelValue', value);
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
  const current = data.value.semesters.find((semester: any) => semester.is_current);
  return data.value.semesters.map((semester: any) => {
    if (dayjs(semester.start_date).isBefore(dayjs(current.start_date))) {
      semester.long_name_with_short += ' (replanning of already started studies)';
    }
    return semester;
  });
});
</script>
