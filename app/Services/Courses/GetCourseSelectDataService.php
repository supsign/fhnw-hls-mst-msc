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

        if ($courseGroup && $this->semester) {
            $courseGroup->courses = $courseGroup->courses->filter(function ($course) {
                return $course->semesters->contains($this->semester);
            })->values();
        }

        return $courseGroup;
    }

    protected function getCourseGroups($ignoreInversion = false): Collection
    {
        $courseGroups = $this
            ->getCourseGroupSpecialization($ignoreInversion)
            ->get()
            ->pluck('courseGroup')
            ->filter(function ($courseGroup) {
                return $courseGroup->courses->count();
            });

        if (!$ignoreInversion) {
            $coursesInSpecialization = $this->getCourseGroups(true)->pluck('courses')->flatten()->pluck('id')->toArray();

            foreach ($courseGroups AS $courseGroup) {
                $courseGroup->courses_filtered = $courseGroup->courses->filter(function ($course) use ($coursesInSpecialization) {
                    return !in_array($course->id, $coursesInSpecialization);
                });

                $courseGroup->specialization = $courseGroup->specializations->first()->toArray();

                unset($courseGroup->courses);
                unset($courseGroup->specializations);
            }   
        }

        return $courseGroups->values();
    }

    protected function getCourseGroupSpecialization($ignoreInversion = false): Builder
    {
        return CourseGroupSpecialization::join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
            ->where(function ($query) use ($ignoreInversion) {
                if ($this->invertSpecialization && !$ignoreInversion) {
                    $query->whereNotIn('specialization_id', [$this->specialization->id]);
                } else {
                    $query->where('specialization_id', $this->specialization->id);
                }
            })->where(function ($query) use ($ignoreInversion) {
                if (!$ignoreInversion) {
                    $query->where('type', $this->courseGroupType->value);
                }
            })->with([
                'courseGroup', 
                'courseGroup.courses', 
                'courseGroup.courses.semesters', 
                'courseGroup.specializations'
            ]);
    }
}