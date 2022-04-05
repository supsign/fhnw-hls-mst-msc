<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\CourseCollection;
use App\Models\Specialization;
use App\Services\Courses\GetCoursesService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public Course $coreCompetenceCourse;
    public CourseCollection $coreCompetenceCourses;
    public Course $clusterSpecificCourse;
    public CourseCollection $clusterSpecificCourses;
    public Course $defaultCourse;
    public CourseCollection $defaultCourses;
    public Course $electiveCourse;
    public CourseCollection $electiveCourses;
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
    public function changeSpecialization(int $selected, GetCoursesService $getCoursesService): void
    {
        $this->specialization = $selected;

        $this->coreCompetenceCourses = $getCoursesService(CourseGroupType::CoreCompetences, Specialization::find($selected));
        $this->clusterSpecificCourses = $getCoursesService(CourseGroupType::ClusterSpecific, Specialization::find($selected));
        $this->defaultCourse = $getCoursesService(CourseGroupType::Default, Specialization::find($selected));
        $this->electiveCourses = $getCoursesService(CourseGroupType::Elective, Specialization::find($selected));
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
