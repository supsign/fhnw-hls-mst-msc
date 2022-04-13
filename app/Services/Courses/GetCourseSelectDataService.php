<?php

namespace App\Services\Courses;

use App\Enums\CourseGroupType;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\Semester;
use App\Models\Specialization;

class GetCourseSelectDataService
{
    protected CourseGroupType $courseGroupType;
    protected ?Semester $semester;
    protected Specialization $specialization;

    public function __invoke(CourseGroupType $courseGroupType, Specialization $specialization, Semester $semester = null): array
    {
        $this->courseGroupType = $courseGroupType;
        $this->semester = $semester;
        $this->specialization = $specialization;

        if (!$this->semester) {
            return $this->getCourseGroup()?->toArray() ?? [];
        }

        return $this->getCourseGroupFilterBySemester()?->toArray() ?? [];
    }

    protected function getCourseGroup(): ?CourseGroup
    {
        return CourseGroupSpecialization::where('specialization_id', $this->specialization->id)
            ->join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
            ->where('type', $this->courseGroupType->value)
            ->with(['courseGroup', 'courseGroup.courses', 'courseGroup.courses.semesters'])
            ->first()
                ?->courseGroup;
    }

    protected function getCourseGroupFilterBySemester(): ?CourseGroup
    {
        $courseGroup = $this->getCourseGroup();

        if (!$courseGroup) {
            return null;
        }

        $courseGroup->courses = $courseGroup->courses->filter(function ($course) {
            return $course->semesters->contains($this->semester);
        })->values();

        return $courseGroup;
    }
}