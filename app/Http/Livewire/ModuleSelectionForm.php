<?php

namespace App\Http\Livewire;


use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModuleSelectionForm extends Component
{
    public array $semesters;
    public array $specializations;
    public array $studyModes;

    public array $selectedCourses = [];

    public ?string $semesterId = null;
    public ?int $specializationId = null;
    public ?int $studyModeId = null;

    protected $listeners = [
        'courseSelected'
    ];

    protected bool $noRender = false;

    public function courseSelected(int $courseGroupId, int $courseId, int|string $semesterId): void
    {
        if ($semesterId !== 'none') {
            $this->selectedCourses[$courseGroupId][$courseId] = $semesterId;
        } else {
            unset($this->selectedCourses[$courseGroupId][$courseId]);
        }
    }

    public function mount(): void
    {
        $this->semesterId = array_key_first($this->semesters);
        $this->specializationId = $this->specializations[0]['id'];      //  remove when reintroducing placeholder
        $this->studyModeId = array_key_first($this->studyModes);
    }

    public function render(): ?View
    {
        return view('livewire.module-selection-form');
    }
}
