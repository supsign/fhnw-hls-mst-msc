
<div class="{{ $class }}">
    @if($title)
        <div class="mb-5 text-lg"><b>{!! $title !!}</b></div>
    @endif

    @if($description)
        <div class="mb-5">{!! $description !!}</div>
    @endif
    <div class="flex">
        <div class="w-[26rem] p-1 border-b"></div>
        <div class="w-10 border-b"></div>
        <div class="flex gap-5 border-b">
            <div class="w-20 text-center">none</div>
            @foreach($nextSemesters AS $nextSemester)
                <div class="w-20 text-center ">{{ $nextSemester['short_name']}}</div>
            @endforeach
            <div class="w-20 text-center">later</div>
        </div>
    </div>

    @foreach($courseGroups AS $courseGroup)
        <livewire:course-group
            :courseGroup="$courseGroup"
            :nextSemesters="$nextSemesters"
            :selectedCourses="$selectedCourses"
            further="true"
        />
    @endforeach
</div>
