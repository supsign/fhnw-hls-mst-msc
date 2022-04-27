<div class="flex flex-col gap-10 mt-10">
    @foreach($this->coursesByCourseGroup AS $courseGroup)
        <livewire:course-group 
            :courseGroup="$courseGroup"
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses[$courseGroup['id']] ?? []"
            class="mb-5"
        />
    @endforeach

    @foreach ($furtherCoursesBySpecialisationAndCluster AS $furtherCourses)
        <livewire:further-courses 
            :furtherCourses="$furtherCourses" 
            :nextSemesters="$nextSemesters" 
            :selectedCourses="$selectedCourses"
        />
    @endforeach



</div>
