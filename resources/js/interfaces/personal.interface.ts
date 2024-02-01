import type { IModel } from './model.interface';
import type { ISemester } from './semester.interface';
import type { ISpecialization } from './specialization.interface';
import type { IStudyMode } from './studyMode.interface';

export interface IPersonalDataResponse {
  semesters: ISemester[];
  specializations: ISpecialization[];
  studyMode: IStudyMode;
  texts: IText[];
}

export interface IPersonalData {
  givenName: string;
  semester_id?: number;
  specialization_id?: number;
  studyMode_id?: number;
  surname: string;
}

interface IText extends IModel {
  content: string;
  name: string;
}
