<?php

namespace App\Services;

use Illuminate\Support\Collection;

class GetOverlappingCourses
{
    public function __invoke(Collection $courses): Collection
    {
        $overlappingCourses = collect();

        if (!$courses->count()) {
            return $overlappingCourses;
        }

        foreach ($courses->load(['slot'])->groupBy('semester_type.name') AS $coursesBySemesterType) {
            foreach ($coursesBySemesterType->groupBy('slot.name') AS $slotName => $coursesBySlot) {
                if ($coursesBySlot->count() === 1) {
                    continue;
                }

                $overlappingCourses->push((object)[
                    'name' => $slotName,
                    'courses' => $coursesBySlot
                ]);
            }
        }

        return $overlappingCourses;
    }
}