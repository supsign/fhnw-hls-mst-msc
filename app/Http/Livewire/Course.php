<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Course extends Component
{
    public array $course;
    public array $nextSemesters;

    public int $groupId;

    public string $courseGroupTypeShortName;
    
    public function render(): View
    {
        // dump($this);

        return view('livewire.course');
    }
}
