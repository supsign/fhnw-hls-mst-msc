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
    public array $selectableSemesters = [];

    public function mount() {
        foreach($this->nextSemesters AS $semester) {
            if (array_search($semester['id'], array_column($this->course['semesters'], 'id'))) {

                $this->selectableSemesters[] = $semester['id'];
            } else {
                $this->selectableSemesters[] = 0;
            }
        }
    }

    public function render(): View
    {
        return view('livewire.course');
    }
}
