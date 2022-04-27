<?php

namespace App\Models;

use App\Enums\CourseGroupType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseGroup extends BaseModel
{
	protected $appends = ['courses_filtered', 'course_group_type_short_name', 'course_group_type_tooltip'];

	protected $casts = [
	    'type' => CourseGroupType::class,
	];

	public function courses(): BelongsToMany
	{
		return $this->belongsToMany(Course::class);
	}

	public function courseGroupTypeShortName(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->type->labelShort(),
		);
	}

	public function courseGroupTypeTooltip(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->type->tooltip(),
		);
	}

	public function coursesFiltered(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->attributes['courses_filtered'] ?? collect(),
			set: fn (Collection $value) => $this->attributes['courses_filtered'] = $value,
		);
	}

	public function specializations(): BelongsToMany
	{
		return $this->belongsToMany(Specialization::class);
	}
}
