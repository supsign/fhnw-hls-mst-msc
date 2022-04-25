<div class="flex flex-col gap-10">
    @if($specializationCourseGroup && count($specializationCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$specializationCourseGroup"
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses[$specializationCourseGroup['id']] ?? []"
            class="mb-5"
        />
    @endif

    @if($electiveCourseGroup && count($electiveCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$electiveCourseGroup" 
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses[$electiveCourseGroup['id']] ?? []"
            class="mb-5"
        />
    @endif

    @if($coreCompetencesCourseGroup && count($coreCompetencesCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$coreCompetencesCourseGroup"
            :nextSemesters="$nextSemesters"  
            :description="$coreCompetencesDescription"
            :selectedCourses="$selectedCourses[$coreCompetencesCourseGroup['id']] ?? []"
            class="mb-5"
        />
    @endif

    @if($clusterSpecificCourseGroup && count($clusterSpecificCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$clusterSpecificCourseGroup" 
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses[$clusterSpecificCourseGroup['id']] ?? []"
            class="mb-5"
        />
    @endif
        <div class="sticky top-3 bg-hls p-2 z-10 self-end w-[30rem]">{!! $descriptionBeforeFurther  !!} Current ECTS: {{ $ects }}/50</div>
    @if(count($furtherSpecializationCourseGroups))
        <livewire:further-course-groups 
            :courseGroups="$furtherSpecializationCourseGroups" 
            :nextSemesters="$nextSemesters" 
            :title="$furtherSpecialisationTitle"
            :selectedCourses="$selectedCourses"
            class="-mt-20 mb-5"
        />
    @endif

    @if(count($furtherClusterSpecificCourseGroups))
        <livewire:further-course-groups 
            :courseGroups="$furtherClusterSpecificCourseGroups" 
            :nextSemesters="$nextSemesters" 
            :title="$furtherClusterTitle"
            :selectedCourses="$selectedCourses"
            class="mb-5"
        />
    @endif

</div>
