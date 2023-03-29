import type { ICourse } from './course.interface';
import type { IModel } from './model.interface';

export interface ISpecialization extends IModel {
    name: string;
    short_name: string;
    cluster_id: number;
    courses: ICourse[];
}
