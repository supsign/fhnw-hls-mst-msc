<template>
  <div
    class="flex hover:bg-gray-50"
    :title="course.content">
    <div class="w-[26rem] border-x border-b p-1">
      {{ course.name }}
    </div>
    <div
      class="w-10 border-r border-b p-1"
      :title="course.type_tooltip">
      {{ course.type_label_short }}
    </div>
    <div class="flex gap-5 border-b">
      <div class="flex w-20 justify-center text-center">
        <input
          v-model="course.selected_semester"
          type="radio"
          class="my-auto h-5 w-5"
          :value="null">
      </div>
      <div
        v-for="(semester, index) in semesters"
        :key="index"
        class="flex w-20 justify-center text-center">
        <input
          v-if="showCourseSelect(semester)"
          v-model="course.selected_semester"
          type="radio"
          class="my-auto h-5 w-5"
          :value="semester">
      </div>
      <div class="flex w-20 justify-center border-r text-center">
        <input
          v-if="laterIsVisible(semesters, course.end_semester)"
          v-model="course.selected_semester"
          type="radio"
          class="my-auto h-5 w-5"
          value="later">
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import dayjs from 'dayjs';
import { computed, ref, type PropType } from 'vue';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';
import type { ICourse } from '../../interfaces/course.interface';
import type { ISemester } from '../../interfaces/semester.interface';
const props = defineProps({
  course: { type: Object as PropType<ICourse>, required: true },
  semesters: { type: Array as PropType<ISemester[]>, required: true },
  type: String,
  further: Boolean,
  tooltip: String
});

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

props.course.selected_semester = null;
</script>
