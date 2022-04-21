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
    public int $groupId;

    public function mount(): void
    {
        $this->semesterId = 'on';
    }

    public function updated(): void {
        switch ($this->semesterId) {

            case 'on':
                $this->emit('findAndDeleteUnselectSelectedCourse', $this->courseId);
                $this->emit('findAndDeleteUnselectLaterCourse', $this->courseId);
                //$this->emit('updateGroupCoursesCount',$this->groupId, $this->courseId, true);
                break;

            case 'later':
                $this->emit('findAndDeleteUnselectSelectedCourse', $this->courseId);
                $this->emit('updateLaterCourse', $this->courseId);
                //$this->emit('updateGroupCoursesCount',$this->groupId, $this->courseId);
                break;

            default:
                $this->emit('findAndDeleteUnselectLaterCourse',$this->courseId);
                $this->emit('updateSelectedCourse', $this->courseId, $this->semesterId);;
                //$this->emit("updateGroupCoursesCount$this->groupId", $this->courseId, false);
                break;
        }
        $this->selectedSemester = $this->semesterId;
    }
    
    public function render(): View
    {
        return view('livewire.radio-group');
    }
}
