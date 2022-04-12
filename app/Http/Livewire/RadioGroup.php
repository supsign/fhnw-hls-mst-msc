<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RadioGroup extends Component
{
    public string $courseName;
    public array $nextSemesters;

    public function render()
    {
        return view('livewire.radio-group');
    }
}
