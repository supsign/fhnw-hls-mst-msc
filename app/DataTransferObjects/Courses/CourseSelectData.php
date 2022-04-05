<?php

namespace App\DataTransferObjects\Courses;

use App\Models\CourseCollection;
use App\Models\CourseGroup;
use Spatie\DataTransferObject\DataTransferObject;

class CourseSelectData extends DataTransferObject
{
    public CourseGroup $courseGroup;
    public CourseCollection $courses;
}