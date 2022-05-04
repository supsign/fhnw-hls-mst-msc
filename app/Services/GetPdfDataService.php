<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Thesis;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetPdfDataService
{
    protected array $data = [];

    public function __invoke(Request $request): array
    {
        foreach ($request->all() AS $key => $value) {
            switch ($key) {
                case 'specialization':
                    $value = Specialization::find($value);
                    break;

                case 'thesis_start':
                    $value = Semester::find($value);
                    $value->end_date = $this->getThesisEndDate($value);
                    break;

                case 'thesis_subject':
                    $value = Thesis::find($value);
                    break;

                case 'selected_courses':
                    $value = $this->getSelectedCourses($value);
                    break;
            }

            $this->data[$key] = $value;
        }
        return $this->data;
    }

    protected function getSelectedCourses(array $selectedCourseData): Collection
    {   
        $semesterIds = collect($selectedCourseData)->flatten(2)->unique();
        $semesters = Semester::find($semesterIds)->sortBy('start_date');
        $coursesGrouped = collect($selectedCourseData)->flatten(1);

        if ($semesterIds->count() > $semesters->count()) {
            $semesters->push(Semester::new(['name' => 'later']));
        }

        foreach ($semesters AS $semester) {
            foreach ($coursesGrouped AS $courseGroup) {
                foreach ($courseGroup AS $courseId => $semesterId) {
                    if ($semester->name === $semesterId) {
                        $semester->selectedCourses->push(Course::find($courseId));
                        continue;
                    }

                    if ($semesterId == $semester->id) {
                        $semester->selectedCourses->push(Course::find($courseId));
                    }
                }
            }
        }

        return $semesters;
    }

    protected function getThesisEndDate($semester) {
        $start = Carbon::parse($semester['start_date']);
        switch ($start->month) {
            case 2:
                return $start->addMonth(8)->toDateTimeString();

            case 6:
                return $start->addMonth(9)->toDateTimeString();
        }
    }
}
