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
    surname: string;
    givenName: string;
    semester: ISemester | undefined;
    specialization: ISpecialization | null;
    studyMode: { id: number; label: string } | null;
}

interface IText extends IModel {
    name: string;
    content: string;
}
