<?php

namespace App\Http\Livewire;

use App\Models\Semester;
use Livewire\Component;

class Course extends Component
{
    public $course;
    public string $internalName;
    public array $nextSemesters;
    public int $selectedSemester;


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
