<template>
  <div class="flex flex-col gap-5">
    <div class="text-lg font-bold">
      Summary Statistics
    </div>
    <div>{{ statistics.specialization }} of Specialisation Modules</div>
    <div>{{ statistics.cluster }} of Cluster-specific Modules</div>
    <div>{{ statistics.core }} of Core Competence Modules</div>
    <div>{{ statistics.outside }} of Modules outside the Curriculum</div>

    <div class="flex w-32 flex-col">
      <div class="flex border-b">
        <div class="w-20 p-1">
          Semester
        </div>
        <div class="w-12 p-1 text-right">
          ECTS
        </div>
      </div>
      <div
        v-for="(semester, index) in semesterWithCourses"
        :key="index"
        class="flex">
        <div class="w-20 border-x border-b p-1">
          {{ semester.short_name ? semester.short_name : semester.name }}
        </div>
        <div class="w-12 border-b border-r p-1 text-right">
          {{ getEctsFromCourses(semester.courses) }}
        </div>
      </div>
      <div class="flex">
        <div class="w-20 border-x border-b p-1">
          Total
        </div>
        <div class="w-12 border-b border-r p-1 text-right">
          {{ statistics.ects }}
        </div>
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
  semesterWithCourses: { type: Array as PropType<ISemester[]>, required: true },
  masterThesis: { type: Object as PropType<IThesisSelection>, required: true },
  statistics: { type: Object as PropType<IStatistics>, required: true }
});
</script>
