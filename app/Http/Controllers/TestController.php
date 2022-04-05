<?php

namespace App\Http\Controllers;

use App\Enums\CourseGroupType;
use App\Models\Specialization;
use App\Services\Courses\GetCoursesService;

class TestController extends Controller
{
    public function test(GetCoursesService $getCoursesService): int
    {
        dump(
            $getCoursesService(
                courseGroupType: CourseGroupType::Default,
                specialization: Specialization::find(1),
            )
        );

        return 1;
    }
}
