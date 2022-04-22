<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class RadioGroup extends Component
{
    public array $selectableSemesters;

    public int $courseId;
    public int $courseGroupId;

    public string $courseName;

    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
