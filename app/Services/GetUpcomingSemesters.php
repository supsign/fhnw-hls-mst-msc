<?php

namespace App\Services;

use App\Enums\SemesterType;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class GetUpcomingSemesters
{
    protected Carbon $currentDate;
    protected int $numberOfSemesters;

    public function __invoke(int $numberOfSemesters = 10, Carbon $startDate = null): Collection
    {
        $this->currentDate = $startDate ?: Carbon::now()->subYears(2)->subMonths(6);
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
            Semester::firstOrCreate([
                'start_date' => Carbon::parse($i.'-'.SemesterType::AutumnStart->startDate()),
                'type' => SemesterType::AutumnStart->value
            ]);
            
            Semester::firstOrCreate([
                'start_date' => Carbon::parse($i.'-'.SemesterType::SpringStart->startDate()),
                'type' => SemesterType::SpringStart->value
            ]);
        }

        return $this;
    }
}