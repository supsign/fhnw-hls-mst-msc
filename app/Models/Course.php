<?php

namespace App\Models;

use App\Enums\SemesterType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends BaseModel
{
	protected $hidden = [
		'block',
		'cluster_id',
        'created_at',
        'specialization_id',
        'pivot',
        // 'tooltip',
        'updated_at',
        'venue_id',
	];

	protected $casts = [
	    'semester_type' => SemesterType::class,
	];

	public function cluster(): BelongsTo
	{
		return $this->belongsTo(Cluster::class);
	}

	public function courseGroup(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->attributes['course_group'] ?? null,
			set: fn (?CourseGroup $courseGroup) => $this->attributes['course_group'] = $courseGroup,
		);
	}

	public function endSemester(): BelongsTo
	{
		return $this->belongsTo(Semester::class, 'end_semester_id');
	}

	public function semesters(): BelongsToMany
	{
		return $this->belongsToMany(Semester::class)->orderBy('start_date');
	}

	public function slot(): BelongsTo
	{
		return $this->belongsTo(Slot::class);
	}

	public function	specialization(): BelongsTo
	{
		return $this->belongsTo(Specialization::class);
	}

	public function startSemester(): BelongsTo
	{
		return $this->belongsTo(Semester::class, 'start_semester_id');
	}

	// public function tooltip(): Attribute
	// {
	// 	return Attribute::make(
	// 		get: fn () => $this->courseGroup?->type->tooltip()
	// 	);
	// }

	public function venue(): BelongsTo
	{
		return $this->belongsTo(Venue::class);
	}
}


