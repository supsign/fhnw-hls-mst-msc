<div class="flex flex-col">
        @dump($selectedCourses)
        @dump($laterCourses)
    @if($coreCompetenceCourseGroup)
        <livewire:course-group :group="$coreCompetenceCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($clusterSpecificCourseGroup)
        <livewire:course-group :group="$clusterSpecificCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($defaultCourseGroup)
        <livewire:course-group :group="$defaultCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif

    @if($electiveCourseGroup)
        <livewire:course-group :group="$electiveCourseGroup" :nextSemesters="$nextSemesters"/>
    @endif
</div>
