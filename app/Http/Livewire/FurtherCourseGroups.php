<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FurtherCourseGroups extends Component
{

    public array $groups;
    public array $nextSemesters;
    public string $class;

    public function render()
    {
        return view('livewire.further-course-groups');
    }
}
