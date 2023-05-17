import type { ICourse, ICourseGroup } from '../interfaces/course.interface';
import type { IModuleOutside } from '../interfaces/moduleOutside.interface';
import type { ISemester } from '../interfaces/semester.interface';

export function getEcts(semestersWithCourses: ISemester[], modulesOutside: IModuleOutside[]) {
  let count = 0;
  for (const semester of semestersWithCourses) {
    count += getEctsFromCourses(semester.courses);
  }
  if (modulesOutside) {
    count += getEctsFromModulesOutside(modulesOutside);
  }
  return count;
}

export function getEctsFromCourses(courses: ICourse[]) {
  let ects = 0;
  for (const course of courses) {
    ects += course.ects;
  }
  return ects;
}

function getEctsFromModulesOutside(modulesOutside: IModuleOutside[]) {
  let count = 0;
  for (const module of modulesOutside) {
    count += module.ects;
  }
  return count;
}

export function getModuleGroupCount(groupsWithSelectedCourses: ICourseGroup[]) {
  const filterModules = groupsWithSelectedCourses.filter((group) => {
    // eslint-disable-next-line no-prototype-builtins
    if (group.hasOwnProperty('id')) {
      return group;
    }
  });
  return filterModules.map((module) => {
    module.count = module.courses.length;
    return module;
  });
}
