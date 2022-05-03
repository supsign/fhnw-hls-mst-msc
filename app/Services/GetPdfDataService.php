<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Thesis;
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
        $semesters = Semester::find(collect($selectedCourseData)->flatten(2)->unique());
        $coursesGrouped = collect($selectedCourseData)->flatten(1);

        dd($selectedCourseData['main']);

        foreach ($semesters AS $semester) {
            foreach ($coursesGrouped AS $courseGroup) {
                foreach ($courseGroup AS $courseId => $semesterId) {
                    if ((int)$semesterId === $semester->id) {
                        $semester->selectedCourses->push(Course::find($courseId));
                    }
                }
            }
        }

        return $semesters;
    }
}