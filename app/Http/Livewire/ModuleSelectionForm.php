<?php

namespace App\Http\Livewire;


use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public array $semesters;
    public array $specializations;
    public array $studyModes;

    public ?string $semesterId = null;
    public ?int $specializationId = null;
    public ?int $studyModeId = null;

    public function mount(): void
    {
        $this->semesterId = array_key_first($this->semesters);
        $this->specializationId = $this->specializations[0]['id'];      //  remove when reintroducing placeholder
        $this->studyModeId = array_key_first($this->studyModes);
    }

    public function render(): View
    {
        return view('livewire.module-selection-form');
    }

}
