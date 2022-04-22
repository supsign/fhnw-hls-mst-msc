<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use App\Services\Semesters\GetUpcomingSemestersService;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;

class CourseSelection extends Component
{
    public array $coreCompetenceCourseGroup;
    public array $clusterSpecificCourseGroup;
    public array $electiveCourseGroup;
    public array $specialisationCourseGroup;

    public array $nextSemesters;

    public int $semesterId;
    public int $specializationId;
    public int $studyModeId;

    public ?string $coreCompetencesDescription;
    public ?string $descriptionBeforeFurther;
    public ?string $furtherClusterTitle;
    public ?string $furtherSpecialisationTitle;

    protected Semester $semester;

    public function mount(): void
    {
        $this
            ->getCourseGroups()
            ->getNextSemesters()
            ->getPageContents();
    }

    public function render(): View
    {
        return view('livewire.course-selection');
    }

    protected function getCourseGroups(): self
    {
        $this->semester = Semester::find($this->semesterId);
        $specialization = Specialization::find($this->specializationId);

        $getCourseSelectDataService = App::make(GetCourseSelectDataService::class);
        $this->coreCompetenceCourseGroup = $getCourseSelectDataService(CourseGroupType::CoreCompetences, $specialization, $this->semester);
        $this->clusterSpecificCourseGroup = $getCourseSelectDataService(CourseGroupType::ClusterSpecific, $specialization, $this->semester);
        $this->electiveCourseGroup = $getCourseSelectDataService(CourseGroupType::Elective, $specialization, $this->semester);
        $this->specialisationCourseGroup = $getCourseSelectDataService(CourseGroupType::Specialization, $specialization, $this->semester);

        return $this;
    }

    protected function getNextSemesters(): self
    {
        $getUpcomingSemestersService = App::make(GetUpcomingSemestersService::class);
        
        $this->nextSemesters = $getUpcomingSemestersService($this->studyModeId === 1 ? 2 : 4 , $this->semester->start_date)->toArray();

        return $this;
    }

    protected function getPageContents(): self
    {
        $this->coreCompetencesDescription = PageContent::where('name', 'core_competences_description')->first()?->content;
        $this->descriptionBeforeFurther = PageContent::where('name', 'description_before_further')->first()?->content;
        $this->furtherSpecialisationTitle = PageContent::where('name', 'further_specialisation_title')->first()?->content;
        $this->furtherClusterTitle = PageContent::where('name', 'further_cluster_title')->first()?->content;

        return $this;
    }
}
