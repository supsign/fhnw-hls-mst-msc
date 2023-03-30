<template>
  <h2 class="mt-10">
    Summary Statistics
  </h2>
  <div class="flex flex-col gap-5">
    <div>{{ statistics.specialization }} of Specialisation Modules</div>
    <div>{{ statistics.cluster }} of Cluster-specific Modules</div>
    <div>{{ statistics.core }} of Core Competence Modules</div>
    <div>{{ statistics.outside }} of Modules outside the Curriculum</div>
    <div class="flex ">
      <div class="flex flex-col border border-light ">
        <div class="flex border-b border-light bg-[#f1f1ee]">
          <div class="w-28 px-5 py-4">
            Semester
          </div>
          <div class="w-20 px-5 py-4">
            ECTS
          </div>
        </div>
        <div
          v-for="(semester, index) in semesterWithCourses"
          :key="index"
          class="flex border-b border-light">
          <div class="w-28 px-5 py-4">
            {{ semester.short_name ? semester.short_name : semester.name }}
          </div>
          <div class="w-20  py-4 px-5 text-right">
            {{ getEctsFromCourses(semester.courses) }}
          </div>
        </div>
        <div class="flex">
          <div class="w-28 px-5 py-4">
            Total
          </div>
          <div class="w-20 px-5 py-4 text-right">
            {{ statistics.ects }}
          </div>
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
import type { IThesisSelection } from '../../interfaces/theses.interface';
import type { ISemester } from '../../interfaces/semester.interface';
import type { IStatistics } from '../../interfaces/statistics.interface';
import { getEctsFromCourses } from '../../helpers/counts';

type Props = {
  semesterWithCourses: ISemester[];
  masterThesis: IThesisSelection;
  statistics: IStatistics;
}
defineProps<Props>();
</script>
