<?php

namespace App\Models;

use App\Enums\SemesterType;
use App\Enums\ThesisStarts;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;

class Semester extends BaseModel
{
	protected $appends = [
	    'name',
	    'long_name',
	    'short_name',
        'long_name_with_short',
	    'tooltip',
	    'year',
	    'is_replanning'
	];
	protected $casts = [
	    'start_date' => 'date',
	    'type' => SemesterType::class,
	];

	public function isReplanning(): Attribute
	{
		return Attribute::make(
			get: fn (): bool => $this->start_date < Carbon::now(),
		);
	}

	protected function longName(): Attribute
	{
		return Attribute::make(
			get: function (): string {
				if (!empty($this->attributes['long_name'])) {
					return $this->attributes['long_name'];
				}

				if (strtolower($this->name) === 'later') {
					return $this->name;
				}

				return $this->year.' '.$this->semesterTypeLongName;
			},
			set: fn (string $longName) => $this->attributes['long_name'] = $longName,
		);
	}

    protected function longNameWithShort(): Attribute
    {
        return Attribute::make(
            get: fn (): string => !empty($this->attributes['long_name']) 
            	? $this->attributes['long_name'] 
            	: $this->semesterTypeLongName.' ('.$this->semesterTypeShortName.') '.$this->year,
        );
    }

	protected function name(): Attribute
	{
		return Attribute::make(
			get: fn (): string => !empty($this->attributes['name']) 
				? $this->attributes['name'] 
				: $this->year.' '.$this->semesterTypeShortName,
			set: fn (string $name) => $this->attributes['name'] = $name,
		);
	}

	protected function nextSemester(): Attribute
	{
		return Attribute::make(
			get: fn (): ?Semester => Semester::where('start_date', '>', $this->start_date)
				->where('type', '<>', $this->type->value)
				->orderBy('start_date')
				->first(),
		);
	}

	protected function overlappingCourses(): Attribute
	{
		return Attribute::make(
			get: fn (): Collection => $courses ?? collect(),
			set: fn (?Collection $courses) => $courses,
		);
	}

	protected function previousSemester(): Attribute
	{
		return Attribute::make(
			get: fn (): ?Semester => Semester::where('start_date', '<', $this->start_date)
				->where('type', '<>', $this->type->value)
				->orderByDesc('start_date')
				->first(),
		);
	}

	protected function selectedCourses(): Attribute
	{
		return Attribute::make(
			get: fn (): Collection => $courses ?? collect(),
			set: fn (?Collection $courses) => $courses,
		);
	}

	protected function semesterTypeShortName(): Attribute
	{
		return Attribute::make(
			get: fn (): string => $this->type->shortName()
		);
	}

	protected function semesterTypeLongName(): Attribute
	{
		return Attribute::make(
			get: fn (): string => $this->type?->longName() ?? 'later',
		);
	}

	protected function shortName(): Attribute
	{
		return Attribute::make(
			get: fn (): string => $this->semesterTypeShortName.substr($this->year, 2, 2),
		);
	}

	protected function thesisEnd(): Attribute
	{
		return Attribute::make(
			get: function (): string {
				if ($this->type === SemesterType::AutumnStart) {
					return 'spring '.($this->year + 2);
				}

				return 'autumn '.($this->year + 1);
			}
		);
	}

	protected function thesisStart(): Attribute
	{
		return Attribute::make(
			get: function (): string {
				if ($this->type === SemesterType::AutumnStart) {
					return ThesisStarts::Middle->label().' '.($this->year + 1);
				}

				return ThesisStarts::Beginning->label().' '.($this->year + 1);
			}
		);
	}

	protected function tooltip(): Attribute
	{
		return Attribute::make(
			get: fn (): string => $this->type->tooltip()
		);
	}

	protected function year(): Attribute
	{
		return Attribute::make(
			get: fn (): int => $this->start_date->year ?? 10000,
		);
	}
}
