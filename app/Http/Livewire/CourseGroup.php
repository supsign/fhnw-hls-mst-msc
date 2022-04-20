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
    public array $groupCoursesIds = [];
    public array $selectedCoursesIds = [];

    protected $listeners = [
        'updateGroupCoursesCount'
    ];

    public function mount() {
        $requiredCoursesCount = $this->group['required_courses_count'];
        $groupName = $this->group['name'];
        $content = PageContent::where('name', 'group_title')->first()->content;
        $titleWithRequired = str_replace("#requiredCoursesCount", $requiredCoursesCount, $content);
        $this->title = str_replace('#groupName', $groupName, $titleWithRequired);
        $this->fillGroupCoursesIds();
    }

    public function fillGroupCoursesIds() {
        foreach($this->group['courses'] as $course) {
            $this->groupCoursesIds[] = $course['id'];
        }

    }
    public function updateGroupCoursesCount(int $groupId, int $courseId, bool $delete){
        if($groupId === $this->group['id']) {
            $key = array_search($courseId, $this->selectedCoursesIds);
            if($key) {
                if($delete) {
                    unset($this->selectedCoursesIds[$key]);
                } else {
                    $this->selectedCoursesIds[$key] = $courseId;
                }
            }
            $this->selectedCoursesIds[] = $courseId;
        }
    }

    public function render()
    {
        return view('livewire.course-group');
    }
}
