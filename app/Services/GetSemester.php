<?php

namespace App\Services;

use App\Enums\SemesterType;
use App\Models\Semester;
use Carbon\Carbon;
use Exception;

class GetSemester
{
    public function __invoke(int $year, bool $autumnSemester = false): Semester
    {
        $currentYear = (string)Carbon::now()->year;

        while(($i = strlen($year)) < strlen($currentYear)) {
            $year = $currentYear[$i - 1].$year;
        }

        if ($year > 2037) {
            throw new Exception('https://en.wikipedia.org/wiki/Year_2038_problem');
        }

        $type = $autumnSemester ? SemesterType::AutumnStart : SemesterType::SpringStart;

        return Semester::firstOrCreate([
            'start_date' => Carbon::parse($year.'-'.($type->startDate())),
            'type' => $type->value,
        ]);
    }
}