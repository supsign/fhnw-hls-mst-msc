<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ModuleSelectionForm extends Component
{

    public array $semesters;
    public array $studyModes;
    public Collection $specializations;
    public string $semester;
    public int $specialization;

    protected $listeners = ['changeSpecialization', 'changeStart'];

    public function changeStart(string $selected): void
    {
        $this->semester = $selected;
    }
    public function changeSpecialization(int $selected): void
    {
        $this->specialization = $selected;
    }

    public function render(): View
    {
        return view('livewire.module-selection-form');
    }
}
