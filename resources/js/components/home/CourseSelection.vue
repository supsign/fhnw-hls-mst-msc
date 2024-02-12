<template>
  <div
    v-if="courseData"
    class="mt-10 flex flex-col gap-10">
    <CourseGroup
      v-for="(group, index) in courseData.courses[0]"
      :key="index"
      :group="group"
      :semesters="courseData.semesters" />
    <EctsCount
      :statistics="statistics"
      :texts="courseData.texts" />
    <CourseGroup
      v-for="(group, index) in courseData.courses[1]"
      :key="index"
      :group="group"
      :semesters="courseData.semesters" />
  </div>
</template>

<script setup lang="ts">
import type { ICourseDataResponse, IStatistics } from '@/interfaces';

import CourseGroup from './CourseGroup.vue';
import EctsCount from './EctsCount.vue';

type Props = {
  courseData: ICourseDataResponse;
  statistics: IStatistics;
};
defineProps<Props>();
</script>
