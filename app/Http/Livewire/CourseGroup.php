<?php

namespace App\Http\Livewire;

use App\Models\PageContent;
use Livewire\Component;

class CourseGroup extends Component
{
    public array $group;
    public array $nextSemesters;
    public bool $further = false;
    public string $class;
    public string $title;
    public string $description;

    public function mount() {
        $requiredCoursesCount = $this->group['required_courses_count'];
        $groupName = $this->group['name'];
        $content = PageContent::where('name', 'group_title')->first()->content;
        $titleWithRequired = str_replace("#requiredCoursesCount", $requiredCoursesCount, $content);
        $this->title = str_replace('#groupName', $groupName, $titleWithRequired);
    }
    
    public function render()
    {
        return view('livewire.course-group');
    }
}
