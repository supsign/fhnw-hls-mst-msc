<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Models\Course;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use App\Services\Semesters\GetUpcomingSemestersService;
use Illuminate\View\View;
use Livewire\Component;

class CourseSelection extends Component
{
    public array $coreCompetenceCourseGroup;
    public array $clusterSpecificCourseGroup;
    public array $specialisationCourseGroup;
    public array $electiveCourseGroup;
    public array $nextSemesters;
    public array $selectedCourses = [];
    public array $laterCourses = [];

    public int $semesterId;
    public int $studyModeId;
    public int $specializationId;

    public ?string $coreCompetencesDescription;
    public ?string $descriptionBeforeFurther;
    public ?string $furtherSpecialisationTitle;
    public ?string $furtherClusterTitle;

    protected $listeners = [
        'updateSelectedCourse',
        'updateLaterCourse',
        'findAndDeleteUnselectSelectedCourse',
        'findAndDeleteUnselectLaterCourse'
    ];

    public function mount(
        int $specializationId, 
        int $semesterId, 
        GetUpcomingSemestersService $getUpcomingSemestersService, 
        GetCourseSelectDataService $getCourseSelectDataService
    ): void {
        $specialization = Specialization::find($specializationId);
        $semester = Semester::find($semesterId);

        $this->coreCompetencesDescription = PageContent::where('name', 'core_competences_description')->first()?->content;
        $this->descriptionBeforeFurther = PageContent::where('name', 'description_before_further')->first()?->content;
        $this->furtherSpecialisationTitle = PageContent::where('name', 'further_specialisation_title')->first()?->content;
        $this->furtherClusterTitle = PageContent::where('name', 'further_cluster_title')->first()?->content;

        $this->coreCompetenceCourseGroup = $getCourseSelectDataService(CourseGroupType::CoreCompetences, $specialization, $semester);
        $this->clusterSpecificCourseGroup = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $specialization, $semester);
        $this->electiveCourseGroup = $getCourseSelectDataService(CourseGroupType::Elective, $specialization, $semester);
        $this->furtherClusterSpecificCourseGroups = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $specialization, $semester, true);
        $this->furtherSpecialisationCourseGroups = $getCourseSelectDataService(CourseGroupType::Specialization, $specialization, $semester, true);
        $this->nextSemesters = $getUpcomingSemestersService($this->studyModeId === 1 ? 4 : 2 , $semester->start_date)->toArray();
        $this->specialisationCourseGroup = $getCourseSelectDataService(CourseGroupType::Specialization, $specialization, $semester);
    }

    public function changeCoreCompetenceCourse(Course $selected): void
    {
        $this->coreCompetenceCourse = $selected;
    }

    public function changeClusterSpecificCourse(Course $selected): void
    {
        $this->clusterSpecificCourse = $selected;
    }

    public function updateSelectedCourse(int $courseId, int|string $semesterId): void
    {
        $this->selectedCourses[$courseId] = $semesterId !== 'on' ? $semesterId : null;
    }

    public function updateLaterCourse(int $courseId): void
    {
        $key = array_search($courseId, $this->laterCourses);
        if($key) {
            $this->laterCourses[$key] = $courseId;
        }
        $this->laterCourses[] = $courseId;
    }

    public function findAndDeleteUnselectSelectedCourse(int $courseId): void
    {
        unset($this->selectedCourses[$courseId]);
    }

    public function findAndDeleteUnselectLaterCourse(int $courseId): void
    {
        $key = array_search($courseId, $this->laterCourses);
        if ($key !== false) {
            unset($this->laterCourses[$key]);
        }
    }

    public function render(): View
    {
        return view('livewire.course-selection');
    }
}
