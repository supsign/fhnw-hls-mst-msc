<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class RadioGroup extends Component
{
    public int $courseId;
    public string $courseName;
    public array $nextSemesters;
    public int $semesterId = 0;

    public function updated() {
        $this->emit('updateSelectedSemester', $this->courseId, $this->semesterId);
    }
    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
