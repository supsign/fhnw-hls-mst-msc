<?php

namespace App\Models;

use App\Enums\Semester as EnumsSemester;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Semester extends BaseModel
{
	protected $appends = [
	    'is_autumn_semester',
	    'is_spring_semester',
	    'name',
	    'long_name',
	    'short_name',
	    'tooltip',
	    'year',
	];

	protected $casts = [
	    'start_date' => 'date',
	];

	public function courses(): BelongsToMany
	{
		return $this->belongsToMany(Course::class);
	}

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
			get: fn () => $this->year.' '.$this->semesterTypeShortName
		);
	}

	public function longName(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->year.' '.$this->semesterTypeLongName
		);
	}

	public function semesterTypeShortName(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->isAutumnSemester 
				? EnumsSemester::AutumnStart->shortName() 
				: EnumsSemester::SpringStart->shortName(),
		);
	}

	public function semesterTypeLongName(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->isAutumnSemester 
				? EnumsSemester::AutumnStart->longName() 
				: EnumsSemester::SpringStart->longName(),
		);
	}

	public function shortName(): Attribute
	{
		return Attribute::make(
			get: fn () => ($this->isAutumnSemester 
				? EnumsSemester::AutumnStart->shortName() 
				: EnumsSemester::SpringStart->shortName()
			).substr($this->year, 2, 2),
		);
	}

	public function tooltip(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->isAutumnSemester 
				? EnumsSemester::AutumnStart->tooltip() 
				: EnumsSemester::SpringStart->tooltip(),
		);
	}

	public function year(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->year
		);
	}
}
