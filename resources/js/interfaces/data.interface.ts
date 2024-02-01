import type { ISemester } from './semester.interface';
import type { ISpecialization } from './specialization.interface';
import type { IStudyMode } from './studyMode.interface';
import type { IText } from './text.interface';

export interface IData {
  semesters: ISemester[];
  specializations: ISpecialization[];
  studyMode: IStudyMode[];
  texts: IText[];
}
