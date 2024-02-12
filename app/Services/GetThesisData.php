<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Specialization;
use stdClass;

class GetThesisData
{
    protected bool $doubleDegree;
    protected bool $early_start;
    protected ?Semester $semester;
    protected StudyMode $studyMode;

    public function __construct(protected GetUpcomingSemesters $getUpcomingSemesters)
    {}

    public function __invoke(
        Specialization $specialization,
        bool $doubleDegree,
        Semester $semester,
        StudyMode $studyMode,
        bool $early_start = false,
    ): stdClass {
        $this->doubleDegree = $doubleDegree;
        $this->early_start = $early_start;
        $this->semester = $semester;
        $this->studyMode = $studyMode;

        return (object)[
            'theses' => $specialization->theses,
            'time_frames' => $this->getStarts(),
            'texts' => PageContent::findByName([
                'thesis_text'
            ]),
        ];
    }

    protected function getStarts(): array
    {
        $timeFrames = [];
        $startSemester = $this->semester;

        if ($this->studyMode === StudyMode::PartTime) {
            $startSemester = $startSemester->next_semester->next_semester;
        }

        if ($this->doubleDegree) {
            $startSemester = $startSemester->next_semester;
        }

        if ($this->early_start) {
            $startSemester = $startSemester->previous_semester;
        }

        $availibleStartSemesters = ($this->getUpcomingSemesters)(3, $startSemester->start_date);

        foreach ($availibleStartSemesters AS $semester) {
            $semester->long_name = $semester->thesis_start;

            $timeFrames[] = (object)[
                'start' => $semester,
                'end' => $semester->thesis_end,
            ];
        }

        return $timeFrames;
    }
}
