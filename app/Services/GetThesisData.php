<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\Specialization;
use Illuminate\Support\Collection;
use stdClass;

class GetThesisData
{
    protected bool $doubleDegree;
    protected ?Semester $semester;

    public function __construct(protected GetUpcomingSemesters $getUpcomingSemesters)
    {}

    public function __invoke(
        Specialization $specialization, 
        bool $doubleDegree = null,
        Semester $semester = null
    ): stdClass {
        $this->doubleDegree = (bool)$doubleDegree;
        $this->semester = $semester;

        return (object)[
            'theses' => $specialization->theses,
            'starts' => $this->getStarts(),
        ];
    }

    protected function getStarts(): Collection
    {

        return collect();
    }
}