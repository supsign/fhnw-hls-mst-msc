<?php

namespace App\Services\Courses;

use App\Enums\CourseGroupType;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\Semester;
use App\Models\Specialization;
use Illuminate\Support\Collection;

class GetCourseSelectDataService
{
    protected CourseGroupType $courseGroupType;
    protected bool $invertSpecialization;
    protected ?Semester $semester;
    protected Specialization $specialization;

    public function __invoke(
        CourseGroupType $courseGroupType, 
        Specialization $specialization, 
        Semester $semester = null, 
        bool $invertSpecialization = false
    ): array {
        $this->courseGroupType = $courseGroupType;
        $this->invertSpecialization = $invertSpecialization;
        $this->semester = $semester;
        $this->specialization = $specialization;

        if ($this->invertSpecialization) {
            return $this->getCourses()->toArray() ?? [];
        }

        return $this->getCourseGroup()?->toArray() ?? [];
    }

    protected function getCourses(): Collection
    {
        $courses = $this->getCourseGroupSpecialization()->get()->pluck('courseGroup.courses')->flatten(1)->unique();

        if ($this->semester) {
            $courses = $courses->filter(function ($course) {
                return $course->semesters->contains($this->semester);
            });
        }

        return $courses->values();
    }

    protected function getCourseGroup(): ?CourseGroup
    {
        $courseGroup = $this->getCourseGroupSpecialization()->first()?->courseGroup;

        if ($this->semester && $courseGroup) {
            $courseGroup->courses = $courseGroup->courses->filter(function ($course) {
                return $course->semesters->contains($this->semester);
            })->values();
        }

        return $courseGroup;
    }

    protected function getCourseGroupSpecialization()
    {
        return CourseGroupSpecialization::join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
            ->where(function ($query) {
                if ($this->invertSpecialization) {
                    $query->whereNotIn('specialization_id', [$this->specialization->id]);
                } else {
                    $query->where('specialization_id', $this->specialization->id);
                }
            })
            ->where('type', $this->courseGroupType->value)
            ->with(['courseGroup', 'courseGroup.courses', 'courseGroup.courses.semesters']);
    }
}