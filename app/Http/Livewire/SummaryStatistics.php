<?php

namespace App\Http\Livewire;

use App\Models\Course;
use App\Models\CourseCourseGroup;
use App\Models\Semester;
use App\Services\Courses\GetCourseIdsFromSelectedCourses;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;

class SummaryStatistics extends Component
{
    public array $selectedCourses;
    public array $statistics;
    public array $masterThesis = [];
    public array $ectsBySemester;

    public int $total;

    public function mount()
    {
        $this->ectsBySemester = $this->getEctsBySemester();
        $this->statistics = $this->getStatistics();
    }

    public function render(): View
    {
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

    protected function getEctsBySemester() 
    {
        $ectsBySemester = [];

        foreach($this->getSelectedCoursesBySemester() AS $semester) {
            foreach ($semester->selectedCourses AS $course) {
                if (!$course->ects) {
                    continue;
                }

                if (!isset($ectsBySemester[$semester->name])) {
                    $ectsBySemester[$semester->name] = $course->ects;
                    continue;
                } 

                $ectsBySemester[$semester->name] += $course->ects;
            }
        }

        return $ectsBySemester;
    }

    protected function getSelectedCoursesBySemester(): Collection
    {
        $var = array_filter(
            array_replace_recursive($this->selectedCourses['further'], ...$this->selectedCourses['main']),
            fn ($value) => $value !== 'none'
        );

        $semesterIds = array_unique($var);
        $semesters = Semester::find($semesterIds);

        if (count($semesterIds) > $semesters->count()) {
            $semesters->push(Semester::new(['name' => 'later']));
        }

        foreach ($var AS $courseId => $semesterId) {
            foreach ($semesters AS $semester) {
                if ($semester->name === $semesterId) {
                    $semester->selectedCourses->push(Course::find($courseId));
                    break;
                }

                if ($semester->id == $semesterId) {
                    $semester->selectedCourses->push(Course::find($courseId));
                    break;
                }
            }
        }

        return $semesters;
    }
}
