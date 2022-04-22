<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CourseGroup extends Component
{
    public array $courseGroup;
    public array $nextSemesters;

    public bool $further = false;

    public ?string $description = null;

    public function mount() 
    {

        // dump(
        //     count($this->courseGroup),
        //     $this->nextSemesters, 
        //     $this->description
        // );


    }

    public function render()
    {
        return view('livewire.course-group');
    }
}
