<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\View\View;
use Livewire\Component;

class CourseSelection extends Component
{

    public int $specializationId;
    public int $semesterId;
    public array $coreCompetenceCourseGroup;
    public array $clusterSpecificCourseGroup;
    public array $defaultCourseGroup;
    public array $electiveCourseGroup;

    public function mount(int $specializationId, GetCourseSelectDataService $getCourseSelectDataService): void

    {
        $specialization = Specialization::find($specializationId);

        $this->coreCompetenceCourseGroup = $getCourseSelectDataService(CourseGroupType::CoreCompetences, $specialization);
        $this->clusterSpecificCourseGroup = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $specialization);
        $this->defaultCourseGroup = $getCourseSelectDataService(CourseGroupType::Default, $specialization);
        $this->electiveCourseGroup = $getCourseSelectDataService(CourseGroupType::Elective, $specialization);

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
        return view('livewire.course-selection');
    }
}
