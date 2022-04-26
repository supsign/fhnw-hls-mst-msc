<?php

namespace App\Http\Livewire;

use App\Models\PageContent;
use Livewire\Component;

class CourseGroup extends Component
{
    public array $courseGroup;
    public array $nextSemesters;
    public array $selectedCourses;
    public array $courses = [];

    public bool $further = false;

    public ?string $description = null;
    public ?string $title = null;

    public function mount() 
    {
        $this->getTitle();
        if(!$this->further) {
            $this->getSortCourses();
        } else {
            $this->courses = $this->courseGroup['courses_filtered'];
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


    protected function getSortCourses() {
      usort($this->courseGroup['courses'], function($a, $b) {
            return $a['semesters'][0]['start_date'] <=> $b['semesters'][0]['start_date'];
        });
        $this->courses = $this->courseGroup['courses'];
    }
}
