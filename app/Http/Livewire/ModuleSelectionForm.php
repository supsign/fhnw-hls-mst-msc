<?php

namespace App\Http\Livewire;

use App\DataTransferObjects\Courses\CourseSelectData;
use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public array $coreCompetenceCourseData;
    public array $clusterSpecificCourseData;
    public array $defaultCourseData;
    public array $electiveCourseData;
    public string $givenName;
    public string $semester;
    public array $semesters;
    public int $specialization;
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
    public function changeSpecialization(int $selected, GetCourseSelectDataService $getCourseSelectDataService): void
    {
        $this->specialization = $selected;
        
        // $this->coreCompetenceCourseData = (string)$getCourseSelectDataService(CourseGroupType::CoreCompetences, $this->specialization);
        // $this->clusterSpecificCourseData = (string)$getCourseSelectDataService(CourseGroupType::ClusterSpecific, $this->specialization);
        // $this->defaultCourseData = (string)$getCourseSelectDataService(CourseGroupType::Default, $this->specialization);
        // $this->electiveCourseData = (string)$getCourseSelectDataService(CourseGroupType::Elective, $this->specialization);
    }
    public function changeCoreCompetenceCourse(Course $selected): void
    {
        $this->coreCompetenceCourse = $selected;
    }

    public function changeClusterSpecificCourse(Course $selected): void
    {
        $this->clusterSpecificCourse = $selected;
    }
    public function render(): View
    {
        return view('livewire.module-selection-form');
    }
}
