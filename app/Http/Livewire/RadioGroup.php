<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class RadioGroup extends Component
{
    public int $courseId;
    public string $courseName;
    public array $selectableSemesters;
    public int|string|null $semesterId = null;
    public int|string $selectedSemester;

    public function mount(): void
    {
        $this->semesterId = $this->selectedSemester;
    }

    public function updated(): void
    {
        if($this->semesterId === 'later') {
            $this->emit('updateLaterCourse', $this->courseId);
        } else {
            $this->emit('updateSelectedCourse', $this->courseId, $this->semesterId);
        }

        $this->selectedSemester = $this->semesterId;
    }
    
    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
