<?php

namespace App\Http\Livewire;

use App\Enums\StudyMode;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Semester;
use App\Services\Semesters\GetUpcomingSemestersService;
use App\Helpers\GeneralHelper;
use App\Models\PageContent;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public array $coursesByCourseGroup;
    public array $nextSemesters;
    public array $semesters;
    public array $specializations;
    public array $studyModes;

    public array $selectedCourses = [];

    public bool $doubleDegree = false;

    public int $ects = 0;
    public ?int $specializationId = null;
    public ?int $studyModeId = null;
    public ?string $semesterId = null;
    public ?string $specializationPlaceholder = '-- Choose Specialization --';
    public ?string $studyModeTooltip = 'full-time: 3 semesters including MSc Thesis, part-time: approximately 5 semesters';

    public ?string $surname = null;
    public ?string $givenName = null;

    public int $specializationSelectedCount = 0;
    public int $specializationRequiredCount = 0;
    public int $electiveSelectedCount = 0;
    public int $electiveRequiredCount = 0;
    public int $coreCompetencesSelectedCount = 0;
    public int $coreCompetencesRequiredCount = 0;

    protected GetCourseSelectDataService $getCourseSelectDataService;
    protected GetUpcomingSemestersService $getUpcomingSemestersService;

    protected $listeners = [
        'courseSelected'
    ];
    protected array $pageContents = [
        'modules_outside_title',
        'modules_outside_description'
    ];

    public function courseSelected(int $courseGroupId, int $courseId, int|string $semesterId): void
    {
        if ($semesterId !== 'none') {
            $this->selectedCourses[$courseGroupId][$courseId] = $semesterId;
        } else {
            foreach ($this->selectedCourses AS $key => $value) {
                unset($this->selectedCourses[$key][$courseId]);

                if (empty($this->selectedCourses[$key])) {
                    unset($this->selectedCourses[$key]);
                }
            }
        }

        $this->getEcts();
    }

    public function mount(): void
    {
        $this->init();
    }

    public function render(): View
    {
        return view('livewire.module-selection-form');
    }

    public function updating(): void
    {
        $this->ects = 0;
        $this->selectedCourses = [];
    }

    public function updated(): void
    {
        if($this->specializationId > 0) {
            $this->specializationPlaceholder = null;
        }

        if($this->specializationId > 0) {
            $this->getCoursesByCourseGroup();
            $this->getRequiredCounts();
        }
    }
    protected function getCoursesByCourseGroup() {

        $this->getCourseSelectDataService = App::make(GetCourseSelectDataService::class);
        $specialization = Specialization::find($this->specializationId);
        $this->coursesByCourseGroup = ($this->getCourseSelectDataService)($specialization);
    }

    protected function getEcts(): self
    {
        $this->ects = 0;

        if (empty($this->selectedCourses)) {
            return $this;
        }

        foreach (Course::find(array_keys(array_replace_recursive(...$this->selectedCourses))) AS $course) {
            $this->ects += $course->ects;
        }

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

    protected function getModuleCounts(): self
    {
        foreach ($this->selectedCourses AS $key => $value ) {
            $group = CourseGroup::find($key);
            $this->{lcfirst($group->type->name)."SelectedCount"} = count($value);
        }

        return $this;
    }

    protected function getRequiredCounts() 
    {
        foreach ($this->coursesByCourseGroup AS $courseGroup) {
            $group = CourseGroup::find($courseGroup['id']);
            $this->{lcfirst($group->type->name) . "RequiredCount"} = $group->required_courses_count;
        }
    }

    protected function init(): self
    {
        $this->getUpcomingSemestersService = App::make(GetUpcomingSemestersService::class);

        $this->semesterId = array_key_first($this->semesters);
        $this->studyModeId = array_key_first($this->studyModes);
        $this->nextSemesters = ($this->getUpcomingSemestersService)(
            $this->studyModeId === StudyMode::FullTime->value ? 2 : 4 , 
            Semester::find($this->semesterId)->start_date)
        ->toArray();

        return $this;
    }

    protected function rules(): array
    {
        return [
            'surname' => 'required',
            'givenName' => 'required',
            'ects' => 'integer|min:50',
            'specializationSelectedCount' => ['integer', 'min:'.$this->specializationRequiredCount],
            'electiveSelectedCount' => ['integer', 'min:'.$this->electiveRequiredCount],
            'coreCompetencesSelectedCount' => ['integer', 'min:'.$this->coreCompetencesRequiredCount],
        ];
    }
    protected array $messages = [
        'ects.min' => 'You have selected modules worth fewer than 50 ECTS.',
        'specializationSelectedCount.min' => 'You have not selected enough modules in :attribute. Please correct.',
        'electiveSelectedCount.min' => 'You have not selected enough modules in :attribute. Please correct.',
        'coreCompetencesSelectedCount.min' => 'You have not selected enough modules in :attribute. Please correct.',
    ];
    protected function validationAttributes(): array
    {
        $groups = [];
        foreach ($this->coursesByCourseGroup AS $courseGroup) {
            $group = CourseGroup::find($courseGroup['id']);

            $groups[lcfirst($group->type->name)."SelectedCount"] = $courseGroup['name'];
        }
       return $groups;
    }

    public function submit(): void
    {
        $this->getModuleCounts();
        $this->validate();
    }
}
