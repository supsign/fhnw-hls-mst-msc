<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FurtherCourseGroups extends Component
{
    public array $coursesGrouped;
    public array $nextSemesters;
    public array $selectedCourses;

    public string $class;
    public ?string $title;
    public ?string $description;

    public function mount(): void
    {
        $this->courseGroups = $this->coursesGrouped['specializations'] ?? $this->coursesGrouped['clusters'] ?? [$this->coursesGrouped['cluster']]; 
    }

    public function render()
    {
        return view('livewire.further-course-groups');
    }
}
