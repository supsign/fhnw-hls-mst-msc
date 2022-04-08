<?php

namespace App\Models;

use App\Enums\Semester as EnumsSemester;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Semester extends BaseModel
{
	protected $appends = [
	    'is_autumn_semester',
	    'is_spring_semester',
	    'name',
	    'year',
	];

	protected $casts = [
	    'start_date' => 'date',
	];

	public function isAutumnSemester(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->monthName === EnumsSemester::AutumnStart->month()
		);
	}

	public function isSpringSemester(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->monthName === EnumsSemester::SpringStart->month()
		);
	}

	public function name(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->year.' '.($this->isAutumnSemester ? EnumsSemester::AutumnStart->shortName() : EnumsSemester::SpringStart->shortName())
		);
	}

	public function year(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->year
		);
	}
}
