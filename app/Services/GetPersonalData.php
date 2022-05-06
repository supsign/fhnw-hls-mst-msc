<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Models\Specialization;
use stdClass;

class GetPersonalData
{
    public function __construct(protected GetUpcomingSemesters $getUpcomingSemesters)
    {}

    public function __invoke(): stdClass 
    {
        return (object)[
            'semesters' => ($this->getUpcomingSemesters)(8),
            'studyModes' => StudyMode::asArray(),
            'specialization' => Specialization::all(),
        ];
    } 
}































