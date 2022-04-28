<div class="flex hover:bg-gray-50" >
    <div class="w-[26rem] border-b border-l border-r p-1 relative " x-data="{ hover: false }">
        <span x-on:mouseover="hover = true" x-on:mouseout="hover = false">{{ $course['name'] }}</span>
        <x-tooltip x-show="hover" class="w-[26rem]">{{ $course['content'] }}</x-tooltip>
    </div>
    
    @if($showType)
        <div class="w-10 border-r border-b p-1 relative" x-data="{ hover: false }">
            <span x-on:mouseover="hover = true" x-on:mouseout="hover = false">{{  $courseGroupTypeShortName  }}</span>
            <x-tooltip x-show="hover" class="w-80">{{ $courseGroupTypeTooltip }}</x-tooltip>
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
