<!-- eslint-disable vue/no-v-html -->
<template>
  <div>
    <h2>
      {{ group.title }}
    </h2>
    <div
      class="mb-5"
      v-html="group.description" />
    <div class="flex">
      <div class="flex flex-col">
        <div class="border-x border-t">
          <div class="flex font-bold">
            <div class="flex border-b border-light bg-[#f1f1ee]">
              <div class="w-[26rem] px-5 py-4">
                Module
              </div>
              <div class="w-20 px-5 py-4">
                Type
              </div>
              <div class="flex gap-5 text-center">
                <div class="w-20 px-5 py-4">
                  none
                </div>
                <div
                  v-for="(semester, index) in semesters"
                  :key="index"
                  class="w-20 px-5 py-4"
                  :title="semester.tooltip">
                  {{ semester.short_name }}
                </div>
                <div class="w-20 px-5 py-4">
                  later
                </div>
              </div>
            </div>
          </div>
          <template v-if="group.specializations">
            <div
              v-for="(specialization, index) in group.specializations"
              :key="index">
              <div class="flex border-b border-light">
                <div class="w-[26rem] px-5 py-4 font-bold">
                  {{ specialization.name }}
                </div>
                <div class="w-10 px-5 py-4" />
                <div
                  v-if="semesters"
                  class="flex gap-5">
                  <div
                    v-for="sIdx in semesters.length + 2"
                    :key="sIdx"
                    class="w-20 px-5 py-4 text-center" />
                </div>
              </div>
              <Course
                v-for="course, cIdx in sortCourses(specialization.courses)"
                :key="cIdx"
                :course="course"
                further
                :semesters="semesters" />
            </div>
          </template>
          <template v-if="group.clusters">
            <div
              v-for="(cluster, index) in group.clusters"
              :key="index">
              <div class="flex border-b border-light">
                <div class="w-[26rem] px-5 py-4 font-bold">
                  {{ cluster.name }}
                </div>
                <div class="w-10 px-5 py-4" />
                <div
                  v-if="semesters"
                  class="flex gap-5">
                  <div
                    v-for="sIdx in semesters.length + 2"
                    :key="sIdx"
                    class="w-20 px-5 py-4 text-center" />
                </div>
              </div>
              <Course
                v-for="course, cIdx in sortCourses(cluster.courses)"
                :key="cIdx"
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
                :semesters="semesters"
                :type="group.course_group_type_short_name" />
            </div>
          </template>
        </div>
        <div
          v-if="group.courses"
          class="mt-2 flex justify-end"
          :class="[count < group.required_courses_count ? 'text-red-600' : 'text-green-600']">
          Chosen {{ count }} / Required {{ group.required_courses_count }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ICourse, ICourseGroup, ISemester } from '@/interfaces';

import Course from './Course.vue';

type Props = {
  group: ICourseGroup;
  semesters: ISemester[];
};
const props = defineProps<Props>();

const sortCourses = (courses: ICourse[]) => courses.sort((a, b) => a.semester_type - b.semester_type);

const count = computed(() => {
  if (props.group.courses) {
    let count = 0;
    for (const course of props.group.courses) {
      if (course.selected_semester) {
        count += 1;
      }
    }
    return count;
  }
  return 0;
});
</script>
