<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CourseGroup extends Component
{
    public array $group;
    public array $nextSemesters;
    public bool $further = false;
    public string $class;
    
    public function render()
    {
        return view('livewire.course-group');
    }
}
