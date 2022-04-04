<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseGroupType extends BaseModel
{
    public function courseGroups(): HasMany
    {
        return $this->hasMany(CourseGroup::class);
    }
}
