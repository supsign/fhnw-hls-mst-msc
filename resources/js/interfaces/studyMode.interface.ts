import type { IModel } from './model.interface';

export interface IStudyMode extends IModel {
    studyModes: { id: number; label: string }[];
    tooltip?: string;
}
