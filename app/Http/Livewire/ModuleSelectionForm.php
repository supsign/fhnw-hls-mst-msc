<?php

namespace App\Http\Livewire;

use App\Models\Course;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public array $semesters;
    public array $specializations;
    public array $studyModes;

    public array $selectedCourses = [];

    public int $ects = 0;
    public ?int $specializationId = null;
    public ?int $studyModeId = null;
    public ?string $semesterId = null;

    protected $listeners = [
        'courseSelected'
    ];

    public function courseSelected(int $courseGroupId, int $courseId, int|string $semesterId): void
    {
        if ($semesterId !== 'none') {
            $this->selectedCourses[$courseGroupId][$courseId] = $semesterId;
        } else {
            unset($this->selectedCourses[$courseGroupId][$courseId]);
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
        $this->selectedCourses = [];
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

    protected function init(): self
    {
        $this->semesterId = array_key_first($this->semesters);
        $this->specializationId = $this->specializations[0]['id'];      //  remove when reintroducing placeholder
        $this->studyModeId = array_key_first($this->studyModes);

        return $this;   
    }
}
