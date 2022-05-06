<?php

namespace App\Services;

use App\Models\Specialization;
use stdClass;

class GetThesisData
{
    public function __construct(protected GetUpcomingSemesters $getUpcomingSemesters)
    {}

    public function __invoke(Specialization $specialization): stdClass 
    {
        return (object)[
            'theses' => $specialization->theses,
            'starts' => collect(),
        ];
    } 
}































