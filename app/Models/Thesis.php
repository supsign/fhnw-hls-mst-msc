<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends BaseModel
{
	public function specialization(): BelongsTo
	{
		return $this->belongsTo(Specialization::class);
	}
}