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

    @if(count($furtherSpecialisationCourses))
        {{-- <livewire:course-group :group="$furtherSpecialisationCourseGroup" :nextSemesters="$nextSemesters"/> --}}
    @endif
    
    @if(count($furtherElectiveCourses))
        {{-- <livewire:course-group :group="$furtherElectiveCourseGroup" :nextSemesters="$nextSemesters"/> --}}
    @endif
</div>
