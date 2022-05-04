<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends BaseModel
{
	public function autumnSemesterSlot(): BelongsTo
	{
		return $this->belongsTo(Slot::class, 'as_slot_id');
	}

	public function cluster(): BelongsTo
	{
		return $this->belongsTo(Cluster::class);
	}

	public function courseGroup(): Attribute
	{
		return Attribute::make(
			get: fn () => $this->attributes['course_group'],
			set: fn (CourseGroup $courseGroup) => $this->attributes['course_group'] = $courseGroup,
		);
	}

	public function semesters(): BelongsToMany
	{
		return $this->belongsToMany(Semester::class)->orderBy('start_date');
	}

	public function springSemesterSlot(): BelongsTo
	{
		return $this->belongsTo(Slot::class, 'ss_slot_id');
	}

	public function	specialization(): BelongsTo
	{
		return $this->belongsTo(Specialization::class);
	}

	public function venue(): BelongsTo
	{
		return $this->belongsTo(Venue::class);
	}

    public function newCollection(array $models = []): CourseCollection
    {
        return new CourseCollection($models);
    }
}
