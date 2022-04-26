<div class="flex">
    <div class="w-[26rem] border-b border-l border-r p-1">
        {{ $course['name'] }}
    </div>
    @if(!$further)
    <div class="w-10 border-r border-b p-1">
       {{  $courseGroupTypeShortName  }}
    </div>
    @endif
    <livewire:radio-group
        :courseId="$course['id']" 
        :courseName="$course['name']" 
        :selectableSemesters="$selectableSemesters"
        :courseGroupId="$courseGroupId"
        :selectedSemester="$further ? ($selectedCourses[$courseGroupId][$course['id']] ?? 'none') : ($selectedCourses[$course['id']] ?? 'none')"
    />
</div>
