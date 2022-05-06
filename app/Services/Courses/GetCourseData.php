<?php

namespace App\Services\Courses;

use App\Enums\CourseGroupType;
use App\Enums\StudyMode;
use App\Models\Cluster;
use App\Models\CourseGroupSpecialization;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\Semesters\GetUpcomingSemestersService;
use Illuminate\Support\Collection;

class GetCourseData
{
    protected array $mainCourseIds;
    protected Specialization $specialization;

    public function __construct(protected GetUpcomingSemestersService $getUpcomingSemestersService)
    {}

    public function __invoke(Specialization $specialization, Semester $semester = null, StudyMode $studyMode = StudyMode::FullTime): array 
    {
        $this->specialization = $specialization;
        $this->mainCourseIds = $this->getCourses()->pluck('id')->toArray();

        return [
            'courses' => array_merge(
                [$this->getCourseGroups()], 
                $this->getFurtherCourses()
            ),
            'semesters' => ($this->getUpcomingSemestersService)(
                $studyMode === StudyMode::FullTime ? 4 : 2,
                $semester
            ),
            'texts' => []
        ];
    }

    protected function getFurtherCourses(): array
    {
        return [
            (object)[
                    'title' => PageContent::findByName('further_specialisation_title')?->content,
                    'description' => PageContent::findByName('further_specialisation_description')?->content,
                    'type' => CourseGroupType::Specialization,
                    'specializations' => $this->getFurtherCoursesBySpecialization()
                ],
                (object)[
                    'title' => PageContent::findByName('further_cluster_title')?->content,
                    'description' => PageContent::findByName('further_cluster_description')?->content,
                    'type' => CourseGroupType::ClusterSpecific,
                    'clusters' => $this->getFurtherCoursesByCluster(),
                ],
                (object)[
                    'title' => PageContent::findByName('further_other_cluster_title')?->content,
                    'description' => PageContent::findByName('further_other_cluster_description')?->content,
                    'type' => CourseGroupType::ClusterSpecific,
                    'clusters' => $this->getFurtherCoursesByCluster(true),
                ]
            ];
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
            ->with(['courses'])
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
            ->with(['courses'])
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