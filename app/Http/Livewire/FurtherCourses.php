<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FurtherCourses extends Component
{
    public array $furtherCourses;
    public array $nextSemesters;
    public array $selectedCourses;

    public function render()
    {
        return view('livewire.further-courses');
    }

}
