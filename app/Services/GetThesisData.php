<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Models\Semester;
use App\Models\Specialization;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use stdClass;

class GetThesisData
{
    protected bool $doubleDegree;
    protected ?Semester $semester;
    protected StudyMode $studyMode;

    public function __construct(protected GetUpcomingSemesters $getUpcomingSemesters)
    {}

    public function __invoke(
        Specialization $specialization,
        bool $doubleDegree,
        Semester $semester,
        StudyMode $studyMode
    ): stdClass {
        $this->doubleDegree = $doubleDegree;
        $this->semester = $semester;
        $this->studyMode = $studyMode;

        return (object)[
            'theses' => $specialization->theses,
            'starts' => $this->getStarts(),
            'end' => $this->getEnd(),
        ];
    }

    protected function getEnd()
    {
        return null;
    }

    protected function getStarts(): Collection
    {
        $count = $this->studyMode === StudyMode::FullTime ? 3 : 6;

        if ($this->doubleDegree) {
            $count++;
        }

        return ($this->getUpcomingSemesters)($count, $this->semester?->start_date);
    }
}