<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Course extends Component
{
    public $course;
    public string $courseGroupTypeShortName;
    public array $nextSemesters;
    public int|string|null $selectedSemester = null;
    public array $selectableSemesters = [];
    public int $groupId;

    public function mount() {
        foreach($this->nextSemesters AS $semester) {
            $this->selectableSemesters[] = in_array($semester['id'], array_column($this->course['semesters'], 'id'))
                ? $semester['id']
                : null;
        }

    }

    public function render(): View
    {
        return view('livewire.course');
    }
}
