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

    public function __invoke(
        Specialization $specialization, 
        bool $furtherCourses = false,
    ): array {
        $this->furtherCourses = $furtherCourses;
        $this->specialization = $specialization;

        if ($this->furtherCourses) {
            return [
                //  toDo: title/description to PageContents
                ['title' => 'Further Specialisation Modules (Muttenz)'] + ['specializations' => $this->getFurtherCoursesBySpecialization()->toArray()],
                ['title' => 'Further Cluster-specific Modules from your Cluster'] + ['clusters' => $this->getFurtherCoursesByCluster()->toArray()],
                [
                    'title' => 'Further Modules from other Clusters',
                    'description' => PageContent::where('name', 'other_cluster_modules_description')->first()?->content
                ] + ['clusters' => $this->getFurtherCoursesByCluster(true)->toArray()],
            ];
        }

        return $this->getCourseGroups()->toArray();
    }

    protected function getFurtherCoursesByCluster($otherClusters = false): Collection
    {
        return Cluster::where(function ($query) use ($otherClusters) {
                if ($otherClusters) {
                    $query->where('id', '<>', $this->specialization->cluster_id);
                } else {
                    $query->where('id', $this->specialization->cluster_id);
                }
            })
            ->with(['courses', 'courses.semesters'])
            ->get()
                ->filter(fn ($cluster) => $cluster->courses->count())
                ->values();
    }

    protected function getFurtherCoursesBySpecialization(): Collection
    {
        return Specialization::where('id', '<>', $this->specialization->id)
            ->with(['courses', 'courses.semesters'])
            ->get()
                ->filter(fn ($specialization) => $specialization->courses->count())
                ->values();
    }

    // $this->mainCourseIds = $this->getCourses()->pluck('id')->toArray();
    // protected function getCourses(): Collection
    // {
    //     return $this->getCourseGroups()->pluck('courses')->flatten();
    // }

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