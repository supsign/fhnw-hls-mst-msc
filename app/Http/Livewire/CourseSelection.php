<?php

namespace App\Http\Livewire;

use App\Models\Semester;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use App\Services\PageContents\PageContentService;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;

class CourseSelection extends Component
{
    public array $coursesByCourseGroup;
    public array $furtherCoursesBySpecialisationAndCluster;

    public array $nextSemesters;
    public array $selectedCourses;

    public int $semesterId;
    public int $specializationId;
    public int $studyModeId;
    public int $ects;
    public ?string $coreCompetencesDescription = null;
    public ?string $descriptionBeforeFurther = null;
    public ?string $furtherClusterTitle = null;
    public ?string $furtherSpecialisationTitle = null;

    protected GetCourseSelectDataService $getCourseSelectDataService;
    protected PageContentService $pageContentService;
    protected Semester $semester;
    protected Specialization $specialization;

    protected array $pageContents = [
        'core_competences_description',
        'description_before_further',
        'further_cluster_title',
        'further_cluster_description',
        'further_specialisation_title',
        'further_specialisation_description',
        'further_other_cluster_title',
        'further_other_cluster_description'
    ];

    public function mount(): void
    {
        $this
            ->initSerivces()
            ->executeServices()
            ->getPageContents();
    }

    public function render(): View
    {
        return view('livewire.course-selection');
    }

    protected function executeServices(): self
    {
        $this->furtherCoursesBySpecialisationAndCluster = ($this->getCourseSelectDataService)($this->specialization, true);

        return $this;
    }

    protected function getPageContents(): self
    {
        foreach (($this->pageContentService)($this->pageContents) AS $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    protected function initSerivces(): self
    {
        $this->getCourseSelectDataService = App::make(GetCourseSelectDataService::class);
        $this->pageContentService = App::make(PageContentService::class);

        return $this->initSerivceData();
    }

    protected function initSerivceData(): self
    {
        $this->semester = Semester::find($this->semesterId);
        $this->specialization = Specialization::find($this->specializationId);

        return $this;
    }
}
