<?php

namespace App\Http\Livewire;


use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public array $semesters;
    public array $specializations;
    public array $studyModes;

    public function render(): View
    {
        return view('livewire.module-selection-form');
    }
}
