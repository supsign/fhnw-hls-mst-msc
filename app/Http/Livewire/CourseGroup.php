<?php

namespace App\Http\Livewire;

use App\Models\PageContent;
use Livewire\Component;

class CourseGroup extends Component
{
    public array $courseGroup;
    public array $nextSemesters;
    public array $selectedCourses;

    public bool $further = false;

    public ?string $description = null;
    public ?string $title = null;
    public bool $showType = false;

    public function mount() 
    {
        if(!$this->further) {
            $this->getTitle();
        }
    }

    public function render()
    {
        return view('livewire.course-group');
    }

    protected function getTitle()
    {
        $this->title = str_replace(
            ['#requiredCoursesCount', '#groupName'], 
            [$this->courseGroup['required_courses_count'], $this->courseGroup['name']],
            PageContent::where('name', 'group_title')->first()?->content
        );
    }
}
