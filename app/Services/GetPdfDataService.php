<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Thesis;
use App\Services\Courses\GetCourseSelectDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetPdfDataService
{
    protected array $courseGroupData;
    protected array $data = [];
    protected array $furthercourseData;

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

    protected function getCourse(int $id): Course
    {
        $course = Course::find($id);

        foreach ($this->getCourseGroupData() AS $courseGroup) {
            if (in_array($id, array_column($courseGroup['courses'], 'id'))) {
                $course->courseGroup = CourseGroup::find($courseGroup['id']);

                return $course;
            }
        }

        foreach ($this->getFurtherCourseData() AS $furtherCourseData) {
            $courseIds = [];

            foreach (['clusters', 'specializations'] AS $key) {
                if (isset($furtherCourseData[$key])) {
                    foreach ($furtherCourseData[$key] AS $$key) {
                        $courseIds = array_merge($courseIds, array_column($$key['courses'], 'id'));
                    }

                    break;
                }
            }

            if (in_array($id, $courseIds)) {
                $course->courseGroup = CourseGroup::new([
                    'type' => $furtherCourseData['type']
                ]);

                return $course;
            }
        }

        return $course;
    }

    protected function getCourseGroupData(): array
    {
        if (!empty($this->courseGroupData)) {
            return $this->courseGroupData;
        }

        return $this->courseGroupData = ($this->getCourseSelectDataService)($this->specialization);
    }

    protected function getFurtherCourseData(): array
    {
        if (!empty($this->furthercourseData)) {
            return $this->furthercourseData;
        }

        return $this->furthercourseData = ($this->getCourseSelectDataService)($this->specialization, true);
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
                        break;
                    }

                    if ($semesterId == $semester->id) {
                        $semester->selectedCourses->push($this->getCourse($courseId));
                        break;
                    }
                }
            }
        }

        return $semesters;
    }

    protected function getThesisEndDate($semester) 
    {
        $start = Carbon::parse($semester['start_date']);
        
        switch ($start->month) {
            case 2:
                return $start->addMonth(8)->toDateTimeString();

            case 6:
                return $start->addMonth(9)->toDateTimeString();
        }
    }
}
