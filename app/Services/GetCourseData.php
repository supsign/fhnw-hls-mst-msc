<?php

namespace App\Services;

use App\Enums\CourseGroupType;
use App\Enums\StudyMode;
use App\Models\Cluster;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\PageContent;
use App\Models\Semester;
use App\Models\Slot;
use App\Models\Specialization;
use App\Services\GetUpcomingSemesters;
use Illuminate\Support\Collection;
use stdClass;

class GetCourseData
{
    protected ?string $courseGroupTitle;
    protected array $mainCourseIds;
    protected Collection $semesters;
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

        if (!$studyMode) {
            $studyMode = StudyMode::FullTime;
        }

        $this->semesters = ($this->getUpcomingSemestersService)(
            $studyMode === StudyMode::FullTime ? 2 : 4,
            $semester?->start_date
        );

        $this->mainCourseIds = $this->getCourses()->pluck('id')->toArray();

        return (object)[
            'courses' => array_merge(
                [$this->getCourseGroups()], 
                [$this->getFurtherCourses()],
            ),
            'optional_courses' => (object)[
                'texts' => PageContent::findByName([
                    'optional_english_title',
                    'optional_english_description',
                ]),
                'courses' => Course::whereNull(['cluster_id', 'specialization_id'])
                    ->get()
                    ->filter($this->courseDateFilter())
                    ->values(),
            ],
            'semesters' => $this->semesters,
            'slots' => Slot::all(),
            'texts' => PageContent::findByName([
                'additional_comments_title',
                'description_before_further',
                'double_degree_checkbox_text',
                'double_degree_description',
                'double_degree_title',
                'modules_outside_description',
            ]),
            'theses' => ($this->getThesesData)($specialization, false, $this->semesters->first(), $studyMode),
        ];
    }

    protected function courseDateFilter(): callable
    {
        return fn (Course $course): bool =>
            is_null($course->endSemester)
            ||
            $course->endSemester->start_date >= $this->semesters->first()->start_date;
    }

    protected function getFurtherCourses(): array
    {
        $furtherCourses = [];

        if ($this->getFurtherCoursesBySpecialization()->count()) {
            $furtherCourses[] = (object)[
                'title' => PageContent::getContentByName('further_specialisation_title'),
                'description' => PageContent::getContentByName('further_specialisation_description'),
                'type' => CourseGroupType::Specialization,
                'specializations' => $this->getFurtherCoursesBySpecialization()
            ];
        }

        if ($this->getFurtherCoursesByCluster()->count()) {
            $furtherCourses[] = (object)[
                'title' => PageContent::getContentByName('further_cluster_title'),
                'description' => PageContent::getContentByName('further_cluster_description'),
                'type' => CourseGroupType::ClusterSpecific,
                'clusters' => $this->getFurtherCoursesByCluster(),
            ];
        }

        if ($this->getFurtherCoursesByCluster(true)->count()) {
            $furtherCourses[] = (object)[
                'title' => PageContent::getContentByName('further_other_cluster_title'),
                'description' => PageContent::getContentByName('further_other_cluster_description'),
                'type' => CourseGroupType::ClusterSpecific,
                'clusters' => $this->getFurtherCoursesByCluster(true),
            ];
        }

        return $furtherCourses;
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
            ->with([
                'courses', 
                'courses.cluster', 
                'courses.endSemester',
                'courses.startSemester'
            ])
            ->get();

        foreach ($clusters AS $cluster) {
            $cluster->setRelation(
                'courses', 
                $cluster
                    ->courses
                    ->filter(fn (Course $course): bool => !in_array($course->id, $this->mainCourseIds))
                    ->filter($this->courseDateFilter())
                    ->values()
            );
        }

        return $clusters
            ->filter(fn (Cluster $cluster): bool => $cluster->courses->count())
            ->values();
    }

    protected function getFurtherCoursesBySpecialization(): Collection
    {
        $specializations = Specialization::where('specializations.id', '<>', $this->specialization->id)
            ->with([
                'courses', 
                'courses.cluster', 
                'courses.endSemester',
                'courses.startSemester'
            ])
            ->get();

        foreach ($specializations AS $specialization) {
            $specialization->setRelation(
                'courses', 
                $specialization
                    ->courses
                    ->filter(fn (Course $course): bool => !in_array($course->id, $this->mainCourseIds))
                    ->filter($this->courseDateFilter())
                    ->values()
            );
        }

        return $specializations
            ->filter(fn (Specialization $specialization): bool => $specialization->courses->count())
            ->values();
    }

    protected function getCourses(): Collection
    {
        return $this->getCourseGroups()->pluck('courses')->flatten();
    }

    protected function getCourseGroupIds(): array
    {
        return array_map(
            fn (CourseGroupType $courseGroupType): int => $courseGroupType->value,
            CourseGroupType::cases()
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
                'courseGroup.courses.endSemester',
                'courseGroup.courses.startSemester',
            ])
            ->orderBy('course_groups.type')
            ->get()
                ->pluck('courseGroup')
                ->unique()
                ->filter(fn (CourseGroup $courseGroup): bool => $courseGroup->courses->count())
                ->values();

        foreach ($courseGroups AS $courseGroup) {
            $courseGroup->title = $this->getCourseGroupTitle($courseGroup);

            $courseGroup->setRelation(
                'courses', 
                $courseGroup
                    ->courses
                    ->filter($this->courseDateFilter())
                    ->values()
            );

            switch ($courseGroup->type->name) {
                case CourseGroupType::CoreCompetences->name:
                    $courseGroup->description = PageContent::getContentByName('core_competences_description');
                    break;

                default:
                    continue 2;
            }
        }

        return $courseGroups
            ->filter(fn (CourseGroup $courseGroup): bool => $courseGroup->courses->count())
            ->values();
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