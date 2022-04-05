<?php

namespace App\Services\Courses;

// use App\DataTransferObjects\Courses\CourseSelectData;
use App\Enums\CourseGroupType;
use App\Enums\CourseGroupType as EnumsCourseGroupType;
use App\Models\CourseCollection;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\Specialization;
use stdClass;

class GetCourseSelectDataService
{
    protected stdClass $data;
    protected EnumsCourseGroupType $courseGroupType;
    protected Specialization $specialization;

    public function __construct()
    {
        $this->data = new stdClass;
    }

    public function __invoke(CourseGroupType $courseGroupType, Specialization $specialization): array
    {
        $this->courseGroupType = $courseGroupType;
        $this->specialization = $specialization;

        return [
            $this->getCourseGroup()->toArray(),
            $this->getCourses()->toArray(),
        ];

        // return new CourseSelectData(
        //     courseGroup: $this->getCourseGroup(),
        //     courses: $this->getCourses(),
        // );
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

    protected function getCourses(): CourseCollection
    {
        return $this->getCourseGroup()->courses;
    }
}