<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudyMode;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\GetCourseData as GetCourseDataRequest;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\GetCourseData as GetCourseDataService;
use App\Services\GetPersonalData;
use App\Services\GetThesisData;
use stdClass;

class Controller extends BaseController
{
    public function getCourseData(
        Specialization $specialization, 
        GetCourseDataRequest $request, 
        GetCourseDataService $getCourseData
    ): stdClass {
        return $getCourseData(
            $specialization,
            $request->semester ? Semester::find($request->semester) : null,
            $request->study_mode ? StudyMode::getByValue($request->study_mode) : null,
        );
    }

    public function getPersonalData(GetPersonalData $getPersonalData): stdClass
    {   
        return $getPersonalData();
    }

    public function getThesisData(Specialization $specialization, GetThesisData $getThesisData)//   :stdClass
    {
        // dump(
        //     $getThesisData(
        //         $specialization
        //     )
        // );

        // return 1;

        return $getThesisData(
            $specialization
        );
    }
}