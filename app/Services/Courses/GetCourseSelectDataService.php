<?php

namespace App\Services\Courses;

use App\Enums\CourseGroupType;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\Specialization;

class GetCourseSelectDataService
{
    protected CourseGroupType $courseGroupType;
    protected Specialization $specialization;

    public function __invoke(CourseGroupType $courseGroupType, Specialization $specialization): array
    {
        $this->courseGroupType = $courseGroupType;
        $this->specialization = $specialization;

        return $this->getCourseGroup()->toArray();
    }

    protected function getCourseGroup(): CourseGroup
    {
        return CourseGroupSpecialization::where('specialization_id', $this->specialization->id)
            ->join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
            ->where('type', $this->courseGroupType->value)
            ->with(['courseGroup', 'courseGroup.courses'])
            ->first()
                ->courseGroup;
    }
}