<?php

namespace App\Services\Courses;

class GetCourseIdsFromSelectedCourses
{
    public function __invoke(array $selectedCourses): array
    {
        return array_keys(
            array_filter(
                array_merge(
                    $selectedCourses['further'], ...$selectedCourses['main']
                ), 
                fn ($value) => $value !== 'none'
            )
        );
    }
}