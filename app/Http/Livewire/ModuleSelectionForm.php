<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use stdClass;

class ModuleSelectionForm extends Component
{
    public stdClass $coreCompetenceCourseData;
    public stdClass $clusterSpecificCourseData;
    public stdClass $defaultCoursesData;
    public stdClass $electiveCourseData;
    public string $givenName;
    public string $semester;
    public array $semesters;
    public int $specialization;
    public Collection $specializations;
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
        'coreCompetenceCourse' => 'required',
        'clusterSpecificCourse' => 'required'
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

        $specalisation = Specialization::find($selected);       //  umbauen das '$selected' bereits das Specialization Model enthält

        $this->coreCompetenceCoursesData = $getCourseSelectDataService(CourseGroupType::CoreCompetences, $specalisation);
        $this->clusterSpecificCoursesData = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $specalisation);
        $this->defaultCoursesData = $getCourseSelectDataService(CourseGroupType::Default, $specalisation);
        $this->electiveCoursesData = $getCourseSelectDataService(CourseGroupType::Elective, $specalisation);
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
