<div class="flex flex-col">
    @if($specialisationCourseGroup && count($specialisationCourseGroup['courses']) > 0)
        <livewire:course-group :group="$specialisationCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif
    
    @if($electiveCourseGroup && count($electiveCourseGroup['courses']) > 0)
            <livewire:course-group :group="$electiveCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($coreCompetenceCourseGroup && count($coreCompetenceCourseGroup['courses']) > 0)
        <livewire:course-group :group="$coreCompetenceCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($clusterSpecificCourseGroup && count($clusterSpecificCourseGroup['courses']) > 0)
        <livewire:course-group :group="$clusterSpecificCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif
    
    @if(count($furtherClusterSpecificCourseGroups))
        {{-- <livewire:course-group :group="$furtherElectiveCourseGroups" :nextSemesters="$nextSemesters"/> --}}
    @endif

    @if(count($furtherSpecialisationCourseGroups))
        {{-- <livewire:course-group :group="$furtherSpecialisationCourseGroups" :nextSemesters="$nextSemesters"/> --}}
    @endif
</div>
