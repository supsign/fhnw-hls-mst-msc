<template>
  <div class="container mx-auto p-3 pb-10">
    <Personal v-model="personalData" @filled-or-changed="getCourseData" />
    <CourseSelection
      v-if="statistics && courseData"
      :course-data="courseData"
      :statistics="statistics" />
    <template v-if="courseData && masterThesisData">
      <ModulesOutside
        :texts="courseData.texts"
        @update-modules-outside-data="modulesOutside = $event" />
      <DoubleDegree
        v-model="doubleDegree"
        :texts="courseData.texts"
        @update:model-value="changedDoubleDegree" />
      <MasterThesis v-model="masterThesis" :data="masterThesisData" />
      <OptionalEnglish :course-data="courseData" />
      <AdditionalComments v-model="additionalComments" />
      <Statistics
        v-if="semesterWithCourses && statistics"
        :master-thesis="masterThesis"
        :semester-with-courses="semesterWithCourses"
        :statistics="statistics" />
      <Warning
        v-if="
          selectedLaterCount ||
          overlappingCourses.length > 0 ||
          (blockCoursesAtEndOfSemester && blockCoursesAtEndOfSemester.courses.length > 0)
        "
        :block-courses-at-end-of-semester="blockCoursesAtEndOfSemester"
        :selected-later-count="selectedLaterCount"
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
import { getEcts, getModuleGroupCount } from '@/helpers/counts';
import type {
  ICourse,
  ICourseDataResponse,
  ICourseGroup,
  IModuleOutside,
  IPersonalData,
  ISemester,
  IStatistics,
  IThesisDataResponse,
  IThesisSelection,
} from '@/interfaces';
import { getOverlappingCourses, pdfDataService } from '@/services';
import { useAxios } from '@vueuse/integrations/useAxios';
import Swal from 'sweetalert2';

const personalData = ref<IPersonalData>({ givenName: '', surname: '' });
const courseData = ref<ICourseDataResponse>();
const modulesOutside: Ref<IModuleOutside[]> = ref([]);
const doubleDegree = ref(false);
const masterThesisData = ref<IThesisDataResponse>();
const masterThesis: Ref<IThesisSelection> = ref({ furtherDetails: '' });
const additionalComments = ref<string>('');
const pdfErrors = ref<{ amount: number; errors: string[] }>();

const groupsWithSelectedCourses: ComputedRef<ICourseGroup[]> = computed(() => {
  if (!courseData.value) {
    return [];
  }

  const [groups, furtherGroups] = courseData.value.courses;
  const groupsWithSelected = groups.map((group) => ({
    ...group,
    courses: group.courses.filter((course) => course.selected_semester),
  }));

  const furtherGroupsWithSelected = furtherGroups.map((group) => {
    let courses: ICourse[] = [];
    if (group.specializations) {
      const specializationCourses = group.specializations.flatMap((spec) => spec.courses);
      courses = [...courses, ...specializationCourses];
    }
    if (group.clusters) {
      const clusterCourses = group.clusters.flatMap((cluster) => cluster.courses);
      courses = [...courses, ...clusterCourses];
    }
    return { ...group, courses: courses.filter((course) => course.selected_semester) };
  });

  return [...groupsWithSelected, ...furtherGroupsWithSelected];
});

const semesterWithCourses = computed<(ISemester & { courses: ICourse[] })[]>(() => {
  if (!groupsWithSelectedCourses.value || !courseData.value) {
    return [];
  }

  const selectedCourses = [
    ...groupsWithSelectedCourses.value.flatMap((group) => group.courses),
    ...courseData.value.optional_courses.courses.filter((optional) => optional.selected_semester),
  ];

  const coursesInSemester: (ISemester & { courses: ICourse[] })[] = courseData.value.semesters.map(
    (semester) => {
      const courses = selectedCourses.filter(
        (course) =>
          course.selected_semester &&
          typeof course.selected_semester !== 'string' &&
          course.selected_semester.id === semester.id
      );
      return { ...semester, courses };
    }
  );

  const laterCourses = selectedCourses.filter((course) => course.selected_semester === 'later');
  if (laterCourses.length > 0) {
    coursesInSemester.push({ courses: laterCourses, id: 0, is_replanning: false, name: 'later' });
  }

  return coursesInSemester;
});

const selectedLastSemesterCount = computed(() => {
  const lastSemester = courseData.value?.semesters.at(-1);
  if (!lastSemester) {
    return 0;
  }
  const lastSemesterEntry = semesterWithCourses.value?.find(
    (semester) => semester.id === lastSemester.id
  );
  return lastSemesterEntry?.courses.length || 0;
});

const selectedLaterCount = computed(() => {
  const laterSemester = semesterWithCourses.value?.find((semester) => semester.name === 'later');
  return laterSemester?.courses.length || 0;
});

const earlierThesisStartAllowed = computed(() => {
  if (selectedLastSemesterCount.value > 0) {
    return false;
  }
  if (selectedLaterCount.value > 0) {
    return false;
  }
  return true;
});

