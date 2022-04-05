<?php

namespace App\Services\Courses;

use App\Enums\CourseGroupType;
use App\Models\CourseCollection;
use App\Models\CourseGroupSpecialization;
use App\Models\Specialization;
use Illuminate\Support\Collection;

class GetCoursesService
{
    protected CourseGroupType $courseGroupType;
    protected Specialization $specialization;

    public function __invoke(CourseGroupType $courseGroupType, Specialization $specialization): CourseCollection
    {
        $this->courseGroupType = $courseGroupType;
        $this->specialization = $specialization;

        return new CourseCollection($this->queryCourses());
    }

    protected function queryCourses(): Collection
    {
        return CourseGroupSpecialization::where('specialization_id', $this->specialization->id)
            ->join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
            ->where('type', $this->courseGroupType->value)
            ->with(['courseGroup', 'courseGroup.courses'])
            ->get()
                ->pluck('courseGroup')
                ->pluck('courses')
                ->flatten();
    }
}