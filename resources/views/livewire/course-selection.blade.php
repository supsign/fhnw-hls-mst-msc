<div class="flex flex-col">
    @if($coreCompetenceCourseGroup)
        <livewire:course-group :group="$coreCompetenceCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($specialisationCourseGroup)
        <livewire:course-group :group="$specialisationCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($electiveCourseGroup)
        <livewire:course-group :group="$electiveCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($clusterSpecificCourseGroup)
        <livewire:course-group :group="$clusterSpecificCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif
    
    @if(count($furtherClusterSpecificCourseGroups))
        {{-- <livewire:course-group :group="$furtherElectiveCourseGroups" :nextSemesters="$nextSemesters"/> --}}
    @endif

    @if(count($furtherSpecialisationCourseGroups))
        {{-- <livewire:course-group :group="$furtherSpecialisationCourseGroups" :nextSemesters="$nextSemesters"/> --}}
    @endif
</div>
