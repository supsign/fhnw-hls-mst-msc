<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends BaseModel
{
    protected $hidden = [
    	'cluter_id',
        'created_at',
        'updated_at',
    ];

	public function courses(): HasMany
	{
		return $this->hasMany(Course::class);
	}

    public function cluster(): BelongsTo
	{
		return $this->belongsTo(Cluster::class);
	}

	public function theses(): HasMany
	{
		return $this->hasMany(Thesis::class);
	}
}
