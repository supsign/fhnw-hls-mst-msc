<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class RadioGroup extends Component
{
    public array $selectableSemesters;

    public bool $further = false;

    public ?int $courseId = null;
    public int $courseGroupId;

    public string $courseName;
    public $selectedSemester;

    public function updatedSelectedSemester(): void
    {
        $this->emit('courseSelected', $this->courseId, $this->selectedSemester, $this->courseGroupId, $this->further);
    }

    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
