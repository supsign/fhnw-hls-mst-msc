<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseGroup extends BaseModel
{
	public function courses(): BelongsToMany
	{
		return $this->belongsToMany(Course::class);
	}

	public function CourseGroupType(): BelongsTo
	{
		return $this->belongsTo(CourseGroupType::class);
	}

	public function specializations(): BelongsToMany
	{
		return $this->belongsToMany(Specialization::class);
	}
}