const statistics = computed<IStatistics | undefined>(() => {
  if (!groupsWithSelectedCourses.value) {
    return;
  }
  const allCourses = groupsWithSelectedCourses.value.flatMap((group) => group.courses);
  if (!semesterWithCourses.value || !modulesOutside.value) {
    return;
  }
  return {
    cluster: allCourses.filter((course) => course.type === 3).length,
    core: allCourses.filter((course) => course.type === 4).length,
    ects: getEcts(semesterWithCourses.value, modulesOutside.value),
    moduleGroupCount: getModuleGroupCount(groupsWithSelectedCourses.value),
    outside: modulesOutside.value.length,
    specialization: allCourses.filter((course) => course.type === 1).length,
  };
});

const overlappingCourses = computed(() => {
  if (!courseData.value) {
    return [];
  }
  const overlapping = getOverlappingCourses(semesterWithCourses.value, courseData.value.slots);
  if (overlapping.every((semester) => semester.courses.length === 0)) {
    return [];
  }
  return overlapping.filter((object) => Object.hasOwn(object.semester, 'id'));
});

const blockCoursesAtEndOfSemester = computed<(ISemester & { courses: ICourse[] }) | undefined>(
  () => {
    if (!semesterWithCourses.value) {
      return;
    }

    const lastEntry = semesterWithCourses.value.at(-1);
    const secondLastEntry = semesterWithCourses.value.at(-2);
    const source = lastEntry?.name === 'later' && secondLastEntry ? secondLastEntry : lastEntry;
    if (!source) {
      return;
    }

    const semester = structuredClone(source);
    semester.courses = semester.courses.filter(
      (course) => course.thesis_warning === 1 && semester.type === 1
    );

    return semester;
  }
);

function resetData() {
  modulesOutside.value = [];
  masterThesis.value = {
    furtherDetails: '',
    start: undefined,
    theses1_id: undefined,
    theses2_id: undefined,
    theses3_id: undefined,
  };
  additionalComments.value = '';
  doubleDegree.value = false;
}

function getErrorHtml(errorMessages: string[]) {
  let html = '';
  for (const error of errorMessages) {
    html += `<div class="text-red-500">${error}</div>`;
  }
  return html;
}

async function getThesisData(value: Required<IPersonalData>) {
  try {
    const { data } = await useAxios<IThesisDataResponse>(`/thesisdata/${value.specialization_id}`, {
      data: {
        double_degree: doubleDegree.value,
        early_start: earlierThesisStartAllowed.value,
        semester: value.semester_id,
        study_mode: value.studyMode_id,
      },
      method: 'POST',
    });
    if (data.value) {
      masterThesisData.value = data.value;
      const [firstTimeFrame] = data.value.time_frames;
      masterThesis.value.start = firstTimeFrame;
    }
  } catch {
    // Thesis data is optional enough to fail silently here.
  }
}

async function getCourseData(value: Required<IPersonalData>) {
  resetData();
  courseData.value = undefined;
  try {
    const { data } = await useAxios<ICourseDataResponse>(
      `/coursedata/${personalData.value.specialization_id}`,
      { data: { semester: value.semester_id, study_mode: value.studyMode_id }, method: 'POST' }
    );
    if (data.value) {
      courseData.value = data.value;
    }
    await getThesisData(value);
  } catch {
    // Course data fetch failure is surfaced by empty UI state.
  }
}

async function changedDoubleDegree() {
  const { value } = personalData;
  if (value?.studyMode_id && value.semester_id && value.specialization_id) {
    await getThesisData(value as Required<IPersonalData>);
  }
}

async function createPdf() {
  if (!personalData.value || !statistics.value) {
    return;
  }

  const pdfData = pdfDataService({
    additionalComments: additionalComments.value,
    doubleDegree: doubleDegree.value,
    groupsWithSelectedCourses: groupsWithSelectedCourses.value,
    masterThesis: {
      ...masterThesis.value,
      start: masterThesisData.value?.time_frames.find(
        (tf) => tf.start?.id === masterThesis.value.start?.start?.id
      ),
    },
    modulesOutside: modulesOutside.value,
    overlappingCourses: overlappingCourses.value,
    personalData: personalData.value as Required<IPersonalData>,
    semestersWithCourses: semesterWithCourses.value,
    statistics: statistics.value,
  });

  if (Object.hasOwn(pdfData, 'errors')) {
    pdfErrors.value = pdfData as { amount: number; errors: string[] };
    return await Swal.fire({
      confirmButtonText: 'OK',
      html: getErrorHtml(pdfErrors.value.errors),
      icon: 'error',
      title: 'Error!',
    });
  }

  const { data } = await useAxios<string>('/pdf', { data: pdfData, method: 'POST' });
  if (data.value) {
    window.open(data.value);
  }
}

watch(earlierThesisStartAllowed, async () => {
  await getThesisData(personalData.value as Required<IPersonalData>);
});
</script>
