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
    public array $coreCompetenceCourseGroup;
    public array $coreCompetenceCourses;
    public array $clusterSpecificCourseGroup;
    public array $clusterSpecificCourses;
    public array $defaultCourseGroup;
    public array $defaultCourses;
    public array $electiveCourseGroup;
    public array $electiveCourses;
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
    public function changeSpecialization(int $selected, GetCourseSelectDataService $getCourseSelectDataService): void
    {
        $this->specializationId = $selected;
        $specialization = Specialization::find($selected);

        [$this->coreCompetenceCourseGroup, $this->coreCompetenceCourses] = $getCourseSelectDataService(CourseGroupType::CoreCompetences, $specialization);
        [$this->clusterSpecificCourseGroup, $this->clusterSpecificCourses] = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $specialization);
        [$this->defaultCourseGroup, $this->defaultCourses] = $getCourseSelectDataService(CourseGroupType::Default, $specialization);
        [$this->electiveCourseGroup, $this->electiveCourses] = $getCourseSelectDataService(CourseGroupType::Elective, $specialization);
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
