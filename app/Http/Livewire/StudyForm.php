<?php

namespace App\Http\Livewire;

use Livewire\Component;

class StudyForm extends Component
{

    public $semesters;
    public $studyModes;
    public $specializations;
    public $semester;
    public $specialization;

    protected $listeners = ['changestart','changespecialization'];

    public function changestart($selected) {
        $this->semester = $selected;
    }
    public function changespecialization($selected) {
        $this->specialization = $selected;
    }


    public function render()
    {
        return view('livewire.study-form');
    }
}
