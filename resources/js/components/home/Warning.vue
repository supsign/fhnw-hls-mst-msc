<template>
  <div class="my-10 bg-orange-400 p-3 shadow-xl">
    <h3>
      Warning
    </h3>
    <div class="flex flex-col">
      <div
        v-if="semestersWithOverlappingCourses.length > 0"
        class="flex flex-wrap gap-10">
        <div class="mb-3 font-bold">
          Overlapping Courses
        </div>
        <div
          v-for="(semester, index) in semestersWithOverlappingCourses"
          :key="index">
          <div v-if="semester.courses.length > 0">
            <div class="font-bold">
              {{ semester.semester.short_name ? semester.semester.short_name : semester.semester.name }}
            </div>
            <div
              v-for="(courses, sIdx) in semester.courses"
              :key="sIdx"
              class="mb-5">
              <div
                v-for="(course, cIdx) in courses"
                :key="cIdx">
                - {{ course.name }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="selectedLaterCount">
        <div class="mb-3 font-bold">
          You have selected modules with the undefined semester ('later'). Please correct for a final study
          plan
        </div>
      </div>
      <template v-if="blockCoursesAtEndOfSemester">
        <div class="font-bold">
          <template v-for="(course, index) in blockCoursesAtEndOfSemester.courses">
            Module {{ course.name
            }}<template
              v-if="blockCoursesAtEndOfSemester && index < blockCoursesAtEndOfSemester.courses.length - 1">
              ,
            </template>
          </template>
          <template v-if="blockCoursesAtEndOfSemester.courses.length === 1">
            is a block course
          </template><template v-else>
            are block courses
          </template> at the end of
          {{ blockCoursesAtEndOfSemester.long_name }}. Please consider taking another module as this might
          interfere with the start of your MSc thesis. A later start of the MSc thesis is possible but entails
          registering for another semester and the payment of an additional semester fee.
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ICourse, ISemester, ISemesterWithOverlappingCourses } from '@/interfaces';

type Props = {
  blockCoursesAtEndOfSemester?: ISemester & { courses: ICourse[] };
  selectedLaterCount: number;
  semestersWithOverlappingCourses: ISemesterWithOverlappingCourses[];
};
defineProps<Props>();
</script>
