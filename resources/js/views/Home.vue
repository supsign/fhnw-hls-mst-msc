<template>
  <div class="container mx-auto p-3 pb-10">
    <Personal v-model="personalData" @get-course-data="getCourseData" />
    <CourseSelection v-if="statistics && courseData" :course-data="courseData" :statistics="statistics" />
    <template v-if="courseData && masterThesisData">
      <ModulesOutside :texts="courseData.texts" @update-modules-outside-data="updateModulesOutsideData" />
      <DoubleDegree v-model="doubleDegree" :texts="courseData.texts" />
      <MasterThesis v-model="masterThesis" :data="masterThesisData" />
      <OptionalEnglish :course-data="courseData" />
      <AdditionalComments v-model="additionalComments" />
      <Statistics
        v-if="semesterWithCourses && statistics" :master-thesis="masterThesis"
        :semester-with-courses="semesterWithCourses" :statistics="statistics" />
      <Warning
        v-if="selectedLaterCount || overlappingCourses.length > 0 || blockCoursesAtEndOfSemester?.courses.length"
        :block-courses-at-end-of-semester="blockCoursesAtEndOfSemester" :selected-later-count="selectedLaterCount"
        :semesters-with-overlapping-courses="overlappingCourses" />
      <div class="flex justify-end">
        <button
          class="flex min-h-[50px] w-1/2 items-center justify-center bg-black text-center font-medium leading-4 text-white hover:bg-primary hover:text-black"
          type="button"
          @click="createPdf">
          <span class="">Generate PDF Document of Study Plan</span>
        </button>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import Swal from 'sweetalert2';
import { computed, type ComputedRef, ref, type Ref, watch } from 'vue';

import type { ICourseDataResponse, ICourseGroup } from '../interfaces/course.interface';
import type { IModuleOutside } from '../interfaces/moduleOutside.interface';
import type { IPersonalData } from '../interfaces/personal.interface';
import type { ISemester } from '../interfaces/semester.interface';
import type { IStatistics } from '../interfaces/statistics.interface';
import type { IThesisDataResponse, IThesisSelection } from '../interfaces/theses.interface';

import AdditionalComments from '../components/home/AdditionalComments.vue';
import CourseSelection from '../components/home/CourseSelection.vue';
import DoubleDegree from '../components/home/DoubleDegree.vue';
import MasterThesis from '../components/home/MasterThesis.vue';
import ModulesOutside from '../components/home/ModulesOutside.vue';
import OptionalEnglish from '../components/home/OptionalEnglish.vue';
import Personal from '../components/home/Personal.vue';
import Statistics from '../components/home/Statistics.vue';
import Warning from '../components/home/Warning.vue';
import { getEcts, getModuleGroupCount } from '../helpers/counts';
import { getOverlappingCourses } from '../services/course.service';
import { pdfDataService } from '../services/pdfData.service';

// Personal Data
const personalData: Ref<IPersonalData> = ref({
  givenName: '',
  semester: undefined,
  specialization: null,
  studyMode: null,
  surname: ''
});

// Course Data
const courseData: Ref<ICourseDataResponse | null> = ref(null);

async function getCourseData() {
  resetData();
  if (!personalData.value.specialization || !personalData.value.studyMode || !personalData.value.semester) {
    return;
  }
  courseData.value = null;
  const response = await axios.post<ICourseDataResponse>(`/coursedata/${personalData.value.specialization.id}`, {
    semester: personalData.value.semester.id,
    study_mode: personalData.value.studyMode.id
  });
  courseData.value = response.data;
  getThesisData();
}

// Modules Outside
const modulesOutside: Ref<IModuleOutside[]> = ref([]);

function updateModulesOutsideData(data: IModuleOutside[]) {
  modulesOutside.value = data;
}

// Double Degree
const doubleDegree = ref(false);
watch(doubleDegree, () => getThesisData());

// Master Thesis
const masterThesisData: Ref<IThesisDataResponse | null> = ref(null);
const masterThesis: Ref<IThesisSelection> = ref({
  furtherDetails: '',
  start: { end: '', start: null },
  theses: []
});

async function getThesisData() {
  if (!personalData.value?.specialization || !personalData.value.semester || !personalData.value.studyMode) {
    return;
  }
  const response = await axios.post<IThesisDataResponse>(`/thesisdata/${personalData.value.specialization.id}`, {
    double_degree: doubleDegree.value,
    semester: personalData.value?.semester.id,
    study_mode: personalData.value?.studyMode.id
  });
  masterThesisData.value = response.data;
  masterThesis.value.start = response.data.time_frames[0];
}

// AdditionalComments
const additionalComments = ref();

// Statistics
const statistics: ComputedRef<IStatistics | null> = computed(() => {
  if (!groupsWithSelectedCourses.value) {
    return null;
  }
  const allCourses = groupsWithSelectedCourses.value
    .flatMap((group) => {
      return group.courses;
    });
  if (!semesterWithCourses.value || !modulesOutside.value) {
    return null;
  }
  return {
    cluster: allCourses.filter(course => course.type === 3).length,
    core: allCourses.filter(course => course.type === 4).length,
    ects: getEcts(semesterWithCourses.value, modulesOutside.value),
    moduleGroupCount: getModuleGroupCount(groupsWithSelectedCourses.value),
    outside: modulesOutside.value.length,
    specialization: allCourses.filter(course => course.type === 1).length
  };
});

