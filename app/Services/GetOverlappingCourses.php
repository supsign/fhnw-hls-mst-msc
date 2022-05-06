<?php

namespace App\Services;

use Illuminate\Support\Collection;

class GetOverlappingCourses
{
    public function __invoke(Collection $courses) 
    {
        $courses->load(['autumnSemesterSlot', 'springSemesterSlot']);

        dump($courses);

        foreach ($courses AS $course) {

        }


    }
}