import type { IModel } from './model.interface';
import type { ISemester } from './semester.interface';
import type { IText } from './text.interface';

export interface IThesis extends IModel {
  name: string;
}

export interface IThesisDataResponse {
  texts: IText[];
  theses: IThesis[];
  time_frames: IThesisTimeFrame[];
}
export interface IThesisTimeFrame {
  end: string;
  start: ISemester | null;
}

export interface ThesisRequestData {
  specialization: number;
}

export interface IThesisSelection {
  furtherDetails: string;
  start: IThesisTimeFrame;
  theses1: IThesis;
  theses2: IThesis;
  theses3?: IThesis;
}
