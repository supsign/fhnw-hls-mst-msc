<div class="flex flex-col">
    @if($defaultCourseGroup && count($defaultCourseGroup['courses']) > 0)
        <livewire:course-group :group="$defaultCourseGroup" :nextSemesters="$nextSemesters"/>
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
</div>
