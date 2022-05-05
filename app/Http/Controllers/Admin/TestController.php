<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use App\Services\Courses\PrepareCourseDataForWireModelService;

class TestController extends Controller
{
    public function test(GetCourseSelectDataService $getCourseSelectDataService, PrepareCourseDataForWireModelService $prepareCourseDataForWireModelService): int
    {
        $spec = Specialization::find(1);

        dump(
            $prepareCourseDataForWireModelService(
                $spec
            ),
        );

        return 1;
    }
}