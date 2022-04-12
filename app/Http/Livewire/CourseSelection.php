<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use App\Services\Semesters\GetUpcomingSemestersService;
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
    public array $nextSemesters;
    public array $selectedCourses = [];

    protected $listeners = [
        'updateSelectedCourse'
    ];

    public function mount(int $specializationId, int $semesterId, GetUpcomingSemestersService $getUpcomingSemestersService, GetCourseSelectDataService $getCourseSelectDataService): void

    {

        $specialization = Specialization::find($specializationId);
        $semester = Semester::find($semesterId);

        $this->coreCompetenceCourseGroup = $getCourseSelectDataService(CourseGroupType::CoreCompetences, $specialization);
        $this->clusterSpecificCourseGroup = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $specialization);
        $this->defaultCourseGroup = $getCourseSelectDataService(CourseGroupType::Default, $specialization);
        $this->electiveCourseGroup = $getCourseSelectDataService(CourseGroupType::Elective, $specialization);
        $this->nextSemesters = $getUpcomingSemestersService(4,  $semester->start_date)->toArray();
    }

    public function changeCoreCompetenceCourse(Course $selected): void
    {
        $this->coreCompetenceCourse = $selected;
    }

    public function changeClusterSpecificCourse(Course $selected): void
    {
        $this->clusterSpecificCourse = $selected;
    }
    public function updateSelectedCourse(int $courseId, int $semesterId) {
        $this->selectedCourses[$courseId] = $semesterId;
    }

    public function render(): View
    {
        return view('livewire.course-selection');
    }
}
