<?php

namespace App\Http\Livewire;


use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{

    public string $givenName;
    public int $semesterId;
    public array $semesters;
    public int $specializationId;
    public array $specializations;
    public string $surname;
    public string $studyMode;
    public array $studyModes;

    protected $listeners = [
        'changeSurname',
        'changeGivenName',
        'changeSemester','changeSpecialization',
        'changeStart',
        'changeCoreCompetenceCourse',
        'changeClusterSpecificCourse',
    ];
    protected array $rules = [
        'surname' => 'required',
        'givenName' => 'required',
        'specialization' => 'required',
    ];

    public function mount()
    {
       $this->semesterId = (int)array_key_first($this->semesters);
    }

    public function dehydrate()
    {
        $this->emit('formErrorBag', $this->getErrorBag());
    }
    public function submit()
    {
        $this->validate();
    }

    public function changeSurname(string $value): void
    {
        $this->surname = $value;
    }

    public function changeGivenName(string $value): void
    {
        $this->givenName = $value;
    }
    
    public function changeSemester(string $selected): void
    {
        $this->semesterId = (int)$selected;
    }

    public function changeSpecialization(int $selected): void
    {
        $this->specializationId = $selected;
    }

    public function render(): View
    {
        return view('livewire.module-selection-form');
    }
}
