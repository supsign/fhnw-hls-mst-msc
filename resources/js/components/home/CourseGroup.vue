<template>
    <div>
        <div class="font-bold text-lg mb-5">
            {{ group.title }}
        </div>
        <div class="mb-5" v-html="group.description"></div>
        <div class="flex">
            <div class="border-b font-bold p-1 w-[26rem]">Module</div>
            <div class="border-b font-bold w-10">Type</div>
            <div class="border-b flex gap-5">
                <div class="font-bold text-center w-20">none</div>
                <div
                    v-for="(semester, index) in semesters"
                    :key="index"
                    class="font-bold text-center w-20"
                    :title="semester.tooltip"
                >
                    {{ semester.short_name }}
                </div>
                <div class="font-bold text-center w-20">later</div>
            </div>
        </div>
        <template v-if="group.specializations">
            <div v-for="(specialization, index) in group.specializations" :key="index">
                <div class="flex">
                    <div class="border-l border-b font-bold p-1 w-[26rem]">{{ specialization.name }}</div>
                    <div class="border-b w-10"></div>
                    <div class="border-b flex gap-5" v-if="semesters">
                        <div
                            v-for="index in semesters.length + 2"
                            :key="index"
                            class="text-center w-20"
                            :class="{ 'border-r': index == semesters.length + 2 }"
                        ></div>
                    </div>
                </div>
                <Course
                    v-for="course in sortCourses(specialization.courses)"
                    :key="index"
                    :course="course"
                    further
                    :semesters="semesters"
                />
            </div>
        </template>
        <template v-if="group.clusters">
            <div v-for="(cluster, index) in group.clusters" :key="index">
                <div class="flex">
                    <div class="border-l border-b font-bold p-1 w-[26rem]">{{ cluster.name }}</div>
                    <div class="border-b w-10"></div>
                    <div class="border-b flex gap-5" v-if="semesters">
                        <div
                            v-for="index in semesters.length + 2"
                            :key="index"
                            class="text-center w-20"
                            :class="{ 'border-r': index == semesters.length + 2 }"
                        ></div>
                    </div>
                </div>
                <Course
                    v-for="course in sortCourses(cluster.courses)"
                    :key="index"
                    :course="course"
                    further
                    :semesters="semesters"
                />
            </div>
        </template>
        <template v-if="group.courses">
            <div class="max-w-min">
                <Course
                    v-for="(course, index) in sortCourses(group.courses)"
                    :key="index"
                    :course="course"
                    :type="group.course_group_type_short_name"
                    :semesters="semesters"
                />
                <div
                    class="flex justify-end"
                    :class="[count < group.required_courses_count ? 'text-red-600' : 'text-green-600']"
                >
                    Chosen {{ count }} / Required {{ group.required_courses_count }}
                </div>
            </div>
        </template>
    </div>
</template>
<script setup lang="ts">
import { type PropType, computed } from 'vue';
import type { ICourseGroup } from '../../interfaces/course.interface';
import type { ICourse } from '../../interfaces/course.interface';
import type { ISemester } from '../../interfaces/semester.interface';
import Course from './Course.vue';

const props = defineProps({
    group: { type: Object as PropType<ICourseGroup>, required: true },
    semesters: { type: Array as PropType<Array<ISemester>>, required: true },
});

function sortCourses(courses: Array<ICourse>) {
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
