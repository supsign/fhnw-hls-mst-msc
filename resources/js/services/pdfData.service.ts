import type { ICourse, ICourseGroup, IModuleOutside, IPersonalData, ISemester, ISemesterWithOverlappingCourses, ISpecialization, IStatistics, IStudyMode, IThesisSelection, IThesisTimeFrame } from '@/interfaces';

import { validateData } from '../helpers/validation';

interface pdfDataServiceInput {
  additionalComments: string;
  doubleDegree: boolean;
  groupsWithSelectedCourses: ICourseGroup[];
  masterThesis: IThesisSelection;
  modulesOutside: IModuleOutside[];
  overlappingCourses: ISemesterWithOverlappingCourses[];
  personalData: Required<IPersonalData>;
  semestersWithCourses: (ISemester & { courses: ICourse[] })[];
  statistics: IStatistics;
}

interface IParsedOverlappingCourses {
  courses: number[][];
  semesterId: number | string;
}

export interface parsedPdfDataInput {
  additional_comments: string;
  double_degree: boolean;
  given_name: string;
  master_thesis: IThesisForPdf;
  modules_outside: IModuleOutside[];
  overlapping_courses: IParsedOverlappingCourses[];
  selected_courses: ISelectedCoursesForPdf[];
  semester: number;
  specialization: number;
  statistics: IStatistics;
  study_mode: number;
  surname: string;
}
interface ISelectedCoursesForPdf {
  courses: number[];
  semesterId: number | string;
}

interface IThesisForPdf {
  further_details: string;
  theses: (number | undefined)[];
  time_frames: IThesisTimeFrame;
}

export function pdfDataService(data: pdfDataServiceInput) {
  console.log(data);
  const parsedData: parsedPdfDataInput = {
    additional_comments: data.additionalComments,
    double_degree: data.doubleDegree,
    given_name: data.personalData.givenName,
    master_thesis: parseMasterThesis(data.masterThesis),
    modules_outside: data.modulesOutside,
    overlapping_courses: parseOverlappingCourses(data.overlappingCourses),
    selected_courses: parseSelectedCoursesForPdf(data.semestersWithCourses),
    semester: data.personalData.semester_id,
    specialization: data.personalData.specialization_id,
    statistics: data.statistics,
    study_mode: data.personalData.studyMode_id,
    surname: data.personalData.surname
  };
  const validator = validateData(parsedData);

  if (!validator.amount) {
    parsedData.statistics.moduleGroupCount = [];
    return parsedData;
  }
  return validator;
}
function parseMasterThesis(masterThesis: IThesisSelection): IThesisForPdf {
  const theses = [masterThesis.theses1_id, masterThesis.theses2_id];
  if (masterThesis.theses3_id) theses.push(masterThesis.theses3_id);
  return {
    further_details: masterThesis.furtherDetails,
    theses: theses,
    time_frames: masterThesis.start as IThesisTimeFrame
  };
}

// eslint-disable-next-line prefer-arrow/prefer-arrow-functions
function parseSelectedCoursesForPdf(semestersWithCourses: (ISemester & { courses: ICourse[] })[]): ISelectedCoursesForPdf[] {
  return semestersWithCourses.map((semester) => {
    return {
      courses: semester.courses.map((course) => {
        return course.id;
      }),
      semesterId: semester.id ?? semester.name
    };
  });
}

// eslint-disable-next-line prefer-arrow/prefer-arrow-functions
function parseOverlappingCourses(semesterWithOverlappingCourses: ISemesterWithOverlappingCourses[]) {
  return semesterWithOverlappingCourses
    .map((object) => {
      return {
        courses: object.courses.map((coursePair) => {
          return coursePair.map((course) => {
            return course.id;
          });
        }),
        semesterId: Object.prototype.hasOwnProperty.call(object.semester, 'id') ? object.semester.id : object.semester.name
      };
    })
    .filter((object) => {
      if (object.courses.length > 0) return object;
    });
}
