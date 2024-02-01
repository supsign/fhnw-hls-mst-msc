import type { ICourse } from './course.interface';
import type { IModel } from './model.interface';

export interface ISpecialization extends IModel {
  cluster_id: number;
  courses: ICourse[];
  name: string;
  short_name: string;
}
