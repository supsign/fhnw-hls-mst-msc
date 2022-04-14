<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseGroupSpecialization extends BaseModel
{
    protected $table = 'course_group_specialization';

    public function courseGroup(): BelongsTo
    {
        return $this->belongsTo(CourseGroup::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
