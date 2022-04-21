<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseGroupType;
use App\Http\Controllers\Controller;
use App\Models\CourseGroup;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;

class TestController extends Controller
{
    public function test(GetCourseSelectDataService $getCourseSelectDataService): int
    {
        // $courseGroup = CourseGroup::find(4);

        // $courseGroup->courses_filtered = 'test';

        // dump(
        //     $courseGroup->coursesFiltered,
        //     $courseGroup->toArray(),
        // );

        // return 1;

        dump(
            // $getCourseSelectDataService(
            //     CourseGroupType::Specialization, 
            //     Specialization::find(1), 
            //     null,
            // ),

            $getCourseSelectDataService(
                CourseGroupType::Specialization, 
                Specialization::find(1), 
                null,
                true
            )
        );

        return 1;
    }
}
