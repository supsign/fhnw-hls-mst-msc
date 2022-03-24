<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends BaseModel
{
	public function venue(): BelongsTo
	{
		return $this->belongsTo(Venue::class)
	}
}
