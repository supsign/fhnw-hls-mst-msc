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
    public int|string|null $selectedSemester = null;

    public function mount(): void
    {
        if(!$this->selectedSemester) {
            $this->semesterId = 0;
        } else {
            $this->semesterId = $this->selectedSemester;
        }

    }

    public function updated(): void {
        if($this->semesterId === 0){
        $this->emit('findAndDeleteUnselectSelectedCourse',$this->courseId);
        $this->emit('findAndDeleteUnselectLaterCourse',$this->courseId);
        }
        else if($this->semesterId === 'later') {
            $this->emit('findAndDeleteUnselectSelectedCourse',$this->courseId);
            $this->emit('updateLaterCourse', $this->courseId);
        } else {
            $this->emit('findAndDeleteUnselectLaterCourse',$this->courseId);
            $this->emit('updateSelectedCourse', $this->courseId, $this->semesterId);
        }
        $this->selectedSemester = $this->semesterId;
    }
    
    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