const groupsWithSelectedCourses: ComputedRef<ICourseGroup[]> = computed(() => {
  if (!courseData.value) {
    return [];
  }
  const courseDataGroups: ICourseDataResponse = JSON.parse(JSON.stringify(courseData.value));
  const groups = courseDataGroups.courses[0];
  const groupsWithSelected = groups.map((group) => {
    group.courses = group.courses.filter((course) => {
      if (course.selected_semester) {
        return course;
      }
    });
    return group;
  });
  const furtherGroups = courseDataGroups.courses[1];
  const furtherGroupsWithSelected = furtherGroups.map((group) => {
    // eslint-disable-next-line no-prototype-builtins
    if (group.hasOwnProperty('specializations')) {
      group.courses = group.specializations
        .flatMap((spec) => {
          return spec.courses;
        })
        .filter((course) => {
          if (course.selected_semester) {
            return course;
          }
        });
    }
    // eslint-disable-next-line no-prototype-builtins
    if (group.hasOwnProperty('clusters')) {
      group.courses = group.clusters
        .flatMap((clusters) => {
          return clusters.courses;
        })
        .filter((course) => {
          if (course.selected_semester) {
            return course;
          }
        });
    }
    return group;
  });
  return groupsWithSelected.concat(furtherGroupsWithSelected);
});

const semesterWithCourses: ComputedRef<ISemester[]> = computed(() => {
  if (!groupsWithSelectedCourses.value || !courseData.value) {
    return null;
  }
  const courses = [];
  for (const group of groupsWithSelectedCourses.value) {
    courses.push(group.courses);
  }
  for (const optional of courseData.value.optional_courses.courses) {
    if (optional.selected_semester) {
      courses.push(optional);
    }
  }

  const selectedCourses = courses.flat(1);
  const coursesInSemester: ISemester[] = courseData.value.semesters.map((semester) => {
    const courses = selectedCourses.filter(course => course.selected_semester.id === semester.id);
    return {
      ...semester,
      courses: courses
    };
  });
  coursesInSemester.push({
    courses: selectedCourses.filter((course) => {
      return course.selected_semester === 'later';
    }),
    id: 0,
    is_replanning: false,
    name: 'later'
  });

  return coursesInSemester;
});

// Warning
const overlappingCourses = computed(() => {
  if (!courseData.value) {
    return [];
  }
  const overlapping = getOverlappingCourses(semesterWithCourses.value, courseData.value.slots);
  if (overlapping.every(semester => semester.courses.length === 0)) {
    return [];
  }
  return overlapping.filter((object) => {
    // eslint-disable-next-line no-prototype-builtins
    if (object.semester.hasOwnProperty('id')) {
      return object;
    }
  });
});

const blockCoursesAtEndOfSemester: ComputedRef<ISemester | null> = computed(() => {
  if (!semesterWithCourses.value) {
    return null;
  }
  const semester: ISemester = JSON.parse(
    JSON.stringify(semesterWithCourses.value.at(-2))
  );

  semester.courses = semester.courses.filter((course) => {
    if (course.block) {
      return course;
    }
  });
  return semester;
});

const selectedLaterCount = computed(() => {
  if (!semesterWithCourses.value) {
    return 0;
  }
  const laterSemester = semesterWithCourses.value.find(semester => semester.name === 'later');
  if (!laterSemester) {
    return 0;
  }
  return laterSemester.courses.length;
});

const errors = ref();
async function createPdf() {
  if (!personalData.value) {
    return;
  }
  const pdfData = ref();
  if (!statistics.value) {
    return;
  }
  pdfData.value = pdfDataService({
    additionalComments: additionalComments.value,
    doubleDegree: doubleDegree.value,
    groupsWithSelectedCourses: groupsWithSelectedCourses.value,
    masterThesis: masterThesis.value,
    modulesOutside: modulesOutside.value,
    overlappingCourses: overlappingCourses.value,
    personalData: personalData.value,
    semestersWithCourses: semesterWithCourses.value,
    statistics: statistics.value
  });
  // eslint-disable-next-line no-prototype-builtins
  if (pdfData.value.hasOwnProperty('errors')) {
    errors.value = pdfData.value;
    Swal.fire({
      confirmButtonText: 'OK',
      html: getErrorHtml(pdfData.value.errors),
      icon: 'error',
      title: 'Error!'
    });
    return;
  }
  const filename = await axios.post('/pdf', pdfData.value);
  window.open(filename.data);
}
function resetData() {
  modulesOutside.value = [];
  masterThesis.value = { furtherDetails: '', start: { end: '', start: null }, theses: [] };
  additionalComments.value = '';
  doubleDegree.value = false;
}

function getErrorHtml(errors: unknown[]) {
  let string = '';
  for (const error of errors) {
    string += `<div class="text-red-500">${error}</div>`;
  }
  return string;
}
</script>
