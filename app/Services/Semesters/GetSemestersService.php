<?php

namespace App\Services\Semesters;

use Carbon\Carbon;

class GetSemestersService
{
    public function __invoke(): array
    {
        $currentYear = Carbon::now()->year;
        $semesters = [];

        for ($i = 0, $n = 4; $i < $n; $i++) {
            $semesters[] = $currentYear + $i.' SS';
            $semesters[] = $currentYear + $i.' AS';
        }

        return $semesters;
    }
}