<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Course extends Component
{
    public array $course;
    public array $nextSemesters;
    public array $selectableSemesters = ['test1', 'test2'];

    public int $courseGroupId;

    public string $courseGroupTypeShortName;
    
    public function render(): View
    {
        return view('livewire.course');
    }
}
