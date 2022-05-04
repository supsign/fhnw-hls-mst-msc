<?php

namespace App\Http\Livewire;

use App\Enums\StudyMode;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Semester;
use App\Models\Thesis;
use App\Services\Semesters\GetUpcomingSemestersService;
use App\Helpers\GeneralHelper;
use App\Models\CourseCourseGroup;
use App\Models\PageContent;
use App\Models\Specialization;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\Redirector;

class ModuleSelectionForm extends Component
{
    public array $pdfData = [];
    public array $selectedCourses = [];

    public array $coursesByCourseGroup;
    public array $nextSemesters;
    public array $semesters;
    public array $specializations;
    public array $studyModes;

    public bool $doubleDegree = false;

    public int $ects = 0;
    public ?int $specializationId = null;
    public ?int $studyModeId = null;
    public ?string $semesterId = null;
    public ?string $specializationPlaceholder = '-- Choose Specialization --';
    public ?string $studyModeTooltip = 'full-time: 3 semesters including MSc Thesis, part-time: approximately 5 semesters';

    public ?string $surname = null;
    public ?string $givenName = null;
    public array $masterThesis = [];
    public array $statistics = [];
    public ?string $additionalComments = null;

    public int $specializationSelectedCount = 0;
    public int $specializationRequiredCount = 0;
    public int $electiveSelectedCount = 0;
    public int $electiveRequiredCount = 0;
    public int $coreCompetencesSelectedCount = 0;
    public int $coreCompetencesRequiredCount = 0;

    protected GetCourseSelectDataService $getCourseSelectDataService;
    protected GetUpcomingSemestersService $getUpcomingSemestersService;

    protected $listeners = [
        'courseSelected',
        'updateMasterThesis'
    ];
    protected array $messages = [
        'ects.min' => 'You have selected modules worth fewer than 50 ECTS.',
        'specializationSelectedCount.min' => 'You have not selected enough modules in :attribute. Please correct.',
        'electiveSelectedCount.min' => 'You have not selected enough modules in :attribute. Please correct.',
        'coreCompetencesSelectedCount.min' => 'You have not selected enough modules in :attribute. Please correct.',
        'masterThesis.theses.required' => 'Please select a broad topic for your MSc Thesis.',
        'statistics.cluster_specific.min' => 'You need to select at least three cluster-specific modules. Please correct.'

    ];
    protected array $pageContents = [
        'modules_outside_title',
        'modules_outside_description'
    ];

    public function courseSelected(int $courseGroupId, int $courseId, int|string $semesterId, bool $further): void
    {
        $type = $further ? 'further' : 'main';

        if ($semesterId !== 'none') {
            $this->selectedCourses[$type][$courseGroupId][$courseId] = $semesterId;
        } else {
            foreach ($this->selectedCourses[$type] AS $key => $value) {
                unset($this->selectedCourses[$type][$key][$courseId]);

                if (empty($this->selectedCourses[$type][$key])) {
                    unset($this->selectedCourses[$type][$key]);
                }

                if (empty($this->selectedCourses[$type])) {
                    unset($this->selectedCourses[$type]);
                }
            }
        }
        $this->statistics = $this->getCoursesCountByCourseGroup();
        $this->getEcts();
    }


    public function mount(): void
    {
        $this->init();
        $this->broadSubjectArea = Thesis::where('specialization_id', $this->specializationId)->get()->toArray();
    }

    public function render(): View
    {
        return view('livewire.module-selection-form');
    }

    public function updating($name): void
    {
        if (!in_array($name, ['givenName', 'surname'])) {
            $this->ects = 0;
            $this->selectedCourses = [];
        }
    }

    public function updated(): void
    {
        if ($this->specializationId > 0) {
            $this->getCoursesByCourseGroup();
            $this->getRequiredCounts();
            $this->getNextSemesters();
        }
    }

    protected function getCoursesByCourseGroup(): void
    {
        $this->getCourseSelectDataService = App::make(GetCourseSelectDataService::class);
        $specialization = Specialization::find($this->specializationId);
        $this->coursesByCourseGroup = ($this->getCourseSelectDataService)($specialization);
    }

