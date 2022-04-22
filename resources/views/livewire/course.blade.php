<div class="flex">
    <div class="w-[26rem] border-b border-l border-r p-1">
        {{ $course['name'] }}
    </div>
    <div class="w-10 border-r border-b p-1">
       {{  $courseGroupTypeShortName  }}
    </div>
    <livewire:radio-group 
        :courseGroupId="$course['id']" 
        :courseName="$course['name']" 
        {{-- :selectedSemester="$selectedSemester"  --}}
        :selectableSemesters="$selectableSemesters"
        :courseGroupId="$courseGroupId"
    />
</div>
