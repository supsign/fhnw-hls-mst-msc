<?php

namespace App\Services\Courses;

use App\Enums\CourseGroupType;
use App\Models\Cluster;
use App\Models\CourseGroupSpecialization;
use App\Models\PageContent;
use App\Models\Specialization;
use Illuminate\Support\Collection;

class GetCourseSelectDataService
{
    protected bool $furtherCourses;
    protected array $mainCourseIds;
    protected Specialization $specialization;

    public function __invoke(Specialization $specialization, bool $furtherCourses = false): array 
    {
        $this->furtherCourses = $furtherCourses;
        $this->specialization = $specialization;

        if ($this->furtherCourses) {
            $this->mainCourseIds = $this->getCourses()->pluck('id')->toArray();

            return [
                [
                    'title' => 'Further Specialisation Modules (Muttenz)',
                    'type' => CourseGroupType::Specialization,
                    'specializations' => $this->getFurtherCoursesBySpecialization()->toArray()
                ],
                [
                    'title' => 'Further Cluster-specific Modules from your Cluster',
                    'type' => CourseGroupType::ClusterSpecific,
                    'clusters' => $this->getFurtherCoursesByCluster()->toArray(),
                ],
                [
                    'title' => 'Further Modules from other Clusters',
                    'type' => CourseGroupType::ClusterSpecific,
                    'description' => PageContent::where('name', 'other_cluster_modules_description')->first()?->content,
                    'clusters' => $this->getFurtherCoursesByCluster(true)->toArray(),
                ]
            ];
        }

        return $this->getCourseGroups()->toArray();
    }

    protected function getFurtherCoursesByCluster($otherClusters = false): Collection
    {
        $clusters = Cluster::where(function ($query) use ($otherClusters) {
                if ($otherClusters) {
                    $query->where('id', '<>', $this->specialization->cluster_id);
                } else {
                    $query->where('id', $this->specialization->cluster_id);
                }
            })
            ->with(['courses', 'courses.semesters'])
            ->get();

        foreach ($clusters AS $cluster) {
            $cluster->setRelation(
                'courses', 
                $cluster->courses->filter(
                    fn ($course) => !in_array($course->id, $this->mainCourseIds)
                )
            );
        }

        return $clusters
            ->filter(fn ($cluster) => $cluster->courses->count())
            ->values();
    }

    protected function getFurtherCoursesBySpecialization(): Collection
    {
        $specializations = Specialization::where('specializations.id', '<>', $this->specialization->id)
            ->with(['courses', 'courses.semesters'])
            ->get();

        foreach ($specializations AS $specialization) {
            $specialization->setRelation(
                'courses', 
                $specialization->courses->filter(
                    fn ($course) => !in_array($course->id, $this->mainCourseIds)
                )
            );
        }

        return $specializations
            ->filter(fn ($specialization) => $specialization->courses->count())
            ->values();
    }

    protected function getCourses(): Collection
    {
        return $this->getCourseGroups()->pluck('courses')->flatten();
    }

    protected function getCourseGroupIds(): array
    {
        return array_map(
            fn ($courseGroupType) => $courseGroupType->value, 
            CourseGroupType::withoutClusterSpecific()
        );
    }

    protected function getCourseGroups(): Collection
    {
        return CourseGroupSpecialization::join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
            ->where('specialization_id', $this->specialization->id)
            ->whereIn('course_groups.type', $this->getCourseGroupIds())
            ->with([
                'courseGroup',
                'courseGroup.courses',
                'courseGroup.courses.semesters',
                'courseGroup.specializations'
            ])
            ->orderBy('course_groups.type')
            ->get()
                ->pluck('courseGroup')
                ->unique()
                ->filter(function ($courseGroup) {
                    return $courseGroup->courses->count();
                })
                ->values();
    }
}