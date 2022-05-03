<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends BaseModel
{
	public function cluster(): BelongsTo
	{
		return $this->belongsTo(Cluster::class);
	}

	public function autumnSemesterSlot(): BelongsTo
	{
		return $this->belongsTo(Slot::class, 'as_slot_id');
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
