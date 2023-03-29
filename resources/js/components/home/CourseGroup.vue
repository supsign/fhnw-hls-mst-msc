<template>
  <div>
    <div class="mb-5 text-lg font-bold">
      {{ group.title }}
    </div>
    <div
      class="mb-5"
      v-html="group.description" />
    <div class="flex">
      <div class="w-[26rem] border-b p-1 font-bold">
        Module
      </div>
      <div class="w-10 border-b font-bold">
        Type
      </div>
      <div class="flex gap-5 border-b">
        <div class="w-20 text-center font-bold">
          none
        </div>
        <div
          v-for="(semester, index) in semesters"
          :key="index"
          class="w-20 text-center font-bold"
          :title="semester.tooltip">
          {{ semester.short_name }}
        </div>
        <div class="w-20 text-center font-bold">
          later
        </div>
      </div>
    </div>
    <template v-if="group.specializations">
      <div
        v-for="(specialization, index) in group.specializations"
        :key="index">
        <div class="flex">
          <div class="w-[26rem] border-l border-b p-1 font-bold">
            {{ specialization.name }}
          </div>
          <div class="w-10 border-b" />
          <div
            v-if="semesters"
            class="flex gap-5 border-b">
            <div
              v-for="index in semesters.length + 2"
              :key="index"
              class="w-20 text-center"
              :class="{ 'border-r': index == semesters.length + 2 }" />
          </div>
        </div>
        <Course
          v-for="course in sortCourses(specialization.courses)"
          :key="index"
          :course="course"
          further
          :semesters="semesters" />
      </div>
    </template>
    <template v-if="group.clusters">
      <div
        v-for="(cluster, index) in group.clusters"
        :key="index">
        <div class="flex">
          <div class="w-[26rem] border-l border-b p-1 font-bold">
            {{ cluster.name }}
          </div>
          <div class="w-10 border-b" />
          <div
            v-if="semesters"
            class="flex gap-5 border-b">
            <div
              v-for="index in semesters.length + 2"
              :key="index"
              class="w-20 text-center"
              :class="{ 'border-r': index == semesters.length + 2 }" />
          </div>
        </div>
        <Course
          v-for="course in sortCourses(cluster.courses)"
          :key="index"
          :course="course"
          further
          :semesters="semesters" />
      </div>
    </template>
    <template v-if="group.courses">
      <div class="max-w-min">
        <Course
          v-for="(course, index) in sortCourses(group.courses)"
          :key="index"
          :course="course"
          :type="group.course_group_type_short_name"
          :semesters="semesters" />
        <div
          class="flex justify-end"
          :class="[count < group.required_courses_count ? 'text-red-600' : 'text-green-600']">
          Chosen {{ count }} / Required {{ group.required_courses_count }}
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { type PropType, computed } from 'vue';
import type { ICourseGroup, ICourse } from '../../interfaces/course.interface';
import type { ISemester } from '../../interfaces/semester.interface';
import Course from './Course.vue';

const props = defineProps({
  group: { type: Object as PropType<ICourseGroup>, required: true },
  semesters: { type: Array as PropType<ISemester[]>, required: true }
});

function sortCourses(courses: ICourse[]) {
  return courses.sort((a, b) => a.semester_type - b.semester_type);
}

const count = computed(() => {
  if (props.group.courses) {
    let count = 0;
    for (let course of props.group.courses) {
      if (course.selected_semester) {
        count += 1;
      }
    }
    return count;
  }
  return 0;
});
</script>
