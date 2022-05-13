<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Helpers\GeneralHelper;
use App\Http\Requests\PostPdfData;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Semester;
use App\Models\Specialization;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use stdClass;

class GetPdfData
{
    protected stdClass $courseData;
    protected Collection $overlappingCoursesData;
    protected array $pdfData = [];
    protected Semester $semester;
    protected Specialization $specialization;
    protected Semester $thesisStart;

    public function __construct(
        protected GetCourseData $getCourseData, 
        protected GetOverlappingCourses $getOverlappingCourses
    ) {
        $this->overlappingCoursesData = collect();
    }

    public function __invoke(PostPdfData $request): array
    {
        $this->addModels(
            $request->only([
                'semester', 
                'specialization',
            ]) + [
                'thesis' => $request->master_thesis['theses']
            ]
        )->addSelectedCourses(
            $request->only([
                'optional_courses',
                'selected_courses',
            ])
        )->addToPdfData(
            $request->only([
                'additional_comments',
                'double_degree',
                'given_name',
                'modules_outside',
                'surname',
                'statistics',
            ]) + [
                'overlapping_courses' => $this->overlappingCoursesData,
                'study_mode' => StudyMode::getByValue($request->study_mode),
                'thesis_further_details' => $request->master_thesis['further_details'],
                'thesis_end' => $request->master_thesis['time_frames']['end'],
                'thesis_start' => $request->master_thesis['time_frames']['start']['long_name']
            ]
        );

        return $this->pdfData;
    }

    protected function addModels(array $data): self
    {
        foreach ($data AS $key => $value) {
            $model = 'App\\Models\\'.ucfirst($key);
            $this->{$key} = $data[$key] = $model::find($value);
        }

        return $this->addToPdfData($data);
    }

    protected function addSelectedCourses(array $selectedCoursesData): self
    {
        $data = [];

        foreach ($selectedCoursesData AS $key => $selectedCourses) {
            $semesterIds = array_column($selectedCourses, 'semesterId');
            $semesters = Semester::find($semesterIds);

            if (count($semesterIds) > $semesters->count()) {
                $semesters->push(Semester::new(['name' => 'later']));
            }

            foreach ($semesters AS $semester) {
                foreach ($selectedCourses AS $value) {
                    if ($value['semesterId'] === $semester->id) {
                        $semester->selectedCourses = Course::find($value['courses'])->load('venue');
                        break;
                    }

                    if ($value['semesterId'] === $semester->name) {
                        $semester->selectedCourses = Course::find($value['courses'])->load('venue');
                        break;
                    }
                }

                $overlappingCourses = ($this->getOverlappingCourses)($semester->selectedCourses);

                if ($overlappingCourses->count()) {
                    $this->overlappingCoursesData->push((object)[
                        'semester' => $semester,
                        'slots' => ($this->getOverlappingCourses)($semester->selectedCourses)
                    ]);     
                }
            }

            $data[$key] = $semesters
                ->filter(fn ($semester) => $semester->selectedCourses->count())
                ->sortBy([
                    ['year', 'asc'],
                    ['type', 'desc'],
                ])->values();
        }

        return $this->addToPdfData($data);
    }

    protected function addToPdfData(array $data): self
    {
        $this->pdfData = $this->pdfData + $this->arrayKeysToCamelCase($data);

        return $this;
    }

    protected function arrayKeysToCamelCase(array $data): array
    {
        foreach ($data AS $key => $value) {
            $newKey = GeneralHelper::snakeToCamelCase($key);

            if ($newKey === $key) {
                continue;
            }

            $data[$newKey] = $value;
            unset($data[$key]);
        }

        return $data;
    }

    protected function getCourseData(): stdClass
    {
        if (empty($this->courseData)) {
            $this->courseData = ($this->getCourseData)($this->specialization);
        }

        return $this->courseData;
    }

    protected function getCourseGroupForCourse(Course $course): ?CourseGroup
    {
        foreach ($this->getCourseData()->courses[0] AS $courseGroup) {
            if ($courseGroup->courses->contains($course)) {
                return $courseGroup;
            }
        }

        foreach ($this->getCourseData()->courses[1] AS $furtherCourseData) {
            foreach (['clusters', 'specializations'] AS $key) {
                if (isset($furtherCourseData->{$key})) {
                    foreach ($furtherCourseData->{$key} AS $$key) {
                        if ($$key->courses->contains($course)) {
                            return CourseGroup::new(['type' => $furtherCourseData->type]);
                        }
                    }
                }
            }
        }

        return null;
    }
}