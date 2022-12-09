import type { IModel } from './model.interface';
import type { ISemester } from './semester.interface';
import type { ISpecialization } from './specialization.interface';
import type { IText } from './text.interface';
import type  { IThesis } from './theses.interface';

export interface ICourseDataResponse {
    courses: ICourseGroup[][];
    semesters: ISemester[];
    texts: IText[];
    slots: ISlot[];
    theses: IThesis;
    optional_courses: { courses: ICourse[]; texts: IText[] };
}

export interface ICourseGroup extends IModel {
    count: number;
    course_group_type_short_name: string;
    course_group_type_tooltip: string;
    courses: ICourse[];
    description?: string;
    internal_name: string;
    name: string;
    required_courses_count: number;
    title: string;
    type: number;
    specializations: ISpecialization[];
    clusters: ICluster[];
    tooltip: string;
}

export interface ICourse extends IModel {
    content: string;
    ects: number;
    end_semester: ISemester;
    internal_name: string;
    name: string;
    short_name: string;
    semester_type: number;
    selected_semester: ISemester | String | null | any;
    slot_id: number;
    start_semester: ISemester
    start_semester_id: number;
    type_label_short: string;
    type_tooltip: string;
    type: number;
    block: boolean;
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
    semester: ISemester;
    courses: ICourse[][];
}
