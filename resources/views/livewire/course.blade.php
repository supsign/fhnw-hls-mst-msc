<div class="flex border">
    <div class="w-[26rem] border-r p-1">
        {{ $course['name'] }}
    </div>
    
    <div class="w-10 border-r p-1">
        {{  $internalName  }}
    </div>
    <livewire:radio-group 
        :courseId="$course['id']" 
        :courseName="$course['name']" 
        :selectedSemester="$selectedSemester" 
        :selectableSemesters="$selectableSemesters"
    />
</div>
