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
        @dump($furtherCourses)


{{--         <livewire:further-course-groups 
            :coursesGrouped="$furtherCourses"
            :nextSemesters="$nextSemesters" 
            :title="$furtherCourses['title'] ?? null"
            :description="$furtherClusterDescription['description'] ?? null"
            :selectedCourses="$selectedCourses"
            class="mb-5"
        /> --}}
    @endforeach



</div>
