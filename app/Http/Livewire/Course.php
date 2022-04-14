<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Course extends Component
{
    public $course;
    public string $internalName;
    public array $nextSemesters;
    public int $selectedSemester;


    public function render(): View
    {
        return view('livewire.course');
    }
}
