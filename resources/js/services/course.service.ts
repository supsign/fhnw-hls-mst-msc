import type { ICourse, ISemester, ISemesterWithOverlappingCourses, ISlot } from '@/interfaces';

export function getOverlappingCourses(semestersWithCourses: (ISemester & { courses: ICourse[] })[], slots: ISlot[]) {
  const semesterWithOverlappingCourses: ISemesterWithOverlappingCourses[] = [];
  for (const [index, semester] of semestersWithCourses.entries()) {
    if (semester.name !== 'later') {
      semesterWithOverlappingCourses.push({ courses: [], semester });
      for (const slot of slots) {
        const slotCourses = semester.courses.filter(course => course.slot_id === slot.id);
        if (slotCourses.length > 1) {
          semesterWithOverlappingCourses[index].courses.push(slotCourses);
        }
      }
    }

  }

  return semesterWithOverlappingCourses;
}
