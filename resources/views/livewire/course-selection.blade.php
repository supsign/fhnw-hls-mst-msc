<div class="flex flex-col">

    {{-- @dump($selectedCoursesIds) --}}

    @if($specialisationCourseGroup && count($specialisationCourseGroup['courses']))
        <livewire:course-group :group="$specialisationCourseGroup" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    @if($electiveCourseGroup && count($electiveCourseGroup['courses']))
        <livewire:course-group :group="$electiveCourseGroup" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    @if($coreCompetenceCourseGroup && count($coreCompetenceCourseGroup['courses']))
        <livewire:course-group :group="$coreCompetenceCourseGroup" :nextSemesters="$nextSemesters"  :description="$coreCompetencesDescription" class="my-5"/>
    @endif

    @if($clusterSpecificCourseGroup && count($clusterSpecificCourseGroup['courses']))
        <livewire:course-group :group="$clusterSpecificCourseGroup" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    <div>{{ $descriptionBeforeFurther }}</div>

    @if(count($furtherSpecialisationCourseGroups))
        <livewire:further-course-groups :groups="$furtherSpecialisationCourseGroups" :nextSemesters="$nextSemesters" :title="$furtherSpecialisationTitle"  class="my-5"/>
    @endif

    @if(count($furtherClusterSpecificCourseGroups))
        <livewire:further-course-groups :groups="$furtherClusterSpecificCourseGroups" :nextSemesters="$nextSemesters" :title="$furtherClusterTitle" class="my-5"/>
    @endif
</div>
