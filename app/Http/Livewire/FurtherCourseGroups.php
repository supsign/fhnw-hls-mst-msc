<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FurtherCourseGroups extends Component
{
    public array $courseGroups;
    public array $nextSemesters;
    public array $selectedCourses;

    public string $class;
    public ?string $title;
    public ?string $description;

    public function mount(): void
    {
        $this->syncSelection();
    }

    public function render()
    {
        return view('livewire.further-course-groups');
    }

    protected function syncSelection(): self
    {
        if (empty($this->selectedCourses)) {
            return $this;
        }
        
        $courseGroupIds = array_column($this->courseGroups, 'id');
        $selectedCoursesFlat = array_replace_recursive(...$this->selectedCourses);
        $this->selectedCourses = [];

        foreach ($courseGroupIds AS $courseGroupId) {
            $this->selectedCourses[$courseGroupId] = $selectedCoursesFlat;
        }

        return $this;
    }
}
