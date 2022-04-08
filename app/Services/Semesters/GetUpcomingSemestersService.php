<?php

namespace App\Services\Semesters;

use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class GetUpcomingSemestersService
{
    protected Carbon $currentDate;
    protected int $numberOfSemesters;
    protected Collection $semesters;

    public function __construct(protected GetCurrentSemestersService $getCurrentSemestersService)
    {
        
    }

    public function __invoke(int $numberOfSemesters = 8): Collection
    {
        $this->currentDate = Carbon::now();
        $this->numberOfSemesters = $numberOfSemesters;

        $this->semesters = Semester::whereDate('start_date', '>', $this->currentDate)
            ->orderBy('start_date')
            ->limit($this->numberOfSemesters)
            ->get();

        if ($this->semesters->count() === $this->numberOfSemesters) {
            return $this->semesters;
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
            Semester::firstOrCreate(['start_date' => Carbon::parse($i.'-02-01')]);
            Semester::firstOrCreate(['start_date' => Carbon::parse($i.'-09-01')]);
        }

        return $this;
    }
}