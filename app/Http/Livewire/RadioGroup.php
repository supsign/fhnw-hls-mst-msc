<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RadioGroup extends Component
{
    public string $courseName;
    public array $semesters;

    public function render()
    {
        return view('livewire.radio-group');
    }
}