    public function updateMasterThesis(array $start, array $theses, ?string $furtherDetails) 
    {
        $this->masterThesis['start'] = $start ?? null;
        $this->masterThesis['theses'] = $theses  ?? null;
        $this->masterThesis['furtherDetails'] = $furtherDetails;
    }

    protected function getEcts(): self
    {
        $this->ects = 0;

        if (empty($this->selectedCourses)) {
            return $this;
        }

        foreach ($this->selectedCourses AS $type) {
            foreach (Course::find(array_keys(array_replace_recursive(...$type))) AS $course) {
                $this->ects += $course->ects;
            }
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
        foreach ($this->selectedCourses['main'] ?? [] AS $key => $value) {
            $group = CourseGroup::find($key);
            $this->{lcfirst($group->type->name).'SelectedCount'} = count($value);
        }

        return $this;
    }

    protected function getNextSemesters(): self
    {
        $this->nextSemesters = App::make(GetUpcomingSemestersService::class)(
            $this->studyModeId === StudyMode::FullTime->value ? 2 : 4 ,
            Semester::find($this->semesterId)->start_date
        )->toArray();

        return $this;
    }

    protected function getRequiredCounts(): void
    {
        foreach ($this->coursesByCourseGroup AS $courseGroup) {
            $group = CourseGroup::find($courseGroup['id']);
            $this->{lcfirst($group->type->name).'RequiredCount'} = $group->required_courses_count;
        }
    }

    protected function getPdfData(): void
    {
        $this->pdfData['givenName'] = $this->givenName;
        $this->pdfData['surname'] = $this->surname;
        $this->pdfData['specialization'] = $this->specializationId;
        $this->pdfData['selected_courses'] = $this->selectedCourses;
        $this->pdfData['specialization_count'] = $this->getCoursesCount();
        $this->pdfData['ects'] = $this->ects;
        $this->pdfData['thesis_start'] = $this->masterThesis['start']['id'];
        $this->pdfData['thesis_subject'] = $this->masterThesis['theses'];
        $this->pdfData['counts'] = $this->getCoursesCountByCourseGroup();
        $this->pdfData['additional_comments'] = $this->additionalComments;
    }

    protected function getCoursesCount(): int
    {
        $count = 0;

        foreach ($this->selectedCourses['main'] ?? [] AS $key => $value) {
            $group = CourseGroup::find($key);
            $count +=  $this->{lcfirst($group->type->name).'SelectedCount'};
        }

        return $count;
    }

    protected function getCoursesCountByCourseGroup()
    {
        $courseIds = [];

        foreach (collect($this->selectedCourses)->flatten(1)->toArray() AS $courses) {
            $courseIds = array_merge($courseIds, array_keys($courses));
        }

        return [
            'specialization' => Course::whereIn('id', $courseIds)->whereNotNull('specialization_id')->count(),
            'cluster_specific' => Course::whereIn('id', $courseIds)->whereNotNull('cluster_id')->count(),
            'core_compentences' => CourseCourseGroup::whereIn('course_id', $courseIds)->where('course_group_id', 4)->count(),
        ];
    }

    protected function init(): self
    {
        $this->semesterId = array_key_first($this->semesters);
        $this->studyModeId = array_key_first($this->studyModes);

        return $this->getNextSemesters();
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
            'masterThesis.theses' => 'required',
            'statistics.cluster_specific' => 'integer|min:3',
        ];
    }

    protected function validationAttributes(): array
    {
        $groups = [];

        foreach ($this->coursesByCourseGroup AS $courseGroup) {
            $groups[lcfirst(CourseGroup::find($courseGroup['id'])->type->name).'SelectedCount'] = $courseGroup['name'];
        }

        return $groups;
    }

    public function submit(): Redirector
    {
        $this->getModuleCounts();
        $this->statistics = $this->getCoursesCountByCourseGroup();
        $this->validate();
        $this->getPdfData();

        return redirect()->route('home.pdf', $this->pdfData);
    }

}
