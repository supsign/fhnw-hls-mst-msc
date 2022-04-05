<?php

namespace App\Http\Livewire;

use App\Models\Course;
use App\Models\CourseCollection;
use App\Models\CourseGroupSpecialization;
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


    public function __construct()
    {

    }

    protected $listeners = ['changeSurname', 'changeGivenName', 'changeSemester','changeSpecialization', 'changeStart'];
    protected array $rules = [
        'surname' => 'required',
        'givenName' => 'required',
        'specialization' => 'required',
        'coreCompetenceCourses' => 'required',
        'clusterSpecificCourses' => 'required'
    ];

    public function updated($surname)
    {
        $this->validateOnly($surname);
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
        $this->semester = $selected;
    }
    public function changeSpecialization(int $selected): void
    {
        $this->specialization = $selected;
        $this->coreCompetenceCourses = $this->getCoreCompetenceCourses();
        $this->clusterSpecificCourses = $this->getClusterSpecificCourses();
    }

    public function render(): View
    {
        return view('livewire.module-selection-form');
    }

    protected function getCoreCompetenceCourses(): CourseCollection
    {
        return new CourseCollection(
            CourseGroupSpecialization::where('specialization_id', $this->specialization)
                ->join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
                ->where('course_group_type_id', 1)
                ->with(['courseGroup', 'courseGroup.courses'])
                ->get()
                    ->pluck('courseGroup')
                    ->pluck('courses')
                    ->flatten()
        );
    }

    protected function getClusterSpecificCourses(): CourseCollection
    {
        return new CourseCollection(
            CourseGroupSpecialization::where('specialization_id', $this->specialization)
                ->join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
                ->where('course_group_type_id', 2)
                ->with(['courseGroup', 'courseGroup.courses'])
                ->get()
                    ->pluck('courseGroup')
                    ->pluck('courses')
                    ->flatten()
        );
    }
}
