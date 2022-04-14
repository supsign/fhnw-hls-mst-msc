<div class="flex">
    <div class="w-[26rem] border-b border-l border-r p-1">{{ $course['name'] }}</div><div class="w-10 border-b border-r p-1">{{  $internalName  }}</div>
    <livewire:radio-group :courseId="$course['id']" :courseName="$course['name']" :selectedSemester="$selectedSemester" :selectableSemesters="$selectableSemesters"/>
</div>
