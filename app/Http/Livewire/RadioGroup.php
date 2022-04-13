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
    public int $selectedSemester;

    public function mount(): void
    {
        $this->semesterId = $this->selectedSemester;
    }
    public function updated(): void 
    {
        $this->emit('updateSelectedSemester', $this->courseId, $this->semesterId);
        $this->selectedSemester = $this->semesterId;
    }
    
    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
