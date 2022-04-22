<?php

namespace App\Http\Livewire;

use App\Enums\CourseGroupType;
use App\Enums\StudyMode;
use App\Helpers\GeneralHelper;
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

    public array $furtherClusterSpecificCourseGroups;
    public array $furtherSpecialisationCourseGroups;

    public array $nextSemesters;
    public array $selectedCourses;

    public int $ects;
    public int $semesterId;
    public int $specializationId;
    public int $studyModeId;

    public ?string $coreCompetencesDescription = null;
    public ?string $descriptionBeforeFurther = null;
    public ?string $furtherClusterTitle = null;
    public ?string $furtherSpecialisationTitle = null;

    protected GetCourseSelectDataService $getCourseSelectDataService;
    protected GetUpcomingSemestersService $getUpcomingSemestersService;
    protected Semester $semester;
    protected Specialization $specialization;

    protected array $pageContents = [
        'core_competences_description',
        'description_before_further',
        'further_cluster_title',
        'further_specialisation_title',
    ];

    public function mount(): void
    {
        $this
            ->initSerivces()
            ->getCourseGroups()
            ->getFurtherCourseGroups()
            ->getNextSemesters()
            ->getPageContents();
    }

    public function render(): View
    {
        return view('livewire.course-selection');
    }

    protected function getCourseGroups(): self
    {
        $this->coreCompetenceCourseGroup = ($this->getCourseSelectDataService)(CourseGroupType::CoreCompetences, $this->specialization, $this->semester);
        $this->clusterSpecificCourseGroup = ($this->getCourseSelectDataService)(CourseGroupType::ClusterSpecific, $this->specialization, $this->semester);
        $this->electiveCourseGroup = ($this->getCourseSelectDataService)(CourseGroupType::Elective, $this->specialization, $this->semester);
        $this->specialisationCourseGroup = ($this->getCourseSelectDataService)(CourseGroupType::Specialization, $this->specialization, $this->semester);

        return $this;
    }

    protected function getFurtherCourseGroups(): self
    {
        $this->furtherClusterSpecificCourseGroups = ($this->getCourseSelectDataService)(CourseGroupType::ClusterSpecific, $this->specialization, $this->semester, true);
        $this->furtherSpecialisationCourseGroups = ($this->getCourseSelectDataService)(CourseGroupType::Specialization, $this->specialization, $this->semester, true);

        return $this;
    }

    protected function getNextSemesters(): self
    {
        $this->nextSemesters = ($this->getUpcomingSemestersService)($this->studyModeId === StudyMode::FullTime->value ? 2 : 4 , $this->semester->start_date)->toArray();

        return $this;
    }

    protected function getPageContents(): self
    {
        $pageContents = PageContent::whereIn('name', $this->pageContents)->get();

        foreach ($pageContents AS $pageContent) {
            $this->{GeneralHelper::snakeToCamelCase($pageContent->name)} = $pageContent->content;
        }

        return $this;
    }

    protected function initSerivces(): self
    {
        $this->getCourseSelectDataService = App::make(GetCourseSelectDataService::class);
        $this->getUpcomingSemestersService = App::make(GetUpcomingSemestersService::class);

        return $this->initSerivceData();
    }

    protected function initSerivceData(): self
    {
        $this->semester = Semester::find($this->semesterId);
        $this->specialization = Specialization::find($this->specializationId);

        return $this;
    }
}
