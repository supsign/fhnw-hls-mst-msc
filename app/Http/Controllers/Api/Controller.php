<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudyMode;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\PostCourseData;
use App\Http\Requests\PostThesisData;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\GetCourseData;
use App\Services\GetOverlappingCourses;
use App\Services\GetPersonalData;
use App\Services\GetThesisData;
use stdClass;

class Controller extends BaseController
{
    public function getPersonalData(GetPersonalData $getPersonalData): stdClass
    {
        return $getPersonalData();
    }

    public function postCourseData(
        Specialization $specialization, 
        PostCourseData $request, 
        GetCourseData $getCourseData
    ): stdClass {
        return $getCourseData(
            $specialization,
            $request->semester ? Semester::find($request->semester) : null,
            $request->study_mode ? StudyMode::getByValue($request->study_mode) : null,
        );
    }

    public function postThesisData(Specialization $specialization, PostThesisData $request, GetThesisData $getThesisData): stdClass
    {
        return $getThesisData(
            $specialization,
            $request->double_degree,
            $request->semester ? Semester::find($request->semester) : null,
            $request->study_mode ? StudyMode::getByValue($request->study_mode) : null,
        );
    }
}
