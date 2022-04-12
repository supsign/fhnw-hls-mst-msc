<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Course extends Component
{
    public $course;
    public string $internalName;
    public array $nextSemesters;


    protected $listeners = [
        'updateSelectedSemester'
    ];
    public function updateSelectedSemester(int $courseId, int $semesterId) {
        $this->emit('updateSelectedCourse', $courseId, $semesterId);
    }
    public function render()
    {
        return view('livewire.course');
    }
}
