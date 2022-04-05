<?php

namespace App\Http\Livewire;

use App\DataTransferObjects\Courses\CourseSelectData;
use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public CourseSelectData $coreCompetenceCourseData;
    public CourseSelectData $clusterSpecificCourseData;
    public CourseSelectData $defaultCoursesData;
    public CourseSelectData $electiveCourseData;
    public string $givenName;
    public string $semester;
    public array $semesters;
    public Specialization $specialization;
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
        $this->specialization = Specialization::find($selected);
        $this->coreCompetenceCoursesData = $getCourseSelectDataService(CourseGroupType::CoreCompetences, $this->specialization);
        $this->clusterSpecificCoursesData = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $this->specialization);
        $this->defaultCoursesData = $getCourseSelectDataService(CourseGroupType::Default, $this->specialization);
        $this->electiveCoursesData = $getCourseSelectDataService(CourseGroupType::Elective, $this->specialization);
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
