<template>
    <div v-if="courseData" class="flex flex-col mt-10 gap-10">
        <CourseGroup
            v-for="(group, index) in courseData.courses[0]"
            :key="index"
            :group="group"
            :semesters="courseData.semesters"
        />
        <EctsCount :texts="courseData.texts" :statistics="statistics" />
        <CourseGroup
            v-for="(group, index) in courseData.courses[1]"
            :key="index"
            :group="group"
            :semesters="courseData.semesters"
        />
    </div>
</template>
<script setup lang="ts">
import type { PropType } from 'vue';
import type { ICourseDataResponse } from '../../interfaces/course.interface';
import type { IStatistics } from '../../interfaces/statistics.interface';
import CourseGroup from './CourseGroup.vue';
import EctsCount from './EctsCount.vue';

defineProps({
    courseData: { type: Object as PropType<ICourseDataResponse>, required: true },
    statistics: { type: Object as PropType<IStatistics>, required: true },
});
</script>
