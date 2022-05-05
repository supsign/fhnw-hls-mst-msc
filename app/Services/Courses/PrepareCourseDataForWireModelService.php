<?php

namespace App\Services\Courses;

use App\Models\Course;
use App\Models\Specialization;

class PrepareCourseDataForWireModelService
{
    const ADDITIONAL = 'additional';
    const FURTHER = 'further';
    const MAIN = 'main';

    public function __construct(protected GetCourseSelectDataService $getCourseSelectDataService)
    {
        
    }

    public function __invoke(Specialization $specialization): array
    {
        $mainCourseData = ($this->getCourseSelectDataService)($specialization);
        $furtherCourseData = ($this->getCourseSelectDataService)($specialization, true);

        $result = [
            self::MAIN => [],
            self::FURTHER => [],
            self::ADDITIONAL => [],
        ];

        foreach ($mainCourseData AS $mainCourseDate) {
            foreach ($mainCourseDate['courses'] AS $course) {
                $result[self::MAIN][$mainCourseDate['id']][$course['id']] = 'none';
            }
        }

        foreach ($furtherCourseData AS $furtherCourseDate) {
            foreach ($furtherCourseDate['clusters'] ?? [] AS $course) {
                $result[self::FURTHER][$course['id']] = 'none';
            }

            foreach ($furtherCourseDate['specializations'] ?? [] AS $course) {
                $result[self::FURTHER][$course['id']] = 'none';
            }
        }

        Course::whereNull('cluster_id')->whereNull('specialization_id')->get();

        foreach (Course::whereNull('cluster_id')->whereNull('specialization_id')->get() AS $course) {
            $result[self::ADDITIONAL][$course->id] = 'none';
        }

        return $result;
    }
}