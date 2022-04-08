<?php

namespace App\Services\Semesters;

class GetSemestersForSelectService
{
	public function __construct(
		protected GetCurrentSemesterService $getCurrentSemesterService, 
		protected GetUpcomingSemestersService $getUpcomingSemestersService
	) {
		
	}

	public function __invoke(int $numberOfSemesters = 8): array
	{
		$return = [];

		$semesters = ($this->getUpcomingSemestersService)($numberOfSemesters - 1)->prepend(
			($this->getCurrentSemesterService)()
		)->sortBy('start_date')->unique()->shift(8)->values();

		foreach ($semesters AS $semester) {
			$return[$semester->id] = $semester->name;
		}

		return $return;
	}
}