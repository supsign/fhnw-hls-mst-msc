<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Specialization;
use App\Services\Courses\GetCourseData;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    public function getCourseDataForm(Specialization $specialization, GetCourseData $getCourseData): array
    {
        dd($getCourseData($specialization));

        return $getCourseData($specialization);
    }

    public function getPersonalDataForm()
    {
        
    }

    public function getThesisDataForm(Specialization $specialization)
    {
        
    }
}
