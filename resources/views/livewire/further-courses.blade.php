<div>
    <div class="mb-5 text-lg"><b>{!! $furtherCourses['title'] ?? null !!}</b></div>
    <div class="mb-5">{!! $furtherCourses['description'] ?? null !!}</div>
    <div class="flex">
        <div class="w-[26rem] p-1 border-b"></div>
        <div class="flex gap-5 border-b">
            <div class="w-20 text-center">none</div>
            @foreach($nextSemesters AS $nextSemester)
                <div class="w-20 text-center ">{{ $nextSemester['short_name']}}</div>
            @endforeach
            <div class="w-20 text-center">later</div>
        </div>
    </div>
    
    @foreach($furtherCourses['specializations'] ?? $furtherCourses['clusters'] AS $courses)
        <livewire:course-group
            :courseGroup="$courses"
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses"
            :further="true"
        />
    @endforeach
</div>
