<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Thesis;
use App\Services\Courses\GetCourseSelectDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetPdfDataService
{
    protected array $data = [];

    protected Specialization $specialization;

    public function __construct(protected GetCourseSelectDataService $getCourseSelectDataService)
    {}

    public function __invoke(Request $request): array
    {
        $requestData = $request->all();
        krsort($requestData);

        foreach ($requestData AS $key => $value) {
            switch ($key) {
                case 'specialization':
                    $this->specialization = $value = Specialization::find($value);
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

        dd($this->data['selected_courses']);

        return $this->data;
    }

    protected function getCourse(int $id): Course
    {





        return Course::find($id);
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
                        $semester->selectedCourses->push($this->getCourse($courseId));
                        continue;
                    }

                    if ($semesterId == $semester->id) {
                        $semester->selectedCourses->push($this->getCourse($courseId));
                    }
                }
            }
        }

        return $semesters;
    }
}