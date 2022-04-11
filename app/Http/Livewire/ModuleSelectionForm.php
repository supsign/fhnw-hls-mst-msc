<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{

    public string $givenName;
    public string $semester;
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
        'changeClusterSpecificCourse'
    ];
    protected array $rules = [
        'surname' => 'required',
        'givenName' => 'required',
        'specialization' => 'required',
    ];

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
        $this->semester = $selected;
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
