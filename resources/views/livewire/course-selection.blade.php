<div class="flex flex-col">
        <livewire:course-group :group="$coreCompetenceCourseGroup" :nextSemesters="$nextSemesters" />
        <livewire:course-group :group="$clusterSpecificCourseGroup" :nextSemesters="$nextSemesters" />
        <livewire:course-group :group="$defaultCourseGroup" :nextSemesters="$nextSemesters" />
        <livewire:course-group :group="$electiveCourseGroup" :nextSemesters="$nextSemesters" />
</div>
