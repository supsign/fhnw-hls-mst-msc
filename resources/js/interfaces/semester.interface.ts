import type { ICourse } from './course.interface';
import type{ IModel } from './model.interface';

export interface ISemester extends IModel {
    is_autumn_semester?: boolean;
    is_spring_semester?: boolean;
    is_replanning: boolean;
    long_name?: string;
    name: string;
    short_name?: string;
    start_date?: string;
    tooltip?: string;
    year?: number;
    type?: number;
    courses: ICourse[];
}
