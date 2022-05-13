<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Models\Semester;
use App\Models\Specialization;
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
        $startSemester = $this->semester;

        if ($this->studyMode === StudyMode::PartTime) {
            $count = 6;
            $startSemester = $startSemester->nextSemester;
        } else {
            $count = 3;
        }

        if ($this->doubleDegree) {
            $startSemester = $startSemester->nextSemester;
        }

        $availibleStartSemesters = ($this->getUpcomingSemesters)($count, $startSemester->start_date);

        foreach ($availibleStartSemesters AS $semester) {
            $semester->long_name = $semester->thesisStart;

            $timeFrames[] = (object)[
                'start' => $semester,
                'end' => $semester->thesis_end,
            ];
        }

        return $timeFrames;
    }
}