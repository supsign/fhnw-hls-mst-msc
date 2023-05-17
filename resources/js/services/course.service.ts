import type { ISlot, ISemesterWithOverlappingCourses } from '../interfaces/course.interface';
import type { ISemester } from '../interfaces/semester.interface';

export function getOverlappingCourses(semestersWithCourses: ISemester[], slots: ISlot[]) {
  const semesterWithOverlappingCourses: ISemesterWithOverlappingCourses[] = [];
  semestersWithCourses.forEach((semester, index) => {
    semesterWithOverlappingCourses.push({ semester, courses: [] });
    for (const slot of slots) {
      const slotCourses = semester.courses.filter((course) => course.slot_id === slot.id);
      if (slotCourses.length > 1) {
        semesterWithOverlappingCourses[index].courses.push(slotCourses);
      }
    }
  });

  return semesterWithOverlappingCourses;
}
