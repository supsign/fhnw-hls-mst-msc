<?php

namespace App\Services;

use App\Enums\CourseGroupType;
use App\Enums\StudyMode;
use App\Models\Cluster;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Specialization;
use App\Services\GetUpcomingSemesters;
use Illuminate\Support\Collection;
use stdClass;

class GetCourseData
{
    protected ?string $courseGroupTitle;
    protected array $mainCourseIds;
    protected Specialization $specialization;

    public function __construct(
        protected GetUpcomingSemesters $getUpcomingSemestersService,
        protected GetThesisData $getThesesData
    ) {
        $this->courseGroupTitle = PageContent::getContentByName('group_title');
    }

    public function __invoke(Specialization $specialization, Semester $semester = null, StudyMode $studyMode = null): stdClass 
    {

        $this->specialization = $specialization;
        $this->mainCourseIds = $this->getCourses()->pluck('id')->toArray();

        if (!$studyMode) {
            $studyMode = StudyMode::FullTime;
        }

        $semesters = ($this->getUpcomingSemestersService)(
            $studyMode === StudyMode::FullTime ? 2 : 4,
            $semester?->start_date
        );

        return (object)[
            'courses' => array_merge(
                [$this->getCourseGroups()], 
                [$this->getFurtherCourses()],
            ),
            'semesters' => $semesters,
            'texts' => PageContent::findByName([
                'additional_comments_title',
                'description_before_further',
                'double_degree_checkbox_text',
                'double_degree_description',
                'double_degree_title',
                'modules_outside_description',
                'optional_english_title',
                'optional_english_description',
            ]),
            'theses' => ($this->getThesesData)($specialization, false, $semesters->first(), $studyMode),
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
            $courseGroup->title = $this->getCourseGroupTitle($courseGroup);

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

    protected function getCourseGroupTitle(CourseGroup $courseGroup): ?string
    {
        if (!$this->courseGroupTitle) {
            return $this->courseGroupTitle;
        }

        return str_replace(
            [
                '#requiredCoursesCount',
                '#groupName',
            ], [
                $courseGroup->required_courses_count,
                $courseGroup->name,
            ], 
            $this->courseGroupTitle
        );
    }
}