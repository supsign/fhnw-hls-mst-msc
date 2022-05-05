<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Course extends Component
{
    public array $course;
    public array $nextSemesters;
    public array $selectableSemesters;

    public bool $further = false;
    public bool $showType = false;

    public int $courseGroupId;
    
    public int|string $selectedSemester;

    public ?string $courseGroupTypeShortName;
    public ?string $courseGroupTypeTooltip;

    public function mount(): void
    {
        $this->getSelectableSemesters();
    }
    
    public function render(): View
    {
        return view('livewire.course');
    }

    protected function getSelectableSemesters(): self
    {
        $this->selectableSemesters[] = 'none';

        foreach ($this->nextSemesters AS $nextSemester) {
            $this->selectableSemesters[] = in_array($nextSemester['id'], array_column($this->course['semesters'], 'id'))
                ? $nextSemester['id']
                : null;
        }

        $this->selectableSemesters[] = 'later';

        return $this;
    }
}
