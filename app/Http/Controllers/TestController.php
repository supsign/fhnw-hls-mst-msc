<?php

namespace App\Http\Controllers;

use App\Models\CourseGroup;
use App\Enums\CourseGroupType;
use App\Helpers\GeneralHelper;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request): int
    {
        // $cg = CourseGroup::where('course_group_type_id', CourseGroupType::CoreCompetences)->first();

        dump(
            CourseGroupType::withoutDefault()
        );


        // foreach (CourseGroupType::cases() AS $case) {

        //     dump(
        //         $case->label(),
        //         // GeneralHelper::splitStringOnUppercase($case->name, ' ', 'ucfirst')

        //     );

        // }




        return 1;
    }
}
