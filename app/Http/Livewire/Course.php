<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Course extends Component
{
    public $course;
    public string $internalName;

    public function render()
    {
        return view('livewire.course');
    }
}
