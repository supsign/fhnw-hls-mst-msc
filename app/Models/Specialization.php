<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specialization extends BaseModel
{
	public function cluster(): BelongsTo
	{
		return $this->belongsTo(Cluster::class);
	}
}
