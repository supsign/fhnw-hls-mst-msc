import type { IModel } from './model.interface';
import type { ISemester } from './semester.interface';
import type { ISpecialization } from './specialization.interface';
import type { IText } from './text.interface';
import type { IThesis } from './theses.interface';

export interface ICourseDataResponse {
  courses: ICourseGroup[][];
  optional_courses: { courses: ICourse[]; texts: IText[] };
  semesters: ISemester[];
  slots: ISlot[];
  texts: IText[];
  theses: IThesis;
}

export interface ICourseGroup extends IModel {
  clusters: ICluster[];
  count: number;
  course_group_type_short_name: string;
  course_group_type_tooltip: string;
  courses: ICourse[];
  description?: string;
  internal_name: string;
  name: string;
  required_courses_count: number;
  specializations: ISpecialization[];
  title: string;
  tooltip: string;
  type: number;
}

export interface ICourse extends IModel {
  block: boolean;
  content: string;
  ects: number;
  end_semester: ISemester;
  internal_name: string;
  name: string;
  selected_semester?: | ISemester | string;
  semester_type: number;
  short_name: string;
  slot_id: number;
  start_semester: ISemester;
  start_semester_id: number;
  type: number;
  type_label_short: string;
  type_tooltip: string;
}

export interface ISelectedCourses {
  course: ICourse;
  semester: ISemester;
}
export interface ICluster extends IModel {
  core_competences: string;
  courses: ICourse[];
  name: string;
}

export interface ISlot extends IModel {
  name: string;
}
export interface ISemesterWithOverlappingCourses {
  courses: ICourse[][];
  semester: ISemester;
}
