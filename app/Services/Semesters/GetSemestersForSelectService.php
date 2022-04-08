<?php

namespace App\Services\Semesters;

class GetSemestersForSelectService
{
	public function __construct(
		protected GetCurrentSemesterService $getCurrentSemesterService, 
		protected GetUpcomingSemestersService $getUpcomingSemestersService
	) {
		
	}

	public function __invoke(int $numberOfSemesters = 6): array
	{
		$return = [];

		$semesters = ($this->getUpcomingSemestersService)($numberOfSemesters - 1)->prepend(
			($this->getCurrentSemesterService)()
		)->sortBy('start_date')->unique()->shift($numberOfSemesters)->values();

		foreach ($semesters AS $semester) {
			$return[$semester->id] = $semester->name;
		}

		return $return;
	}
}