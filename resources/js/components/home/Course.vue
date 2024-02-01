<!-- eslint-disable vue/no-mutating-props -->
<template>
  <div
    class="flex border-b border-light "
    :title="course.content">
    <div class="w-[26rem] px-5 py-4">
      {{ course.name }}
    </div>
    <div
      class="my-auto w-20 px-5 py-4"
      :title="course.type_tooltip">
      {{ course.type_label_short }}
    </div>
    <div class="flex gap-5 border-b">
      <div class="flex w-20 justify-center px-5 py-4 text-center">
        <input
          v-model="course.selected_semester"
          class="my-auto size-5 cursor-pointer"
          type="radio"
          :value="undefined">
      </div>
      <div
        v-for="(semester, index) in semesters"
        :key="index"
        class="flex w-20 justify-center px-5 py-4 text-center">
        <div v-if="semester.is_replanning">
          replaning
        </div>
        <input
          v-else-if="showCourseSelect(semester)"
          v-model="course.selected_semester"
          class="my-auto size-5 cursor-pointer"
          type="radio"
          :value="semester">
      </div>
      <div class="flex w-20 justify-center px-5 py-4 text-center">
        <input
          v-if="laterIsVisible(semesters, course.end_semester)"
          v-model="course.selected_semester"
          class="my-auto size-5 cursor-pointer"
          type="radio"
          value="later">
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ICourse, ISemester } from '@/interfaces';

import dayjs from 'dayjs';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';

type Props = {
  course: ICourse;
  further?: boolean;
  semesters: ISemester[];
  tooltip?: string;
  type?: string;
};
const props = defineProps<Props>();

dayjs.extend(isSameOrAfter);

const startDayjs = computed(() => {
  if (!props.course.start_semester) return;
  return dayjs(props.course.start_semester.start_date);
});
const endDayjs = computed(() => {
  if (!props.course.end_semester) return;
  return dayjs(props.course.end_semester.start_date);
});

function showCourseSelect(semester: ISemester) {
  if (semester.type !== props.course.semester_type) return false;
  if (startDayjs.value && dayjs(semester.start_date).isBefore(startDayjs.value)) return false;
  return !(endDayjs.value && dayjs(semester.start_date).isAfter(endDayjs.value));
}

function laterIsVisible(semesters: ISemester[], endSemester: ISemester) {
  if (!endSemester) {
    return true;
  }
  // eslint-disable-next-line unicorn/prefer-at
  const lastSemester = semesters[semesters.length - 1];
  return dayjs(endSemester.start_date).isAfter(dayjs(lastSemester.start_date));
}

// eslint-disable-next-line vue/no-mutating-props
props.course.selected_semester = undefined;
</script>
