<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;

class Semester extends BaseModel
{
	protected $appends = [
	    'is_autumn_semester',
	    'is_spring_semester',
	    'year',
	];

	protected $casts = [
	    'start_date' => 'date',
	];

	public function isAutumnSemester(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->monthName === 'September'
		);
	}

	public function isSpringSemester(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->monthName === 'February'
		);
	}

	public function name(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->year.' '.($this->isAutumnSemester ? 'AS' : 'SS')
		);
	}

	public function year(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->year
		);
	}
}
