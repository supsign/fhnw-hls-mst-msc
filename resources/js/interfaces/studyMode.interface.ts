import { IModel } from './model.interface';

export interface IStudyMode extends IModel {
    studyModes: Array<{ id: number; label: string }>;
    tooltip?: String;
}
