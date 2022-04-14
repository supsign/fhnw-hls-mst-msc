<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
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

    public function updateSelectedSemester(int $courseId, int $semesterId): void
    {
        $this->emit('updateSelectedCourse', $courseId, $semesterId);
    }

    public function render(): View
    {
        return view('livewire.course');
    }
}
