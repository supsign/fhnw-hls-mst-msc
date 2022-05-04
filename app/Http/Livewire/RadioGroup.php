<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class RadioGroup extends Component
{
    public array $selectableSemesters;

    public bool $further = false;

    public int $courseId;
    public int | null $courseGroupId;

    public string $courseName;
    public $selectedSemester;

    public function updatedSelectedSemester(): void
    {
        $this->emit('courseSelected', $this->courseGroupId, $this->courseId, $this->selectedSemester, $this->further);
    }

    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
