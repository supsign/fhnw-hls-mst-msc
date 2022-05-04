<div class="flex flex-col gap-10 mt-10">
    @foreach($this->coursesByCourseGroup AS $courseGroup)
        <livewire:course-group
            :description="$courseGroup['id'] === 4 ? $coreCompetencesDescription : null"
            :courseGroup="$courseGroup"
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses['main'][$courseGroup['id']] ?? []"
            :showType="true"
            class="mb-5"
        />
    @endforeach

    <div class="sticky top-0 bg-hls p-2 z-10 self-start">{!! $descriptionBeforeFurther  !!} Current ECTS: {{ $ects }}/50</div>
    
    @foreach ($furtherCoursesBySpecialisationAndCluster AS $furtherCourses)
        <livewire:further-courses
            :furtherCourses="$furtherCourses"
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses['further'] ?? []"
        />
    @endforeach
</div>
