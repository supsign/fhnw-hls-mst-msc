<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thesis extends BaseModel
{
	protected $hidden = [
        'created_at',
        'specialization_id',
        'updated_at',
	];

	public function specialization(): BelongsTo
	{
		return $this->belongsTo(Specialization::class);
	}
}