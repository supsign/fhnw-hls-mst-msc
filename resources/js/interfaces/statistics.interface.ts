import type { ICourseGroup } from './course.interface';

export interface IStatistics {
  cluster: number;
  core: number;
  ects: number;
  moduleGroupCount: ICourseGroup[];
  outside: number;
  specialization: number;
}
