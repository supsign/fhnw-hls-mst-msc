<template>
  <div class="container mx-auto p-3 pb-10">
    <Card>
      <Personal
        v-model="personalData"
        @getCourseData="getCourseData" />
    </Card>
    <Card v-if="courseData">
      <CourseSelection
        v-if="statistics"
        :course-data="courseData"
        :statistics="statistics" />
    </Card>
    <Card v-if="courseData && masterThesisData">
      <ModulesOutside
        :texts="courseData.texts"
        @updateModulesOutsideData="updateModulesOutsideData" />
      <DoubleDegree
        v-model="doubleDegree"
        :texts="courseData.texts" />
      <MasterThesis
        v-model="masterThesis"
        :data="masterThesisData" />
      <OptionalEnglish :course-data="courseData" />
      <AdditionalComments v-model="additionalComments" />
      <Statistics
        v-if="semesterWithCourses && statistics"
        :semester-with-courses="semesterWithCourses"
        :master-thesis="masterThesis"
        :statistics="statistics" />
      <Warning
        v-if="selectedLaterCount || overlappingCourses.length || blockCoursesAtEndOfSemester?.courses.length"
        :semesters-with-overlapping-courses="overlappingCourses"
        :selected-later-count="selectedLaterCount"
        :block-courses-at-end-of-semester="blockCoursesAtEndOfSemester" />
      <div class="flex justify-end">
        <button
          type="button"
          class="w-60 cursor-pointer rounded-md bg-blue-700 py-1 px-4 text-white shadow-sm transition duration-300 ease-in-out hover:bg-blue-800 hover:shadow-xl"
          @click="createPdf">
          Submit
        </button>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import Personal from '../components/home/Personal.vue';
import axios from 'axios';
import { type Ref, ref, watch, computed, type ComputedRef } from 'vue';
import ModulesOutside from '../components/home/ModulesOutside.vue';
import DoubleDegree from '../components/home/DoubleDegree.vue';
import OptionalEnglish from '../components/home/OptionalEnglish.vue';
import AdditionalComments from '../components/home/AdditionalComments.vue';
import type { ICourseDataResponse, ICourseGroup } from '../interfaces/course.interface';
import MasterThesis from '../components/home/MasterThesis.vue';
import type { IThesisDataResponse, IThesisSelection } from '../interfaces/theses.interface';
import CourseSelection from '../components/home/CourseSelection.vue';
import Card from '../components/base/Card.vue';
import { pdfDataService } from '../services/pdfData.service';
import Statistics from '../components/home/Statistics.vue';
import Swal from 'sweetalert2';
import type { IPersonalData } from '../interfaces/personal.interface';
import type { IModuleOutside } from '../interfaces/moduleOutside.interface';
import type { ISemester } from '../interfaces/semester.interface';
import Warning from '../components/home/Warning.vue';
import { getOverlappingCourses } from '../services/course.service';
import type { IStatistics } from '../interfaces/statistics.interface';
import { getEcts, getModuleGroupCount } from '../helpers/counts';
// Personal Data
const personalData: Ref<IPersonalData> = ref({
  surname: '',
  givenName: '',
  semester: undefined,
  studyMode: null,
  specialization: null
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
    study_mode: personalData.value.studyMode.id,
    semester: personalData.value.semester.id
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
  start: { start: null, end: '' },
  theses: [],
  furtherDetails: ''
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
    .map((group) => {
      return group.courses;
    })
    .flat(1);
  if (!semesterWithCourses.value || !modulesOutside.value) {
    return null;
  }
  return {
    specialization: allCourses.filter((course) => course.type === 1).length,
    core: allCourses.filter((course) => course.type === 4).length,
    cluster: allCourses.filter((course) => course.type === 3).length,
    outside: modulesOutside.value.length,
    ects: getEcts(semesterWithCourses.value, modulesOutside.value),
    moduleGroupCount: getModuleGroupCount(groupsWithSelectedCourses.value)
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
    if (group.hasOwnProperty('specializations')) {
      group.courses = group.specializations
        .map((spec) => {
          return spec.courses;
        })
        .flat(1)
        .filter((course) => {
          if (course.selected_semester) {
            return course;
          }
        });
    }
    if (group.hasOwnProperty('clusters')) {
      group.courses = group.clusters
        .map((clusters) => {
          return clusters.courses;
        })
        .flat(1)
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

// @ts-expect-error: ???
const semesterWithCourses: ComputedRef<ISemester[]> = computed(() => {
  if (!groupsWithSelectedCourses.value || !courseData.value) {
    return null;
  }
  const courses = [];
  for (let group of groupsWithSelectedCourses.value) {
    courses.push(group.courses);
  }
  for (let optional of courseData.value.optional_courses.courses) {
    if (optional.selected_semester) {
      courses.push(optional);
    }
  }

  const selectedCourses = courses.flat(1);
  const coursesInSemester: ISemester[] = courseData.value.semesters.map((semester) => {
    const courses = selectedCourses.filter((course) => course.selected_semester.id === semester.id);
    return {
      ...semester,
      courses: courses
    };
  });
  coursesInSemester.push({
    id: 0,
    name: 'later',
    courses: selectedCourses.filter((course) => {
      return course.selected_semester === 'later';
    }),
    is_current: false
  });

  return coursesInSemester;
});

// Warning
const overlappingCourses = computed(() => {
  if (!courseData.value) {
    return [];
  }
  const overlapping = getOverlappingCourses(semesterWithCourses.value, courseData.value.slots);
  if (overlapping.every((semester) => semester.courses.length === 0)) {
    return [];
  }
  return overlapping.filter((obj) => {
    if (obj.semester.hasOwnProperty('id')) {
      return obj;
    }
  });
});

const blockCoursesAtEndOfSemester: ComputedRef<ISemester | null> = computed(() => {
  if (!semesterWithCourses.value) {
    return null;
  }
  const semester: ISemester = JSON.parse(
    JSON.stringify(semesterWithCourses.value[semesterWithCourses.value.length - 2])
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
  const laterSemester = semesterWithCourses.value.find((semester) => semester.name === 'later');
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
    personalData: personalData.value,
    semestersWithCourses: semesterWithCourses.value,
    modulesOutside: modulesOutside.value,
    doubleDegree: doubleDegree.value,
    masterThesis: masterThesis.value,
    additionalComments: additionalComments.value,
    groupsWithSelectedCourses: groupsWithSelectedCourses.value,
    statistics: statistics.value,
    overlappingCourses: overlappingCourses.value
  });
  if (pdfData.value.hasOwnProperty('errors')) {
    errors.value = pdfData.value;
    Swal.fire({
      title: 'Error!',
      html: getErrorHtml(pdfData.value.errors),
      icon: 'error',
      confirmButtonText: 'OK'
    });
    return;
  }
  const filename = await axios.post('/pdf', pdfData.value);
  window.open(filename.data);
}
function resetData() {
  modulesOutside.value = [];
  masterThesis.value = { start: { start: null, end: '' }, theses: [], furtherDetails: '' };
  additionalComments.value = '';
  doubleDegree.value = false;
}

function getErrorHtml(errors: any) {
  let string = '';
  for (let error of errors) {
    string += `<div class="text-red-500">${error}</div>`;
  }
  return string;
}
</script>
