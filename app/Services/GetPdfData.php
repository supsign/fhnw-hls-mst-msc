<?php

namespace App\Services;

use App\Enums\StudyMode;
use App\Helpers\GeneralHelper;
use App\Http\Requests\PostPdfData;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Semester;
use App\Models\Specialization;
use stdClass;

class GetPdfData
{
    protected stdClass $courseData;
    protected array $pdfData = [];
    protected Semester $semester;
    protected Specialization $specialization;

    public function __construct(protected GetCourseData $getCourseData)
    {}

    public function __invoke(PostPdfData $request): array
    {
        $this->addToPdfData(
            $request->only([
                'additional_comments',
                'double_degree',
                'given_name',
                'surname',
            ])
        )->addToPdfData([
            'studyMode' => StudyMode::getByValue($request->study_mode),
            'outsideModules' => $request->modules_outside,
        ])->addModels(
            $request->only([
                'semester', 
                'specialization',
            ])
        )->addSelectedCourses(
            $request->selected_courses
        );

        var_dump(
            $request->except([
                'additional_comments',
                'double_degree',
                'given_name', 
                'surname',
                'study_mode',
                'semester', 
                'specialization',
                'selected_courses',
                'modules_outside',
            ])
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

    protected function addSelectedCourses(array $selectedCourses): self
    {
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

            foreach ($semester->selectedCourses AS $course) {
                $course->courseGroup = $this->getCourseGroupForCourse($course);
            }
        }

        return $this->addToPdfData(['selectedCourses' => $semesters]);
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

    protected function getCourseGroupForCourse(Course $course): CourseGroup
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
    }
}