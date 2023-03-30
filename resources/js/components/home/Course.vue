<!-- eslint-disable vue/no-mutating-props -->
<template>
  <div
    class="flex border-b border-light "
    :title="course.content">
    <div class="w-[26rem] px-5 py-4">
      {{ course.name }}
    </div>
    <div
      class="w-20 px-5 py-4"
      :title="course.type_tooltip">
      {{ course.type_label_short }}
    </div>
    <div class="flex gap-5">
      <div class="flex w-20 justify-center px-5 py-4 text-center">
        <input
          v-model="course.selected_semester"
          type="radio"
          class="my-auto h-5 w-5 cursor-pointer"
          :value="null">
      </div>
      <div
        v-for="(semester, index) in semesters"
        :key="index"
        class="flex w-20 justify-center px-5 py-4 text-center">
        <input
          v-if="showCourseSelect(semester)"
          v-model="course.selected_semester"
          type="radio"
          class="my-auto h-5 w-5 cursor-pointer"
          :value="semester">
      </div>
      <div class="flex w-20 justify-center px-5 py-4 text-center">
        <input
          v-if="laterIsVisible(semesters, course.end_semester)"
          v-model="course.selected_semester"
          type="radio"
          class="my-auto h-5 w-5 cursor-pointer"
          value="later">
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import dayjs from 'dayjs';
import { computed } from 'vue';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';
import type { ICourse } from '../../interfaces/course.interface';
import type { ISemester } from '../../interfaces/semester.interface';

type Props = {
  course: ICourse;
  semesters: ISemester[];
  type?: string;
  further?: boolean;
  tooltip?: string;
}
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
  if (endDayjs.value && dayjs(semester.start_date).isAfter(endDayjs.value)) return false;
  return true;
}

function laterIsVisible(semesters: ISemester[], endSemester: ISemester) {
  if (!endSemester) {
    return true;
  }
  const lastSemester = semesters[semesters.length - 1];
  if (dayjs(endSemester.start_date).isAfter(dayjs(lastSemester.start_date))) {
    return true;
  }
  return false;
}

// eslint-disable-next-line vue/no-mutating-props
props.course.selected_semester = null;
</script>
