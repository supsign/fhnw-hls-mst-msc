<?php

namespace App\Services\Semesters;

use App\Enums\Semester as EnumsSemester;
use App\Models\Semester;
use Carbon\Carbon;
use Exception;

class GetSemesterService
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

        return Semester::firstOrCreate([
            'start_date' => Carbon::parse($year.'-'.($autumnSemester ? EnumsSemester::AutumnStart->value : EnumsSemester::SpringStart->value)),
        ]);
    }
}