<template>
    <div class="flex hover:bg-gray-50" :title="course.content">
        <div class="border-b border-l border-r p-1 w-[26rem]">
            {{ course.name }}
        </div>
        <div class="border-r border-b p-1 w-10" :title="course.type_tooltip">
            {{ course.type_label_short }}
        </div>
        <div class="border-b flex gap-5">
            <div class="flex text-center w-20 justify-center">
                <input type="radio" class="my-auto h-5 w-5" v-model="course.selected_semester" :value="null" />
            </div>
            <div v-for="(semester, index) in semesters" :key="index" class="flex text-center w-20 justify-center">
                <input
                    v-if="semester.type === course.semester_type && checkEndDate(semester, course.end_semester)"
                    type="radio"
                    class="my-auto h-5 w-5"
                    v-model="course.selected_semester"
                    :value="semester"
                />
            </div>
            <div class="border-r flex text-center w-20 justify-center">
                <input
                    type="radio"
                    class="my-auto h-5 w-5"
                    v-model="course.selected_semester"
                    value="later"
                    v-if="laterIsVisible(semesters, course.end_semester)"
                />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import dayjs from 'dayjs';
import type { PropType } from 'vue';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';
import type { ICourse } from '../../interfaces/course.interface';
import type { ISemester } from '../../interfaces/semester.interface';
dayjs.extend(isSameOrAfter);

const props = defineProps({
    course: { type: Object as PropType<ICourse>, required: true },
    semesters: { type: Array as PropType<Array<ISemester>>, required: true },
    type: String,
    further: Boolean,
    tooltip: String,
});

function checkEndDate(semester: ISemester, endSemester: ISemester) {
    if (!endSemester) {
        return true;
    }
    if (dayjs(endSemester.start_date).isSameOrAfter(dayjs(semester.start_date))) {
        return true;
    }
    return false;
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
