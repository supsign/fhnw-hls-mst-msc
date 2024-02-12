import type { IModel } from './model.interface';

export interface ISemester extends IModel {
  is_replanning: boolean;
  long_name?: string;
  long_name_with_short?: string;
  name: string;
  short_name?: string;
  start_date?: string;
  tooltip?: string;
  type?: number;
  year?: number;
}
