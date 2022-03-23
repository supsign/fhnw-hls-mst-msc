<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseGroup extends BaseModel
{
	public function courses(): BelongsToMany
	{
		return $this->belongsToMany(Course::class);
	}

	public function specializations(): BelongsToMany
	{
		return $this->belongsToMany(Specialization::class);
	}
}