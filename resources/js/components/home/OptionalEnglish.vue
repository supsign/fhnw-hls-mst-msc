<!-- eslint-disable vue/no-v-html -->
<template>
  <div v-if="courseData">
    <h2 class="mt-10">
      Optional English Class for MSc Students (no ECTS gained)
    </h2>
    <div
      v-if="description"
      class="mb-5"
      v-html="description.content" />
    <div class="flex">
      <div class="flex flex-col">
        <div class="border-x border-t border-light">
          <div class="flex border-b border-light bg-[#f1f1ee] font-bold">
            <div class="w-[26rem] px-5 py-4">
              Module
            </div>
            <div class="w-20 px-5 py-4" />
            <div class="flex gap-5 text-center">
              <div class="w-20 px-5 py-4">
                none
              </div>
              <div
                v-for="(semester, index) in courseData.semesters"
                :key="index"
                class="w-20 px-5 py-4">
                {{ semester.short_name }}
              </div>
              <div class="w-20 px-5 py-4">
                later
              </div>
            </div>
          </div>

          <Course
            :course="courseData.optional_courses.courses[0]"
            :semesters="courseData.semesters" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ICourseDataResponse, IText } from '@/interfaces';

import Course from './Course.vue';

type Props = {
  courseData: ICourseDataResponse;
};
const props = defineProps<Props>();

const description: IText | undefined = props.courseData.optional_courses?.texts.find(text => text.name === 'optional_english_description');
</script>
