<?php

namespace App\Services\Semesters;

use App\Enums\Semester as EnumsSemester;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class GetUpcomingSemestersService
{
    protected Carbon $currentDate;
    protected int $numberOfSemesters;

    public function __invoke(int $numberOfSemesters = 10, Carbon $startDate = null): Collection
    {
        $this->currentDate = $startDate ?: Carbon::now();
        $this->numberOfSemesters = $numberOfSemesters;

        $semesters = Semester::whereDate('start_date', '>=', $this->currentDate)
            ->orderBy('start_date')
            ->limit($this->numberOfSemesters)
            ->get();

        if ($semesters->count() === $this->numberOfSemesters) {
            return $semesters;
        }

        $this->createSemesters();

        return $this($numberOfSemesters);
    }

    protected function createSemesters(): self
    {
        for (
            $i = $this->currentDate->year + (int)round($this->numberOfSemesters / 2);
            $i >= $this->currentDate->year;
            $i--
        ) {
            Semester::firstOrCreate(['start_date' => Carbon::parse($i.'-'.EnumsSemester::AutumnStart->value)]);
            Semester::firstOrCreate(['start_date' => Carbon::parse($i.'-'.EnumsSemester::SpringStart->value)]);
        }

        return $this;
    }
}