<?php

namespace App\Services\Semesters;

use App\Enums\Semester as EnumsSemester;
use App\Models\Semester;
use Carbon\Carbon;

class GetCurrentSemesterService
{
    public function __invoke(): Semester
    {
        $now = Carbon::now();
        $semester = Semester::orderByDesc('start_date')->where('start_date', '<=', $now)->first();

        if ($semester) {
            return $semester;
        }

        $autumnSemesterStart = Carbon::parse($now->year.'-'.EnumsSemester::SpringStart->value);
        $springSemesterStart = Carbon::parse($now->year.'-'.EnumsSemester::AutumnStart->value);

        if ($autumnSemesterStart->isBefore($now)) {
            return Semester::create([
                'start_date' => $autumnSemesterStart,
            ]);
        }

        if ($springSemesterStart->isBefore($now)) {
            return Semester::create([
                'start_date' => $springSemesterStart,
            ]);
        }

        return Semester::create([
            'start_date' => $autumnSemesterStart->subYear(),
        ]);
    }
}