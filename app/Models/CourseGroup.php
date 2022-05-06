<?php

namespace App\Models;

use App\Enums\CourseGroupType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseGroup extends BaseModel
{
	protected $appends = [
		'course_group_type_short_name', 
		'course_group_type_tooltip', 
		'description', 
		'title'
	];

	protected $casts = [
	    'type' => CourseGroupType::class,
	];

	public function courses(): BelongsToMany
	{
		return $this->belongsToMany(Course::class);
	}

	public function description(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->attributes['description'] ?? null,
			set: fn (?string $description) => $this->attributes['description'] = $description
		);
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

	public function specializations(): BelongsToMany
	{
		return $this->belongsToMany(Specialization::class);
	}

	public function title(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->attributes['title'] ?? null,
			set: fn (?string $title) => $this->attributes['title'] = $title
		);
	}
}
