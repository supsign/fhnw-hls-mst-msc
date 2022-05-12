<?php

namespace App\Models;

use App\Enums\SemesterType;
use App\Enums\ThesisStarts;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;

class Semester extends BaseModel
{
	protected $appends = [
	    'name',
	    'long_name',
	    'short_name',
	    // '',
	    'tooltip',
	    'year',
	];

	protected $casts = [
	    'start_date' => 'date',
	    'type' => SemesterType::class,
	];

	public function name(): Attribute
	{
		return Attribute::make(
			get: fn () => !empty($this->attributes['name']) ? $this->attributes['name'] : $this->year.' '.$this->semesterTypeShortName,
			set: fn (string $name) => $this->attributes['name'] = $name,
		);
	}

	public function nextSemester(): Attribute
	{
		return Attribute::make(
			get: fn () => Semester::where('start_date', '>', $this->start_date)
				->where('type', '<>', $this->type->value)
				->orderBy('start_date')
				->first(),
		);
	}

	public function longName(): Attribute
	{
		return Attribute::make(
			get: fn () => !empty($this->attributes['long_name']) ? $this->attributes['long_name'] : $this->year.' '.$this->semesterTypeLongName,
			set: fn (string $longName) => $this->attributes['long_name'] = $longName,
		);
	}

	public function selectedCourses(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->attributes['selected_courses'] ?? collect(),
			set: fn (?Collection $courses) => $this->attributes['selected_courses'] = $courses,
		);
	}

	public function semesterTypeShortName(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->type->shortName()
		);
	}

	public function semesterTypeLongName(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->type->longName()
		);
	}

	public function shortName(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->semesterTypeShortName.substr($this->year, 2, 2),
		);
	}

	public function thesisStart(): Attribute
	{
		return Attribute::make(
			get: function () {
				if ($this->type === SemesterType::AutumnStart) {
					return ThesisStarts::Middle->label().' '.($this->year + 1);
				}

				return ThesisStarts::Beginning->label().' '.($this->year + 1);
			}
		);
	}

	public function tooltip(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->type->tooltip()
		);
	}

	public function year(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->start_date->year
		);
	}
}
