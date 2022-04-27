<div>
    <div class="mb-5 text-lg"><b>{!!$optionalEnglishTitle ?? null !!}</b></div>
    <div class="mb-5">{!! $optionalEnglishDescription  ?? null !!}</div>
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
    @foreach($courses AS $course)
        <livewire:course
                :courseGroupTypeShortName="null"
                :course="$course"
                :courseGroupId="4"
                :nextSemesters="$nextSemesters"
                :selectedCourses="[]"
                key="{{ $course['id'] }}"
        />
    @endforeach
</div>
