<div class="flex flex-col">
    @if($specialisationCourseGroup && count($specialisationCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$specialisationCourseGroup" 
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses[$specialisationCourseGroup['id']] ?? []"
            class="my-5"
        />
    @endif

    @if($electiveCourseGroup && count($electiveCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$electiveCourseGroup" 
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses[$electiveCourseGroup['id']] ?? []"
            class="my-5"
        />
    @endif

    @if($coreCompetenceCourseGroup && count($coreCompetenceCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$coreCompetenceCourseGroup" 
            :nextSemesters="$nextSemesters"  
            :description="$coreCompetencesDescription"
            :selectedCourses="$selectedCourses[$coreCompetenceCourseGroup['id']] ?? []"
            class="my-5"
        />
    @endif

    @if($clusterSpecificCourseGroup && count($clusterSpecificCourseGroup['courses']))
        <livewire:course-group 
            :courseGroup="$clusterSpecificCourseGroup" 
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses[$clusterSpecificCourseGroup['id']] ?? []"
            class="my-5"
        />
    @endif

    <div>{{ $descriptionBeforeFurther }}</div>

{{--     @if(count($furtherSpecialisationCourseGroups))
        <livewire:further-course-groups 
            :groups="$furtherSpecialisationCourseGroups" 
            :nextSemesters="$nextSemesters" 
            :title="$furtherSpecialisationTitle"
            :selectedCourses="$selectedCourses"
            class="my-5"/>
    @endif

    @if(count($furtherClusterSpecificCourseGroups))
        <livewire:further-course-groups 
            :groups="$furtherClusterSpecificCourseGroups" 
            :nextSemesters="$nextSemesters" 
            :title="$furtherClusterTitle"
            :selectedCourses="$selectedCourses"
            class="my-5"/>
    @endif --}}
</div>
