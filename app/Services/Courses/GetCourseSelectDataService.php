<?php

namespace App\Services\Courses;

use App\Enums\CourseGroupType;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\Semester;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Builder;
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
            return $this->getCourseGroups()->toArray() ?? [];
        }

        return $this->getCourseGroup()?->toArray() ?? [];
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

    protected function getCourseGroups(): Collection
    {
        $courseGroups = $this
            ->getCourseGroupSpecialization()
            ->get()
            ->pluck('courseGroup')
            ->filter(function ($courseGroup) {
                return $courseGroup->courses->count();
            });

        ////  should be obsolete
        // foreach ($courseGroups AS $courseGroup) {
        //     $courseGroup->courses = $courseGroup->courses->filter(function ($course) {
        //         return $course->specialization_id !== $this->specialization->id;
        //     });
        // }

        return $courseGroups->values();
    }

    protected function getCourseGroupSpecialization(): Builder
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