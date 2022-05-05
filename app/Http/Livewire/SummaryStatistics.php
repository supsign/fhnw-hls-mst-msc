<?php

namespace App\Http\Livewire;

use App\Models\Course;
use App\Models\CourseCourseGroup;
use App\Services\Courses\GetCourseIdsFromSelectedCourses;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;

class SummaryStatistics extends Component
{
    public array $selectedCourses;
    public array $statistics;
    public array $masterThesis = [];

    public function mount()
    {
        $this->statistics = $this->getStatistics();
    }

    public function render(): View
    {
        // dump($this->selectedCourses);

        return view('livewire.summary-statistics');
    }

    protected function getStatistics(): array
    {
        $courseIds = App::make(GetCourseIdsFromSelectedCourses::class)($this->selectedCourses);

        return [
            'specialization' => Course::whereIn('id', $courseIds)->whereNotNull('specialization_id')->count(),
            'cluster_specific' => Course::whereIn('id', $courseIds)->whereNotNull('cluster_id')->count(),
            'core_compentences' => CourseCourseGroup::whereIn('course_id', $courseIds)->where('course_group_id', 4)->count(),
        ];
    }
}
