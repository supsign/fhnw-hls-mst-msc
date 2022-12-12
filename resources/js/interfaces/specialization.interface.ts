import type { ICourse } from './course.interface';
import type { IModel } from './model.interface';

export interface ISpecialization extends IModel {
    name: String;
    short_name: String;
    cluster_id: number;
    courses: ICourse[];
}
