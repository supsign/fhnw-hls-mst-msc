<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CourseSelection extends Component
{

    public array $coreCompetenceCourseGroup;
    public array $clusterSpecificCourseGroup;
    public array $defaultCourseGroup;
    public array $electiveCourseGroup;

    public function render()
    {
        return view('livewire.course-selection');
    }
}
