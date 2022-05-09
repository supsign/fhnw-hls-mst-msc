<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Models\Semester;
use App\Models\Specialization;
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
            'time_frames' => $this->getStarts(),
        ];
    }

    protected function getStarts(): array
    {
        $timeFrames = [];

        $startSemester = ($this->getUpcomingSemesters)(
            $this->studyMode === StudyMode::FullTime ? 2 : 4,
            $this->semester->start_date
        )->last();

        $count = $this->studyMode === StudyMode::FullTime ? 3 : 6;

        if ($this->doubleDegree) {
            $count++;
            $startSemester = $startSemester->nextSemester;
        }

        $availibleStartSemesters = ($this->getUpcomingSemesters)($count, $startSemester->start_date);

        foreach ($availibleStartSemesters AS $semester) {
            $timeFrames[] = (object)[
                'start' => $semester,
                'end' => $semester->start_date->addMonth(6),
            ];
        }

        return $timeFrames;
    }
}