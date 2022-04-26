<div>
    <div class="mb-5 text-lg"><b>{{ $optionalEnglishTitle }}</b></div>
    <div>{!! $optionalEnglishDescription !!}</div>

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
