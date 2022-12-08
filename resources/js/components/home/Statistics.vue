<template>
    <div class="flex flex-col gap-5">
        <div class="font-bold text-lg">Summary Statistics</div>
        <div>{{ statistics.specialization }} of Specialisation Modules</div>
        <div>{{ statistics.cluster }} of Cluster-specific Modules</div>
        <div>{{ statistics.core }} of Core Competence Modules</div>
        <div>{{ statistics.outside }} of Modules outside the Curriculum</div>

        <div class="flex flex-col w-32">
            <div class="border-b flex">
                <div class="p-1 w-20">Semester</div>
                <div class="text-right p-1 w-12">ECTS</div>
            </div>
            <div v-for="(semester, index) in semesterWithCourses" :key="index" class="flex">
                <div class="border-x border-b p-1 w-20">
                    {{ semester.short_name ? semester.short_name : semester.name }}
                </div>
                <div class="border-b border-r text-right p-1 w-12">{{ getEctsFromCourses(semester.courses) }}</div>
            </div>
            <div class="flex">
                <div class="border-x border-b p-1 w-20">Total</div>
                <div class="border-b border-r text-right p-1 w-12">{{ statistics.ects }}</div>
            </div>
        </div>
        <div v-if="masterThesis.start.start">
            Possible Time Frame of Thesis: {{ masterThesis.start.start.long_name }} -
            {{ masterThesis.start.end }}
        </div>
    </div>
</template>
<script setup lang="ts">
import type { PropType } from 'vue';
import type { IThesisSelection } from '../../interfaces/theses.interface';
import type { ISemester } from '../../interfaces/semester.interface';
import type { IStatistics } from '../../interfaces/statistics.interface';
import { getEctsFromCourses } from '../../helpers/counts';
const props = defineProps({
    semesterWithCourses: { type: Array as PropType<Array<ISemester>>, required: true },
    masterThesis: { type: Object as PropType<IThesisSelection>, required: true },
    statistics: { type: Object as PropType<IStatistics>, required: true },
});
</script>
