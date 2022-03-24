<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends BaseModel
{
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
