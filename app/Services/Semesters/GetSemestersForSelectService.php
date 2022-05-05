<?php

namespace App\Services\Semesters;

class GetSemestersForSelectService
{
	public function __construct(protected GetUpcomingSemestersService $getUpcomingSemestersService) 
	{}

	public function __invoke(int $numberOfSemesters = 8): array
	{
		$return = [];

		$semesters = ($this->getUpcomingSemestersService)($numberOfSemesters)->sortBy('start_date')->unique()->shift($numberOfSemesters)->values();

		foreach ($semesters AS $semester) {
			$return[$semester->id.'_'] = $semester->longName;
		}

		return $return;
	}
}