<template>
    <div v-if="courseData">
        <div class="font-bold text-lg mb-5">Optional English Class for MSc Students (no ECTS gained)</div>
        <div v-if="description" v-html="description.content" class="mb-5"></div>

        <div class="flex">
            <div class="border-b p-1 w-[26rem]"></div>
            <div class="border-b w-10"></div>
            <div class="border-b flex gap-5">
                <div class="text-center w-20">none</div>
                <div v-for="(semester, index) in courseData.semesters" :key="index" class="text-center w-20">
                    {{ semester.short_name }}
                </div>
                <div class="text-center w-20">later</div>
            </div>
        </div>
        <Course :course="courseData.optional_courses.courses[0]" :semesters="courseData.semesters" />
    </div>
</template>
<script setup lang="ts">
import type { PropType } from 'vue';
import type { ICourseDataResponse } from '../../interfaces/course.interface';
import type { IText } from '../../interfaces/text.interface';
import Course from './Course.vue';
const props = defineProps({
    courseData: { type: Object as PropType<ICourseDataResponse>, required: true },
});

const description: IText | null =
    props.courseData.optional_courses?.texts.find((text) => text.name === 'optional_english_description') || null;
</script>
