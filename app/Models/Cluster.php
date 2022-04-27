<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Cluster extends BaseModel
{
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function specializations(): HasMany
    {
        return $this->hasMany(Specialization::class);
    }


}