<?php

namespace App\Services;

use App\Enums\CourseGroupType;
use App\Enums\StudyMode;
use App\Models\Cluster;
use App\Models\CourseGroupSpecialization;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\GetUpcomingSemesters;
use Illuminate\Support\Collection;
use stdClass;

class GetCourseData
{
    protected array $mainCourseIds;
    protected Specialization $specialization;

    public function __construct(
        protected GetUpcomingSemesters $getUpcomingSemestersService,
        protected GetThesisData $getThesesData
    ) {}

    public function __invoke(Specialization $specialization, Semester $semester = null, ?StudyMode $studyMode = StudyMode::FullTime): stdClass 
    {
        $this->specialization = $specialization;
        $this->mainCourseIds = $this->getCourses()->pluck('id')->toArray();

        return (object)[
            'courses' => array_merge(
                [$this->getCourseGroups()], 
                $this->getFurtherCourses()
            ),
            'semesters' => ($this->getUpcomingSemestersService)(
                $studyMode === StudyMode::FullTime ? 4 : 2,
                $semester?->start_date
            ),
            'texts' => PageContent::findByName([
                'additional_comments_title',
                'description_before_further',
                'double_degree_title',
                'double_degree_description',
                'group_title',
                'modules_outside_description',
                'optional_english_title',
                'optional_english_description',
            ]),
            'theses' => ($this->getThesesData)($specialization),
        ];
    }

    protected function getFurtherCourses(): array
    {
        return [
            (object)[
                    'title' => PageContent::getContentByName('further_specialisation_title'),
                    'description' => PageContent::getContentByName('further_specialisation_description'),
                    'type' => CourseGroupType::Specialization,
                    'specializations' => $this->getFurtherCoursesBySpecialization()
                ],
            (object)[
                'title' => PageContent::getContentByName('further_cluster_title'),
                'description' => PageContent::getContentByName('further_cluster_description'),
                'type' => CourseGroupType::ClusterSpecific,
                'clusters' => $this->getFurtherCoursesByCluster(),
            ],
            (object)[
                'title' => PageContent::getContentByName('further_other_cluster_title'),
                'description' => PageContent::getContentByName('further_other_cluster_description'),
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
        $courseGroups = CourseGroupSpecialization::join('course_groups', 'course_group_specialization.course_group_id', '=', 'course_groups.id')
            ->where('specialization_id', $this->specialization->id)
            ->whereIn('course_groups.type', $this->getCourseGroupIds())
            ->with([
                'courseGroup',
                'courseGroup.courses',
            ])
            ->orderBy('course_groups.type')
            ->get()
                ->pluck('courseGroup')
                ->unique()
                ->filter(function ($courseGroup) {
                    return $courseGroup->courses->count();
                })
                ->values();

        foreach ($courseGroups AS $courseGroup) {
            switch ($courseGroup->type->name) {
                case CourseGroupType::CoreCompetences->name:
                    $courseGroup->description = PageContent::getContentByName('core_competences_description');
                    break;

                default:
                    continue 2;
            }
        }

        return $courseGroups;
    }
}