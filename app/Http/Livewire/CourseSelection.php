<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class CourseSelection extends Component
{
    public int $semesterId;
    public int $specializationId;
    public int $studyModeId;


    public function mount(): void
    {
        dump($this);
    }

    public function render(): View
    {
        return view('livewire.course-selection');
    }
}
