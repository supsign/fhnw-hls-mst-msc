<?php

namespace App\Http\Livewire;

use App\Helpers\GeneralHelper;
use App\Models\Course;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
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

    public function render(): ?View
    {
        return view('livewire.module-selection-form');
    }

    public function updating(): void
    {
        $this->ects = 0;
        $this->selectedCourses = [];
    }

    public function updated(): void {
        if ($this->specializationId > 0) {
            $this->specializationPlaceholder = null;
        }
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


    protected function init(): self
    {
        $this->semesterId = array_key_first($this->semesters);
        $this->studyModeId = array_key_first($this->studyModes);

        return $this;
    }
}
