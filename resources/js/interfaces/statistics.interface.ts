import type { ICourseGroup } from './course.interface';

export interface IStatistics {
    specialization: number;
    cluster: number;
    core: number;
    ects: number;
    outside: number;
    moduleGroupCount: ICourseGroup[];
}
