<?php

namespace App\Http\Livewire;

use Livewire\Component;

class StudyForm extends Component
{

    public $semesters;
    public $studyModes;
    public $specializations;
    public $test= 'Init';

    protected $listeners = ['test'];

    public function test($selected) {
        $this->test = $selected;
    }

    public function render()
    {
        return view('livewire.study-form');
    }
}
