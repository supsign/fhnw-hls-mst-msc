<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseGroupType;
use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;

class TestController extends Controller
{
    public function test(GetCourseSelectDataService $getCourseSelectDataService): int
    {

        dump(
            $getCourseSelectDataService(
                Specialization::find(3),
            ),
            $getCourseSelectDataService(
                Specialization::find(3),
                furtherCourses: true
            )
        );

        return 1;
    }
}