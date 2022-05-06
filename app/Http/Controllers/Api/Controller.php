<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudyMode;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\GetCourseData as GetCourseDataRequest;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\Courses\GetCourseData as GetCourseDataService;

class Controller extends BaseController
{
    public function getCourseData(
        Specialization $specialization, 
        GetCourseDataRequest $request, 
        GetCourseDataService $getCourseData
    ): array {
        return $getCourseData(
            $specialization,
            $request->semester ? Semester::find($request->semester) : null,
            $request->study_mode ? StudyMode::getByValue($request->study_mode) : null,
        );
    }

    public function getPersonalData()
    {
        
    }

    public function getThesisData(Specialization $specialization)
    {
        
    }
}
