<div class="flex flex-col">
        @dump($selectedCourses)
    @if($specialisationCourseGroup && count($specialisationCourseGroup['courses']) > 0)
        <livewire:course-group :group="$specialisationCourseGroup" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    @if($electiveCourseGroup && count($electiveCourseGroup['courses']) > 0)
        <livewire:course-group :group="$electiveCourseGroup" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    @if($coreCompetenceCourseGroup && count($coreCompetenceCourseGroup['courses']) > 0)
        <livewire:course-group :group="$coreCompetenceCourseGroup" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    @if($clusterSpecificCourseGroup && count($clusterSpecificCourseGroup['courses']) > 0)
        <livewire:course-group :group="$clusterSpecificCourseGroup" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    @if(count($furtherSpecialisationCourseGroups))
        <livewire:further-course-groups :groups="$furtherSpecialisationCourseGroups" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif

    @if(count($furtherClusterSpecificCourseGroups))
        <livewire:further-course-groups :groups="$furtherClusterSpecificCourseGroups" :nextSemesters="$nextSemesters" class="my-5"/>
    @endif
</div>
