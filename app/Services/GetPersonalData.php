<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Models\PageContent;
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
            'studyMode' => (object)[
                'tooltip' => PageContent::getContentByName('study_mode_tooltip'),
                'studyModes' => StudyMode::asArray(),
            ],
            'specializations' => Specialization::all(),
            'texts' => PageContent::findByName([
                'intro_title',
                'intro_content',
                'hints_title',
                'hints_list'
            ]),
        ];
    } 
}
