<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Course extends Component
{
    public $course;
    public string $internalName;
    public array $nextSemesters;
    public ?int $selectedSemester;
    public array $selectableSemesters = [];

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
